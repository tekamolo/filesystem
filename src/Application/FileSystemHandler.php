<?php


namespace FileSystem\Application;


use FileSystem\Application\Exception\ExceptionDeletingFolderYouAreIn;
use FileSystem\Domain\Exception\theResourceIsNotADirectory;
use FileSystem\Domain\File;
use FileSystem\Domain\FileId;
use FileSystem\Domain\Folder;
use FileSystem\Domain\FolderId;
use FileSystem\Domain\Pointer;
use FileSystem\Domain\ResourceFactory;
use FileSystem\Domain\ResourceInterface;
use FileSystem\Domain\TypeInterface;
use FileSystem\Shared\AggregateId;
use FileSystem\Shared\ResourceCollection;
use FileSystem\Shared\DateTime;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Aqui es en donde gestiono eventos como borrar, crear y desplazar el punteador/pointer.
 * Class FileSystemHandler
 * @package FileSystem\Application
 */
class FileSystemHandler
{
    private Pointer $pointer;

    private ResourceFactory $resourceFactory;

    private ResourceCollection $resources;

    public function __construct(Pointer $pointer)
    {
        $this->pointer = $pointer;
        $this->resourceFactory = new ResourceFactory();
        $this->resources = new ResourceCollection();

        /**
         * Por defecto creo un directorio raiz. La fecha puede ser ajustada a algo más coherente
         */
        $folder = $this->resourceFactory->createFolder(new FolderId("//"),"/",new DateTime(),null);
        $this->resources->addOffset($folder->getAggregateId(),$folder);
        $this->pointer->enterFolder($folder);
    }

    public function createFolder(FolderId $folderId,string $name,DateTime $dateTime): void
    {
        $parentFolder = $this->pointer->getCurrentFolder();
        $folder = $this->resourceFactory->createFolder($folderId,$name,$dateTime,$parentFolder->getAggregateId());
        $this->resources->addOffset($folder->getAggregateId(),$folder);
        $parentFolder->addChildResource($folder->getAggregateId());
    }

    public function enterFolder(FolderId $folderId): void
    {
        /** @var TypeInterface|Folder $resource */
        $resource = $this->resources->get($folderId);
        if($resource->isDirectory() === false){
            throw new theResourceIsNotADirectory();
        }
        $this->pointer->enterFolder($resource);
    }

    public function goToParentFolder(): void
    {
        $currentFolder = $this->pointer->getCurrentFolder();
        $parentFolderId = $currentFolder->getParentAggregateId();
        $parentFolder = $this->resources->get($parentFolderId);
        $this->pointer->enterFolder($parentFolder);
    }

    public function getCurrentPointerFolderLocation(): Folder
    {
        return $this->pointer->getCurrentFolder();
    }

    public function getCurrentFolderResources(): ResourceCollection
    {
        $collectionReferences =  $this->pointer->getFolderResources();
        $filteredResourceCollection = new ResourceCollection();
        foreach ($collectionReferences as $c){
            $filteredResourceCollection->add($this->resources->getByStringReference($c));
        }
        return $filteredResourceCollection;
    }

    public function createFile(FileId $fileId,string $name, DateTime $dateTime, FolderId $containingFolderId): void
    {
        $file = $this->resourceFactory->createFile($fileId,$name,$dateTime,$containingFolderId);
        $this->resources->addOffset($file->getAggregateId(),$file);
        $parentFolder = $this->pointer->getCurrentFolder();
        $parentFolder->addChildResource($file->getAggregateId());
    }

    public function deleteFile(FileId $fileId): void
    {
        $this->resources->remove($fileId);
        $parentFolder = $this->pointer->getCurrentFolder();
        $parentFolder->detachChildResource($fileId);
    }

    /**
     * Para borrar un folder uso recursividad lo que me permite condensar código, También evito tener que mapear
     * la estrúctura árbol/arborescence. El punto flaco es si jamás un problema surge.
     * @param FolderId $folderId
     */
    public function deleteFolder(FolderId $folderId){
        /**
         * TODO: aqui también tendría que controllar que no estamos borrando un directorio pariente. Tendría que verificar
         * cada bloque hasta llegar al directorio raíz el cual en teoría no se tendría que borrar
         */
        if($folderId->equals($this->pointer->getCurrentFolder()->getAggregateId())){
            throw new ExceptionDeletingFolderYouAreIn();
        }

        $parentFolder = $this->pointer->getCurrentFolder();
        $parentFolder->detachChildResource($folderId);
        $currentResources = $this->pointer->getFolderResources();
        foreach ($currentResources as $r){
            $resource = $this->resources->getByStringReference($r);
            if($resource instanceof Folder){
                $this->deleteFolder($resource->getAggregateId());
            }else{
                $this->deleteFile($resource->getAggregateId());
            }
        }
    }
}
<?php


namespace FileSystem\Application;


use FileSystem\Application\Exception\ExceptionDeletingFolderYouAreIn;
use FileSystem\Application\Exception\ResourceWithTheSameNameAlreadyExistException;
use FileSystem\Domain\Exception\theResourceIsNotADirectory;
use FileSystem\Domain\FileId;
use FileSystem\Domain\FileName;
use FileSystem\Domain\FileSystem;
use FileSystem\Domain\FileSystemInterface;
use FileSystem\Domain\Folder;
use FileSystem\Domain\FolderId;
use FileSystem\Domain\FolderPath;
use FileSystem\Domain\IdFactory;
use FileSystem\Domain\Pointer;
use FileSystem\Domain\ResourceFactory;
use FileSystem\Domain\TypeInterface;
use FileSystem\Shared\AggregateId;
use FileSystem\Shared\ResourceCollection;
use FileSystem\Shared\DateTime;
use FileSystem\Shared\ValueObjectString;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Aqui es en donde gestiono eventos como borrar, crear y desplazar el punteador/pointer.
 * Si fueran más complicados se podrían dividir en diferentes clases
 * Class FileSystemHandler
 * @package FileSystem\Application
 */
class FileSystemHandler implements FileSystemInterface
{
    private const FOLDER_ROOT_PATH = "/";

    private Pointer $pointer;

    private ResourceFactory $resourceFactory;

    private FileSystem $fileSystem;

    public function __construct(Pointer $pointer,FileSystem $fileSystem)
    {
        $this->pointer = $pointer;
        $this->resourceFactory = new ResourceFactory();
        $this->fileSystem = $fileSystem;

        /**
         * Por defecto creo un directorio raiz. La fecha puede ser ajustada a algo más coherente (anterior a lo que tenemos en el mock)
         */
        $folder = $this->resourceFactory->createFolder(new FolderId(self::FOLDER_ROOT_PATH),new FolderPath(self::FOLDER_ROOT_PATH),new DateTime(),null);
        $this->fileSystem->addResource($folder);
        $this->pointer->enterFolder($folder);
    }

    public function createFolder(FolderPath $folderPath, DateTime $dateTime): FolderId
    {
        $folderId = IdFactory::createFolderId($folderPath);
        $this->checkResourceWithTheSameName($folderId,$folderPath);
        $parentFolder = $this->pointer->getCurrentFolder();
        $folder = $this->resourceFactory->createFolder($folderId,$folderPath,$dateTime,$parentFolder->getAggregateId());
        $this->fileSystem->addResource($folder);
        $parentFolder->addChildResource($folder->getAggregateId());
        return $folderId;
    }

    public function enterFolder(FolderId $folderId): void
    {
        /** @var TypeInterface|Folder $resource */
        $resource = $this->fileSystem->getResourceByReference($folderId);
        if($resource->isDirectory() === false){
            throw new theResourceIsNotADirectory();
        }
        $this->pointer->enterFolder($resource);
    }

    public function goToParentFolder(): void
    {
        $currentFolder = $this->pointer->getCurrentFolder();
        $parentFolderId = $currentFolder->getParentAggregateId();
        $parentFolder = $this->fileSystem->getResourceByAggregateId($parentFolderId);
        $this->pointer->enterFolder($parentFolder);
    }

    public function getCurrentPointerFolderLocation(): Folder
    {
        return $this->pointer->getCurrentFolder();
    }

    public function getCurrentFolderResources(): ResourceCollection
    {
        $collectionReferences =  $this->pointer->getFolderResourcesReferences();
        $filteredResourceCollection = new ResourceCollection();
        foreach ($collectionReferences as $r){
            $filteredResourceCollection->add($this->fileSystem->getResourceByReference($r));
        }
        return $filteredResourceCollection;
    }

    public function createFile(FileName $name, DateTime $dateTime): FileId
    {
        $parentFolder = $this->pointer->getCurrentFolder();
        $fileId = IdFactory::createFileId($parentFolder->getName(),$name);
        $this->checkResourceWithTheSameName($fileId,$name);
        $file = $this->resourceFactory->createFile($fileId,$name,$dateTime,$parentFolder->getAggregateId());
        $this->fileSystem->addResource($file);
        $parentFolder = $this->pointer->getCurrentFolder();
        $parentFolder->addChildResource($file->getAggregateId());
        return $fileId;
    }

    private function checkResourceWithTheSameName(AggregateId $resourceId, ValueObjectString $name){
        /** @var string $reference */
        foreach ($this->pointer->getFolderResourcesReferences() as $reference){
            if($resourceId->value() === $reference){
                throw new ResourceWithTheSameNameAlreadyExistException("The resource ".$name->get()." already exists, please chose another name");
            }
        }
    }

    public function deleteFile(FileId $fileId): void
    {
        $this->fileSystem->removeResource($fileId);
        $parentFolder = $this->pointer->getCurrentFolder();
        $parentFolder->detachChildResource($fileId);
    }

    /**
     * Para borrar un folder uso recursividad lo que me permite condensar código, También evito tener que mapear
     * la estrúctura árbol/arborescence. El punto flaco es si jamás un problema surge.
     * @param FolderId $folderId
     */
    public function deleteFolder(FolderId $folderId): void
    {
        /**
         * Aqui verifico que el directorio que estoy borrando no es un ante pasado
         */
        if($this->isAncestorFolder($folderId)){
            throw new ExceptionDeletingFolderYouAreIn("You cannot remove the folder where you are in or an ancestor");
        }

        $parentFolder = $this->pointer->getCurrentFolder();
        $parentFolder->detachChildResource($folderId);
        $currentResources = $this->pointer->getFolderResourcesReferences();
        foreach ($currentResources as $r){
            $resource = $this->fileSystem->getResourceByReference($r);
            if($resource instanceof Folder){
                $this->deleteFolder($resource->getAggregateId());
            }else{
                $this->deleteFile($resource->getAggregateId());
            }
        }
    }

    private function isAncestorFolder(FolderId $folderId): bool
    {
        $parentFolder = $this->pointer->getCurrentFolder();
        $folderToBeDeleted = $this->fileSystem->getResourceByAggregateId($folderId);

        while ($parentFolder->getAggregateId()->value() != $this->fileSystem->getRootFolder()->getAggregateId()->value()){
            if($parentFolder->getAggregateId()->value() === $folderToBeDeleted->getAggregateId()->value()){
                return true;
            }
            $parentFolder = $this->fileSystem->getResourceByAggregateId($parentFolder->getParentAggregateId());
        }
        return false;
    }
}
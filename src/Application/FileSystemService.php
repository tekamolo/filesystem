<?php
namespace FileSystem\Application;

use FileSystem\Domain\FileId;
use FileSystem\Domain\FileName;
use FileSystem\Domain\FolderId;
use FileSystem\Domain\FolderPath;
use FileSystem\Domain\IdFactory;
use FileSystem\Domain\MemoryInterface;
use FileSystem\Domain\ResourceInterface;
use FileSystem\Domain\TypeInterface;
use FileSystem\Infrastructure\ResourceMemory;
use FileSystem\Shared\AggregateId;
use FileSystem\Shared\DateTime;
use FileSystem\Shared\ResourceCollection;

/**
 * El file system service servirá de capa/interfaz para los commandos gestionados desde el exterior
 * Los eventos seran gestionados por la capa siguiente el FileSystemHandler, acciones como crear, borrar ficheros o folders
 * Aqui en FileSystemService la usaremos para hacer displays de información o interactuar con la capa siguiente.
 * Class FileSystemService
 * @package FileSystem\Application
 */

final class FileSystemService
{
    private FileSystemHandler $fileSystemHandler;
    private MemoryInterface $memory;

    /**
     * Como comentado la interface Memoria que pretende actuar como un repositorio o un pack hibernate/sleep tiene
     * como objetivo interactuar con el aggregate root. Aqui FileSystem que contiene los aggregates File y Folder
     * como el ejercicio no pide esto lo dejo aqui ya que es un pattern habitual
     * FileSystemService constructor.
     * @param FileSystemHandler $fileSystemHandler
     * @param MemoryInterface $memory
     */
    public function __construct(FileSystemHandler $fileSystemHandler, MemoryInterface $memory)
    {
        $this->fileSystemHandler = $fileSystemHandler;
    }

    public function createFolder(FolderPath $name,DateTime $dateTime): FolderId
    {
        return $this->fileSystemHandler->createFolder($name,$dateTime);
    }

    /**
     * Para movernos a traves de los differentes folders/carpetas usaremos un punteador que nos locarizará
     * imitando un poco lo que pasaría cuando exploramos un file system windows o en commando dos/linux
     * @param FolderId $folderId
     */
    public function enterFolder(FolderId $folderId): void
    {
        $this->fileSystemHandler->enterFolder($folderId);
    }

    public function goToParentFolder(): void
    {
        $this->fileSystemHandler->goToParentFolder();
    }

    public function createFile(FileName $name, DateTime $dateTime): FileId
    {
        return $this->fileSystemHandler->createFile($name,$dateTime);
    }

    public function deleteFile(FileId $fileId): void
    {
        $this->fileSystemHandler->deleteFile($fileId);
    }

    public function deleteFolder(FolderId $folderId):void
    {
        $this->fileSystemHandler->deleteFolder($folderId);
    }

    public function getCurrentFolderDetails(): string
    {
        $folder = $this->fileSystemHandler->getCurrentPointerFolderLocation();
        return $folder->getName()->get(). " created at " .$folder->getCreated()->getDatetimeFileSystem();
    }

    public function getCurrentFolderId(): FolderId
    {
        $folder = $this->fileSystemHandler->getCurrentPointerFolderLocation();
        return $folder->getAggregateId();
    }

    public function getCurrentFolderResources(): ResourceCollection
    {
        return $this->fileSystemHandler->getCurrentFolderResources();
    }

    public function displayDirectoryResources(): string
    {
        /** @var ResourceInterface|TypeInterface $r */
        $output = "";
        foreach ($this->fileSystemHandler->getCurrentFolderResources() as $r){
            $isDirectory = $r->isDirectory() ? "yes" : "no";
            $output .= $r->getName()->get()." ".$r->getCreated()->getDatetimeFileSystem()." isDirectory: ".$isDirectory." \n ";
        }

        return $output;
    }
}
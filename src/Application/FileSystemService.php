<?php
namespace FileSystem\Application;

use FileSystem\Domain\FileId;
use FileSystem\Domain\FolderId;
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

    public function __construct(FileSystemHandler $fileSystemHandler)
    {
        $this->fileSystemHandler = $fileSystemHandler;
    }

    public function createFolder(FolderId $folderId,string $name,DateTime $dateTime): void
    {
        $this->fileSystemHandler->createFolder($folderId,$name,$dateTime);
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

    public function createFile(FileId $fileId,string $name, DateTime $dateTime, FolderId $containerFolderId): void
    {
        $this->fileSystemHandler->createFile($fileId,$name,$dateTime,$containerFolderId);
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
        return $folder->getRoot(). " created at " .$folder->getCreated()->getDatetimeFileSystem();
    }

    public function getCurrentFolderResources(): ResourceCollection
    {
        return $this->fileSystemHandler->getCurrentFolderResources();
    }
}
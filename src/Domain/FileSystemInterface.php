<?php


namespace FileSystem\Domain;


use FileSystem\Shared\DateTime;
use FileSystem\Shared\ResourceCollection;

interface FileSystemInterface
{
    public function createFolder(FolderPath $fold, DateTime $dateTime): FolderId;
    public function enterFolder(FolderId $folderId): void;
    public function goToParentFolder(): void;
    public function getCurrentPointerFolderLocation(): Folder;
    public function getCurrentFolderResources(): ResourceCollection;
    public function createFile(FileName $name, DateTime $dateTime): FileId;
    public function deleteFile(FileId $fileId): void;
    public function deleteFolder(FolderId $folderId): void;
}
<?php


namespace FileSystem\Domain;


use FileSystem\Shared\DateTime;

class ResourceFactory
{
    public function createFolder(FolderId $folderId, FolderPath $folderPath, DateTime $created, ?FolderId $parentFolderId): Folder
    {
        return new Folder($folderId,$folderPath,$created,$parentFolderId);
    }

    public function createFile(FileId $fileId,FileName $name, DateTime $created,FolderId $folder): File
    {
        return new File($fileId,$name,$created,$folder);
    }
}
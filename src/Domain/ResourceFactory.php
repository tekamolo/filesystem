<?php


namespace FileSystem\Domain;


use FileSystem\Shared\DateTime;

class ResourceFactory
{
    public function createFolder(FolderId $folderId,string $root,DateTime $created,?FolderId $parentFolderId): Folder
    {
        return new Folder($folderId,$root,$created,$parentFolderId);
    }

    public function createFile(FileId $fileId,string $name, DateTime $created,FolderId $folder): File
    {
        return new File($fileId,$name,$created,$folder);
    }
}
<?php


namespace FileSystem\Domain;


use FileSystem\Shared\ValueObjectString;

class IdFactory
{
    static public function createFolderId(FolderPath $folderPath): FolderId
    {
        return new FolderId($folderPath->get());
    }

    static public function createFileId(ValueObjectString $folderPath,FileName $fileName): FileId
    {
        return new FileId($folderPath->get().$fileName->get());
    }
}
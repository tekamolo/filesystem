<?php


namespace FileSystem\Domain;


use FileSystem\Shared\ReferenceCollection;

/**
 * El punteador es una simple clase que opera como un contenedor
 * Class Pointer
 * @package FileSystem\Domain
 */
class Pointer
{
    private Folder $folder;

    public function enterFolder(Folder $folder){
        $this->folder = $folder;
    }

    public function getCurrentFolder(): Folder
    {
        return $this->folder;
    }

    public function getFolderResourcesReferences(): ReferenceCollection
    {
        return $this->folder->getResources();
    }
}
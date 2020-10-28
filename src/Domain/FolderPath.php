<?php


namespace FileSystem\Domain;


use FileSystem\Domain\Exception\InvalidPathNameException;
use FileSystem\Shared\ValueObjectString;

class FolderPath implements ValueObjectString
{
    private string $path;

    public function __construct(string $path){
        $this->setNamePath($path);
    }

    private function setNamePath(string $path): void
    {
        if(empty(preg_match("#(.)*/$#",$path))){
            throw new InvalidPathNameException("Invalid Path name: ".$path);
        }
        $this->path = $path;
    }

    public function get(): string
    {
        return $this->path;
    }
}
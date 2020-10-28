<?php


namespace FileSystem\Domain;


use FileSystem\Domain\Exception\InvalidFileNameException;
use FileSystem\Domain\Exception\InvalidPathNameException;
use FileSystem\Shared\ValueObjectString;

final class FileName implements ValueObjectString
{
    private string $name;

    public function __construct(string $name)
    {
        $this->setFileName($name);
    }

    private function setFileName(string $name): void
    {
        if(empty(preg_match("#.*\.{1}.+$#",$name))){
            throw new InvalidFileNameException("Invalid File name");
        }
        $this->name = $name;
    }

    public function get(): string
    {
        return $this->name;
    }
}
<?php


namespace FileSystem\Domain;


interface TypeInterface
{
    public function isDirectory(): bool;
}
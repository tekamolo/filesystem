<?php


namespace Tests\Domain;


use FileSystem\Domain\FolderPath;
use PHPUnit\Framework\TestCase;

class FolderPathTest extends TestCase
{
    public function testIncorrectName(){
        $this->expectException(\Exception::class);
        $folderPath = new FolderPath("Home");
    }

    public function testCorrectName(){
        $folderPath = new FolderPath("Home/");
        $this->assertEquals("Home/",$folderPath->get());

        $folderPath = new FolderPath("/");
        $this->assertEquals("/",$folderPath->get());
    }
}
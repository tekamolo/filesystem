<?php


namespace Tests\Domain;


use FileSystem\Domain\Exception\InvalidFileNameException;
use FileSystem\Domain\FileName;
use PHPUnit\Framework\TestCase;

class FileNameTest extends TestCase
{
    public function testCorrectName(){
        $fileName = new FileName("elections.pdf");
        $this->assertEquals("elections.pdf",$fileName->get());

        $fileName = new FileName(".gitignore");
        $this->assertEquals(".gitignore",$fileName->get());
    }

    public function testUncorrectName(){
        $this->expectException(InvalidFileNameException::class);
        $fileName = new FileName("electionspdf");
    }

    public function testUncorrectNameEnding(){
        $this->expectException(InvalidFileNameException::class);
        $fileName = new FileName("electionspdf.");
    }

}
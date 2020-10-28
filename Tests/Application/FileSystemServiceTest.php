<?php

namespace Tests\Application;

use FileSystem\Application\Exception\ExceptionDeletingFolderYouAreIn;
use FileSystem\Application\Exception\ResourceWithTheSameNameAlreadyExistException;
use FileSystem\Application\FileSystemHandler;
use FileSystem\Application\FileSystemService;
use FileSystem\Domain\FileName;
use FileSystem\Domain\FileSystem;
use FileSystem\Domain\FileSystemId;
use FileSystem\Domain\FolderPath;
use FileSystem\Domain\Pointer;
use FileSystem\Domain\ResourceInterface;
use FileSystem\Infrastructure\ResourceMemory;
use FileSystem\Shared\AggregateId;
use FileSystem\Shared\DateTime;
use PHPUnit\Framework\TestCase;

class FileSystemServiceTest extends TestCase
{
    private FileSystemService $service;

    public function setUp()
    {
       $this->service = new FileSystemService(
           new FileSystemHandler(
               new Pointer(),
               new FileSystem(new FileSystemId("1"))
           ),
           new ResourceMemory()
       );
    }

    public function testFolderCreation(){
        $folderId = $this->service->createFolder(new FolderPath("Home/"),new DateTime("2012-09-28 19:35:00"));
        /** @var $directoryResources ResourceInterface[] */
        $directoryResources = $this->service->getCurrentFolderResources();
        $directoryResources[0]->getName();

        $this->assertEquals("Home/",$directoryResources[0]->getName()->get());
        $this->assertEquals($folderId,$directoryResources[0]->getAggregateId());
    }

    public function testEnterFolder(){
        $folderId = $this->service->createFolder(new FolderPath("Home/"),new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderId);

        $this->assertEquals("Home/ created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());
        $this->assertEquals($folderId,$this->service->getCurrentFolderId());
    }

    public function testGoBackPreviousFolder(){
        $baseFolderId = $this->service->createFolder(new FolderPath("Home/"),new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($baseFolderId);
        $this->assertEquals("Home/ created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());
        $this->assertEquals($baseFolderId,$this->service->getCurrentFolderId());

        $folderProjectId = $this->service->createFolder(new FolderPath("Home/MyProject/"),new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderProjectId);
        $this->assertEquals("Home/MyProject/ created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());
        $this->assertEquals($folderProjectId,$this->service->getCurrentFolderId());

        $this->service->goToParentFolder();
        $this->assertEquals("Home/ created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());
        $this->assertEquals($baseFolderId,$this->service->getCurrentFolderId());
    }

    public function testCreateResource(){
        $folderId = $this->service->createFolder(new FolderPath("Home/"),new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderId);
        $fileAggregateId = $this->service->createFile(new FileName("main_logo.png"),new DateTime("2013-02-01 09:35:00"));

        /** @var $resources ResourceInterface[] */
        $resources = $this->service->getCurrentFolderResources();
        $this->assertEquals($fileAggregateId,$resources[0]->getAggregateId());
    }

    public function testCannotCreateTwiceTheSameFileAtTheSameLocation(){
        $folderId = $this->service->createFolder(new FolderPath("Home/"),new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderId);

        $this->expectException(ResourceWithTheSameNameAlreadyExistException::class);
        $this->expectExceptionMessage("The resource main_logo.png already exists, please chose another name");
        $this->service->createFile(new FileName("main_logo.png"),new DateTime("2013-02-01 09:35:00"));
        $this->service->createFile(new FileName("main_logo.png"),new DateTime("2013-02-01 09:35:00"));
    }

    public function testCannotCreateTwiceTheSameFolderAtTheSameLocation(){
        $this->expectException(ResourceWithTheSameNameAlreadyExistException::class);
        $this->expectExceptionMessage("The resource Home/ already exists, please chose another name");
        $this->service->createFolder(new FolderPath("Home/"),new DateTime("2012-09-28 19:35:00"));
        $this->service->createFolder(new FolderPath("Home/"),new DateTime("2012-09-28 19:35:00"));
    }

    public function testCannotDeleteCurrentFolder(){
        $folderId = $this->service->createFolder(new FolderPath("Home/"),new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderId);
        $this->expectException(ExceptionDeletingFolderYouAreIn::class);
        $this->service->deleteFolder($folderId);
    }

    public function testCannotDeleteParentFolder(){
        $folderId = $this->service->createFolder(new FolderPath("Home/"),new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderId);
        $folderProjectId = $this->service->createFolder(new FolderPath("Home/MyProject/"),new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderProjectId);

        $this->expectException(ExceptionDeletingFolderYouAreIn::class);
        $this->service->deleteFolder($folderId);
    }

    public function testDeleteFile(){
        $folderId = $this->service->createFolder(new FolderPath("Home/"),new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderId);
        $this->assertEquals($folderId,$this->service->getCurrentFolderId());

        $fileAggregateId = $this->service->createFile(new FileName("main_logo.png"),new DateTime("2013-02-01 09:35:00"));
        $this->service->deleteFile($fileAggregateId);

        /** @var $resources ResourceInterface[] */
        $resources = $this->service->getCurrentFolderResources();
        $this->assertEmpty($resources);
    }

    public function testDeleteFolder(){
        $homeFolderId = $this->service->createFolder(new FolderPath("Home/"),new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($homeFolderId);
        $this->assertEquals("Home/ created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());

        $folderProjectId = $this->service->createFolder(new FolderPath("Home/MyProject/"),new DateTime("2013-02-01 09:35:00"));
        $this->service->enterFolder($folderProjectId);
        $this->assertEquals("Home/MyProject/ created at 2013-02-01 09:35:00",$this->service->getCurrentFolderDetails());
        $this->service->createFile(new FileName("main_logo.png"),new DateTime("2013-02-01 09:35:00"));

        $this->service->enterFolder($homeFolderId);
        $this->service->deleteFolder($folderProjectId);

        $resources = $this->service->getCurrentFolderResources();
        $this->assertEmpty($resources);
    }

    public function testFileSystemServiceMockSubject(){
        $folderHomeId =$this->service->createFolder(new FolderPath("Home/"),new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderHomeId);
        $this->assertEquals("Home/ created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());

        $folderMyProjectId = $this->service->createFolder(new FolderPath("Home/MyProject/"),new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderMyProjectId);
        $this->assertEquals("Home/MyProject/ created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());

        $folderImagesId = $this->service->createFolder(new FolderPath("Home/MyProject/images/"),new DateTime("2013-02-01 09:35:00"));
        $this->service->createFolder(new FolderPath("Home/MyProject/src/"),new DateTime("2013-02-01 09:35:00"));
        $this->service->createFolder(new FolderPath("Home/MyProject/test/"),new DateTime("2013-02-01 09:35:00"));
        $this->service->createFile(new FileName("README.md"),new DateTime("2013-02-01 09:35:00"));
        $this->assertEquals(
            'Home/MyProject/images/ 2013-02-01 09:35:00 isDirectory: yes 
 Home/MyProject/src/ 2013-02-01 09:35:00 isDirectory: yes 
 Home/MyProject/test/ 2013-02-01 09:35:00 isDirectory: yes 
 README.md 2013-02-01 09:35:00 isDirectory: no 
 ',
            $this->service->displayDirectoryResources()
        );

        $this->service->enterFolder($folderImagesId);
        $this->assertEquals("Home/MyProject/images/ created at 2013-02-01 09:35:00",$this->service->getCurrentFolderDetails());


        $fileMainLogoId = $this->service->createFile(new FileName("main_logo.png"),new DateTime("2013-02-01 09:35:00"));
        $fileSmallLogoId =$this->service->createFile(new FileName("logo_small.png"),new DateTime("2013-02-01 09:35:00"));
        $iconId = $this->service->createFile(new FileName("icons.png"),new DateTime("2013-02-01 09:35:00"));


        /** @var $resources ResourceInterface[] */
        $resources = $this->service->getCurrentFolderResources();
        $this->assertEquals($fileMainLogoId,$resources[0]->getAggregateId());
        $this->assertEquals($fileSmallLogoId,$resources[1]->getAggregateId());
        $this->assertEquals($iconId,$resources[2]->getAggregateId());

        $this->assertEquals(
            'main_logo.png 2013-02-01 09:35:00 isDirectory: no 
 logo_small.png 2013-02-01 09:35:00 isDirectory: no 
 icons.png 2013-02-01 09:35:00 isDirectory: no 
 ',
            $this->service->displayDirectoryResources()
        );
    }
}
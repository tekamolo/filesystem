<?php

namespace Tests\Application;

use FileSystem\Application\FileSystemHandler;
use FileSystem\Application\FileSystemService;
use FileSystem\Domain\FileId;
use FileSystem\Domain\Pointer;
use FileSystem\Domain\FolderId;
use FileSystem\Domain\ResourceInterface;
use FileSystem\Shared\DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\VarDumper\VarDumper;

class FileSystemServiceTest extends TestCase
{
    private FileSystemService $service;

    public function setUp()
    {
       $this->service = new FileSystemService(new FileSystemHandler(new Pointer()));
    }

    public function testFolderCreation(){
        $folderId = new FolderId("Directory\\");
        $this->service->createFolder($folderId,"Home\\",new DateTime("2012-09-28 19:35:00"));
        /** @var $directoryResources ResourceInterface[] */
        $directoryResources = $this->service->getCurrentFolderResources();
        $directoryResources[0]->getAggregateId();

        $this->assertEquals($folderId,$directoryResources[0]->getAggregateId());
    }

    public function testEnterFolder(){
        $folderId = new FolderId("Directory\\");
        $this->service->createFolder($folderId,"Home\\",new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderId);

        $this->assertEquals("Home\ created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());
    }

    public function testGoBackPreviousFolder(){
        $folderId = new FolderId("Home\\");
        $this->service->createFolder($folderId,"Home\\",new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderId);
        $this->assertEquals("Home\ created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());

        $folderId = new FolderId("Home\\MyProject");
        $this->service->createFolder($folderId,"Home\\MyProject",new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderId);
        $this->assertEquals("Home\MyProject created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());

        $this->service->goToParentFolder();
        $this->assertEquals("Home\ created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());
    }

    public function testCreateResource(){
        $folderId = new FolderId("Home\\");
        $this->service->createFolder($folderId,"Home\\",new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderId);
        $this->assertEquals("Home\ created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());

        $fileAggregateId = new FileId("main_logo.png");
        $this->service->createFile($fileAggregateId,"main_logo.png",new DateTime("2013-02-01 09:35:00"),$folderId);

        /** @var $resources ResourceInterface[] */
        $resources = $this->service->getCurrentFolderResources();
        $this->assertEquals($fileAggregateId,$resources[0]->getAggregateId());
    }

    public function testDeleteFile(){
        $folderId = new FolderId("Home\\");
        $this->service->createFolder($folderId,"Home\\",new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderId);
        $this->assertEquals("Home\ created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());

        $fileAggregateId = new FileId("main_logo.png");
        $this->service->createFile($fileAggregateId,"main_logo.png",new DateTime("2013-02-01 09:35:00"),$folderId);
        $this->service->deleteFile($fileAggregateId);

        /** @var $resources ResourceInterface[] */
        $resources = $this->service->getCurrentFolderResources();
        $this->assertEmpty($resources);
    }

    public function testDeleteFolder(){
        $homeFolderId = new FolderId("Home\\");
        $this->service->createFolder($homeFolderId,"Home\\",new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($homeFolderId);
        $this->assertEquals("Home\ created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());

        $folderProjectId = new FolderId("Home\\MyProject");
        $this->service->createFolder($folderProjectId,"Home\\MyProject",new DateTime("2013-02-01 09:35:00"));
        $this->service->enterFolder($folderProjectId);
        $this->assertEquals("Home\MyProject created at 2013-02-01 09:35:00",$this->service->getCurrentFolderDetails());
        $fileAggregateId = new FileId("main_logo.png");
        $this->service->createFile($fileAggregateId,"main_logo.png",new DateTime("2013-02-01 09:35:00"),$homeFolderId);

        $this->service->enterFolder($homeFolderId);
        $this->service->deleteFolder($folderProjectId);

        $resources = $this->service->getCurrentFolderResources();
        $this->assertEmpty($resources);
    }

    public function testFileSystemServiceMockSubject(){
        $folderHomeId = new FolderId("Home\\");
        $this->service->createFolder($folderHomeId,"Home\\",new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderHomeId);
        $this->assertEquals("Home\ created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());

        $folderMyProjectId = new FolderId("Home\\MyProject");
        $this->service->createFolder($folderMyProjectId,"Home\\MyProject",new DateTime("2012-09-28 19:35:00"));
        $this->service->enterFolder($folderMyProjectId);
        $this->assertEquals("Home\MyProject created at 2012-09-28 19:35:00",$this->service->getCurrentFolderDetails());

        $folderImagesId = new FolderId("Home\\MyProject\\images");
        $this->service->createFolder($folderImagesId,"Home\\MyProject\\images",new DateTime("2013-02-01 09:35:00"));
        $this->service->createFolder(new FolderId("Home\\MyProject\\src"),"Home\\MyProject\\src",new DateTime("2013-02-01 09:35:00"));
        $this->service->createFolder(new FolderId("Home\\MyProject\\test"),"Home\\MyProject\\test",new DateTime("2013-02-01 09:35:00"));
        $this->service->createFile(new FileId("README.md"),"README.md",new DateTime("2013-02-01 09:35:00"),$folderMyProjectId);
        $this->assertEquals(
            'Home\MyProject\images 2013-02-01 09:35:00 isDirectory: yes 
 Home\MyProject\src 2013-02-01 09:35:00 isDirectory: yes 
 Home\MyProject\test 2013-02-01 09:35:00 isDirectory: yes 
 README.md 2013-02-01 09:35:00 isDirectory: no 
 ',
            $this->service->displayDirectoryResources()
        );

        $this->service->enterFolder($folderImagesId);
        $this->assertEquals("Home\MyProject\images created at 2013-02-01 09:35:00",$this->service->getCurrentFolderDetails());


        $fileMainLogoId = new FileId("main_logo.png");
        $this->service->createFile($fileMainLogoId,"main_logo.png",new DateTime("2013-02-01 09:35:00"),$folderImagesId);
        $fileSmallLogoId = new FileId("logo_small.png");
        $this->service->createFile($fileSmallLogoId,"logo_small.png",new DateTime("2013-02-01 09:35:00"),$folderImagesId);
        $iconId = new FileId("icons.png");
        $this->service->createFile($iconId,"icons.png",new DateTime("2013-02-01 09:35:00"),$folderImagesId);


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
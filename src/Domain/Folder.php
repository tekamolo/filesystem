<?php

namespace FileSystem\Domain;

use FileSystem\Shared\AggregateId;
use FileSystem\Shared\DateTime;
use FileSystem\Shared\ReferenceCollection;

/**
 * He tenido que diseñar estas entidades la cual folder lleva referencias de otras instancias Folder y File
 * Class Folder
 * @package FileSystem\Domain
 */
class Folder implements TypeInterface,ResourceInterface
{
    private FolderId $folderId;
    private string $name;
    private DateTime $created;
    private ReferenceCollection $childResources;
    private ?FolderId $parentFolderId;

    public function __construct(FolderId $folderId, string $root, DateTime $created,?FolderId $parentFolderId)
    {
        $this->folderId = $folderId;
        $this->name = $root;
        $this->created = $created;
        $this->childResources = new ReferenceCollection();
        $this->parentFolderId = $parentFolderId;
    }

    /**
     * @return FolderId
     */
    public function getAggregateId(): FolderId
    {
        return $this->folderId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function addChildResource(AggregateId $aggregateId){
        $this->childResources->add($aggregateId);
    }

    public function detachChildResource(AggregateId $aggregateId){
        $this->childResources->remove($aggregateId);
    }

    public function getResources(): ReferenceCollection
    {
        return $this->childResources;
    }

    public function isDirectory(): bool
    {
        return true;
    }

    public function getParentAggregateId(): ?FolderId
    {
        return $this->parentFolderId;
    }
}
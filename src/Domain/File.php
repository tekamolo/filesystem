<?php

namespace FileSystem\Domain;

use FileSystem\Shared\AggregateId;
use FileSystem\Shared\DateTime;

class File implements TypeInterface,ResourceInterface
{
    private AggregateId $id;
    private string $name;
    private DateTime $created;
    private FolderId $containerFolderId;

    public function __construct(FileId $id,string $name,DateTime $created,FolderId $containerFolderId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->created = $created;
        $this->containerFolderId = $containerFolderId;
    }

    /**
     * @return AggregateId
     */
    public function getAggregateId(): AggregateId
    {
        return $this->id;
    }

    /**
     * @return mixed
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

    public function isDirectory(): bool
    {
        return false;
    }
}
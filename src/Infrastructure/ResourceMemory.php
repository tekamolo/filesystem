<?php

namespace FileSystem\Infrastructure;

use FileSystem\Domain\Folder;
use FileSystem\Domain\MemoryInterface;
use FileSystem\Domain\ResourceInterface;
use FileSystem\Shared\AggregateId;
use FileSystem\Shared\ResourceCollection;

/**
 * El según la description del test no tengo que interactuar con base de datos o con memoria
 * Esto puede dar una idea de donde hacer un store de los datos.
 * Class ResourceMemory
 * @package FileSystem\Infrastructure
 */

class ResourceMemory implements MemoryInterface
{
    /**
     * @var array<string, ResourceCollection>
     */
    private $folders = [];

    public function get(AggregateId $aggregateId)
    {
        if (!isset($this->$folders[$aggregateId->value()])) {
            throw new ItemNotInMemoryException();
        }
        return $this->folders[$aggregateId->value()];
    }

    public function save(AggregateId $aggregateId,ResourceInterface $resource): void
    {
        $this->folders[$aggregateId->value()] = $resource;
    }
}
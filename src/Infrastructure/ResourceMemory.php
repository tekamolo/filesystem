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

    public function get(string $userId): ResourceInterface
    {
        if (!isset($this->$folders[$userId])) {
            throw new ItemNotInMemoryException();
        }
        return $this->folders[$userId];
    }

    public function save(string $userId,ResourceCollection $resources): void
    {
        $this->folders[$userId] = $resources;
    }
}
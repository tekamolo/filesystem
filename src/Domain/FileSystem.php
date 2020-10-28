<?php

namespace FileSystem\Domain;


use FileSystem\Shared\AggregateId;
use FileSystem\Shared\ResourceCollection;

class FileSystem
{
    private AggregateId $aggregateId;

    private ResourceCollection $resources;

    private ?ResourceInterface $rootFolder = null;

    public function __construct(AggregateId $aggregateId){
        $this->aggregateId = $aggregateId;
        $this->resources = new ResourceCollection();
    }

    public function addResource(ResourceInterface $resource): void
    {
        if($this->resources->count() == 0) $this->rootFolder = $resource;
        $this->resources->offsetSet($resource->getAggregateId()->value(),$resource);
    }

    public function removeResource(AggregateId $aggregateId): void
    {
        $this->resources->remove($aggregateId);
    }

    public function getResourceByAggregateId(AggregateId $aggregateId): ResourceInterface
    {
        return $this->resources->get($aggregateId);
    }

    public function getResourceByReference(string $reference): ResourceInterface
    {
        return $this->resources->offsetGet($reference);
    }

    public function getAll(): ResourceCollection
    {
        return $this->resources;
    }

    public function getRootFolder(): ?ResourceInterface
    {
        return $this->rootFolder;
    }
}
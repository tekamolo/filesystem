<?php
namespace FileSystem\Shared;

use FileSystem\Domain\ResourceInterface;

class ResourceCollection extends \ArrayIterator
{

    public function addOffset(AggregateId $aggregateId,ResourceInterface $item){
        $this->offsetSet($aggregateId->value(),$item);
    }

    public function add(ResourceInterface $resource){
        $this->append($resource);
    }

    public function remove(AggregateId $aggregateId){
        $this->offsetUnset($aggregateId->value());
    }

    public function get(AggregateId $aggregateId){
        if(!$this->offsetExists($aggregateId->value()))
            throw new ItemNotInCollection();
        return $this->offsetGet($aggregateId->value());
    }

    public function getByStringReference(string $reference): ResourceInterface
    {
        if(!$this->offsetExists($reference))
            throw new ItemNotInCollection();
        return $this->offsetGet($reference);
    }
}
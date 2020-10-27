<?php


namespace FileSystem\Shared;


class ReferenceCollection extends \ArrayIterator
{

    public function addOffset(AggregateId $aggregateId, $item){
        $this->offsetSet($aggregateId->value(),$item);
    }

    public function add(AggregateId $aggregateId){
        $this->append($aggregateId->value());
    }

    public function remove(AggregateId $aggregateId){
        foreach ($this as $k => $v){
            if($v === $aggregateId->value()){
                $this->offsetUnset($k);
            }
        }
    }

    public function get(AggregateId $aggregateId){
        if(!$this->offsetExists($aggregateId->value()))
            throw new ItemNotInCollection();
        return $this->offsetGet($aggregateId->value());
    }
}
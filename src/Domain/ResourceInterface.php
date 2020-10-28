<?php


namespace FileSystem\Domain;


use FileSystem\Shared\AggregateId;
use FileSystem\Shared\DateTime;
use FileSystem\Shared\ValueObjectString;

interface ResourceInterface
{
    public function getAggregateId(): AggregateId;

    public function getCreated(): DateTime;

    public function getName(): ValueObjectString;
}
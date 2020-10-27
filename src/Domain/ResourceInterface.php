<?php


namespace FileSystem\Domain;


use FileSystem\Shared\AggregateId;
use FileSystem\Shared\DateTime;

interface ResourceInterface
{
    public function getAggregateId(): AggregateId;

    public function getCreated(): DateTime;
}
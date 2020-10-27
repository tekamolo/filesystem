<?php

namespace FileSystem\Shared;

/**
 * Aqui implemento un lógica DDD y cada id es supuestamente único, podemos implementar un lógica más compleja para asegurarnos de eso
 * Class AggregateId
 * @package FileSystem\Shared
 */

class AggregateId
{
    private string $value;

    /**
     * @param string $string
     */
    public function __construct(string $string = null)
    {
        $this->value = uniqid($string);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value;
    }

    public function equals(AggregateId $aggregateId): bool
    {
        return $this->value === $aggregateId->value();
    }
}
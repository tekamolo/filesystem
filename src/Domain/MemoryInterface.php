<?php


namespace FileSystem\Domain;

use FileSystem\Shared\AggregateId;
use FileSystem\Shared\ResourceCollection;

/**
 * Creo esta interfaz para que el sistema pueda guardar o poner en cache la información que se va recogiendo
 * Aunque entiendo por el tests que no se debe de hacer. Si se tuviera que guardar datos en el sistema
 * lo más probable es que tengamos que crear otro "aggregate" fileSystem como root o incluso otra llamada "User"
 *
 * Interface MemoryInterface
 * @package FileSystem\Domain
 */

interface MemoryInterface
{
    public function get(AggregateId $aggregateId);

    public function save(AggregateId $aggregateId,ResourceInterface $resource): void;
}
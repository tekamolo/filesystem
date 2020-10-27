<?php


namespace FileSystem\Domain;

use FileSystem\Shared\ResourceCollection;

/**
 * Creo esta interfaz para que el sistema pueda guardar o poner en cache la información que se va recogiendo
 * Aunque entiendo por el tests que no se debe de hacer
 * Interface MemoryInterface
 * @package FileSystem\Domain
 */

interface MemoryInterface
{
    public function get(string $userId): ResourceInterface;

    public function save(string $userId,ResourceCollection $resource): void;
}
<?php


namespace FileSystem\Shared;


use DateTimeZone;

final class DateTime extends \DateTimeImmutable
{
    public function __construct($time = 'now', DateTimeZone $timezone = null)
    {
        parent::__construct($time, $timezone);
    }

    public function getDatetimeFileSystem(): string
    {
        return $this->format("Y-m-d H:i:s");
    }
}
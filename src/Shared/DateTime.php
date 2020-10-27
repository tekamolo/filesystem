<?php


namespace FileSystem\Shared;


use DateTimeZone;

class DateTime extends \DateTime
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
<?php

class Event
{
    private $name, $data, $dateTime;

    public function __construct($name, \DateTimeImmutable $dateTime, array $data)
    {
        $this->name = $name;
        $this->data = $data;
        $this->dateTime = $dateTime;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getDateTime()
    {
        return $this->dateTime;
    }
}

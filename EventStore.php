<?php

require_once('Event.php');

class EventStore
{
    private $events = [];

    public function record(Event $e)
    {
        $this->events[] = $e;
    }

    public function all()
    {
        return $this->events;
    }
}

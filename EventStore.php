<?php

require_once('Projector.php');
require_once('Event.php');

class EventStore
{
    private $events = [];

    public function record(Event $e)
    {
        $this->events[] = $e;
    }

    public function project(Projector $projector)
    {
        $filtered = array_filter(
            $this->events,
            function(Event $e) use ($projector) {
                return $projector->filter($e);
            }
        );

        return array_reduce(
            $filtered,
            $projector->reduce(),
            $projector->initial()
        );
    }
}

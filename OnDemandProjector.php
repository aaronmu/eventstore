<?php

require_once('Concept.php');

class OnDemandProjector
{
    public function project(array $events, Concept $concept)
    {
        $filteredEvents = array_filter($events, [$concept, 'filter']);

        return array_reduce(
            $filteredEvents,
            [$concept, 'step'],
            $concept->getInitialVal()
        );
    }
}

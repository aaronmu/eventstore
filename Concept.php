<?php

require_once('Event.php');

interface Concept
{
    public function getInitialVal();
    public function filter(Event $e);
    public function step($carry, Event $e);
}

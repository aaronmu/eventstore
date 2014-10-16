<?php

require_once('Event.php');

interface Projector
{
    public function filter(Event $e);
    public function initial();
    public function reduce();
}

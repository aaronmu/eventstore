<?php

require_once('Event.php');
require_once('EventStore.php');
require_once('OnDemandProjector.php');
require_once('Concept.php');

$es = new EventStore;
$projector = new OnDemandProjector;

$es->record(that('product registered', ['id' => 1, 'qty' => 5], at('01/01/2014 09:00')));
$es->record(that('product sold', ['id' => 1, 'qty' => 2], at('01/01/2014 10:15')));
$es->record(that('product sold', ['id' => 1, 'qty' => 1], at('01/01/2014 15:15')));
$es->record(that('product sold', ['id' => 1, 'qty' => 1], at('01/01/2014 16:32')));
$es->record(that('product registered', ['id' => 1, 'qty' => 5], at('02/01/2014 09:00')));

class Stock implements Concept
{
    public function getInitialVal()
    {
        return 0;
    }

    public function filter(Event $e)
    {
        return in_array($e->getName(), ['product registered', 'product sold']);
    }

    public function step($carry, Event $e)
    {
        return 'product registered' === $e->getName()
            ? $carry+$e->getData()['qty']
            : $carry-$e->getData()['qty'];
    }
}

$stock = $projector->project($es->all(), new Stock);

echo $stock;
exit;

function that($eventName, array $data, \DateTimeImmutable $at)
{
    return new Event($eventName, $at, $data);
}

function at($string)
{
    return \DateTimeImmutable::createFromFormat('d/m/Y H:i', $string);
}

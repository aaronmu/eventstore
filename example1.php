<?php

require_once('EventStore.php');
require_once('Projector.php');
require_once('Event.php');

function at($string)
{
    return DateTimeImmutable::createFromFormat('d/m/Y H:i:s', $string);
}

// Stuff happens
$eventStore = new EventStore();
$eventStore->record(new Event('product registered', at('10/01/2015 14:15:00'), ['productId' => 1, 'quantity' => 10]));
$eventStore->record(new Event('product registered', at('11/01/2015 08:00:00'), ['productId' => 1, 'quantity' => 20]));
$eventStore->record(new Event('product sold', at('11/01/2015 09:15:00'), ['productId' => 1, 'quantity' => 1]));
$eventStore->record(new Event('product registered', at('01/02/2015 09:00:12'), ['productId' => 1, 'quantity' => 10]));
$eventStore->record(new Event('product sold', at('05/02/2015 15:07:02'), ['productId' => 1, 'quantity' => 5]));
$eventStore->record(new Event('product registered', at('01/03/2015 09:00:08'), ['productId' => 2, 'quantity' => 5]));

// We want to know how many products we have in stock.
class ProductsInStock implements Projector
{
    public function filter(Event $e)
    {
        return in_array($e->getName(), ['product registered', 'product sold']);
    }

    public function initial()
    {
        return 0;
    }

    public function reduce()
    {
        return function($carry, Event $e) {
            $qty = $e->getData()['quantity'];

            if ('product registered' === $e->getName()) {
                return $carry + $qty;
            }

            if ('product sold' === $e->getName()) {
                return $carry - $qty;
            }
        };
    }
}

$projection = $eventStore->project(new ProductsInStock());

echo $projection;
echo "\n";

// And how much stock we have of a certain product.
class ProductInStock implements Projector
{
    private $productId;

    public function __construct($productId)
    {
        $this->productId = $productId;
    }

    public function filter(Event $e)
    {
        return in_array($e->getName(), ['product registered', 'product sold']) &&
            $e->getData()['productId'] === $this->productId;
    }

    public function initial()
    {
        return 0;
    }

    public function reduce()
    {
        return function($carry, Event $e) {
            $qty = $e->getData()['quantity'];

            if ('product registered' === $e->getName()) {
                return $carry + $qty;
            }

            if ('product sold' === $e->getName()) {
                return $carry - $qty;
            }
        };
    }
}

echo $eventStore->project(new ProductInStock(1));
echo "\n";

// And now for the more interesting stuff. Facts!
class ProductsInStockAt implements Projector
{
    private $dateTime;

    public function __construct(\DateTimeImmutable $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function filter(Event $e)
    {
        return in_array($e->getName(), ['product registered', 'product sold']) &&
            $this->dateTime >= $e->getDateTime();
    }

    public function initial()
    {
        return 0;
    }

    public function reduce()
    {
        return function($carry, Event $e) {
            $qty = $e->getData()['quantity'];

            if ('product registered' === $e->getName()) {
                return $carry + $qty;
            }

            if ('product sold' === $e->getName()) {
                return $carry - $qty;
            }
        };
    }
}

echo $eventStore->project(new ProductsInStockAt(at('10/01/2015 15:15:00')));

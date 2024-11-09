<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/vendor/autoload.php';

$on_recieved = function($msg)
{
    echo "Europe service recieved message: \"$msg->body\"\n";
};

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$exchange = 'topic_exchange';
$channel->exchange_declare($exchange, 'topic', false, false, false);

[$queueName,,,] = $queue = $channel->queue_declare('', false, false, false, true, false);

$channel->queue_bind($queueName, $exchange, 'europe.#');

$channel->basic_consume($queueName, '', false, true, false, false, $on_recieved);

echo "Start consuming\n";

while ($channel->is_consuming()) {
    $channel->wait();
};

$channel->close();
$connection->close();

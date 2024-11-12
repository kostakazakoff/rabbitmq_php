<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/vendor/autoload.php';

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$exchange = 'topic_exchange';
$channel->exchange_declare($exchange, 'topic', false, false, false);
$routingKey = 'europe.new.users';

$c = 1;

while (true)
{
    $messageBody = "New user registration number ".$c;
    $message = new AMQPMessage($messageBody);

    $channel->basic_publish($message, $exchange, $routingKey);
    echo "Message sent with body: \"$messageBody\" to exchange \"$exchange\" using routing key \"$routingKey\".\n";
    $c++;
    sleep(1);
}

$channel->close();
$connection->close();
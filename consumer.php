<?php

// create a new Connection for publishing messages
$connection = new AMQPConnection(
	array(
		'host'     => '127.0.0.1', 
		'vhost'    => '/', 
		'port'     => 5672, 
		'login'    => 'guest', 
		'password' => 'guest'
	)
);
// connect to the AMQP server
$connection->connect();

// check if the connection is connected
if ($connection->isConnected()) {
	echo 'Connection is connected ' . PHP_EOL;
}

// create the channel
$channel = new AMQPChannel($connection);

// create a queue
$queue = new AMQPQueue($channel);
$queue->setName('hello_world');
$queue->declare();
$queue->bind('hello', 'my.key');

$queue->consume(function($envelope, $queue) {

	$message = $envelope->getBody();
	echo $message . PHP_EOL;

	return true;
}, AMQP_AUTOACK);

?>
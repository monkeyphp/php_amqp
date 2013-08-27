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

// create a channel
try {
	$channel = new AMQPChannel($connection);
} catch(AMQPConnectionException $amqpException) {
	echo $amqpException->getMessage();
	exit;
}

// create an exchange
try {
	$exchange = new AMQPExchange($channel);
	$exchange->setName('hello');
	$exchange->setType(AMQP_EX_TYPE_DIRECT);
	$exchange->declare();
} catch(AMQPConnectionException $amqpException) {
	echo $amqpException->getMessage();
	exit;
}

// create a queue
try {
	$queue = new AMQPQueue($channel);
	$queue->setName('hello_world');
	$queue->declare();
} catch(AMQPConnectionException $amqpException) {
	echo $amqpException->getMessage();
	exit;
}


if (! $exchange->bind('hello', 'my.key')) {
	echo 'Failed to bind exchange to queue' . PHP_EOL;
}

$x = 0;

while($x < 1000) {

	$message = 'Message[' . str_repeat('.', rand(1, 5)) . ']';

	$success = $exchange->publish($message, 'my.key');

	if (! $success) {
		echo 'Failed to send the message to the exchange';
	} else {
		echo 'Sent message: ' . $message . PHP_EOL;
	}
	$x++;
	
}

$connection->disconnect();
// check if the connection is connected
if (! $connection->isConnected()) {
	echo 'Connection is disconnected ' . PHP_EOL;
}
?>
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
$channel = new AMQPChannel($connection);


// create an exchange
$exchange = new AMQPExchange($channel);
$exchange->setName('hello');
$exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->declare();

// create a queue
$queue = new AMQPQueue($channel);
$queue->setName('hello_world');
$queue->declare();

$exchange->bind('hello', 'my.key');

$x = 0;

while($x < 10000) {

	$success = $exchange->publish("[$x]Hello World!", 'my.key');

	if (! $success) {
		echo 'Failed to send the message to the exchange';
	} else {
		echo 'Sent message ' . PHP_EOL;
	}
	$x++;
	usleep(rand(10, 2000));
}

$connection->disconnect();

?>
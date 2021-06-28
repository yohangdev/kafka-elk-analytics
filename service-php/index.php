<?php

require __DIR__ . '/vendor/autoload.php';

$topic = $argv[1];
$payload = $argv[2];

$config = \Kafka\ProducerConfig::getInstance();
$config->setMetadataRefreshIntervalMs(10000);
$config->setMetadataBrokerList('kafka:9092');
$config->setRequiredAck(1);
$config->setIsAsyn(false);
$config->setProduceInterval(500);

$producer = new \Kafka\Producer();

$producer->send([
    [
        'topic' => $topic,
        'value' => $payload,
        'partId' => 0,
        'key' => 'my-key',
    ],
]);
<?php

require __DIR__ . '/vendor/autoload.php';

$topic = 'event-topic';

$config = \Kafka\ProducerConfig::getInstance();
$config->setMetadataRefreshIntervalMs(10000);
$config->setMetadataBrokerList('kafka:9092');
$config->setRequiredAck(1);
$config->setIsAsyn(false);
$config->setProduceInterval(500);

$producer = new \Kafka\Producer();

$faker = Faker\Factory::create();

$users = [];

$eventNames = [
    'account_registered', 'account_activated', 'account_logged_in', 'account_login_failed',
    'news_list', 'news_detail', 'news_bookmark', 
    'videos_list', 'videos_detail', 'videos_share', 'videos_liked', 'videos_disliked',
];

for ($n = 0; $n < 100; $n++) {
    $users[] = [
        'id' => $faker->uuid(),
        'name' => $faker->name(),
    ];
}

while (true) {
    $user = $faker->randomElement($users);
    
    $payload = [
        'user_id' => $user['id'],
        'user_name' => $user['name'],
        'event_name' => $faker->randomElement($eventNames),
    ];

    $producer->send([
        [
            'topic' => $topic,
            'value' => json_encode($payload),
            'partId' => 0,
            'key' => 'my-key',
        ],
    ]);

    sleep(0.2);
}

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

$eventAttributes = [];

for ($n = 0; $n < 20; $n++) {
    $eventAttributes[] = [
        ['video_id' => $faker->uuid(), 'video_title' => $faker->sentence()],
        ['news_id' => $faker->uuid(), 'news_title' => $faker->sentence()],
    ];
}

for ($n = 0; $n < 100; $n++) {
    $users[] = [
        'id' => $faker->uuid(),
        'name' => $faker->name(),
        'latitude' => $faker->latitude(-7.225436473271717, -6.62696411642353),
        'longitude' => $faker->longitude(106.83070063591005, 108.4484374523163),
    ];
}

while (true) {
    $user = $faker->randomElement($users);
    
    $payload = [
        'user_id' => $user['id'],
        'user_name' => $user['name'],
        'user_location' => [
            'lat' => $user['latitude'],
            'lon' => $user['longitude'],
        ],
        'event_name' => $faker->randomElement($eventNames),
        'attributes' => $faker->randomElement($eventAttributes),
    ];

    $producer->send([
        [
            'topic' => $topic,
            'value' => json_encode($payload),
            'partId' => 0,
            'key' => 'my-key',
        ],
    ]);


    $time = mt_rand() / mt_getrandmax();
    usleep($time * 1000000);
}

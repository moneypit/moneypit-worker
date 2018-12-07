<?php

require __DIR__.'../../vendor/autoload.php';
use RedisClient\RedisClient;
use RedisClient\Client\Version\RedisClient2x6;
use RedisClient\ClientFactory;
use Elasticsearch\ClientBuilder;

$config = json_decode(file_get_contents($argv[1]), TRUE);

$rClient = ClientFactory::create([
    'server' => $config['redis']['host'].":".$config['redis']['port'],
    'timeout' => 2,
    'version' => '2.8.24'
]);

$esClient = ClientBuilder::create()->setHosts($config['elasticsearch']['hosts'])->build();

$workers = json_decode(file_get_contents($config['resources']['worker']['data']), TRUE);

foreach ($workers['workers'] as $k=>$worker) {

  $redis_key = $config['resources']['worker']['redis_prefix'].$worker['name'];
  echo "[".$redis_key."]\n";

  $worker_details = $rClient->get($redis_key);

  if ($worker_details == null) {
    echo " <NULL>\n";

  } else {
    echo " <INDEX> => ";
    $worker_details = json_decode($worker_details, TRUE);

    if (isset($worker_details['details']['timestamp']))
    unset($worker_details['root_pw']);

    $uid_string = $worker_details['timestamp'].$worker['name'];
    $uid_hash = hash('sha256',$uid_string);
    echo $uid_hash." ";

    $params = [
      'index' => strtolower($config['elasticsearch']['index'].$worker['name']),
      'type' => strtolower($config['elasticsearch']['index'].$worker['name']),
      'id' => $uid_hash,
      'body' => $worker_details
    ];

    $r = $esClient->index($params);

    echo json_encode($r)."\n";

  }

}

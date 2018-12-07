<?php

require __DIR__.'../../vendor/autoload.php';
use RedisClient\RedisClient;
use RedisClient\Client\Version\RedisClient2x6;
use RedisClient\ClientFactory;

$config = json_decode(file_get_contents($argv[1]), TRUE);

$rClient = ClientFactory::create([
    'server' => $config['redis']['host'].":".$config['redis']['port'],
    'timeout' => 2,
    'version' => '2.8.24'
]);

while (TRUE) {

  $workers = json_decode(file_get_contents($config['resources']['worker']['data']), TRUE);

  foreach ($workers['workers'] as $k=>$worker) {

    $redis_key = $config['resources']['worker']['redis_prefix'].$worker['name'];
    echo "[".$redis_key."]\n";

    $details_url = $config['resources']['worker']['detail_url']."?ip=".$worker['ip']."&type=".$worker['type']."&pw=".$worker['root_pw'];
    echo " Fetching detail...($details_url)\n";
    $details = json_decode(file_get_contents($details_url), TRUE);

    echo " Caching results...\n";
    if ($details == null) {
      $workerResourceInstance = array_merge(array('timestamp' => date("c")), $worker, array('details'=>array()));
    } else {
      $workerResourceInstance = array_merge(array('timestamp' => date("c")), $worker, array('details'=>$details));
      unset($workerResourceInstance['details']['timestamp']);
    }

    $rClient->set($redis_key, json_encode($workerResourceInstance));
    $rClient->save();

  }


}

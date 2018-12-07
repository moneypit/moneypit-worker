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



$workers = json_decode(file_get_contents($config['resources']['worker']['data']), TRUE);

foreach ($workers['workers'] as $k=>$worker) {
  $workerLookup[$worker['name']] = $worker;
}

$workerDef = $workerLookup[$argv[2]];

$redis_key = $config['resources']['worker']['redis_prefix'].$workerDef['name'];
echo "[".$redis_key."]\n";

$details_url = $config['resources']['worker']['detail_url']."?ip=".$workerDef['ip']."&type=".$workerDef['type']."&pw=".$workerDef['root_pw'];
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

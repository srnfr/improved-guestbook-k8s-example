<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require './vendor/predis/predis/autoload.php';

Predis\Autoloader::register();

if (isset($_GET['cmd']) === true) {
  $host = 'redis';
  if (getenv('GET_HOSTS_FROM') == 'env') {
    $host = getenv('REDIS_SENTINEL_SERVICE_HOST')?:'redis';
  }:q
  
  
  if (getenv('REDIS_PWD')) {
    $pwd = getenv('REDIS_PWD');
  } else {
    $pwd='redis-password';
  }
  
  header('Content-Type: application/json');
  
  /* predis bug : https://github.com/predis/predis/issues/658 */
  // $sentinels = ['tcp://'.$host.':26379?password='.$pwd];
  $sentinels = ['tcp://'.$host.':26379'];
  $options = [ 
      'replication' => 'sentinel', 
      'service' => 'mymaster' , 
      'parameters'  => ['database' => 0, 
        //'password' => $pwd,
       ],
  ];
  
  if ($_GET['cmd'] == 'set') {

    $client = new Predis\Client($sentinels,$options);
    $client->set($_GET['key'], $_GET['value']);
    print('{"message": "Updated"}');
    
  } else {

    $client = new Predis\Client('tcp://redis:6379');
    $value = $client->get($_GET['key']);
    print('{"data": "' . $value . '"}');
  }
  
} else {
  phpinfo();
} ?>

<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require './vendor/predis/predis/autoload.php';

Predis\Autoloader::register();

if (isset($_GET['cmd']) === true) {
  $host = 'redis-sentinel';
  if (getenv('GET_HOSTS_FROM') == 'env') {
    $host = getenv('REDIS_MASTER_SERVICE_HOST');
  }
  header('Content-Type: application/json');
  if ($_GET['cmd'] == 'set') {
    $client = new Predis\Client(
      ['tcp://'.$host],
      [ 
        'replication' => 'sentinel', 
        'service' => 'mymaster' , 
        'parameters'  => ['database' => 0, 'password' => 'redis-password'],
      ],
    );

    $client->set($_GET['key'], $_GET['value']);
    print('{"message": "Updated"}');
  } else {
    $host = 'redis';
    if (getenv('GET_HOSTS_FROM') == 'env') {
      $host = getenv('REDIS_SLAVE_SERVICE_HOST');
    }
    $client = new Predis\Client(
      ['tcp://'.$host],
      [ 
        'replication' => 'sentinel',
        'parameters'  => ['database' => 0, 'password' => 'redis-password'],
      ],
    );

    $value = $client->get($_GET['key']);
    print('{"data": "' . $value . '"}');
  }
} else {
  phpinfo();
} ?>

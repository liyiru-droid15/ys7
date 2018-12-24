<?php 
require_once __DIR__ . '/vendor/autoload.php'; 

use droid15\Run;

$ys7 = new Run('Your appKey','Your appSecret');

var_dump($ys7->deviceList());
//var_dump($ys7->getPlayUrl(['sn'=>'A123456']));

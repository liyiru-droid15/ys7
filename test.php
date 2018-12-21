<?php 
require_once __DIR__ . '/vendor/autoload.php'; 

use droid15\Run;

$ys7 = new Run('your appKey','your appSecret');

var_dump($ys7->deviceList());

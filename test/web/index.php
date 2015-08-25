<?php

use Symfony\Component\HttpFoundation\Request;

require '../../vendor/autoload.php';
require '../app/MyApplication.php';

// for test purpose only
$_SERVER['REQUEST_URI'] = '/welcome/Tales';

$request = Request::createFromGlobals();

$app      = new MyApplication('dev');
$response = $app->handle($request);
$response->send();
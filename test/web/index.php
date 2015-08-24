<?php

use Symfony\Component\HttpFoundation\Request;

require '../../vendor/autoload.php';
require '../app/MyApplication.php';

$request = Request::createFromGlobals();

$app      = new MyApplication('dev');
$response = $app->handle($request);
$response->send();
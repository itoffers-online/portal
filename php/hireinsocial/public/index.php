<?php

use Symfony\Component\HttpFoundation\Request;

$projectRootPath = dirname(__DIR__);

require $projectRootPath . '/src/autoload.php';

$config = \HireInSocial\bootstrap($projectRootPath);

$kernel = \App\symfony(
    $config,
    \HireInSocial\system($config)
);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
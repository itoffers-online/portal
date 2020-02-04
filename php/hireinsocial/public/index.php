<?php

use HireInSocial\HireInSocial;
use Symfony\Component\HttpFoundation\Request;
use function App\symfony;

$projectRootPath = dirname(__DIR__);

require $projectRootPath . '/src/autoload.php';

$kernel = symfony(new HireInSocial($projectRootPath));

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
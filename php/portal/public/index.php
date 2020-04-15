<?php

use Symfony\Component\HttpFoundation\Request;
use function App\initializeSymfony;
use function ITOffers\Offers\Infrastructure\bootstrap;

$projectRootPath = dirname(__DIR__);

require $projectRootPath . '/src/autoload.php';

$kernel = initializeSymfony(bootstrap($projectRootPath));

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
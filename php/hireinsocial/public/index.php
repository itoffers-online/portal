<?php

use Symfony\Component\HttpFoundation\Request;
use function App\symfony;
use function HireInSocial\Infrastructure\bootstrap;
use function HireInSocial\Infrastructure\offersFacade;

$projectRootPath = dirname(__DIR__);

require $projectRootPath . '/src/autoload.php';

$config = bootstrap($projectRootPath);

$kernel = symfony($config, offersFacade($config));

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
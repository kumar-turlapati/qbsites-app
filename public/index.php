<?php

require_once '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;


$route_file_name = 'routes_default.php';

$request = Request::createFromGlobals();
$routes = include __DIR__.'/../src/'.$route_file_name;

$context = new Routing\RequestContext();
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);
$resolver = new HttpKernel\Controller\ControllerResolver();

$framework = new Framework\Framework($matcher, $resolver);
$response = $framework->handle($request);

$response->send();
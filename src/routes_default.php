<?php

use Symfony\Component\Routing;
use Symfony\Component\HttpFoundation\Response;

$routes = new Routing\RouteCollection();
$routes->add('default_route', new Routing\Route('/', array(
  '_controller' => 'User\\Controller\\UserController::errorActionNotFound',
)));
$routes->add('view_catalog', new Routing\Route('/catalog/view/{catalogCode}', array(
  '_controller' => 'Catalogs\\Controller\\CatalogsController::viewCatalog',
  'catalogCode' => null,
)));

return $routes;

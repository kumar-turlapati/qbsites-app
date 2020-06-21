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
$routes->add('cart_operations', new Routing\Route('/cart/{operation}', array(
  '_controller' => 'Catalogs\\Controller\\CatalogsController::cartOperations',
  'operation' => null,
)));
$routes->add('send_otp', new Routing\Route('/send-otp/{mobileNo}', array(
  '_controller' => 'Catalogs\\Controller\\CatalogsController::sendOtp',
  'mobileNo' => null,
)));
$routes->add('order', new Routing\Route('/order', array(
  '_controller' => 'Catalogs\\Controller\\CatalogsController::orderItems',
  'catalogCode' => null,
)));
$routes->add('order_submit', new Routing\Route('/order/submit', array(
  '_controller' => 'Catalogs\\Controller\\CatalogsController::orderSubmit',
)));
return $routes;
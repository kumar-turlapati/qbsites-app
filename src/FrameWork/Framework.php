<?php

namespace FrameWork;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpFoundation\Cookie;
use Framework\Template;
use Framework\Utilities;

ini_set('date.timezone', 'Asia/Kolkata');

if(Utilities::is_session_started() === FALSE) session_start();

class Framework {

  protected $matcher;
  protected $resolver;
  protected $template;
  protected $response;

  public function __construct(UrlMatcher $matcher, ControllerResolver $resolver) {
    $this->matcher = $matcher;
    $this->resolver = $resolver;
    $this->template = new Template(__DIR__.'/../Layout/');
    $this->response = new Response;
  }

  public function handle(Request $request) {
    $this->matcher->getContext()->fromRequest($request);
    $path = $request->getPathInfo();

    try {
      $request->attributes->add($this->matcher->match($path));
      $controller = $this->resolver->getController($request);
      $arguments = $this->resolver->getArguments($request, $controller);
      $controller_response = call_user_func_array($controller, $arguments);
      if(is_array($controller_response) && count($controller_response)>0) {
        $controller_output = $controller_response[0];
        if(is_array($controller_response[1]) && count($controller_response[1]) > 0) {
          $view_vars = $controller_response[1];
        } else {
          $view_vars = [];
        }
      } else {
        $controller_output = $controller_response;
        $view_vars = [];
      }

      $page_content = $this->template->render_view('layout', array('content' => $controller_output, 'path_url' => $path, 'view_vars' => $view_vars));
      return new Response($page_content);
    } catch (ResourceNotFoundException $e) {
      // dump($e);
      return new Response('Not Found', 404);
    } catch (\Exception $e) {
      if($_SERVER['SERVER_ADDR'] === '127.0.0.1') {
        dump($e);
      }
      return new Response('An error occurred', 500);
    }
  }
}

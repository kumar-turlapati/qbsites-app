<?php

namespace User\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FrameWork\Utilities;
use FrameWork\Template;

class UserController {

	public function __construct() {
    $this->template = new Template(__DIR__.'/../Views/');
	}

  /** 404 error template **/
  public function errorActionNotFound() {
    $controller_vars = array(
      'page_title' => 'Page not found'
    );
    $template_vars = [];
    return array($this->template->render_view('error-404', $template_vars),$controller_vars);    
  } 
}
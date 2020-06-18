<?php 

namespace Catalogs\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Framework\Utilities;
use Framework\Template;
use Framework\Flash;

use Catalogs\Model\Catalogs;

class CatalogsController {
	protected $views_path,$flash,$catalogs_model,$template;

	public function __construct() {
    $this->template = new Template(__DIR__.'/../Views/');
    $this->flash = new Flash();
    $this->catalogs_model = new Catalogs;
	}

  public function viewCatalog(Request $request) {

    $catalog_hash = !is_null($request->get('catalogCode')) ? Utilities::clean_string($request->get('catalogCode')) : '';
    $catalog_params = $this->_validate_catalog_code($catalog_hash);
    if($catalog_params === false) {
      Utilities::redirect('/');
    }

    $api_response = $this->catalogs_model->get_catalog_details($catalog_params);
    dump($api_response);
    exit;
    if($api_response['status']) {
      $catalog_response = $api_response['response'];
    } else {
      Utilities::redirect('/');
    }

    // prepare form variables.
    $template_vars = array(
      'catalog_details' => $catalog_response,
    );

    // build variables
    $controller_vars = array(
      'page_title' => 'Product Galleries',
    );

    // render template
    return array($this->template->render_view('view-catalog', $template_vars),$controller_vars);    
  }

  // validate gallery code
  private function _validate_catalog_code($catalog_hash = '') {
    $client_code = substr($catalog_hash, 0, 15);
    $catalog_code = substr($catalog_hash,15, 15);
    if(strlen($catalog_hash) === 0 || strlen($client_code) !== 15 && strlen($catalog_code) !== 15) {
      return false;
    } else {
      return ['clientCode' => $client_code, 'catalogCode' => $catalog_code];
    }
    return false;
  }
}
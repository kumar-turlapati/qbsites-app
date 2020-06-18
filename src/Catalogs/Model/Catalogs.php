<?php

namespace Catalogs\Model;

use Framework\ApiCaller;
use Framework\Utilities;

class Catalogs {
	
	private $api_caller;

	public function __construct() {
		$this->api_caller = new ApiCaller;
	}

	public function get_catalog_details($catalog_params = []) {
		$api_uri = '/qbsites/catalog/details/'.$catalog_params['catalogCode'];
		$response = $this->api_caller->sendRequest('get',$api_uri,[],true, false, $catalog_params['clientCode']);
		if($response['status']==='success') {
			return ['status'=>true,'response'=>$response['response']];
		} else {
			return ['status'=>false,'apierror'=> $response['reason']];
		}
	}
}
<?php

namespace Catalogs\Model;

use FrameWork\ApiCaller;
use FrameWork\Utilities;

class Catalogs {
	
	private $api_caller;

	public function __construct() {
		$this->api_caller = new ApiCaller;
	}

	public function get_catalog_details($catalog_params = []) {
		$api_uri = 'qbsites/catalog/details/'.$catalog_params['catalogCode'];
		$response = $this->api_caller->sendRequest('get',$api_uri,[],true, false, $catalog_params['clientCode']);
		if($response['status']==='success') {
			return ['status'=>true,'response'=>$response['response']];
		} else {
			return ['status'=>false,'apierror'=> $response['reason']];
		}
	}

	public function place_order($catalog_code = '', $client_code='', $order_items = []) {
		$api_uri = 'qbsites/catalog-order/'.$catalog_code;
		$response = $this->api_caller->sendRequest('post', $api_uri, $order_items, true, false, $client_code);
		if($response['status']==='success') {
			return ['status'=>true,'response'=>$response['response']];
		} else {
			return ['status'=>false,'apierror'=> $response['reason']];
		}
	}

}
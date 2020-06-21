<?php 

namespace Catalogs\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Framework\Utilities;
use Framework\Template;
use Framework\Flash;
use Framework\Config\Config;

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
    if($api_response['status'] && is_array($api_response['response']['catalogItems']) && count($api_response['response']['catalogItems']) > 0) {
      $catalog_response = $api_response['response'];
    } else {
      Utilities::redirect('/');
    }

    // dump($api_response['response']['catalogItems']);
    // exit;

    $items_list = [];
    foreach($api_response['response']['catalogItems'] as $catalog_item_details) {
      $items_list[$catalog_item_details['itemCode']] = [
        'itemName' => $catalog_item_details['itemName'],
        'uomName' => $catalog_item_details['uomName'],
        'itemDescription' => $catalog_item_details['itemDescription'],
        'itemRate' => $catalog_item_details['itemRate'],
      ];
    }

    // unset session if already exists
    if(isset($_SESSION['catalog'])) {
      unset($_SESSION['catalog']);
    }
    // store catalog hash for future redirects
    $_SESSION['catalog_hash'] = $catalog_hash;
    $_SESSION['catalog'] = $items_list;
    $_SESSION['org_name'] = $catalog_response['businessDetails']['businessName'];
    $_SESSION['ios_url'] =  $catalog_response['businessDetails']['iosUrl'];
    $_SESSION['android_url'] = $catalog_response['businessDetails']['androidUrl'];
    $_SESSION['catalog_name'] = $catalog_response['catalogName'];

    // prepare form variables.
    $template_vars = array(
      'catalog_details' => $catalog_response,
      'org_code' => $catalog_params['clientCode'],
    );

    // build variables
    $controller_vars = array(
      'page_title' => $_SESSION['org_name'].' - '. $_SESSION['catalog_name'],
      'org_name' => $catalog_response['businessDetails']['businessName'],
      'ios_url' => $catalog_response['businessDetails']['iosUrl'],
      'android_url' => $catalog_response['businessDetails']['androidUrl'],
      'catalog_name' => $catalog_response['catalogName'],
      'catalog_hash' => $catalog_hash,
    );

    // render template
    return array($this->template->render_view('view-catalog', $template_vars),$controller_vars);    
  }

  public function orderItems(Request $request) {
    if(!isset($_SESSION['catalog_hash']) ) {
      Utilities::redirect('/');
    }
    if(isset($_SESSION['orderOtp'])) {
      unset($_SESSION['orderOtp']);
    }    

    // prepare form variables.
    // $template_vars = array(
    //   'catalog_details' => $catalog_response,
    //   'org_code' => $catalog_params['clientCode'],
    // );

    // build variables
    $controller_vars = array(
      'page_title' => 'Place Order'.' - '.$_SESSION['org_name'].' - '. $_SESSION['catalog_name'],
      'org_name' => $_SESSION['org_name'],
      'ios_url' => $_SESSION['ios_url'],
      'android_url' => $_SESSION['android_url'],
      'catalog_name' => $_SESSION['catalog_name'],
      'catalog_hash' => $_SESSION['catalog_hash'],
      'is_cart' => true,
    );

    // render template
    return array($this->template->render_view('view-cart', []),$controller_vars);        
  }

  public function cartOperations(Request $request) {
    $operation = !is_null($request->get('operation')) ? Utilities::clean_string($request->get('operation')) : false;
    $item_code = !is_null($request->get('itemCode')) ? Utilities::clean_string($request->get('itemCode')) : false;
    $qty = !is_null($request->get('qty')) ? (int)Utilities::clean_string($request->get('qty')) : false;
    $cntr = !is_null($request->get('cntr')) && is_numeric($request->get('cntr')) ? (int)Utilities::clean_string($request->get('cntr')) : false;
    // var_dump($operation, $item_code, $qty);
    if($operation === false || $qty === false || $item_code === '' || !is_numeric($qty) || $cntr === false) {
      $response = ['status' => false, 'reason' => 'Invalid input'];
      echo json_encode($response);
      exit;
    }
    if($operation === 'add' || $operation === 'remove') {

      if($operation === 'add') {
        $item_rate = 0;
        // check item rate is available.
        if(isset($_SESSION['catalog'][$item_code])) {
          $item_rate = (int)$_SESSION['catalog'][$item_code]['itemRate'];
        }
        // send error if item rate is invalid.
        if($item_rate <= 0) {
          $response = ['status' => false, 'reason' => 'Invalid item rate'];
          header('Content-Type: application/json');
          echo json_encode($response);
          exit;
        }
        $_SESSION['cart'][$item_code]['qty'] = $qty;
        $_SESSION['cart'][$item_code]['imageCntr'] = $cntr;
        $message = 'Item added to Cart';
      } elseif($operation === 'remove') {
        $item_codes = explode(',', $item_code);
        foreach($item_codes as $item_code) {
          unset($_SESSION['cart'][$item_code]);
        }
        $message = 'Item removed from Cart';      
      }
      $response = ['status' => true, 'reason' => $message, 'ic' => count($_SESSION['cart'])];
      header('Content-Type: application/json');
      echo json_encode($response);
      exit;
    } else {
      $response = ['status' => false, 'reason' => 'Invalid operation.'];
      header('Content-Type: application/json');
      echo json_encode($response);
      exit;
    }
  }

  public function sendOtp(Request $request) {
    $mobile_no = !is_null($request->get('mobileNo')) ? Utilities::clean_string($request->get('mobileNo')) : false;
    if(is_numeric($mobile_no) && strlen($mobile_no) === 10 && substr($mobile_no, 0, 1) > 5) {
      $mobile_no_with_country_code = '91'.$mobile_no;
      // generate random number and assign it to session
      $digits = 4;
      $otp = rand(pow(10, $digits-1), pow(10, $digits)-1);      
      // store otp in current user session.
      if(isset($_SESSION['orderOtp'])) {
        unset($_SESSION['orderOtp']);
      }
      $_SESSION['orderOtp'] = $otp;

      // connect with api and push the message.
      $sms_gatway_details = Config::get_sms_api_details();
      $apiKey = urlencode($sms_gatway_details['apiKey']);
      
      // Message details
      $sender = urlencode('TXTLCL');
      $message = rawurlencode("OTP for submitting your order at qwikbills platform is $otp");
     
      // Prepare data for POST request
      $data = array('apikey' => $apiKey, 'numbers' => $mobile_no_with_country_code, "sender" => $sender, "message" => $message);
     
      // Send the POST request with cURL
      $ch = curl_init($sms_gatway_details['apiUrl']);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($ch);
      curl_close($ch);

      $response_sms = json_decode($response, true);

      // check status
      if(is_array($response_sms) && isset($response_sms['status']) ) {
        $status = $response_sms['status'];
        if($status === 'success') {
          $response = ['status' => true, 'reason' => 'OTP sent successfully'];
        } else {
          $response = ['status' => false, 'reason' => 'Unable to send OTP'];
        }
      } else {
        $response = ['status' => false, 'reason' => 'Unable to send OTP'];
      }
    } else {
      $response = ['status' => false, 'reason' => 'Invalid mobile no.'];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
  }

  public function orderSubmit(Request $request) {
    // check whether it is a form submit or not.
    if(count($request->request->all()) > 0) {
      $form_data = $request->request->all();
      $mobile_number = isset($form_data['mobileNumber']) && is_numeric($form_data['mobileNumber']) ? (int)Utilities::clean_string($form_data['mobileNumber']) : false;
      $otp = isset($form_data['otp']) && is_numeric($form_data['otp']) ? (int)Utilities::clean_string($form_data['otp']) : false;
      $business_name = isset($form_data['businessName']) && strlen($form_data['businessName']) > 1 !== '' ? Utilities::clean_string($form_data['businessName']) : false;
      if($mobile_number === false || $otp === false || $business_name === false) {
        $response = ['status' => false, 'reason' => 'Invalid input'];
      } elseif(isset($_SESSION['orderOtp']) && (int)$_SESSION['orderOtp'] === $otp) {
        
        // submit order to platform.





      } else {
        $response = ['status' => false, 'reason' => 'Invalid otp.'];
      }
    } else {
      $response = ['status' => false, 'reason' => 'Invalid request.'];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
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
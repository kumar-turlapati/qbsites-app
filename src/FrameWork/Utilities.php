<?php

namespace FrameWork;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FrameWork\Constants;
use FrameWork\ApiCaller;
use FrameWork\Config\Config;

use User\Model\User;

class Utilities
{

  public static function enc_dec_string($action = 'encrypt', $string = '') {
    $token_config = Config::get_enc_dec_data();
    $key = hash('sha256', $token_config['secret_key']);
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $token_config['secret_iv']), 0, 16);
    if( $action === 'encrypt' ) {
      $output = openssl_encrypt($string, $token_config['encrypt_method'], $key, 0, $iv);
      $output = base64_encode($output);
    } elseif( $action === 'decrypt' ){
      $output = openssl_decrypt(base64_decode($string), $token_config['encrypt_method'], $key, 0, $iv);
    }
    return $output;
  }

	public static function redirect($url, $type='external')	{
    $response = new RedirectResponse($url);
    $response->send();
    exit;
	}

	public static function validateDate($date = '') {
		if(! is_numeric(str_replace('-', '', $date)) ) {
			return false;
		} else {
      $date_a = explode('-', $date);
      if(checkdate($date_a[1],$date_a[0],$date_a[2])) {
        return true;
      } else {
			  return false;
      }
		}
	}

  public static function validateMonth($month = '') {
    if(!is_numeric($month) || $month<=0 || $month>12 ) {
      return false;
    } else {
      return true;
    }
  }

  public static function validateYear($year = '') {
    if(!is_numeric($year) || $year<=2015 ) {
      return false;
    } else {
      return true;
    }
  }   

  public static function clean_string($string = '', $breaks_needed=false) {
    if($breaks_needed) {
      return trim(strip_tags($string));
    } else {
    	return trim(str_replace("\r\n",'',strip_tags($string)));
    }
  }

  public static function validateName($name='') {
  	if(!preg_match("/^[a-zA-Z ]*$/",$name)) {
  		return false;
  	} else {
  		return true;
  	}
  }

  public static function validateEmail($email='') {
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return false;
    } else {
    	return true;
    }
  }  

  public static function validateMobileNo($mobile_no='') {
  	if( strlen(trim(str_replace("\r\n",'',$mobile_no))) !== 10 ) {
  		return false;
  	} elseif(!is_numeric($mobile_no)) {
  		return false;
  	}
  	return true;
  }

  public static function set_flash_message($message = '', $error=0) {
    if(isset($_SESSION['__FLASH'])) {
      unset($_SESSION['__FLASH']);
    }
    $_SESSION['__FLASH']['message'] = $message;
    $_SESSION['__FLASH']['error']   = $error;
  }

  public static function get_flash_message() {
    if(isset($_SESSION['__FLASH'])) {
        $message = $_SESSION['__FLASH']['message'];
        $status  = $_SESSION['__FLASH']['error'];
        unset($_SESSION['__FLASH']);
        return array('message'=>$message, 'error'=>$status);
    } else {
        return '';
    }
  }

  public static function print_flash_message($return=true) {
    $flash                  =   Utilities::get_flash_message();
    if(is_array($flash) && count($flash)>0) {
      $flash_message_error  =   $flash['error'];
      $flash_message        =   $flash['message'];
    } else {
      $flash_message        =   '';
    }

    if($flash_message != '' && $flash_message_error) {
      $message =  "<div class='alert alert-danger' role='alert'>
      							<strong>$flash_message</strong>
                  </div>";
    } elseif($flash_message != '') {
      $message =  "<div class='alert alert-success' role='alert'>
      								<strong>$flash_message</strong>
                   </div>";
    } else {
    	$message = '';
    }

    if($return) {
      return $message;
    } else {
      echo $message;
    }
  }

  public static function get_business_category() {
    return 2;
  }

  public static function get_api_environment() {
    $business_category = Utilities::get_business_category();
    $environment = $_SERVER['apiEnvironment'];
    $api_urls = Config::get_api_urls();
    return $api_urls[$business_category][$environment];
  }

  public static function is_session_started() {
    if (php_sapi_name()!=='cli') {
      if ( version_compare(phpversion(), '5.4.0', '>=') ) {
        return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
      } else {
        return session_id() === '' ? FALSE : TRUE;
      }
    }
    return FALSE;
  }
}

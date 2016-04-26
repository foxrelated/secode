<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Yundraising
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Gateway.php
 */
class Ynfundraising_Api_Gateway extends Core_Api_Abstract
{
     // check Sandbox mode
     public function isSandBoxMode(){
        return Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfundraising.mode', 1);
     }

     public function getCommission(){

         $viewer = Engine_Api::_ ()->user ()->getViewer ();
         $commission =  Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynfundraising_campaign', $viewer, 'commission');

         $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
         $msselect = $mtable->select()
         ->where("type = 'ynfundraising_campaign'")
         ->where("level_id = ?", $viewer->level_id)
         ->where("name = 'commission'");
         $mallow = $mtable->fetchRow($msselect);
         //$commission = 0;
         if (!empty($mallow) && $commission == null)
         {
             $commission = $mallow['value'];
         }
         return $commission;
     }

     public function getConfig($gateway_name = 'paypal')
     {
     	$table = Engine_Api::_()->getDbTable('gateways', 'ynfundraising');
     	$select = $table->select()->from($table)->where('gateway_name  = ?', $gateway_name);
     	$result =  $table->fetchRow($select)->toArray();
     	return Zend_Json::decode($result['params']);
     }

     public function checkConfig($gateway_name = 'paypal') {
        $config = $this->getConfig($gateway_name);
        if (!empty($config['username']) &&
            !empty($config['password']) &&
            !empty($config['signature']) &&
            !empty($config['appid']) &&
            !empty($config['account'])) {
            return true;
        }
        return false;
     }
     /**
      * hash_call: Function to perform the API call to PayPal using API signature
      * @methodName is name of API  method.
      * @nvpStr is nvp string.
      * returns an associtive array containing the response from the server.
      */
     public function hash_call($methodName, $nvpStr, $sandboxEmailAddress = '')
     {
         $URL = Ynfundraising_Plugin_Utilities::getAdaptivePaymentsUrl() . $methodName;
         //setting the curl parameters.
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL,$URL);
         curl_setopt($ch, CURLOPT_VERBOSE, 1);

         //turning off the server and peer verification(TrustManager Concept).
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

         curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
         curl_setopt($ch, CURLOPT_POST, 1);

         $headers_array = $this->setupHeaders();

         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_array);
         curl_setopt($ch, CURLOPT_HEADER, false);
         //setting the nvpreq as POST FIELD to curl
         curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpStr);

         //getting response from server
         $response = curl_exec($ch);

         //convrting NVPResponse to an Associative Array
         $nvpResArray = $this->deformatNVP($response);

         if (curl_errno($ch)) {
             // display curl errors
             Zend_Registry::get('Zend_Log')->log('Paypal error', Zend_Log::DEBUG);
             Zend_Registry::get('Zend_Log')->log(print_r(curl_error($ch),true), Zend_Log::DEBUG);
         } else {
             //closing the curl
             curl_close($ch);
         }

         return $nvpResArray;
     }

     /** This function will take NVPString and convert it to an Associative Array and it will decode the response.
      * It is usefull to search for a particular key and displaying arrays.
      * @nvpstr is NVPString.
      * @nvpArray is Associative Array.
      */

     public function deformatNVP($nvpstr)
     {

         $intial=0;
         $nvpArray = array();

         while(strlen($nvpstr)){
             //postion of Key
             $keypos= strpos($nvpstr,'=');
             //position of value
             $valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

             /*getting the Key and Value values and storing in a Associative Array*/
             $keyval=substr($nvpstr,$intial,$keypos);
             $valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
             //decoding the respose
             $nvpArray[urldecode($keyval)] =urldecode( $valval);
             $nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
         }
         return $nvpArray;
     }

     public function setupHeaders() {
         $config = $this->getConfig();

         $headers_arr = array();
         $headers_arr[]="X-PAYPAL-SECURITY-SIGNATURE: " . $config['signature'];
         $headers_arr[]="X-PAYPAL-SECURITY-USERID:  " . $config['username'];
         $headers_arr[]="X-PAYPAL-SECURITY-PASSWORD: " . $config['password'];
         $headers_arr[]="X-PAYPAL-APPLICATION-ID: " . $config['appid'];
         $headers_arr[] = "X-PAYPAL-REQUEST-DATA-FORMAT: " . Ynfundraising_Plugin_Constants::REQUEST_FORMAT;
         $headers_arr[] = "X-PAYPAL-RESPONSE-DATA-FORMAT: "  . Ynfundraising_Plugin_Constants::RESPONSE_FORMAT;
         //$headers_arr[]="X-PAYPAL-DEVICE-IPADDRESS: " . DEVICE_IPADDRESS;

         /*
         if(!defined('X-PAYPAL-REQUEST-SOURCE'))
         {
             $headers_arr[]="X-PAYPAL-REQUEST-SOURCE: " . X_PAYPAL_REQUEST_SOURCE;
         }
         else
             $headers_arr[]="X-PAYPAL-REQUEST-SOURCE: " . X_PAYPAL_REQUEST_SOURCE . "-" . X-PAYPAL-REQUEST-SOURCE;
         */
         return $headers_arr;

     }
}
?>

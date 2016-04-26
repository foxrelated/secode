<?php 

include_once dirname(__FILE__)  . '/library/Braintree.php';

class Ynmobile_Api_Braintreepayment
{

	public function __construct()
	{
		

		error_reporting(E_ALL);
		ini_set('display_startup_errors',1);
		ini_set('display_errors',1);

		Braintree_Configuration::environment('sandbox');
		Braintree_Configuration::merchantId('fb9dtg78wbb5nktr');
		Braintree_Configuration::publicKey('y277jn357pd3x5ms');
		Braintree_Configuration::privateKey('98b39a04354092db1309cc3be5d704ef');

	}

	public function _create_customer_then_response_client_token(){
		$viewer =  Engine_Api::_()->user()->getViewer();

		$result = Braintree_Customer::create(array(
            'firstName' => $viewer->getTitle(),
            // 'lastName' => 'Jones',
            // 'company' => 'Jones Co.',
            'email' => $viewer->email,
            // 'phone' => '281.330.8004',
            // 'fax' => '419.555.1235',
            // 'website' => 'http://example.com'
        ));

        $result->success;
    # true

        $customerId =  $result->customer->id;
    # Generated customer id

        $clientToken = Braintree_ClientToken::generate(
            array(
                "customerId" => $customerId
            ));

        $response =  array(
            'client_token'=>$clientToken,
            'customer_id'=>$customerId,
            );

        return $response;
	}

	public function get_client_token()
	{
		$viewer =  Engine_Api::_()->user()->getViewer();

		// check customer id exists on our database.

		return $this->_create_customer_then_response_client_token();


	}
}
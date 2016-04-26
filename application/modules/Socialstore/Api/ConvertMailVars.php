<?php


/**
 *
 * XXX: longlt continue implement 
 */
class Socialstore_Api_ConvertMailVars extends Core_Api_Abstract 
{
	
	protected static $_baseUrl;
	
	public static function getBaseUrl(){
		if(self::$_baseUrl == NULL){
			$request =  Zend_Controller_Front::getInstance()->getRequest();
			self::$_baseUrl = sprintf('%s://%s', $request->getScheme(), $request->getHttpHost());
			
		}
		return self::$_baseUrl;
	}
	/**
	 * @param   string $type
	 * @return  string
	 */
	public function selfURL() 
    {
      return self::getBaseUrl();
    }

	public function inflect($type) {
		return sprintf('vars_%s', $type);
	}

	public function vars_default($params, $vars) {
		return $params;
	}

	/**
	 * call from api
	 */
	public function process($params, $vars, $type) {
		$method_name = $this->inflect($type);
		if(method_exists($this, $method_name)) {
			return $this -> {$method_name}($params, $vars);
		}
		return $this -> vars_default($params, $vars);
	}

	/**
	 *
	 */


	public function vars_store_approvestore($params, $vars) {
		$rparams = array();
		$rparams['store_title'] = "\"".$params['title']."\"";
		$store = Engine_Api::_()->getItem('social_store', $params['store_id']);  
		$rparams['store_link'] = $this->selfURL().$store->getHref();
		return $rparams;
	}
	
	public function vars_store_approveproduct($params, $vars) {
		$rparams = array();
		$rparams['product_title'] = "\"".$params['title']."\"";
		$product = Engine_Api::_()->getItem('social_product', $params['product_id']);  
		$store = Engine_Api::_()->getItem('social_store', $params['store_id']);
		$rparams['product_link'] = $this->selfURL().$product->getHref();
		$rparams['store_title'] = $store->title;
		$rparams['store_link'] = $this->selfURL().$store->getHref();
		return $rparams;
	}
	
	public function vars_store_purchasebuyer($params, $vars) {
		$rparams = array();
		$rparams['ordercontent'] = $params['ordercontent'];
		$rparams['buyer_name'] = $params['buyer_name'];
		$rparams['buyer_email'] = $params['buyer_email'];
		$rparams['buyer_address'] = $params['buyer_address'];
		return $rparams;
	}
	public function vars_store_purchaseseller($params, $vars) {
		$rparams = array();
		$rparams['store_title'] = $params['store_title'];
		$rparams['store_link'] = $params['store_link'];
		$rparams['product_title'] = $params['product_title'];
		$rparams['product_link'] = $params['product_link'];
		$rparams['store_orderid'] = $params['store_orderid'];
		$rparams['product_quantity'] = $params['product_quantity'];
		$rparams['product_price'] = $params['product_price'];
		$rparams['product_total'] = $params['product_total'];
		$rparams['buyer_name'] = $params['buyer_name'];
		$rparams['buyer_email'] = $params['buyer_email'];
		$rparams['buyer_address'] = $params['buyer_address'];
		return $rparams;
	}	
	public function vars_store_requestaccept($params, $vars) {
		$rparams = array();
		$store = Engine_Api::_()->getItem('social_store', $params['store_id']);
		$rparams['store_title'] = $store->title;
		$rparams['store_link'] = $this->selfURL().$store->getHref();
		$rparams['request_amount'] = $params['request_amount'];
		return $rparams;
	}
	public function vars_store_requestdeny($params, $vars) {
		$rparams = array();
		$store = Engine_Api::_()->getItem('social_store', $params['store_id']);
		$rparams['store_title'] = $store->title;
		$rparams['store_link'] = $this->selfURL().$store->getHref();
		$rparams['request_amount'] = $params['request_amount'];
		return $rparams;
	}
	public function vars_store_productdelete($params, $vars) {
		$rparams = array();
		$store = Engine_Api::_()->getItem('social_store', $params['store_id']);
		$rparams['store_title'] = $store->title;
		$rparams['store_link'] = $this->selfURL().$store->getHref();
		$rparams['product_title'] = $params['title'];
		return $rparams;
	}
	public function vars_store_productdelbuyers($params, $vars) {
		$rparams = array();
		$store = Engine_Api::_()->getItem('social_store', $params['owner_id']);
		$rparams['store_title'] = $store->title;
		$rparams['store_link'] = $this->selfURL().$store->getHref();
		$rparams['product_title'] = $params['title'];
		return $rparams;
	}
	public function vars_store_productdelfav($params, $vars) {
		$rparams = array();
		$rparams['store_title'] = $params['store_title'];
		$rparams['store_link'] = $this->selfURL().$params['store_link'];
		$rparams['product_title'] = $params['product_title'];
		return $rparams;
	}
	public function vars_store_follownotice($params, $vars) {
		$rparams = array();
		$rparams['store_title'] = $params['store_title'];
		$rparams['store_link'] = $this->selfURL().$params['store_link'];
		$rparams['product_title'] = $params['product_title'];
		$rparams['product_link'] = $this->selfURL().$params['product_link'];
		return $rparams;
	}
	public function vars_store_refundbuyer($params, $vars) {
		$rparams = array();
		$rparams['store_title'] = $params['store_title'];
		$rparams['store_link'] = $this->selfURL().$params['store_link'];
		$rparams['product_title'] = $params['product_title'];
		$rparams['product_link'] = $this->selfURL().$params['product_link'];
		$rparams['store_orderid'] = $params['store_orderid'];
		return $rparams;
	}
	public function vars_store_refundseller($params, $vars) {
		$rparams = array();
		$rparams['store_title'] = $params['store_title'];
		$rparams['store_link'] = $this->selfURL().$params['store_link'];
		$rparams['product_title'] = $params['product_title'];
		$rparams['product_link'] = $this->selfURL().$params['product_link'];
		$rparams['store_orderid'] = $params['store_orderid'];
		
		$rparams['buyer_name'] = $params['buyer_name'];
		$rparams['buyer_email'] = $params['buyer_email'];
		$rparams['buyer_address'] = $params['buyer_address'];
		return $rparams;
	}
}



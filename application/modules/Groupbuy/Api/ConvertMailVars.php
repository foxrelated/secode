<?php


/**
 *
 * XXX: longlt continue implement 
 */
class Groupbuy_Api_ConvertMailVars extends Core_Api_Abstract 
{
	
	protected static $_baseUrl;
	
	public static function getBaseUrl(){
		if(self::$_baseUrl == NULL){
            //self::$_baseUrl = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://');
            $pageURL = 'http';
            if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
            $pageURL .= "://";
            $pageURL .= $_SERVER["SERVER_NAME"];
			self::$_baseUrl = $pageURL;
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
	public function vars_groupbuy_dealbought($params, $vars) {
		return $params;
	}
	public function vars_groupbuy_dealday($params, $vars) {
		return $params;
	}
	public function vars_groupbuy_dealsubscribed($params, $vars) {
		return $params;
	}
	public function vars_groupbuy_buyerdealdel($params, $vars) {
		$rparams[] = array();
		$rparams['deal_title'] = "\"".$params['title']."\"";
		$deal = Engine_Api::_()->getItem('deal', $params['deal_id']);  
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://'); 
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		$rparams['deal_price'] = number_format($deal->final_price,2)." ".$deal->currency;
		$rparams['deal_quantity'] = $params['total_number'];
		$rparams['deal_total'] = number_format($params['total_amount'],2)." ".$deal->currency;
		return $rparams;
	}
	public function vars_groupbuy_sellerdealdel($params, $vars) {
		$rparams[] = array();
		$rparams['deal_title'] = "\"".$params['title']."\"";
		$deal = Engine_Api::_()->getItem('deal', $params['deal_id']);  
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://'); 
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		return $rparams;
	}
	public function vars_groupbuy_buyerdealclosed($params, $vars) {
		$deal = Engine_Api::_()->getItem('deal', $params['deal_id']);  
		if(!is_object($deal)){
			return ;
		}
		$rparams[] = array();
		$rparams['deal_title'] = "\"".$params['title']."\"";
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://'); 
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		$rparams['deal_price'] = number_format($deal->final_price,2)." ".$deal->currency;
		$rparams['deal_quantity'] = $params['total_number'];
		$rparams['deal_total'] = number_format($params['total_amount'],2)." ".$deal->currency;
		return $rparams;
	}	
	public function vars_groupbuy_sellerdealclosed($params, $vars) {
		$rparams[] = array();
		$rparams['deal_title'] = "\"".$params['title']."\"";
		$deal = Engine_Api::_()->getItem('deal', $params['deal_id']);  
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://');
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		return $rparams;
	}
	public function vars_groupbuy_dealrunning($params, $vars) {
		$rparams[] = array();
		$rparams['deal_title'] = "\"".$params['title']."\"";
		$deal = Engine_Api::_()->getItem('deal', $params['deal_id']);  
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://');
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		return $rparams;
	}
	public function vars_groupbuy_buydealseller($params, $vars) {
		$rparams = array();
		$deal = Engine_Api::_()->getItem('deal', $params['item_id']);  
		$rparams['deal_title'] = "\"".$deal->title."\"";
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://');
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		$rparams['deal_price'] = number_format($deal->final_price,2)." ".$deal->currency;
		$rparams['deal_quantity'] = $params['number'];
		$rparams['deal_total'] = number_format($params['amount'],2)." ".$deal->currency;
		$buyer = Engine_Api::_()->getItem('user', $params['user_id']);
		$rparams['buyer_name'] = $buyer->displayname;
		$rparams['buyer_email'] = $buyer->email;
		$rparams['deal_tranid'] = $params['code'];
		$rparams['deal_code'] = $params['coupon_codes'];
		return $rparams;
	}
	public function vars_groupbuy_buydealbuyer($params, $vars) {
		$rparams = array();
		$deal = Engine_Api::_()->getItem('deal', $params['item_id']);  
		$rparams['deal_title'] = "\"".$deal->title."\"";
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://');
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		$rparams['deal_price'] = number_format($deal->final_price,2)." ".$deal->currency;
		$rparams['deal_quantity'] = $params['number'];
		$rparams['deal_total'] = number_format($params['amount'],2)." ".$deal->currency;
		$buyer = Engine_Api::_()->getItem('user', $params['user_id']);
		$rparams['buyer_name'] = $buyer->displayname;
		$rparams['buyer_email'] = $buyer->email;
		$rparams['seller_name'] = $deal->company_name;
		$rparams['seller_address'] = $deal->address;
		$rparams['seller_email'] = $deal->getOwner()->email;
		$rparams['deal_tranid'] = $params['code'];
		$rparams['deal_code'] = $params['coupon_codes'];
		return $rparams;
	}
	public function vars_groupbuy_approvedeal($params, $vars) {
		$rparams = array();
		$rparams['deal_title'] = "\"".$params['title']."\"";
		$deal = Engine_Api::_()->getItem('deal', $params['deal_id']);  
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://');
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		return $rparams;
	}
	
	public function vars_groupbuy_codseller($params, $vars) {
		$rparams = array();
		$deal = Engine_Api::_()->getItem('deal', $params['deal_id']);  
		$rparams['deal_title'] = "\"".$deal->title."\"";
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://');
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		$rparams['deal_price'] = number_format($deal->final_price,2)." ".$deal->currency;
		$rparams['deal_quantity'] = $params['number'];
		$rparams['deal_total'] = number_format($params['amount'],2)." ".$deal->currency;
		$rparams['buyer_name'] = $params['buyer_name'];
		$rparams['buyer_email'] = $params['email'];
		$rparams['buyer_address'] = $params['address'];
		$rparams['buyer_phone'] = $params['phone'];
		$rparams['buyer_extranote'] = $params['note'];
		$rparams['deal_code'] = $params['coupon_codes'];
		return $rparams;
	}
	public function vars_groupbuy_codbuy($params, $vars) {
		
		$rparams = array();
		$deal = Engine_Api::_()->getItem('deal', $params['deal_id']);  
		$rparams['deal_title'] = "\"".$deal->title."\"";
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://');
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		$rparams['deal_price'] = number_format($deal->final_price,2)." ".$deal->currency;
		$rparams['deal_quantity'] = $params['number'];
		$rparams['deal_total'] = number_format($params['amount'],2)." ".$deal->currency;
		$rparams['buyer_name'] = $params['buyer_name'];
		$rparams['buyer_email'] = $params['email'];
		$rparams['buyer_address'] = $params['address'];
		$rparams['buyer_phone'] = $params['phone'];
		$rparams['buyer_extranote'] = $params['note'];
		$rparams['seller_name'] = $deal->company_name;
		$rparams['seller_address'] = $deal->address;
		$rparams['seller_email'] = $deal->getOwner()->email;
		return $rparams;
	}
	public function vars_groupbuy_deletebuyToBuyer($params, $vars) {
		$rparams = array();
		$deal = Engine_Api::_()->getItem('deal', $params['item_id']);  
		$rparams['deal_title'] = "\"".$deal->title."\"";
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://');
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		return $rparams;
	}
	public function vars_groupbuy_deletebuyToSeller($params, $vars) {
		$rparams = array();
		$deal = Engine_Api::_()->getItem('deal', $params['item_id']);  
		$rparams['deal_title'] = "\"".$deal->title."\"";
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://');
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		$rparams['deal_price'] = number_format($deal->final_price,2)." ".$deal->currency;
		$rparams['deal_quantity'] = $params['number'];
		$rparams['deal_total'] = number_format($params['amount'],2)." ".$deal->currency;
		$codTable = Engine_Api::_()->getDbtable('buyCods', 'groupbuy');
		$codSelect = $codTable->select()->from($codTable->info('name'));
		$codSelect->where('deal_id = ?', $params['item_id']);
		$codSelect->where('user_id = ?', $params['user_id']);
		$codResult = $codTable->fetchAll($codSelect)->toArray();
		$rparams['buyer_name'] = $codResult[0]['buyer_name'];
		$rparams['buyer_email'] = $codResult[0]['email'];
		$rparams['buyer_address'] = $codResult[0]['address'];
		$rparams['buyer_phone'] = $codResult[0]['phone'];
		$rparams['buyer_extranote'] = $codResult[0]['note'];
		return $rparams;
	}
	public function vars_groupbuy_giftconfirm($params, $vars) {
		$rparams = array();
		$deal = Engine_Api::_()->getItem('deal', $params['item_id']);  
		$rparams['deal_title'] = "\"".$deal->title."\"";
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://');
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		$rparams['deal_quantity'] = $params['number'];
		$buyer = Engine_Api::_()->getItem('user', $params['user_id']);
		$rparams['buyer_name'] = $buyer->displayname;
		$rparams['buyer_email'] = $buyer->email;
		$Bills  =  new Groupbuy_Model_DbTable_Bills;
		$select =  $Bills->select()->where('bill_id=?',$params['bill_id'] );
		$bill =  $Bills->fetchRow($select);
		$gift =  $bill->getGift();	
		$rparams['buyer_note'] = $gift->note;
		$rparams['friend_name'] = $gift->friend_name;
		$rparams['friend_email'] = $gift->friend_email;
		$rparams['friend_address'] = $gift->friend_address;
		$rparams['friend_phone'] = $gift->friend_phone;
		//$rparams['confirm_link'] = "['confirm_link']";
		$rparams['deal_code'] = $params['coupon_codes'];
				return $rparams;
	}
	public function vars_groupbuy_giftunconfirm($params, $vars) {
		$rparams = array();
		$deal = Engine_Api::_()->getItem('deal', $params['deal_id']);  
		$rparams['deal_title'] = "\"".$deal->title."\"";
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://');
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		$rparams['deal_quantity'] = $params['number'];
		$buyer = Engine_Api::_()->getItem('user', $params['user_id']);
		$rparams['buyer_name'] = $buyer->displayname;
		$rparams['buyer_email'] = $buyer->email;
		$rparams['deal_code'] = $params['coupon_codes'];
		return $rparams;
	}
	public function vars_groupbuy_buygiftbuyer($params, $vars) {
		$rparams = array();
		$deal = Engine_Api::_()->getItem('deal', $params['item_id']);  
		$rparams['deal_title'] = "\"".$deal->title."\"";
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://');
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		$rparams['deal_price'] = number_format($deal->final_price,2)." ".$deal->currency;
		$rparams['deal_quantity'] = $params['number'];
		$rparams['deal_total'] = number_format($params['amount'],2)." ".$deal->currency;
		$buyer = Engine_Api::_()->getItem('user', $params['user_id']);
		$rparams['buyer_name'] = $buyer->displayname;
		$rparams['buyer_email'] = $buyer->email;
		$Bills  =  new Groupbuy_Model_DbTable_Bills;
		$select =  $Bills->select()->where('bill_id=?',$params['bill_id'] );
		$bill =  $Bills->fetchRow($select);
		$gift =  $bill->getGift();	
		$rparams['buyer_note'] = $gift->note;
		$rparams['friend_name'] = $gift->friend_name;
		$rparams['friend_email'] = $gift->friend_email;
		$rparams['friend_address'] = $gift->friend_address;
		$rparams['friend_phone'] = $gift->friend_phone;
		$rparams['seller_name'] = $deal->company_name;
		$rparams['seller_address'] = $deal->address;
		$rparams['seller_email'] = $deal->getOwner()->email;	
		$rparams['deal_tranid'] = $params['code'];
				return $rparams;
	}
	public function vars_groupbuy_buygiftseller($params, $vars) {
		$rparams = array();
		$deal = Engine_Api::_()->getItem('deal', $params['item_id']);  
		$rparams['deal_title'] = "\"".$deal->title."\"";
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseUrl','http://');
		$rparams['deal_link'] = $this->getBaseUrl().$deal->deal_href;
		$rparams['deal_price'] = number_format($deal->final_price,2)." ".$deal->currency;
		$rparams['deal_quantity'] = $params['number'];
		$rparams['deal_total'] = number_format($params['amount'],2)." ".$deal->currency;
		$buyer = Engine_Api::_()->getItem('user', $params['user_id']);
		$rparams['buyer_name'] = $buyer->displayname;
		$rparams['buyer_email'] = $buyer->email;
		$Bills  =  new Groupbuy_Model_DbTable_Bills;
		$select =  $Bills->select()->where('bill_id=?',$params['bill_id'] );
		$bill =  $Bills->fetchRow($select);
		$gift =  $bill->getGift();	
		$rparams['buyer_note'] = $gift->note;
		$rparams['friend_name'] = $gift->friend_name;
		$rparams['friend_email'] = $gift->friend_email;
		$rparams['friend_address'] = $gift->friend_address;
		$rparams['friend_phone'] = $gift->friend_phone;
		$rparams['deal_tranid'] = $params['code'];	
		$rparams['deal_code'] = $params['coupon_codes'];
				return $rparams;
	}
}



<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: IndexController.php
 * @author     Minh Nguyen
 */
class Groupbuy_IndexController extends Core_Controller_Action_Standard {
	protected $_paginate_params = array();
	protected $_maxAllowCategory = 0;
	public function init() {
		$this -> view -> headScript() -> appendFile($this -> view -> baseUrl() . '/application/modules/Groupbuy/externals/scripts/core.js');
		$this -> view -> headScript() -> appendFile('//maps.googleapis.com/maps/api/js?sensor=false&libraries=places');

		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$this -> _paginate_params['limit'] = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('groupbuy.page', 10);
		$this -> _paginate_params['sort'] = $this -> getRequest() -> getParam('sort', 'recent');
		$this -> _paginate_params['page'] = $this -> getRequest() -> getParam('page', 1);
		$this -> _paginate_params['search'] = $this -> getRequest() -> getParam('search', '');
	}

	public function browseAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> authorization() -> isAllowed('groupbuy_deal', $viewer, 'view')) {
			return false;
		}
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_main', array(), 'groupbuy_main_browse');

		if ($this -> getRequest() -> isPost()) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'listing'), 'groupbuy_general', true);
		}
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function listingAction() {
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function deliveryAction() {
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_main');
		$this -> view -> form = $form = new Groupbuy_Form_Cod();
		$form -> populate($this -> _getAllParams());
		$deal = Engine_Api::_() -> getItem('deal', $this -> _getParam('deal'));
		$this -> view -> deal = $deal;
		$post = $this -> getRequest() -> getPost();
		if (!isset($post['email']))
			return;
		if (!$form -> isValid($post))
			return;
		$email = $this -> _getParam('email');
		if (trim($email) == "") {
			$form -> getElement('email') -> addError('Please enter valid email!');
			return;
		} else if (trim($email) != "") {
			$regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
			if (!preg_match($regexp, $email)) {
				$form -> getElement('email') -> addError('Please enter valid email!');
				return;
			}
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$deal_id = $form -> getValue('deal');
		$number = $form -> getValue('number_buy');
		$total_amount = $form -> getValue('total_amount');
		$deal = Engine_Api::_() -> getItem('deal', $deal_id);
		$deal -> current_sold = $deal -> current_sold + $number;
		if ($deal -> current_sold >= $deal -> max_sold) {
			$deal -> status = 40;
			$deal -> end_time = date("Y-m-d H:i:s");
		}
		$deal -> save();
		$tttable = Engine_Api::_() -> getDbtable('transactionTrackings', 'groupbuy');
		$ttdb = $tttable -> getAdapter();
		$ttdb -> beginTransaction();
		try {
			$ttvalues = array('transaction_date' => date('Y-m-d H:i:s'), 'user_seller' => $deal -> user_id, 'user_buyer' => $viewer -> getIdentity(), 'item_id' => $deal_id, 'amount' => $total_amount, 'account_seller_id' => Groupbuy_Api_Cart::getFinanceAccount($deal -> user_id, 2), 'account_buyer_id' => Groupbuy_Api_Cart::getFinanceAccount($viewer -> getIdentity(), 2), 'number' => $number, 'transaction_status' => '1', 'params' => 'Cash on Delivery', );
			$ttrow = $tttable -> createRow();
			$ttrow -> setFromArray($ttvalues);
			$ttrow -> save();
			$tranid = $ttrow -> transactiontracking_id;
			$ttdb -> commit();
		} catch (exception $e) {
			$ttdb -> rollBack();
			throw $e;
		}
		$table = Engine_Api::_() -> getDbtable('buyCods', 'groupbuy');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			// Create groupbuy
			$values = array_merge($form -> getValues(), array('user_id' => $viewer -> getIdentity(), 'deal_id' => $deal_id, ));
			$cod = $table -> createRow();
			$cod -> setFromArray($values);
			$cod -> tran_id = $ttrow -> transactiontracking_id;
			$cod -> save();

			for ($i = 1; $i <= $number; $i++) {
				$coupon_code = Engine_Api::_() -> getDbTable('coupons', 'groupbuy') -> addCoupon($viewer -> getIdentity(), $deal_id, $cod -> buycod_id, 1, $tranid);
			}
			$db -> commit();
		} catch (exception $e) {
			$db -> rollBack();
			throw $e;
		}
		$bdtable = Engine_Api::_() -> getDbtable('buyDeals', 'groupbuy');
		$bddb = $bdtable -> getAdapter();
		$bddb -> beginTransaction();
		try {
			$bdvalues = array('item_id' => $deal_id, 'owner_id' => $deal -> user_id, 'user_id' => $viewer -> getIdentity(), 'amount' => $total_amount, 'number' => $number, 'status' => '2', 'buy_date' => date('Y-m-d H:i:s'), );
			$bdrow = $bdtable -> createRow();
			$bdrow -> setFromArray($bdvalues);
			$bdrow -> save();
			$bddb -> commit();
		} catch (exception $e) {
			$bddb -> rollBack();
			throw $e;
		}

		/**
		 * Call Event from Affiliate
		 */
		$module = 'ynaffiliate';
		$modulesTable = Engine_Api::_() -> getDbtable('modules', 'core');
		$mselect = $modulesTable -> select() -> where('enabled = ?', 1) -> where('name  = ?', $module);
		$module_result = $modulesTable -> fetchRow($mselect);
		if (count($module_result) > 0) {
			$params['module'] = 'groupbuy';
			$params['user_id'] = $viewer -> getIdentity();
			$params['rule_name'] = 'buy_deal';
			$params['currency'] = $deal -> currency;
			$params['total_amount'] = $total_amount;
			Engine_Hooks_Dispatcher::getInstance() -> callEvent('onPaymentAfter', $params);
		}

		/**
		 * End Call Event from Affiliate
		 */

		$params = $cod -> toArray();
		$params1 = $bdrow -> toArray();
		$params['amount'] = $params1['amount'];
		$params['number'] = $params1['number'];
		$ctable = new Groupbuy_Model_DbTable_Coupons;
		$cselect = $ctable -> select() -> where('cod_id=?', $cod -> buycod_id);
		$crows = $ctable -> fetchAll($cselect);
		$cresult = array();
		foreach ($crows as $crow) {
			$cresult[] = $crow -> code;
		}
		$params['coupon_codes'] = implode(' - ', $cresult);
		$owner = $deal -> getOwner();
			if ($owner -> getIdentity()) {
			$sendTo = $owner -> email;
			Engine_Api::_() -> getApi('mail', 'groupbuy') -> send($sendTo, 'groupbuy_codseller', $params);
		}

		$sendTo = $cod -> email;
		$params = $cod -> toArray();
		$params1 = $bdrow -> toArray();
		$params['amount'] = $params1['amount'];
		$params['number'] = $params1['number'];
		Engine_Api::_() -> getApi('mail', 'groupbuy') -> send($sendTo, 'groupbuy_codbuy', $params);

		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage-buying'), 'groupbuy_general', true);
	}

	public function accountmoneyAction() {
		$receiver = array (
			'email' => '',
			'invoice' => Groupbuy_Api_Cart::getSecurityCode()
		);
		$deal_id = $this -> _getParam('deal');
		$deal = Engine_Api::_() -> getItem('deal', $deal_id);
		$numbers = $this -> _getParam('number_buy1');
		//create bill
		$bill = Groupbuy_Api_Cart::makeBillFromCart($deal, $receiver, 1, $numbers, true);
		//update status bill
		$bill -> bill_status = 1;
		$bill -> save();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$paymentaccount = Groupbuy_Api_Cart::getFinanceAccount($viewer -> getIdentity(), 2);
		Groupbuy_Api_Account::updateAmount($paymentaccount['paymentaccount_id'], $bill -> amount, 2);

		$paymentaccount = Groupbuy_Api_Cart::getFinanceAccount($deal -> user_id, 2);
		Groupbuy_Api_Account::updateAmount($paymentaccount['paymentaccount_id'], $bill -> amount - $bill -> commission_fee, 1);

		//check number sell
		$deal -> current_sold = $deal -> current_sold + $numbers;
		if ($deal -> current_sold >= $deal -> max_sold) {
			$deal -> status = 40;
			$deal -> end_time = date("Y-m-d H:i:s");
		}
		$deal -> save();
		//Save transaction tracking
		$tttable = Engine_Api::_() -> getDbtable('transactionTrackings', 'groupbuy');
		$ttdb = $tttable -> getAdapter();
		$ttdb -> beginTransaction();
		try {
			$account_seller = Groupbuy_Api_Cart::getFinanceAccount($deal -> user_id, 2);
			$account_buyer = Groupbuy_Api_Cart::getFinanceAccount($viewer -> getIdentity(), 2);
			$ttvalues = array('transaction_date' => date('Y-m-d H:i:s'), 'user_seller' => $deal -> user_id, 'user_buyer' => $viewer -> getIdentity(), 'item_id' => $deal_id, 'amount' => $bill -> amount, 'commission_fee' => $bill -> commission_fee, 'currency' => $bill -> currency, 'account_seller_id' => $account_seller['paymentaccount_id'], 'account_buyer_id' => $account_buyer['paymentaccount_id'], 'number' => $numbers, 'transaction_status' => '1', 'params' => 'Buy deal by virtual money', );
			$ttrow = $tttable -> createRow();
			$ttrow -> setFromArray($ttvalues);
			$ttrow -> save();
			$tranid = $ttrow -> transactiontracking_id;
			$ttdb -> commit();
		} catch (exception $e) {
			$ttdb -> rollBack();
			throw $e;
		}
		//create coupon code
		for ($i = 1; $i <= $bill -> number; $i++) {
			$coupon_code = Engine_Api::_() -> getDbTable('coupons', 'groupbuy') -> addCoupon($bill -> user_id, $bill -> item_id, $bill -> bill_id, 0, $tranid);
		}
		//save to table buy deal
		$bdtable = Engine_Api::_() -> getDbtable('buyDeals', 'groupbuy');
		$bddb = $bdtable -> getAdapter();
		$bddb -> beginTransaction();
		try {
			$bdvalues = array('item_id' => $deal_id, 'owner_id' => $deal -> user_id, 'user_id' => $viewer -> getIdentity(), 'amount' => $bill -> amount, 'number' => $numbers, 'status' => '2', 'buy_date' => date('Y-m-d H:i:s'), );
			$bdrow = $bdtable -> createRow();
			$bdrow -> setFromArray($bdvalues);
			$bdrow -> save();
			$bddb -> commit();
		} catch (exception $e) {
			$bddb -> rollBack();
			throw $e;
		}
		/**
		 * Call Event from Affiliate
		 */

		$module = 'ynaffiliate';
		$modulesTable = Engine_Api::_() -> getDbtable('modules', 'core');
		$mselect = $modulesTable -> select() -> where('enabled = ?', 1) -> where('name  = ?', $module);
		$module_result = $modulesTable -> fetchRow($mselect);
		if (count($module_result) > 0) {
			Zend_Registry::get('Zend_Log') -> log(print_r('count vo', true), Zend_Log::DEBUG);
			$params['module'] = 'groupbuy';
			$params['user_id'] = $viewer -> getIdentity();
			$params['rule_name'] = 'buy_deal';
			$params['currency'] = $deal -> currency;
			$params['total_amount'] = number_format($bill -> amount, 2);
			Zend_Registry::get('Zend_Log') -> log(print_r($params, true), Zend_Log::DEBUG);
			Engine_Hooks_Dispatcher::getInstance() -> callEvent('onPaymentAfter', $params);
		}

		/**
		 * End Call Event from Affiliate
		 */
		// send a bill to user.
		$billInfo = $bill -> toArray();
		$billInfo['code'] = '';
		//get all coupon code to seand mail
		$billInfo['coupon_codes'] = $bill -> getCoupons(' - ');

		$buyer = Engine_Api::_() -> getItem('user', $bill -> user_id);
		$seller = Engine_Api::_() -> getItem('user', $bill -> owner_id);

		// get mail service object
		$mailService = Engine_Api::_() -> getApi('mail', 'groupbuy');
		// send notification to seller.
		$mailService -> send($seller -> email, 'groupbuy_buydealseller', $billInfo);
		// send notification to buyer
		$mailService -> send($buyer -> email, 'groupbuy_buydealbuyer', $billInfo);
		$_SESSION['buy_succ'] = true;
		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage-buying'), 'groupbuy_general', true);
	}

	public function emailAction() {
		// Not a post

		if (!$this -> getRequest() -> isPost()) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('No action taken');
			return;
		}
		$email = $this -> _getAllParams();
		Groupbuy_Model_Email::insertEmail($email);
		//return $this->_helper->redirector->gotoRoute(array('action' => 'browse'), 'groupbuy_general', true);
	}

	public function createAction() {
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams('groupbuy_deal', null, 'create') -> isValid())
			return;
		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_main');
		$allow_payment = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('groupbuy.sellermethod', 0);

		$this -> view -> form = $form = new Groupbuy_Form_Create();
		$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$info = Groupbuy_Api_Account::getCurrentInfo($user_id);

		if ($info['currency'])
			$form -> removeElement('currency');
		// If not post or form not valid, return
		if ($allow_payment == 0) {
			$options = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('groupbuy.adminmethod', '0');
			$form -> removeElement('method');
		}
		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		$post = $this -> getRequest() -> getPost();

		// hardcode allow category is 3
		$allowCategory = 3;

		if (!$form -> isValid($post))
			return;

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$values = array_merge($form -> getValues(), array('user_id' => $viewer -> getIdentity(), ));
		$values['address'] = $values['location_address'];
		$values['latitude'] = $values['lat'];
		$values['longitude'] = $values['long'];
		if (!$values['address']) {
			$form -> getElement('location_map') -> addError('Value is required and can\'t be empty');
			return;
		}

		// Process
		$table = Engine_Api::_() -> getItemTable('deal');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			// Create groupbuy
			$deal = $table -> createRow();
			$deal -> setFromArray($values);

			$flag = 0;
			/*if(trim($values['title']) == "")
			 {
			 $form->getElement('title')->addError('Please complete this field - it is required.');
			 $flag = 1;
			 }
			 if(trim($values['company_name']) == "")
			 {
			 $form->getElement('company_name')->addError('Please complete this field - it is required.');
			 $flag = 1;
			 }
			 if(trim($values['address']) == "")
			 {
			 $form->getElement('address')->addError('Please complete this field - it is required.');
			 $flag = 1;
			 }
			 if(trim($values['features']) == "")
			 {
			 $form->getElement('features')->addError('Please complete this field - it is required.');
			 $flag = 1;
			 }
			 if(trim($values['fine_print']) == "")
			 {
			 $form->getElement('fine_print')->addError('Please complete this field - it is required.');
			 $flag = 1;
			 }
			 if(trim($values['description']) == "")
			 {
			 $form->getElement('description')->addError('Please complete this field - it is required.');
			 $flag = 1;
			 }
			 if($values['start_time'] <= 0)
			 {
			 $form->getElement('start_time')->addError('Please select a date from the calendar.');
			 $flag = 1;
			 }
			 if($values['end_time'] <= 0)
			 {
			 $form->getElement('end_time')->addError('Please select a date from the calendar.');
			 $flag = 1;
			 }*/
			//check price
			if (!is_numeric($values['price']) || $values['price'] < 0) {
				$form -> getElement('price') -> addError('The price number is invalid! (Ex: 2000.25)');
				$flag = 1;
			}
			if (!is_numeric($values['value_deal']) || $values['value_deal'] <= 0) {
				$form -> getElement('value_deal') -> addError('The value of deal number is invalid! (Ex: 2000.25)');
				$flag = 1;
			}
			if ($values['price'] > $values['value_deal']) {
				$form -> getElement('price') -> addError('Price should be equal or smaller than value of deal!');
				$flag = 1;
			}
			if ($values['max_sold'] != "") {
				if (!is_numeric($values['max_sold']) || $values['max_sold'] <= 0) {
					$form -> getElement('max_sold') -> addError('The maximum sold number is invalid! (Ex: 10)');
					$flag = 1;
				}
			}
			if (!is_numeric($values['min_sold']) || $values['min_sold'] <= 0) {
				$form -> getElement('min_sold') -> addError('The minimum sold number is invalid! (Ex: 1)');
				$flag = 1;
			}
			$min_sold = $values['min_sold'];
			$max_sold = $values['max_sold'];
			if ($min_sold > $max_sold) {
				$form -> getElement('max_sold') -> addError('Maximum sold should be greater than minimum sold!');
				$flag = 1;
			}
			//check start time and end time

			$oldTz = date_default_timezone_get();
			date_default_timezone_set($viewer -> timezone);
			$start_time = strtotime($values['start_time']);
			$end_time = strtotime($values['end_time']);
			$now = strtotime(date('Y-m-d H:i:s'));
			date_default_timezone_set($oldTz);

			$deal -> start_time = date('Y-m-d H:i:s', $start_time);
			$deal -> end_time = date('Y-m-d H:i:s', $end_time);

			if ($start_time >= $end_time) {
				$form -> getElement('end_time') -> addError('End Time should be greater than Start Time!');
				$flag = 1;
			}
			//date_default_timezone_set($values['timezone']);
			if ($start_time < $now) {
				$form -> getElement('start_time') -> addError('Start Time should be equal or greater than Current Time!');
				$flag = 1;
			}
			//check image
			if (!empty($values['thumbnail'])) {
				$file = $form -> thumbnail -> getFileName();
				$info = getimagesize($file);
				if ($info[2] > 3 || $info[2] == "") {
					$form -> getElement('thumbnail') -> addError('The uploaded file is not supported or is corrupt.');
					$flag = 1;
				}
			}
			// else {
			//     $form->getElement('thumbnail')->addError("The file 'thumbnail' was not uploaded.");
			//     $flag = 1;
			// }

			if ($flag == 1) {
				return false;
			}
			if (isset($values['method'])) {
				if ($values['method'] == null) {
					$deal -> method = $options;
				} else {
					$deal -> method = $values['method'];
				}
			} else
				$deal -> method = $options;
			$paymentaccount = Groupbuy_Api_Cart::getFinanceAccount($viewer -> getIdentity(), 2);
			if ($paymentaccount['currency'])
				$deal -> currency = $paymentaccount['currency'];
			else {
				Groupbuy_Api_Account::updatecurrency_account($paymentaccount['paymentaccount_id'], $deal -> currency);
			}
			$itcurrency = $deal -> getCurrency();
			$precision = $itcurrency -> precision;
			$deal -> price = round($deal -> price, $precision);
			$deal -> value_deal = round($deal -> value_deal, $precision);
			$deal -> discount = round((1 - $deal -> price / $deal -> value_deal) * 100);
			// fix set commission and VAT by member level settings.

			// fix commission to sold price.
			$commission = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('groupbuy_deal', $viewer, 'commission');

			if ($commission == "") {
				$mtable = Engine_Api::_() -> getDbtable('permissions', 'authorization');
				$maselect = $mtable -> select() -> where("type = 'groupbuy_deal'") -> where("level_id = ?", $viewer -> level_id) -> where("name = 'commission'");
				$mallow_a = $mtable -> fetchRow($maselect);
				if (!empty($mallow_a))
					$commission = $mallow_a['value'];
				else
					$commission = 0;
			}

			//Set Fee
			$total_fee = 0;
			$display_fee = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('groupbuy.displayfee', 10);
			$freeP = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('groupbuy_deal', $viewer, 'free_display');
			if ($freeP == 0)
				$total_fee = $total_fee + $display_fee;
			$freeF = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('groupbuy_deal', $viewer, 'free_fee');
			if ($freeF == 0) {
				if ($values['featured'] == 1)
					$total_fee = $total_fee + Engine_Api::_() -> getApi('settings', 'core') -> getSetting('groupbuy.fee', 10);
			}
			$deal -> total_fee = $total_fee;
			//$deal->updateVAT();
			$vat = 0;
			$table = Engine_Api::_() -> getDbtable('vats', 'groupbuy');
			$rName = $table -> info('name');
			$select = $table -> select() -> from($rName);
			$select -> where('vat_id = ?', $deal -> vat_id);
			$vatitem = $table -> fetchRow($select);
			//$vatitem = $this -> find((int)$deal->vat_id) -> current();
			if (is_object($vatitem)) {
				$vat = $vatitem -> value;
			}
			$deal -> vat = round($vat, 2);
			$deal -> vat_value = round($deal -> price * ($vat / 100), $precision);
			$deal -> final_price = round($deal -> price + $deal -> vat_value, $precision);

			$deal -> save();
			// Set photo
			if (!empty($values['thumbnail'])) {
				$deal -> setPhoto($form -> thumbnail, 0);
			}
			// Add fields
			$customfieldform = $form -> getSubForm('fields');
			$customfieldform -> setItem($deal);
			$customfieldform -> saveValues();
			// Set privacy
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
			$values['auth_view'] = 'everyone';

			if (empty($values['auth_comment'])) {
				$values['auth_comment'] = 'everyone';
			}

			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);

			foreach ($roles as $i => $role) {
				$auth -> setAllowed($deal, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($deal, $role, 'comment', ($i <= $commentMax));
			}
			// update vat at this time.
			$dealtable = Engine_Api::_() -> getDbTable('deals', 'groupbuy');
			$dealtable -> update(array('deal_href' => $deal -> getHref(), ), array('deal_id = ?' => $deal -> deal_id, ));

			// Commit
			$db -> commit();

			// $deal

		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
		/*
		 $db->beginTransaction();
		 try {
		 $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $deal, 'groupbuy_new');
		 if( $action != null ) {
		 Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $deal);
		 }
		 $db->commit();
		 }

		 catch( Exception $e )
		 {
		 $db->rollBack();
		 throw $e;
		 }
		 */
		// Redirect

		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'success', 'deal_id' => $deal -> deal_id), 'groupbuy_general', true);

	}

	public function getPostedCategories($values) {
		$allowCategory = Engine_Api::_() -> groupbuy() -> getMaxAllowCategory();
		$categories = array();
		for ($i = 0; $i < $allowCategory; ++$i) {
			$name = 'category_id_' . $i;
			if (isset($values[$name]) && $values[$name]) {
				$categories[] = $values[$name];
			}
		}
		return $categories;
	}

	public function successAction() {
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_main', array(), 'groupbuy_main_manage');
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$this -> view -> deal = $deal = Engine_Api::_() -> getItem('deal', $this -> _getParam('deal_id'));
		if ($viewer -> getIdentity() != $deal -> user_id) {
			return $this -> _forward('requireauth', 'error', 'core');
		}
		if ($this -> getRequest() -> isPost() && $this -> getRequest() -> getPost('confirm') == true) {
			return $this -> _redirect("groupbuy/photo/upload/subject/deal_" . $this -> _getParam('deal_id'));
		}
	}

	public function subcategoriesAction() {
		//tat di layout
		$this -> _helper -> layout -> disableLayout();
		//khong su dung view
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$cat_id = $this -> getRequest() -> getParam('cat_id');
		$subCategories = Engine_Api::_() -> groupbuy() -> getCategories($cat_id);
		$html = '';
		foreach ($subCategories as $subCategorie) {
			$html .= '<option value="' . $subCategorie -> category_id . '" label="' . $subCategorie -> title . '" >' . $subCategorie -> title . '</option>';
		}
		echo $html;
		return;
	}

	public function editAction() {
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_main', array(), 'groupbuy_main_managedeal');
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$deal = Engine_Api::_() -> getItem('deal', $this -> _getParam('deal'));
		$category = Engine_Api::_() -> getItem('groupbuy_category', $deal -> category_id);
		if (!Engine_Api::_() -> core() -> hasSubject('deal')) {
			Engine_Api::_() -> core() -> setSubject($deal);
		}
		// Check auth
		if (!$this -> _helper -> requireSubject() -> isValid()) {
			return;
		}
		if (!$this -> _helper -> requireAuth() -> setAuthParams($deal, $viewer, 'edit') -> isValid())
			return;
		// Prepare form
		$this -> view -> form = $form = new Groupbuy_Form_Edit( array('item' => $deal));
		$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$info = Groupbuy_Api_Account::getCurrentInfo($user_id);
		if ($info['currency'])
			$form -> removeElement('currency');
		$form -> removeElement('thumbnail');
		if ($deal -> published >= 10) {
			$form -> removeElement('feep');
			$form -> removeElement('featured');
			$form -> removeElement('total_fee');
			$form -> removeElement('method');
			$form -> removeElement('vat_id');
		}
		if ($deal -> photo_id > 0)
			if (!$deal -> getPhoto($deal -> photo_id)) {
				$deal -> addPhoto($deal -> photo_id);
			}

		$form -> populate(array('location_address' => $deal->address));
		$form -> populate(array('lat' => $deal->latitude));
		$form -> populate(array('long' => $deal->longitude));

		$this -> view -> album = $album = $deal -> getSingletonAlbum();
		$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();

		$paginator -> setCurrentPageNumber($this -> _getParam('page'));
		$paginator -> setItemCountPerPage(100);

		foreach ($paginator as $photo) {
			$subform = new Groupbuy_Form_Photo_Edit( array('elementsBelongTo' => $photo -> getGuid()));
			$subform -> removeElement('title');
			if ($photo -> file_id == $deal -> photo_id)
				$subform -> removeElement('delete');
			$subform -> populate($photo -> toArray());
			$form -> addSubForm($subform, $photo -> getGuid());
			$form -> cover -> addMultiOption($photo -> getIdentity(), $photo -> getIdentity());
		}
		$this -> view -> deal = $deal;
		// Populate form
		// date_default_timezone_set($viewer->timezone);
		$array = $deal -> toArray();
		$options = array();
		$options['format'] = 'Y-M-d H:m:s';
		$array['start_time'] = date('Y-m-d H:i:s', strtotime($this -> view -> locale() -> toDateTime($array['start_time'], $options)));
		$array['end_time'] = date('Y-m-d H:i:s', strtotime($this -> view -> locale() -> toDateTime($array['end_time'], $options)));
		$form -> populate($array);
		$form -> location_address -> setValue($array['address']);
		$form -> lat -> setValue($array['latitude']);
		$form -> long -> setValue($array['longitude']);
		$auth = Engine_Api::_() -> authorization() -> context;
		$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');

		foreach ($roles as $role) {
			//if( $auth->isAllowed($deal, $role, 'view') ) {
			//  $form->auth_view->setValue($role);
			// }
			if ($auth -> isAllowed($deal, $role, 'comment')) {
				$form -> auth_comment -> setValue($role);
			}
		}
		// Check post/form
		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		$post = $this -> getRequest() -> getPost();
		if (!$form -> isValid($post))
			return;
		// Process
		$values = $form -> getValues();
		$values['address'] = $values['location_address'];
		$values['latitude'] = $values['lat'];
		$values['longitude'] = $values['long'];
		if (!$values['address']) {
			$form -> getElement('location_map') -> addError('Value is required and can\'t be empty');
			return;
		}

		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();

		try {
			$deal -> setFromArray($values);
			$deal -> modified_date = date('Y-m-d H:i:s');
			$flag = 0;
			if (trim($values['title']) == "") {
				$form -> getElement('title') -> addError('Please complete this field - it is required.');
				$flag = 1;
			}
			if (trim($values['company_name']) == "") {
				$form -> getElement('company_name') -> addError('Please complete this field - it is required.');
				$flag = 1;
			}
			if (trim($values['address']) == "") {
				$form -> getElement('address') -> addError('Please complete this field - it is required.');
				$flag = 1;
			}
			if (trim($values['features']) == "") {
				$form -> getElement('features') -> addError('Please complete this field - it is required.');
				$flag = 1;
			}
			if (trim($values['fine_print']) == "") {
				$form -> getElement('fine_print') -> addError('Please complete this field - it is required.');
				$flag = 1;
			}
			if (trim($values['description']) == "") {
				$form -> getElement('description') -> addError('Please complete this field - it is required.');
				$flag = 1;
			}
			if ($values['start_time'] <= 0) {
				$form -> getElement('start_time') -> addError('Please select a date from the calendar.');
				$flag = 1;
			}
			if ($values['end_time'] <= 0) {
				$form -> getElement('end_time') -> addError('Please select a date from the calendar.');
				$flag = 1;
			}
			//check price
			if (!is_numeric($values['price']) || $values['price'] < 0) {
				$form -> getElement('price') -> addError('The price number is invalid! (Ex: 2000.25)');
				$flag = 1;
			}
			if (!is_numeric($values['value_deal']) || $values['value_deal'] < 0) {
				$form -> getElement('value_deal') -> addError('The value of deal number is invalid! (Ex: 2000.25)');
				$flag = 1;
			}
			if ($values['price'] > $values['value_deal']) {
				$form -> getElement('price') -> addError('Price should be equal or smaller than value of deal!');
				$flag = 1;
			}
			if ($values['max_sold'] != "") {
				if (!is_numeric($values['max_sold']) || $values['max_sold'] <= 0) {
					$form -> getElement('max_sold') -> addError('The maximum sold number is invalid! (Ex: 10)');
					$flag = 1;
				}
			}
			if (!is_numeric($values['min_sold']) || $values['min_sold'] <= 0) {
				$form -> getElement('min_sold') -> addError('The minimum sold number is invalid! (Ex: 1)');
				$flag = 1;
			}
			$min_sold = $values['min_sold'];
			$max_sold = $values['max_sold'];
			if ($min_sold > $max_sold) {
				$form -> getElement('max_sold') -> addError('Maximum sold should be greater than minimum sold!');
				$flag = 1;
			}
			//check start time and end time
			// $start_time = $values['start_time'];
			// $end_time =  $values['end_time'];
			$oldTz = date_default_timezone_get();
			date_default_timezone_set($viewer -> timezone);
			$start_time = strtotime($values['start_time']);
			$end_time = strtotime($values['end_time']);
			$now = strtotime(date('Y-m-d H:i:s'));
			date_default_timezone_set($oldTz);
			$deal -> start_time = date('Y-m-d H:i:s', $start_time);
			$deal -> end_time = date('Y-m-d H:i:s', $end_time);
			if ($start_time >= $end_time) {
				$form -> getElement('end_time') -> addError('End Time should be greater than Start Time!');
				$flag = 1;
			}
			//date_default_timezone_set($values['timezone']);
			//$now = date('Y-m-d H:i:s');
			if ($start_time < $now) {
				$form -> getElement('start_time') -> addError('Start Time should be equal or greater than Current Time!');
				$flag = 1;
			}
			//check image
			if (!empty($values['thumbnail'])) {
				$file = $form -> thumbnail -> getFileName();
				$info = getimagesize($file);
				if ($info[2] > 3 || $info[2] == "") {
					$form -> getElement('thumbnail') -> addError('The uploaded file is not supported or is corrupt.');
					$flag = 1;
				}
			}

			if ($flag == 1) {
				return false;
			}
			$itcurrency = $deal -> getCurrency();
			$precision = $itcurrency -> precision;
			$deal -> price = round($deal -> price, $precision);
			$deal -> value_deal = round($deal -> value_deal, $precision);
			$deal -> discount = round((1 - $deal -> price / $deal -> value_deal) * 100);
			$deal -> published = 0;
			$deal -> status = 0;

			$paymentaccount = Groupbuy_Api_Cart::getFinanceAccount($viewer -> getIdentity(), 2);
			if (!$paymentaccount['currency']) {
				Groupbuy_Api_Account::updatecurrency_account($paymentaccount['paymentaccount_id'], $deal -> currency);
			}

			//Set Fee
			$total_fee = 0;
			$display_fee = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('groupbuy.displayfee', 10);
			$freeP = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('groupbuy_deal', $viewer, 'free_display');
			if ($freeP == 0)
				$total_fee = $total_fee + $display_fee;
			$freeF = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('groupbuy_deal', $viewer, 'free_fee');
			if ($freeF == 0) {
				if ($values['featured'] == 1)
					$total_fee = $total_fee + Engine_Api::_() -> getApi('settings', 'core') -> getSetting('groupbuy.fee', 10);
			}
			$deal -> total_fee = $total_fee;
			//$deal->updateVAT();
			$table = Engine_Api::_() -> getDbtable('vats', 'groupbuy');
			$rName = $table -> info('name');
			$select = $table -> select() -> from($rName);
			$select -> where('vat_id = ?', $deal -> vat_id);
			$vatitem = $table -> fetchRow($select);
			//    $vatitem = $this -> find((int)$deal->vat_id) -> current();
			if (is_object($vatitem)) {
				$vat = $vatitem -> value;
			}
			$deal -> vat = round($vat, 2);
			$deal -> vat_value = round($deal -> price * ($vat / 100), $precision);
			$deal -> final_price = round($deal -> price + $deal -> vat_value, $precision);
			$deal -> save();
			$cover = $values['cover'];
			// Process
			foreach ($paginator as $photo) {
				$subform = $form -> getSubForm($photo -> getGuid());
				$subValues = $subform -> getValues();
				$subValues = $subValues[$photo -> getGuid()];
				unset($subValues['photo_id']);

				if (isset($cover) && $cover == $photo -> photo_id) {
					$deal -> photo_id = $photo -> file_id;
					$deal -> save();
				}

				if (isset($subValues['delete']) && $subValues['delete'] == '1') {
					if ($deal -> photo_id == $photo -> file_id) {
						$deal -> photo_id = 0;
						$deal -> save();
					}
					$photo -> delete();
				} else {
					$photo -> setFromArray($subValues);
					$photo -> save();
				}
			}
			// Save custom fields
			$customfieldform = $form -> getSubForm('fields');
			$customfieldform -> setItem($deal);
			$customfieldform -> saveValues();
			// Set photo
			if (!empty($values['thumbnail'])) {
				$deal -> setPhoto($form -> thumbnail);
			}
			// Auth

			$values['auth_view'] = 'everyone';

			if (empty($values['auth_comment'])) {
				$values['auth_comment'] = 'everyone';
			}

			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);

			foreach ($roles as $i => $role) {
				$auth -> setAllowed($deal, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($deal, $role, 'comment', ($i <= $commentMax));
			}
			/*
			 // insert new activity
			 $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($deal);
			 if( count($action->toArray()) <= 0) {
			 $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $deal, 'groupbuy_new');
			 }

			 // Rebuild privacy
			 $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
			 foreach( $actionTable->getActionsByObject($deal) as $action ) {
			 $actionTable->resetActivityBindings($action);
			 }
			 */
			// update vat at this time.
			$db -> commit();

		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
		if ($deal -> published == 10)
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage-selling'), 'groupbuy_general', true);
		else
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'publish', 'deal' => $deal -> deal_id), 'groupbuy_general', true);
	}

	public function deleteAction() {
		$deal = Engine_Api::_() -> getItem('deal', $this -> _getParam('deal'));
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($deal, $viewer, 'delete') -> isValid())
			return;
		$this -> view -> deal_id = $deal -> getIdentity();
		// This is a smoothbox by default
		if (null === $this -> _helper -> ajaxContext -> getCurrentContext())
			$this -> _helper -> layout -> setLayout('default-simple');
		else// Otherwise no layout
			$this -> _helper -> layout -> disableLayout(true);
		if (!$this -> getRequest() -> isPost())
			return;
		$db = Engine_Api::_() -> getDbtable('deals', 'groupbuy') -> getAdapter();
		$db -> beginTransaction();
		try {
			if ($this -> _getParam('admin')) {
				$params = $deal -> toArray();
				$owner = $deal -> getOwner();
				if ($owner -> getIdentity()) {
					$sendTo = $owner -> email;
					// send mail to the seller
					Engine_Api::_() -> getApi('mail', 'groupbuy') -> send($sendTo, 'groupbuy_sellerdealdel', $params);
				}

				// send mail to all buyers
				foreach ($deal->getBuyerEmails() as $buyerEmail) {
					$params['total_amount'] = $buyerEmail['total_amount'];
					$params['total_number'] = $buyerEmail['total_number'];
					Engine_Api::_() -> getApi('mail', 'groupbuy') -> send($buyerEmail['email'], 'groupbuy_buyerdealdel', $params);
				}
				$billtable = Engine_Api::_() -> getDbTable('bills', 'groupbuy');
				// if gift id is null
				$billselect = $billtable -> select() -> where('item_id=?', $deal -> deal_id);
				$billresult = $billtable -> fetchAll($billselect);
				foreach ($billresult as $billre) {
					$gift = $billre -> getGift();
					if (is_object($gift)) {
						$params['number'] = $billre -> number;
						$params['user_id'] = $billre -> user_id;
						$params['coupon_codes'] = $billre -> getCoupons(' - ');
						Engine_Api::_() -> getApi('mail', 'groupbuy') -> send($gift -> friend_email, 'groupbuy_giftunconfirm', $params);
					}
				}
				$deal -> delete();
				$db -> commit();
			} else {
				$deal -> delete();
				$db -> commit();
			}
			$this -> view -> success = true;
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Delete deal successfully.')));
		} catch (Exception $e) {
			$db -> rollback();
			$this -> view -> success = false;
			throw $e;
		}
	}

	public function manageSellingAction() {
		// Get navigation
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_main', array(), 'groupbuy_main_manage-selling');
		// Get quick navigation
		$this -> view -> quickNavigation = $quickNavigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_quick');

		$form = new Groupbuy_Form_Search();
		$form -> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage-selling', ), 'groupbuy_general', true));
		$this -> view -> form = $form;
		$post = $this -> getRequest() -> getPost();

		$form -> status -> setValue(-1);
		$form -> status -> clearMultiOptions();
		$form -> status -> addMultiOption('-2', 'All');
		$form -> status -> addMultiOption(0, 'Created');
		$form -> status -> addMultiOption(10, 'Pending');
		$form -> status -> addMultiOption(20, 'Upcoming');
		$form -> status -> addMultiOption(30, 'Running');
		$form -> status -> addMultiOption(40, 'Closed');
		$form -> status -> addMultiOption(50, 'Canceled');

		if ($post) {
			// Process form
			$form -> isValid($post);
			$values = $form -> getValues();
			$this -> view -> search = true;
		}
		$values['user_id'] = $this -> view -> viewer_id;
		if (isset($post['status'])) {
			if ($post['status'] === '' || !isset($post['status'])) {
				$values['status'] = -2;
			}
		} else
			$values['status'] = -2;

		$paginator = Engine_Api::_() -> groupbuy() -> getDealsPaginator($values);
		$this -> view -> paginator = $paginator;
		$items_per_page = Engine_Api::_() -> getApi('settings', 'core') -> groupbuy_page;
		$paginator -> setItemCountPerPage($items_per_page);
		if (isset($values['page']))
			$this -> view -> paginator = $paginator -> setCurrentPageNumber($values['page']);
		$this -> view -> canCreate = $this -> _helper -> requireAuth() -> setAuthParams('groupbuy_deal', null, 'create') -> checkRequire();
		$view = $this -> view;
		$view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
	}

	public function manageBuyingAction() {
		// Get navigation
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_main', array(), 'groupbuy_main_manage-buying');
		// Get quick navigation
		$this -> view -> quickNavigation = $quickNavigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_quick');
		$form = new Groupbuy_Form_Search();
		$form -> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage-buying', ), 'groupbuy_general', true));
		$this -> view -> form = $form;
		$form -> status -> clearMultiOptions();
		$form -> status -> setValue(-2);
		$form -> status -> addMultiOption('-2', 'All');
		$form -> status -> addMultiOption(30, 'Running');
		$form -> status -> addMultiOption(40, 'Closed');
		$form -> status -> addMultiOption(50, 'Canceled');
		$form -> removeElement('published');

		$post = $this -> getRequest() -> getPost();

		if ($post) {
			// Process form
			$form -> isValid($post);
			$values = $form -> getValues();
			$this -> view -> search = true;
		}

		$values['buyer_id'] = $this -> view -> viewer_id;
		if (@$post['status'] === '' || !isset($post['status'])) {
			$values['status'] = -2;
		}
		$values['orderby'] = 'buy_date ';
		$paginator = Engine_Api::_() -> groupbuy() -> getDealsPaginator($values);
		$this -> view -> paginator = $paginator;
		$items_per_page = Engine_Api::_() -> getApi('settings', 'core') -> groupbuy_page;
		$paginator -> setItemCountPerPage($items_per_page);
		if (isset($values['page']))
			$this -> view -> paginator = $paginator -> setCurrentPageNumber($values['page']);
		$this -> view -> canCreate = $this -> _helper -> requireAuth() -> setAuthParams('groupbuy_deal', null, 'create') -> checkRequire();
		$view = $this -> view;
		$view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
		if (isset($_SESSION['buy_succ'])) {
			if ($_SESSION['buy_succ'])
				$this -> view -> message = true;
		}
		$_SESSION['buy_succ'] = false;
	}

	public function detailAction() {
		// Check auth
		//if( !$this->_helper->requireUser()->isValid() ) return;
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		// Get navigation
		$this -> view -> viewer = $viewer;
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_main');
		$deal = Engine_Api::_() -> getItem('deal', $this -> _getParam('deal'));
		// check can rate
		if ($deal -> status == 20 && $deal -> start_time <= date('Y-m-d H:i:s')) {
			$deal -> status = 30;
			$deal -> save();
		}
		if (!$viewer -> getIdentity()) {
			$this -> view -> can_rate = $can_rate = 0;
		} else {
			$this -> view -> can_rate = $can_rate = Engine_Api::_() -> groupbuy() -> canRate($deal, $viewer -> getIdentity());
		}

		/**
		 * disable view privacy
		 */
		// if( !$this->_helper->requireAuth()->setAuthParams($deal, $viewer, 'view')->isValid()){return;}

		$this -> view -> owner = $owner = Engine_Api::_() -> getItem('user', $deal -> user_id);
		$this -> view -> viewer = $viewer;
		if (!$owner -> isSelf($viewer)) {
			$deal -> view_count++;
			$deal -> save();
		}
		$this -> view -> deal = $deal;
		if ($deal -> photo_id) {
			$this -> view -> main_photo = $deal -> getPhoto($deal -> photo_id);
		}
		if ($deal -> status == 30) {
			$the_countdown_date = $deal -> end_time;
			$date = $the_countdown_date;
			$difference = strtotime($date) - time();
			$this -> view -> difference = $difference;
		}

		// Load fields view helpers
		$view = $this -> view;
		$view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
		$this -> view -> fieldStructure = $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($deal);
		// album material
		$this -> view -> album = $album = $deal -> getSingletonAlbum();
		$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		$paginator -> setItemCountPerPage(100);
	}

	public function rateAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);

		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		$deal_id = (int)$this -> _getParam('deal_id');
		$rates = (int)$this -> _getParam('rates');

		$viewer = Engine_Api::_() -> user() -> getViewer();

		if ($rates == 0 || $deal_id == 0) {
			return;
		}
		// Check deal exist
		$deal = Engine_Api::_() -> getItem('deal', $deal_id);
		$can_rate = Engine_Api::_() -> groupbuy() -> canRate($deal, $viewer -> getIdentity());
		// Check user rated
		if (!$can_rate) {
			return;
		}
		$rateTable = Engine_Api::_() -> getDbtable('rates', 'groupbuy');
		$db = $rateTable -> getAdapter();
		$db -> beginTransaction();
		try {
			$rate = $rateTable -> createRow();
			$rate -> poster_id = $viewer -> getIdentity();
			$rate -> deal_id = $deal_id;
			$rate -> rate_number = $rates;
			$rate -> save();
			$rates = Engine_Api::_() -> groupbuy() -> getAVGrate($deal_id);
			$deal -> rates = $rates;
			$deal -> save();
			// Commit
			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
		return $this -> _redirect("group-buy/detail/deal/" . $deal_id . '#ratedeal');

	}

	public function publishAction() {
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_main', array(), 'groupbuy_main_manage-selling');
		$session_id_cart = $this -> getRequest() -> getParam('session_id');
		$deal = Engine_Api::_() -> getItem('deal', $this -> _getParam('deal'));
		if (!Engine_Api::_() -> core() -> hasSubject('deal')) {
			Engine_Api::_() -> core() -> setSubject($deal);
		}
		// Check auth
		if (!$this -> _helper -> requireSubject() -> isValid()) {
			return;
		}
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$canSell = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('groupbuy_deal', $viewer, 'can_sell_deal');
		$this -> view -> canSell = $canSell;
		if (!$this -> _helper -> requireAuth() -> setAuthParams($deal, $viewer, 'create') -> isValid())
			return;
		$_SESSION['payment_sercurity'] = Groupbuy_Api_Cart::getSecurityCode();
		
		$invoice = Groupbuy_Api_Cart::getSecurityCode();
		$account = Engine_Api::_() -> groupbuy() ->  getFinanceAccount($viewer -> getIdentity());
		
		if($account){
			$receiver = array(
				'invoice' => $invoice,
				'email' => $account -> account_username,
			);
		} else {
			$message = $this -> view -> translate('There are no account.');
            return $this -> _redirector($message);
		}
		
		$_SESSION['receiver'] = array($receiver);
		//******************IMPLEMENT INTERGRATE ADV-PAYMENT*************************
        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');

        if ((!$gatewayTable -> getEnabledGatewayCount() && !Engine_Api::_() -> hasModuleBootstrap('yncredit'))) {
            $message = $this -> view -> translate('There are no payment gateways.');
            return $this -> _redirector($message);
        }
        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'groupbuy');
		
        if ($row = $ordersTable -> getLastPendingOrder()) {
           $row -> delete();
        }
        $db = $ordersTable -> getAdapter();
        $db -> beginTransaction();
        try {
            $ordersTable -> insert(array(
            	'user_id' => $viewer -> getIdentity(), 
	            'creation_date' => new Zend_Db_Expr('NOW()'), 
	            'item_id' => $deal -> getIdentity(),
	            'price' => $deal -> total_fee, 
	            'currency' => Engine_Api::_()->groupbuy()->getDefaultCurrency(), 
				'security_code' => $_SESSION['payment_sercurity'],
				'invoice_code' => $invoice,
				'params' => array ('type' => 'publish', 'gift_id' => $gift_id)
			));
            // Commit
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }
		
		// Makebill
		$bill = Groupbuy_Api_Cart::makeBillFromCart($deal, $receiver, 0, 1, true);
        // Gateways
        $gatewaySelect = $gatewayTable -> select() -> where('enabled = ?', 1);
        $gateways = $gatewayTable -> fetchAll($gatewaySelect);

        $gatewayPlugins = array();
        foreach ($gateways as $gateway) 
        {
            $gatewayPlugins[] = array('gateway' => $gateway, 'plugin' => $gateway -> getGateway());
        }
        $this -> view -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
        $this -> view -> gateways = $gatewayPlugins;
		
		//******************END IMPLEMENT INTERGRATE ADV-PAYMENT*************************
		$info_account = Groupbuy_Api_Account::getCurrentAccount($viewer -> getIdentity());
		$requested_amount = Groupbuy_Api_Account::getTotalRequest($viewer -> getIdentity(), 1);
		$rest = $info_account['total_amount'] - $requested_amount;
		$this -> view -> current_amount = round($rest, 2);
		$this -> view -> deal = $deal;
	}
	
	public function updateOrderAction()  {
		$publish = $this -> _getParam('publish', 1);
		$gateway_id = $this -> _getParam('gateway_id', 0);
		if (!$gateway_id) {
            $message = $this -> view -> translate('Invalid gateway.');
            return $this -> _redirector($message);
        }
		
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable -> select() -> where('gateway_id = ?', $gateway_id) -> where('enabled = ?', 1);
        $gateway = $gatewayTable -> fetchRow($gatewaySelect);
        if (!$gateway) {
            $message = $this -> view -> translate('Invalid gateway.');
            return $this -> _redirector($message);
        }
			
		$ordersTable = Engine_Api::_() -> getDbTable('orders', 'groupbuy');
		if ($publish == 1) {
	        $order = $ordersTable -> getLastPendingOrder();
	        if (!$order) {
	            $message = $this -> view -> translate('Can not find order.');
	            return $this -> _redirector($message);
	        }
	        $order -> gateway_id = $gateway -> getIdentity();
	        $order -> save();
	
	        $this -> view -> status = true;
	        if (!in_array($gateway -> title, array('2Checkout', 'PayPal'))) {
	            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process-advanced', 'order_id' => $order -> getIdentity(), 'm' => 'groupbuy', 'cancel_route' => 'groupbuy_transaction', 'return_route' => 'groupbuy_transaction', ), 'ynpayment_paypackage', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
	        } else {
	            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process', 'order_id' => $order -> getIdentity(), ), 'groupbuy_transaction', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
	        }
        }
		else {
	        if ($row = $ordersTable -> getLastPendingOrder()) {
	           $row -> delete();
	        }
			$id = $this -> _getParam('id', 0);
			$deal = Engine_Api::_()->getItem('deal', $id);
			if (!$deal) {
				$message = $this -> view -> translate('Can not find deal.');
	            return $this -> _redirector($message);
			}
			
			$viewer = Engine_Api::_()->user()->getViewer();
			$invoice = Groupbuy_Api_Cart::getSecurityCode();
		
			$receiver = array(
				'invoice' => $invoice,
				'email' => '',
			);
			$gift_id = $this -> _getParam('gift_id', 0);
			if (isset($_SESSION['buygift'])) {
				unset($_SESSION['buygift']);
			}
			$quantity = $this -> _getParam('quantity', 1);
			$quantity = intval($quantity);
	        $db = $ordersTable -> getAdapter();
	        $db -> beginTransaction();
	        try {
	            $ordersTable -> insert(array(
	            	'user_id' => $viewer -> getIdentity(), 
		            'creation_date' => new Zend_Db_Expr('NOW()'), 
		            'item_id' => $deal -> getIdentity(),
		            'price' => ($deal -> final_price)*$quantity, 
		            'currency' => Engine_Api::_()->groupbuy()->getDefaultCurrency(), 
					'security_code' => $_SESSION['payment_sercurity'],
					'invoice_code' => $invoice,
					'params' => array ('type' => 'buy', 'gift_id' => $gift_id)
				));
	            // Commit
	            $db -> commit();
	        } catch (Exception $e) {
	            $db -> rollBack();
	            throw $e;
	        }
			
			// Makebill
			$bill = Groupbuy_Api_Cart::makeBillFromCart($deal, $receiver, 1, $quantity, true);
			$order = $ordersTable -> getLastPendingOrder();
			$order -> gateway_id = $gateway -> getIdentity();
	        $order -> save();
	
	        $this -> view -> status = true;
	        if (!in_array($gateway -> title, array('2Checkout', 'PayPal'))) {
	            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process-advanced', 'order_id' => $order -> getIdentity(), 'm' => 'groupbuy', 'cancel_route' => 'groupbuy_transaction', 'return_route' => 'groupbuy_transaction', ), 'ynpayment_paypackage', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
	        } else {
	            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process', 'order_id' => $order -> getIdentity(), ), 'groupbuy_transaction', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
	        }
		} 
    }

	public function makebillAction() {
		//tat di layout
		$this -> _helper -> layout -> disableLayout();
		//khong su dung view
		$receiver = $_SESSION['receiver'];
		$deal = Engine_Api::_() -> getItem('deal', $this -> _getParam('deal'));
		$bill = Groupbuy_Api_Cart::makeBillFromCart($deal, $receiver[0], 0, 1);
	}

	public function selfURL() {
		$server_array = explode("/", $_SERVER['PHP_SELF']);
		$server_array_mod = array_pop($server_array);
		if ($server_array[count($server_array) - 1] == "admin") { $server_array_mod = array_pop($server_array);
		}
		$server_info = implode("/", $server_array);
		$http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
		return $http . $_SERVER['HTTP_HOST'] . $server_info . "/";
	}

	public function stopAction() {
		$pro_id = $this -> _getParam('deal');
		$deal = Engine_Api::_() -> getItem('deal', $pro_id);
		if ($deal) {
			$deal -> stop = 1;
			$deal -> save();
			if ($this -> _getParam('admin')) {
				$owner = $deal -> getOwner();
				if ($owner -> getIdentity()) {
					$sendTo = $owner -> email;
					$params = $deal -> toArray();
					Engine_Api::_() -> getApi('mail', 'groupbuy') -> send($sendTo, 'groupbuy_stopdeal', $params);
				}
			}
		}
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Stop deal successfully.')));
	}

	public function startAction() {
		$pro_id = $this -> _getParam('deal');
		$deal = Engine_Api::_() -> getItem('deal', $pro_id);
		if ($deal) {
			$deal -> stop = 0;
			$deal -> save();
		}
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Start deal successfully.')));
	}

	public function approveAction() {
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$deal_id = $this -> _getParam('deal');
		$deal = Engine_Api::_() -> getItem('deal', $deal_id);
		if ($deal && $deal -> published < 20 && $deal -> status < 20) {
			$deal -> published = 20;
			$deal -> status = 20;
			$deal -> modified_date = date('Y-m-d H:i:s');
			$deal -> save();
			//add activity feed.
			$table = Engine_Api::_() -> getItemTable('deal');
			$db = $table -> getAdapter();
			$db -> beginTransaction();
			try {
				$user = Engine_Api::_() -> getItem('user', $deal -> user_id);
				$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($user, $deal, 'groupbuy_new');
				if ($action != null) {
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $deal);
				}
				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			$owner = $deal -> getOwner();
			if ($owner -> getIdentity()) {
				$sendTo = $owner -> email;
				$params = $deal -> toArray();
				Engine_Api::_() -> getApi('mail', 'groupbuy') -> send($sendTo, 'groupbuy_approvedeal', $params);
			}
		}
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Approve deal successfully.')));
	}

	public function denyAction() {
		$deal_id = $this -> _getParam('deal');
		$deal = Engine_Api::_() -> getItem('deal', $deal_id);
		if ($deal) {
			$deal -> published = 30;
			$deal -> stop = 1;
			$deal -> status = 50;
			$deal -> save();
			$owner = $deal -> getOwner();
			if ($owner -> getIdentity()) {
				$sendTo = $owner -> email;
				$params = $deal -> toArray();
				Engine_Api::_() -> getApi('mail', 'groupbuy') -> send($sendTo, 'groupbuy_sellerdealdel', $params);
			}
		}
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Deny deal successfully.')));
	}

	public function reopenAction() {
		$deal_id = $this -> _getParam('deal');
		$deal = Engine_Api::_() -> getItem('deal', $deal_id);
		if ($deal) {
			$deal -> published = 20;
			$deal -> stop = 0;
			$deal -> status = 20;
			$deal -> save();
			$owner = $deal -> getOwner();
			if ($owner -> getIdentity()) {
				$sendTo = $owner -> email;
				$params = $deal -> toArray();
				Engine_Api::_() -> getApi('mail', 'groupbuy') -> send($sendTo, 'groupbuy_approvedeal', $params);
			}
		}
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Re-Open deal successfully.')));
	}

	public function statisticAction() {
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_main', array(), 'groupbuy_main_manage-selling');
		$page = $this -> _getParam('page', 1);
		$this -> view -> form = $form = new Groupbuy_Form_Statistic_Search();
		$deal_id = $this -> getRequest() -> getParam('deal');
		$deal = Engine_Api::_() -> getItem('deal', $deal_id);
		$values = array();

		if ($form -> isValid($this -> _getAllParams())) {
			$values = $form -> getValues();
			if (strtotime($values['toDate']) < strtotime($values['fromDate'])) {
				$this -> view -> message = 'Date(To) should be equal or greater than Date(From)!';
			}
			if (!(isset($values['code'])) || ($values['code'] == '')) {
				$codeallow = 1;
			}
			$this -> view -> values = $values;
		}
		$values['deal_id'] = $this -> getRequest() -> getParam('deal');
		$this -> view -> deal = $deal;
		//$this->view->statistics = Engine_Api::_()->groupbuy()->getStatistics($deal_id);
		$limit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('groupbuy.transactions', 10);
		$values['limit'] = $limit;
		$this -> view -> statistics = Engine_Api::_() -> groupbuy() -> getStatistics($values, $codeallow);
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$this -> view -> statistics -> setCurrentPageNumber($page);
		$this -> view -> values = $values;
	}

	public function editcouponAction() {
		$coupon_id = $this -> _getParam('id');
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$status = $this -> _getParam('status');
		$table = Engine_Api::_() -> getDbTable('coupons', 'groupbuy');
		$rName = $table -> info('name');
		$select = $table -> select() -> from($rName);
		$select -> where('coupon_id = ?', $coupon_id);
		$result = $table -> fetchRow($select);
		$dealtable = Engine_Api::_() -> getDbTable('deals', 'groupbuy');
		$dName = $dealtable -> info('name');
		$dealselect = $dealtable -> select() -> from($dName);
		$dealselect -> where('deal_id = ?', $result -> deal_id);
		$dealresult = $dealtable -> fetchRow($dealselect);
		if ($dealresult -> user_id != $viewer -> getIdentity()) {
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('You cannot change this coupon code!')));
		} else {
			$table -> update(array('status' => $status, ), array('coupon_id = ?' => $coupon_id, ));
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('Coupon Code Status Changed Successfully!')));
		}

	}

	public function transactionAction() {
		$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_main', array(), 'groupbuy_main_account');
		$limit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('groupbuy.transactions', 10);
		$params = array_merge($this -> _paginate_params, array('user_id' => $user_id, 'limit' => $limit));
		$this -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$this -> view -> history = Groupbuy_Api_Cart::getTrackingTransaction($params);
		/*       $page = $this->_getParam('page',1);
		 $this->view->form = $form = new Groupbuy_Form_Statistic_Search();
		 $deal_id = $this->getRequest()->getParam('deal');
		 $deal = Engine_Api::_()->getItem('deal', $deal_id);
		 $values = array();

		 if ($form->isValid($this->_getAllParams())) {
		 $values = $form->getValues();
		 if (!(isset($values['code'])) || ($values['code'] == ''))
		 {
		 $codeallow = 1;
		 }
		 $this->view->values = $values;
		 }
		 $values['deal_id'] = $this->getRequest()->getParam('deal');
		 $this->view->deal = $deal;
		 //$this->view->statistics = Engine_Api::_()->groupbuy()->getStatistics($deal_id);
		 $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.transactions', 10);
		 $values['limit'] = $limit;
		 $this->view->statistics = Engine_Api::_()->groupbuy()->getStatistics($values, $codeallow);
		 $this->view->viewer_id =  Engine_Api::_()->user()->getViewer()->getIdentity();
		 $this->view->statistics->setCurrentPageNumber($page);
		 $this->view->values = $values;*/
	}

	public function viewTransactionAction() {
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		//tat di layout
		$this -> _helper -> layout -> disableLayout();
		$user_id = $this -> getRequest() -> getParam('id');
		$user_name = $this -> getRequest() -> getParam('username');
		$this -> view -> user_name = $user_name;
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$params = array_merge($this -> _paginate_params, array('user_id' => $user_id));
		$this -> view -> viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> history = $his = Groupbuy_Api_Cart::getTrackingTransaction($params);
		$his -> setItemCountPerPage(1000000000000);
	}

	public function publishFreeAction() {
		$deal_id = $this -> _getParam('deal');
		$deal = Engine_Api::_() -> getItem('deal', $deal_id);
		$auto = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('groupbuy.approveAuto', 0);
		if ($deal) {
			if ($auto > 0) {
				Engine_Api::_() -> groupbuy() -> approveDeal($deal_id);
			} else {
				$deal -> published = 10;
				$deal -> status = 10;
				$deal -> save();
			}
		}
		// Redirect

		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage-selling'), 'groupbuy_general', true);
	}

	public function publishmoneyAction() {
		$receiver = array (
			'email' => '',
			'invoice' => Groupbuy_Api_Cart::getSecurityCode()
		);
		$deal_id = $this -> _getParam('deal');
		$deal = Engine_Api::_() -> getItem('deal', $deal_id);
		//create bill
		$bill = Groupbuy_Api_Cart::makeBillFromCart($deal, $receiver, 0, 1, true);
		//update status bill
		$bill -> bill_status = 1;
		$bill -> save();

		$viewer = Engine_Api::_() -> user() -> getViewer();

		$paymentaccount = Groupbuy_Api_Cart::getFinanceAccount($viewer -> getIdentity(), 2);
		Groupbuy_Api_Account::updateAmount($paymentaccount['paymentaccount_id'], $bill -> amount, 2);

		$paymentaccount = Groupbuy_Api_Cart::getFinanceAccount($bill -> owner_id, 1);
		Groupbuy_Api_Account::updateAmount($paymentaccount['paymentaccount_id'], $bill -> amount, 1);

		$auto = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('groupbuy.approveAuto', 0);
		if ($deal) {
			if ($auto > 0) {
				Engine_Api::_() -> groupbuy() -> approveDeal($deal_id);
			} else {
				$deal -> published = 10;
				$deal -> status = 10;
				$deal -> save();
			}
		}
		//Save transaction tracking
		$tttable = Engine_Api::_() -> getDbtable('transactionTrackings', 'groupbuy');
		$ttdb = $tttable -> getAdapter();
		$ttdb -> beginTransaction();
		try {
			$ttvalues = array('transaction_date' => date('Y-m-d H:i:s'), 'user_seller' => $deal -> user_id, 'user_buyer' => $viewer -> getIdentity(), 'item_id' => $deal_id, 'commission_fee' => $bill -> commission_fee, 'currency' => $bill -> currency, 'amount' => $bill -> amount, 'account_seller_id' => Groupbuy_Api_Cart::getFinanceAccount($bill -> owner_id, 1), 'account_buyer_id' => Groupbuy_Api_Cart::getFinanceAccount($viewer -> getIdentity(), 2), 'number' => 1, 'transaction_status' => '1', 'params' => 'Pay publishing fee by virtual money', );
			$ttrow = $tttable -> createRow();
			$ttrow -> setFromArray($ttvalues);
			$ttrow -> save();
			$tranid = $ttrow -> transactiontracking_id;
			$ttdb -> commit();
			/**
			 * Call Event from Affiliate
			 */
			$module = 'ynaffiliate';
			$modulesTable = Engine_Api::_() -> getDbtable('modules', 'core');
			$mselect = $modulesTable -> select() -> where('enabled = ?', 1) -> where('name  = ?', $module);
			$module_result = $modulesTable -> fetchRow($mselect);
			if (count($module_result) > 0) {
				$deal = Engine_Api::_() -> getItem('deal', $item);
				$params['module'] = 'groupbuy';
				$params['user_id'] = $viewer -> getIdentity();
				$params['rule_name'] = 'publish_deal';
				$params['currency'] = $bill -> currency;
				$params['total_amount'] = number_format($bill -> amount, 2);
				Engine_Hooks_Dispatcher::getInstance() -> callEvent('onPaymentAfter', $params);
			}

			/**
			 * End Call Event from Affiliate
			 */
		} catch (exception $e) {
			$ttdb -> rollBack();
			throw $e;
		}
		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage-selling'), 'groupbuy_general', true);
	}

	public function deleteBuyAction() {
		$item = Engine_Api::_() -> getItem('groupbuy_buy_deal', $this -> _getParam('item'));
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$this -> view -> item_id = $item -> getIdentity();
		// This is a smoothbox by default
		if (null === $this -> _helper -> ajaxContext -> getCurrentContext())
			$this -> _helper -> layout -> setLayout('default-simple');
		else// Otherwise no layout
			$this -> _helper -> layout -> disableLayout(true);
		if (!$this -> getRequest() -> isPost())
			return;
		$db = Engine_Api::_() -> getDbtable('buyDeals', 'groupbuy') -> getAdapter();
		$db -> beginTransaction();
		try {
			$owner_id = $item -> owner_id;
			$item_id = $item -> item_id;
			$params = $item -> toArray();
			$item -> delete();
			$deal = Engine_Api::_() -> getItem('deal', $item_id);
			$deal -> current_sold = $deal -> current_sold - $params['number'];
			$deal -> save();
			$db -> commit();
			$seller = Engine_Api::_() -> getItem('user', $owner_id);
			if ($seller) {
				$sendTo = $seller -> email;
				Engine_Api::_() -> getApi('mail', 'groupbuy') -> send($sendTo, 'groupbuy_deletebuyToSeller', $params);
			}

			$sendTo = $viewer -> email;
			Engine_Api::_() -> getApi('mail', 'groupbuy') -> send($sendTo, 'groupbuy_deletebuyToBuyer', $params);
			$this -> view -> success = true;
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Delete item successfully.')));
		} catch (Exception $e) {
			$db -> rollback();
			$this -> view -> success = false;
			throw $e;
		}
	}

	/**
	 *
	 */
	public function buydealsAction() {
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_main', array(), 'groupbuy_main_manage-buying');
		$canBuy = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('groupbuy_deal', $viewer, 'can_buy_deal');
		$this -> view -> canBuy = $canBuy;
		$session_id_cart = $this -> getRequest() -> getParam('session_id');
		$deal = Engine_Api::_() -> getItem('deal', $this -> _getParam('deal'));
		$this -> view -> maxBought = $maxBought = $deal -> getMaxBought($viewer);

		if (!Engine_Api::_() -> core() -> hasSubject('deal')) {
			Engine_Api::_() -> core() -> setSubject($deal);
		}
		// Check auth
		if (!$this -> _helper -> requireSubject() -> isValid()) {
			return;
		}
		$this -> view -> deal = $deal;
	}

	/**
	 * XXX: update canBuy status with more prevent.
	 */
	public function buyDealAction() {

		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_main', array(), 'groupbuy_main_manage-buying');
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$this -> view -> viewer = $viewer;
		$canBuy = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('groupbuy_deal', $viewer, 'can_buy_deal');
		$this -> view -> canBuy = $canBuy;
		$session_id_cart = $this -> getRequest() -> getParam('session_id');
		$deal = Engine_Api::_() -> getItem('deal', $this -> _getParam('deal'));
		if (!Engine_Api::_() -> core() -> hasSubject('deal')) {
			Engine_Api::_() -> core() -> setSubject($deal);
		}
		// Check auth
		if (!$this -> _helper -> requireSubject() -> isValid()) {
			return;
		}
		if ($this -> _getParam('buyff') && $this -> _getParam('buyff') == 1 && $deal -> status == 30 && $deal -> published == 20) {
			if ($deal -> method != 2) {
				$this -> view -> buyff = 1;
			} else {
				$this -> view -> buyff = 2;
			}
		} else {
			$this -> view -> buyff = 0;
		}
		if (isset($_SESSION['buygift']) && ($_SESSION['buygift']['deal'] != $deal -> deal_id)) {
			unset($_SESSION['buygift']);
		}
		$_SESSION['payment_sercurity'] = Groupbuy_Api_Cart::getSecurityCode();

		$gift_id = 0;
		if (isset($_SESSION['buygift']) && is_array($_SESSION['buygift'])) {
			$gift_id = @$_SESSION['buygift']['gift_id'];
		}
		$this->view->gift_id = $gift_id;
		
		$invoice = Groupbuy_Api_Cart::getSecurityCode();
		$receiver = array(
			'invoice' => $invoice,
			'email' => '',
		);
		$_SESSION['receiver'] = array($receiver);
		//******************IMPLEMENT INTERGRATE ADV-PAYMENT*************************
        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');

        if ((!$gatewayTable -> getEnabledGatewayCount() && !Engine_Api::_() -> hasModuleBootstrap('yncredit'))) {
            $message = $this -> view -> translate('There are no payment gateways.');
            return $this -> _redirector($message);
        }
		
        // Gateways
        $gatewaySelect = $gatewayTable -> select() -> where('enabled = ?', 1);
        $gateways = $gatewayTable -> fetchAll($gatewaySelect);

        $gatewayPlugins = array();
        foreach ($gateways as $gateway) 
        {
            $gatewayPlugins[] = array('gateway' => $gateway, 'plugin' => $gateway -> getGateway());
        }
        $this -> view -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
        $this -> view -> gateways = $gatewayPlugins;
		
		//******************END IMPLEMENT INTERGRATE ADV-PAYMENT*************************

		$this -> view -> deal = $deal;
		$this -> view -> method = $method_payment;
		$this -> view -> sercurity = $_SESSION['payment_sercurity'];
		$info_account = Groupbuy_Api_Account::getCurrentAccount($viewer -> getIdentity());
		$requested_amount = Groupbuy_Api_Account::getTotalRequest($viewer -> getIdentity(), 1);
		$rest = $info_account['total_amount'] - $requested_amount;
		$this -> view -> current_amount = round($rest, 2);
		$this -> view -> currency = $info_account['currency'];
	}

	public function makebillBuyAction() {
		//tat di layout
		$this -> _helper -> layout -> disableLayout();
		//khong su dung view
		$receiver = $_SESSION['receiver'];
		$deal = Engine_Api::_() -> getItem('deal', $this -> _getParam('deal'));
		$numbers = $this -> _getParam('numbers');
		$gateway_name = $this -> _getParam('payment');
		$receiver1 = Groupbuy_Api_Cart::getReceivers($gateway_name);
		if (isset($_SESSION['buygift'])) {
			unset($_SESSION['buygift']);
		}
		$receiver1[0]['invoice'] = $receiver[0]['invoice'];
		$bill = Groupbuy_Api_Cart::makeBillFromCart($deal, $receiver1[0], 1, $numbers);
	}

	public function makerequestAction() {
		//tat di layout
		$this -> _helper -> layout -> disableLayout();
		$message = $this -> getRequest() -> getParam('message');
		$request_id = $this -> getRequest() -> getParam('request_id');
		$_SESSION['request_id'] = $request_id;
		$_SESSION['message'] = $message;
	}

	public function requestRefundAction() {
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$buy = Engine_Api::_() -> getItem('groupbuy_buy_deal', $this -> _getParam('item'));
		$this -> view -> total_price = $buy -> amount;
		$this -> view -> buy_id = $buy -> buydeal_id;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$account = Groupbuy_Api_Cart::getFinanceAccount($viewer -> getIdentity());
		if ($account['account_username'] == '')
			$this -> view -> canRefund = false;
		else
			$this -> view -> canRefund = true;
	}

	public function refundAction() {
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		//tat di layout
		$this -> _helper -> layout -> disableLayout();
		//khong su dung view
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$current_money = $this -> getRequest() -> getParam('currentmoney');
		$info_account = Groupbuy_Api_Account::getCurrentAccount(Engine_Api::_() -> user() -> getViewer() -> getIdentity());
		$vals = array();
		$vals['request_user_id'] = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$vals['request_amount'] = round($current_money, 2);
		$vals['request_date'] = date('Y-m-d H:i:s');
		$vals['request_reason'] = strip_tags($this -> getRequest() -> getParam('reason'));
		$vals['request_status'] = 0;
		$vals['request_type'] = 2;
		$vals['dealbuy_id'] = $this -> getRequest() -> getParam('buy_id');
		$vals['request_payment_acount_id'] = $info_account['paymentaccount_id'];
		$request_id = Groupbuy_Api_Account::insertRequest($vals);
		$buy = Engine_Api::_() -> getItem('groupbuy_buy_deal', $this -> getRequest() -> getParam('buy_id'));
		$buy -> status = -1;
		$buy -> save();
		$html = "<h2>Request successfully!<h2>";
		echo '{"html":"' . $html . '"}';
	}

	public function buygiftAction() {
		$this -> _helper -> layout -> setLayout('admin-simple');
		$this -> view -> form = $form = new Groupbuy_Form_Gift();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (($this -> _getParam('method')) && $this -> _getParam('method') == 2) {
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'buy-deal', 'deal' => $deal_id, ), 'groupbuy_general'), 'format' => 'smoothbox', 'messages' => array("This deal does not support Buy For Friend!")));
		} else {
			if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
				$values = $form -> getValues();
				$table = Engine_Api::_() -> getDbtable('gifts', 'groupbuy');
				$db = $table -> getAdapter();
				$db -> beginTransaction();
				try {
					// Create groupbuy
					$values = array_merge($form -> getValues(), array('user_id' => $viewer -> getIdentity(), 'creation_date' => date('Y-m-d H:i:s'), ));
					$gift = $table -> createRow();
					$gift -> setFromArray($values);
					$gift -> save();
					$db -> commit();
				} catch (exception $e) {
					$db -> rollBack();
					if (APPLICATION_ENV == 'development') {
						throw $e;
					}
				}
				$values['gift_id'] = $gift -> gift_id;
				$values['buygift'] = 1;
				$values['deal'] = $this -> _getParam('deal_id');
				$values['numberbuy'] = $this -> _getParam('numberbuy');
				$_SESSION['buygift'] = $values;
				$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
			}
		}
		// Output
		$this -> renderScript('buygift/form.tpl');

	}

	public function editgiftAction() {
		$this -> _helper -> layout -> setLayout('admin-simple');
		$this -> view -> form = $form = new Groupbuy_Form_Gift();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
		$form -> populate($_SESSION['buygift']);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			$values = $form -> getValues();
			$values['buygift'] = 1;
			$values['deal'] = $this -> _getParam('deal_id');
			$values = array_merge($form -> getValues(), array('user_id' => $viewer -> getIdentity(), 'modified_date' => date('Y-m-d H:i:s'), ));
			$gift_id = $_SESSION['buygift']['gift_id'];

			$table = Engine_Api::_() -> getDbtable('gifts', 'groupbuy');
			$select = $table -> select() -> where('gift_id = ?', $gift_id);
			$gift = $table -> fetchRow($select);
			//$gift = $gifts['0'];
			$db = $table -> getAdapter();
			$db -> beginTransaction();
			try {
				// Create groupbuy
				$gift -> setFromArray($values);
				$gift -> save();
				$db -> commit();
			} catch (exception $e) {
				$db -> rollBack();
				throw $e;
			}
			$values['gift_id'] = $gift_id;
			$values['buygift'] = 1;
			$_SESSION['buygift'] = $values;
			$_SESSION['buygift']['numberbuy'] = $this -> _getParam('numberbuy');
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}
		// Output
		$this -> renderScript('buygift/form.tpl');
	}

	public function deletegiftAction() {
		$gift_id = $_SESSION['buygift']['gift_id'];
		$deal_id = $_SESSION['buygift']['deal'];
		if ($gift_id) {
			$table = Engine_Api::_() -> getDbtable('gifts', 'groupbuy');
			$select = $table -> select() -> where('gift_id = ?', $gift_id);
			$gift = @$table -> fetchRow($select);
			$gift -> delete();
		}
		if (isset($_SESSION['buygift'])) {
			unset($_SESSION['buygift']);
		}

		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'buy-deal', 'deal' => $deal_id, ), 'groupbuy_general'), 'format' => 'smoothbox', 'messages' => array('Cancel successfully.')));
	}

	public function adminEditAction() {
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		if (!$this -> _helper -> requireUser() -> isValid() || $viewer -> level_id > 2)
			return;
		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_main', array(), 'groupbuy_main_managedeal');
		$deal = Engine_Api::_() -> getItem('deal', $this -> _getParam('deal'));
		$category = Engine_Api::_() -> getItem('groupbuy_category', $deal -> category_id);
		if (!Engine_Api::_() -> core() -> hasSubject('deal')) {
			Engine_Api::_() -> core() -> setSubject($deal);
		}
		// Check auth
		if (!$this -> _helper -> requireSubject() -> isValid()) {
			return;
		}
		if (!$this -> _helper -> requireAuth() -> setAuthParams($deal, $viewer, 'edit') -> isValid())
			return;
		// Prepare form
		$this -> view -> form = $form = new Groupbuy_Form_Edit( array('item' => $deal));
		$form -> removeElement('thumbnail');
		//if($deal->published >= 10)
		//{
		$form -> removeElement('feep');
		$form -> removeElement('featured');
		$form -> removeElement('total_fee');

		//$form->removeElement('vat_id');
		//}
		if ($deal -> published >= 20) {
			$form -> removeElement('method');
		}
		if ($deal -> photo_id > 0)
			if (!$deal -> getPhoto($deal -> photo_id)) {
				$deal -> addPhoto($deal -> photo_id);
			}

		$this -> view -> album = $album = $deal -> getSingletonAlbum();
		$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();

		$paginator -> setCurrentPageNumber($this -> _getParam('page'));
		$paginator -> setItemCountPerPage(100);

		foreach ($paginator as $photo) {
			$subform = new Groupbuy_Form_Photo_Edit( array('elementsBelongTo' => $photo -> getGuid()));
			$subform -> removeElement('title');
			if ($photo -> file_id == $deal -> photo_id)
				$subform -> removeElement('delete');
			$subform -> populate($photo -> toArray());
			$form -> addSubForm($subform, $photo -> getGuid());
			$form -> cover -> addMultiOption($photo -> getIdentity(), $photo -> getIdentity());
		}
		$this -> view -> deal = $deal;
		// Populate form
		// date_default_timezone_set($viewer->timezone);
		$array = $deal -> toArray();
		$options = array();
		$options['format'] = 'Y-M-d H:m:s';
		$array['start_time'] = date('Y-m-d H:i:s', strtotime($this -> view -> locale() -> toDateTime($array['start_time'], $options)));
		$array['end_time'] = date('Y-m-d H:i:s', strtotime($this -> view -> locale() -> toDateTime($array['end_time'], $options)));
		$form -> populate($array);
		$auth = Engine_Api::_() -> authorization() -> context;
		$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');

		foreach ($roles as $role) {
			//if( $auth->isAllowed($deal, $role, 'view') ) {
			//  $form->auth_view->setValue($role);
			// }
			if ($auth -> isAllowed($deal, $role, 'comment')) {
				$form -> auth_comment -> setValue($role);
			}
		}
		// Check post/form
		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		$post = $this -> getRequest() -> getPost();
		if (!$form -> isValid($post))
			return;
		// Process
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();

		try {
			$values = $form -> getValues();
			$deal -> setFromArray($values);
			$deal -> modified_date = date('Y-m-d H:i:s');
			$flag = 0;
			if (trim($values['title']) == "") {
				$form -> getElement('title') -> addError('Please complete this field - it is required.');
				$flag = 1;
			}
			if (trim($values['company_name']) == "") {
				$form -> getElement('company_name') -> addError('Please complete this field - it is required.');
				$flag = 1;
			}
			if (trim($values['address']) == "") {
				$form -> getElement('address') -> addError('Please complete this field - it is required.');
				$flag = 1;
			}
			if (trim($values['features']) == "") {
				$form -> getElement('features') -> addError('Please complete this field - it is required.');
				$flag = 1;
			}
			if (trim($values['fine_print']) == "") {
				$form -> getElement('fine_print') -> addError('Please complete this field - it is required.');
				$flag = 1;
			}
			if (trim($values['description']) == "") {
				$form -> getElement('description') -> addError('Please complete this field - it is required.');
				$flag = 1;
			}
			if ($values['start_time'] <= 0) {
				$form -> getElement('start_time') -> addError('Please select a date from the calendar.');
				$flag = 1;
			}
			if ($values['end_time'] <= 0) {
				$form -> getElement('end_time') -> addError('Please select a date from the calendar.');
				$flag = 1;
			}
			//check price
			if (!is_numeric($values['price']) || $values['price'] < 0) {
				$form -> getElement('price') -> addError('The price number is invalid! (Ex: 2000.25)');
				$flag = 1;
			}
			if (!is_numeric($values['value_deal']) || $values['value_deal'] < 0) {
				$form -> getElement('value_deal') -> addError('The value of deal number is invalid! (Ex: 2000.25)');
				$flag = 1;
			}
			if ($values['price'] > $values['value_deal']) {
				$form -> getElement('price') -> addError('Price should be equal or smaller than value of deal!');
				$flag = 1;
			}
			if ($values['max_sold'] != "") {
				if (!is_numeric($values['max_sold']) || $values['max_sold'] <= 0) {
					$form -> getElement('max_sold') -> addError('The maximum sold number is invalid! (Ex: 10)');
					$flag = 1;
				}
			}
			if (!is_numeric($values['min_sold']) || $values['min_sold'] <= 0) {
				$form -> getElement('min_sold') -> addError('The minimum sold number is invalid! (Ex: 1)');
				$flag = 1;
			}
			$min_sold = $values['min_sold'];
			$max_sold = $values['max_sold'];
			if ($min_sold > $max_sold) {
				$form -> getElement('max_sold') -> addError('Maximum sold should be greater than minimum sold!');
				$flag = 1;
			}
			//check start time and end time
			$oldTz = date_default_timezone_get();
			date_default_timezone_set($viewer -> timezone);
			$start_time = strtotime($values['start_time']);
			$end_time = strtotime($values['end_time']);
			$now = strtotime(date('Y-m-d H:i:s'));
			date_default_timezone_set($oldTz);
			$deal -> start_time = date('Y-m-d H:i:s', $start_time);
			$deal -> end_time = date('Y-m-d H:i:s', $end_time);
			if ($start_time >= $end_time) {
				$form -> getElement('end_time') -> addError('End Time should be greater than Start Time!');
				$flag = 1;
			}
			if ($deal -> status < 30) {
				if ($start_time < $now) {
					$form -> getElement('start_time') -> addError('Start Time should be equal or greater than Current Time!');
					$flag = 1;
				}
			}
			//check image
			if (!empty($values['thumbnail'])) {
				$file = $form -> thumbnail -> getFileName();
				$info = getimagesize($file);
				if ($info[2] > 3 || $info[2] == "") {
					$form -> getElement('thumbnail') -> addError('The uploaded file is not supported or is corrupt.');
					$flag = 1;
				}
			}

			if ($flag == 1) {
				return false;
			}
			$deal -> price = round($deal -> price, 2);
			$deal -> value_deal = round($deal -> value_deal, 2);
			$deal -> discount = round((1 - $deal -> price / $deal -> value_deal) * 100);
			//Set Fee
			$total_fee = 0;
			$display_fee = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('groupbuy.displayfee', 10);
			$freeP = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('groupbuy_deal', $viewer, 'free_display');
			if ($freeP == 0)
				$total_fee = $total_fee + $display_fee;
			$freeF = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('groupbuy_deal', $viewer, 'free_fee');
			if ($freeF == 0) {
				if ($values['featured'] == 1)
					$total_fee = $total_fee + Engine_Api::_() -> getApi('settings', 'core') -> getSetting('groupbuy.fee', 10);
			}
			$deal -> total_fee = $total_fee;
			//$deal->updateVAT();
			$table = Engine_Api::_() -> getDbtable('vats', 'groupbuy');
			$rName = $table -> info('name');
			$select = $table -> select() -> from($rName);
			$select -> where('vat_id = ?', $deal -> vat_id);
			$vatitem = $table -> fetchRow($select);
			//$vatitem = $this -> find((int)$deal->vat_id) -> current();
			if (is_object($vatitem)) {
				$vat = $vatitem -> value;
			}
			$deal -> vat = round($vat, 2);
			$deal -> vat_value = round($deal -> price * ($vat / 100), 2);
			$deal -> final_price = round($deal -> price + $deal -> vat_value, 2);
			$deal -> save();
			$cover = $values['cover'];
			// Process
			foreach ($paginator as $photo) {
				$subform = $form -> getSubForm($photo -> getGuid());
				$subValues = $subform -> getValues();
				$subValues = $subValues[$photo -> getGuid()];
				unset($subValues['photo_id']);

				if (isset($cover) && $cover == $photo -> photo_id) {
					$deal -> photo_id = $photo -> file_id;
					$deal -> save();
				}

				if (isset($subValues['delete']) && $subValues['delete'] == '1') {
					if ($deal -> photo_id == $photo -> file_id) {
						$deal -> photo_id = 0;
						$deal -> save();
					}
					$photo -> delete();
				} else {
					$photo -> setFromArray($subValues);
					$photo -> save();
				}
			}
			// Save custom fields
			$customfieldform = $form -> getSubForm('fields');
			$customfieldform -> setItem($deal);
			$customfieldform -> saveValues();
			// Set photo
			if (!empty($values['thumbnail'])) {
				$deal -> setPhoto($form -> thumbnail);
			}
			// Auth
			//if( empty($values['auth_view']) ) {
			///   $values['auth_view'] = 'everyone';
			//}

			if (empty($values['auth_comment'])) {
				$values['auth_comment'] = 'everyone';
			}

			//$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);

			foreach ($roles as $i => $role) {
				//$auth->setAllowed($deal, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($deal, $role, 'comment', ($i <= $commentMax));
			}
			// update vat at this time.

			$db -> commit();

		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
		return $this -> _helper -> redirector -> gotoRoute(array('module' => 'groupbuy', 'controller' => 'manage', 'action' => 'index'), 'admin_default', true);
	}

	public function updateAction() {
		$id = $this -> _getParam('id');
		$status = $this -> _getParam('status');
		$message = $this -> _getParam('message');
		$total_amount = $this -> _getParam('total_amount');
		Groupbuy_Api_Cart::updatePaymentRequest($id, $message, $status);
		if ($status == 1)
			Groupbuy_Api_Cart::updateTotalAmount($id, $total_amount);
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('Your Changed Successfully!')));
	}
	
	protected function _redirector($message = null) {
		if(empty($message))
		{
			$message = Zend_Registry::get('Zend_Translate') -> _('Error!');
		}
		$this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'groupbuy_general', true), 'messages' => array($message)));
	}
}

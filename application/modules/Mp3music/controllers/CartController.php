<?php
class Mp3music_CartController extends Core_Controller_Action_Standard
{
	protected $_paginate_params = array();
	public function init()
	{
		$this -> view -> navigation = $this -> getNavigation();
		$this -> _paginate_params['page'] = $this -> getRequest() -> getParam('page', 1);
		$this -> _paginate_params['limit'] = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('mp3music.songsPerPage', 10);
	}

	public function indexAction()
	{
	    $this -> _helper -> content
        // ->    setNoRender()
        -> setEnabled();
        
		$req3 = $this -> getRequest() -> getParam('req3');
		$req4 = $this -> getRequest() -> getParam('req4');
		$req5 = $this -> getRequest() -> getParam('req5');
		$current_browser = false;

		//callback URL

		//END
		$bill = Mp3music_Api_Shop::getBill($req4, $req5);
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$pay = $this -> getRequest() -> getParam('pay');

		if (isset($_SESSION['payment_sercurity']) && $_SESSION['payment_sercurity'] != "" && isset($pay['task']) && isset($pay['sercurity']) && $pay['task'] == 'checkout' && $pay['sercurity'] == $_SESSION['payment_sercurity'])
		{
			$gateway_name = $pay['gateway'];
			$gateway = Mp3music_Api_Cart::loadGateWay($gateway_name);
			$settings = Mp3music_Api_Cart::getSettingsGateWay($gateway_name);
			$gateway -> set($settings);
			$params = array_merge(array(
				'pstatus' => "success",
				'req4' => $_SESSION['payment_sercurity']
			), $params);
			$returnUrl = Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, 'mp3music_cart', true);
			$params = array_merge(array(
				'pstatus' => "cancel",
				'req4' => $_SESSION['payment_sercurity']
			), $params);
			$cancelUrl = Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, 'mp3music_cart', true);
			$notifyUrl = $this -> selfURL() . 'application/modules/Mp3music/externals/scripts/callback.php?action=callback&req4=' . $_SESSION['payment_sercurity'] . '&req5=';
			$method_payment = 'directly';

			list($receiver, $paramsPay) = Mp3music_Api_Cart::getParamsPay($gateway_name, $returnUrl, $cancelUrl, $method_payment, $notifyUrl);
			$carts = Mp3music_Api_Shop::getCart();
			$bill = Mp3music_Api_Shop::makeBillFromCart($carts, $receiver);
			$settings = Mp3music_Api_Cart::getSettingsSelling(Engine_Api::_() -> user() -> getViewer() -> level_id);
			$fee = "EACHRECEIVER";
			switch($settings['who_who_payment'])
			{
				case 1 :
					$fee = "PRIMARYRECEIVER";
					break;
				case 2 :
					$fee = "SENDER";
					break;
				case 3 :
					$fee = "EACHRECEIVER";
					break;
				default :
					$fee = "EACHRECEIVER";
					break;
			}
			$paramsPay['feesPayer'] = $fee;

			if ($bill > 0)
			{

				$res = $gateway -> checkOut($paramsPay);
			}
			else
			{
				//$this->url()->send('musicsharing.cart.index','','This is some problems when create a bill . Please contact to admin to
				// get more information.');
			}
			$this -> view -> moveitem = 1;
		}

		if ($req3 == 'success' && $req4 == $_SESSION['payment_sercurity'] && isset($_SESSION['payment_sercurity']) && $_SESSION['payment_sercurity'] != "")
		{
			if (Engine_Api::_() -> user() -> getViewer() -> getIdentity() == Mp3music_Api_Shop::getCurrentUserCart() && $bill != null && $bill[0]['bill_status'] == 0)
			{
				//transaction success.
				//move item from cart to downloadlist
				$cartitem = Mp3music_Api_Shop::getCartItems();
				Mp3music_Api_Cart::moveItems2DownloadList($cartitem);

				//update status of bill
				Mp3music_Api_Shop::updateBillStatus($bill, 1);
				$type = 'bill';
				$date = date('Y-m-d', $bill[0]['date_bill']);

				$arrtoDate = explode('-', $date);
				$timestamp = mktime(12, 0, 0, $arrtoDate[1], $arrtoDate[2], $arrtoDate[0]);
				$bill[0]['bill_status'] = 1;
				//saveTracking
				Mp3music_Api_Cart::saveTrackingPayIn($bill, $type);
				//save to history

				//Mp3music_Api_Cart::updateHistories($bill,$type,$timestamp);
				//pay for owner of item
				$pta = array();
				$totsl = 0;
				foreach ($cartitem as $itc)
				{
					if (isset($pta[$itc['owner_id']]))
					{
						$pta[$itc['owner_id']] = $pta[$itc['owner_id']] + $itc['amount'];
					}
					else
					{
						$pta[$itc['owner_id']] = $itc['amount'];
					}
					$totsl += $itc['amount'];
				}
				foreach ($pta as $key => $value)
				{
					$user_group_id = Mp3music_Api_Cart::getGroupUser($key);
					$settings = Mp3music_Api_Cart::getSettingsSelling($user_group_id);
					if (!isset($settings['comission_fee']))
					{
						$fee = 0;
					}
					else
					{
						$fee = $settings['comission_fee'];
					}

					$coupon = $_SESSION['musicsharing_cart']['coupon_code']['value'];
					$coupon = $coupon / $totsl;

					$coupon = round($coupon, 2);
					$val = ($value - $coupon * $value);
					$fee = $fee * $val / 100;
					$fee = round($fee, 2);
					$valuer = $val - $fee;
					Mp3music_Api_Cart::updateTotalAmount($key, $valuer, false);
				}
				// Affiliate integration
				$module = 'ynaffiliate';
				$modulesTable = Engine_Api::_() -> getDbtable('modules', 'core');
				$mselect = $modulesTable -> select() -> where('enabled = ?', 1) -> where('name  = ?', $module);
				$module_result = $modulesTable -> fetchRow($mselect);
				if (count($module_result) > 0)
				{

					$affi_params = array();
					$affi_params['module'] = 'mp3music';
					$affi_params['user_id'] = $bill[0]['user_id'];
					$affi_params['rule_name'] = 'buy_mp3music';
					$affi_params['total_amount'] = $totsl;
					$affi_params['currency'] = 'USD';
					Engine_Hooks_Dispatcher::getInstance() -> callEvent('onPaymentAfter', $affi_params);
				}

				//send money to admin
				$admin = Mp3music_Api_Cart::getFinanceAccount(null, 1);
				//Mp3music_Api_Cart::updateTotalAmount($admin['user_id'],$totsl,false)   ;
				Mp3music_Api_Cart::sendNotifycation('admin', $admin['user_id'], $cartitem);
				//send notification for owner
				foreach ($cartitem as $itc)
				{
					Mp3music_Api_Cart::sendNotifycation($itc['type'], $itc['owner_id'], $itc);
				}
				//clear cart.
				Mp3music_Api_Shop::clearCart();
				$current_browser = true;
			}
			$this -> view -> moveitem = 1;

		}

		if ($req3 == 'success' && $bill != null && $current_browser == false && $bill[0]['bill_status'] == 0)
		{
			if (Engine_Api::_() -> user() -> getViewer() -> getIdentity() == $bill[0]['user_id'])
			{

				$cartitem = unserialize($bill['params']);

				Mp3music_Api_Cart::moveItems2DownloadList($cartitem['items']);
				//update status of bill

				Mp3music_Api_Shop::updateBillStatus($bill, 1);
				//save to history
				$type = 'bill';
				$date = date('Y-m-d');
				//$timestamp = strtotime($date);
				$arrtoDate = explode('-', $date);
				$timestamp = mktime(12, 0, 0, $arrtoDate[1], $arrtoDate[2], $arrtoDate[0]);
				$bill[0]['bill_status'] = 1;
				//Mp3music_Api_Cart::updateHistories($bill,$type,$timestamp);
				//saveTracking
				Mp3music_Api_Cart::saveTrackingPayIn($bill, $type);
				//pay for owner of item
				$pta = array();
				$totsl = 0;
				foreach ($cartitem['items'] as $itc)
				{
					if (isset($pta[$itc['owner_id']]))
					{
						$pta[$itc['owner_id']] = $pta[$itc['owner_id']] + $itc['amount'];
					}
					else
					{
						$pta[$itc['owner_id']] = $itc['amount'];
					}
					$totsl += $itc['amount'];
				}
				foreach ($pta as $key => $value)
				{
					$user_group_id = Mp3music_Api_Cart::getGroupUser($key);
					$settings = Mp3music_Api_Cart::getSettingsSelling($user_group_id);
					if (!isset($settings['comission_fee']))
					{
						$fee = 0;
					}
					else
					{
						$fee = $settings['comission_fee'];
					}
					$coupon = $cartitem['coupon_code']['value'];
					$coupon = $coupon / $totsl;

					$coupon = round($coupon, 2);
					$val = ($value - $coupon * $value);
					$fee = $fee * $val / 100;
					$fee = round($fee, 2);
					$valuer = $val - $fee;
					Mp3music_Api_Cart::updateTotalAmount($key, $valuer, false);
				}
				// Affiliate integration
				$module = 'ynaffiliate';
				$modulesTable = Engine_Api::_() -> getDbtable('modules', 'core');
				$mselect = $modulesTable -> select() -> where('enabled = ?', 1) -> where('name  = ?', $module);
				$module_result = $modulesTable -> fetchRow($mselect);
				if (count($module_result) > 0)
				{

					$affi_params = array();
					$affi_params['module'] = 'mp3music';
					$affi_params['user_id'] = $bill[0]['user_id'];
					$affi_params['rule_name'] = 'buy_mp3music';
					$affi_params['total_amount'] = $totsl;
					$affi_params['currency'] = 'USD';
					Engine_Hooks_Dispatcher::getInstance() -> callEvent('onPaymentAfter', $affi_params);
				}
				//send money to admin
				$admin = Mp3music_Api_Cart::getFinanceAccount(null, 1);

				Mp3music_Api_Cart::sendNotifycation('admin', $admin['user_id'], $cartitem);
				//end
				//send notification for owner
				foreach ($cartitem as $itc)
				{
					Mp3music_Api_Cart::sendNotifycation($itc['type'], $itc['owner_id'], $itc);
				}

				//clear cart.
				Mp3music_Api_Shop::clearCart();
			}
			$this -> view -> moveitem = 1;

		}
		
		if ($this -> getRequest() -> isPost())
		{
			$values = $this -> getRequest() -> getPost();
			foreach ($values as $key => $value)
			{
				$arr = split("-", $key);
				if ($key == 'car_id_item_' . $value . '-' . $arr[1])
				{
					Mp3music_Api_Shop::removeCartItem($value, $arr[1]);
				}
			}
		}
		$cartsec = Mp3music_Api_Shop::getCartItems();
		$hiddencartalbum = Mp3music_Api_Shop::getDownloadList('album', Engine_Api::_() -> user() -> getViewer() -> getIdentity());
		$this -> view -> hiddencartalbum = $hiddencartalbum;
		$hiddencartsong = Mp3music_Api_Shop::getDownloadList('song', Engine_Api::_() -> user() -> getViewer() -> getIdentity());
		$this -> view -> hiddencartsong = $hiddencartsong;
		$cartsec = Mp3music_Api_Shop::reCheckCart($cartsec, $hiddencartalbum, $hiddencartsong);
		list($total_amount, $cartlist) = Mp3music_Api_Shop::getCartItemsInfo($cartsec);
		$this -> view -> total_amount = $total_amount;
		$this -> view -> cartlist = $cartlist;
	}

	public function addToCartAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$item_id = $this -> getRequest() -> getParam('item_id');
		$type = $this -> getRequest() -> getParam('type');
		$result = array();
		if ($type == 'song')
		{
			$song = Engine_Api::_() -> getItem('mp3music_album_song', $item_id);
			$album = Engine_Api::_() -> getItem('mp3music_album', $song -> album_id);
		}
		if ($type == 'album')
		{
			$album = Engine_Api::_() -> getItem('mp3music_album', $item_id);
		}
		if ($album != null)
		{
			$acc = Mp3music_Api_Cart::getFinanceAccount($album -> user_id);
			if ($acc == null)
			{
				$message = $this -> view -> translate("Owner of item does not have finance account. So, you can not pay for him with this item.");
				echo '{"result":"0", "message":"' . $message . '"}';
				return;
			}
			else
			{
				if ($type == 'album')
					$item = array(
						'item_id' => $item_id,
						'type' => $type,
						'amount' => $album -> price,
						'owner_id' => $album -> user_id,
						'account_id' => $acc['paymentaccount_id'],
					);
				else
					$item = array(
						'item_id' => $item_id,
						'type' => $type,
						'amount' => $song -> price,
						'owner_id' => $album -> user_id,
						'account_id' => $acc['paymentaccount_id'],
					);
				if (Mp3music_Api_Shop::setCartItem($item))
				{
					echo '{"result":"1", "message":"' . $this -> view -> translate("Add ") . $type . $this -> view -> translate(" to your cart successfully!") . '"}';
				}
			}

		}
		else
		{
			echo '{"result":"0", "message":"your request failed"}';
		}
		return;
	}

	public function removeCartAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$item_id = $this -> getRequest() -> getParam('item_id');
		$type = $this -> getRequest() -> getParam('type');
		$strResult = "";
		if (Mp3music_Api_Shop::removeCartItem($item_id, $type))
		{
			$strResult = '{"result":"1", "message":""}';
		}
		else
			$strResult = '{"result":"0", "message":""}';
		$cartsec = Mp3music_Api_Shop::getCartItems();
		$sub = $_SESSION['musicsharing_cart']['total_amount'];
		$coupon = $_SESSION['musicsharing_cart']['coupon_code']['value'];
		$checkout = $sub - $coupon;
		$strResult = '{"result":"1", "message":"' . $checkout . '"}';
		if (count($cartsec) <= 0)
		{
			Mp3music_Api_Shop::clearCart();
			$strResult = '{"result":"2", "message":"<div style=\'text-align: center ; margin: 20px;\'>There are no items in your cart.</div>"}';
		}
		echo $strResult;
	}

	public function loadMessageAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$item_id = $this -> getRequest() -> getParam('item_id');
		$request = Mp3music_Api_Cart::getRequestsFromUser($item_id);
		$html = '<ul class=\'global_form_box\' style=\'padding-left:10px; background: none; margin-bottom: 10px; overflow: auto;\'>';
		foreach ($request as $req)
		{
			if ($req['request_status'] == 1)
			{
				$a = '<div style=\' height:30px; background:url(./application/modules/Mp3music/externals/images/music/message_success.png) no-repeat scroll left center\'><span style=\'color:#8ABD3A;margin-left:40px;font-weight:bold\'> ' . $this -> view -> translate("Success") . ' ' . '</span><br/><span style=\'margin-left:40px; \'>' . $req['pDate'] . '</span></div>';
				$a .= "<p>" . $req['request_answer'] . "</p>";

			}
			else
			{
				$a = '<div style=\'height:30px; background:url(./application/modules/Mp3music/externals/images/music/message_fail.png) no-repeat scroll left center\'><span style=\'color:#CC6666;margin-left:40px;font-weight:bold\'> ' . $this -> view -> translate("Fail") . ' ' . '</span><br/><span style=\'margin-left:40px; \'>' . $req['pDate'] . '</span></div>';
				$a .= '<p>' . $req['request_answer'] . '</p>';
			}

			$html .= '<div class=\'p_4\'>' . $a . '</div>';
		}
		if (count($request) <= 0)
		{
			$html .= $this -> view -> translate("There are no messages from admin.");
		}
		else
		{
			if (count($request) > 8)
				$html = '<div style=\'overflow: auto ;height:200px ;margin-top:5px\'>' . $html . '</div>';

		}
		$html .= '<div class=\'p_4\'><a href=\'javascript:close(' . $item_id . ')\'>' . $this -> view -> translate("Close") . '</a></div></ul>';
		echo Zend_Json::encode(array('html' => $html));
	}

	public function loadalbumitemAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$item_id = $this -> getRequest() -> getParam('item_id');
		$album = Engine_Api::_() -> getItem('mp3music_album', $item_id);
		$songs = $album -> getDLSongs();
		$html = "<h4>" . $this -> view -> translate('Song List') . "</h4>";
		foreach ($songs as $so)
		{
			if (strlen($so -> title) >= 50)
				$a = '<a class =\'title_thongtin2\' style = \'padding-left:13px;vertical-align:center\' title=\' ' . $this -> view -> translate("Click to download") . '\' href=\"./application/modules/Mp3music/externals/scripts/download.php?idsong=' . $so -> song_id . '\">' . mb_substr($so -> title, 0, 50, 'utf-8') . '...</a>';
			else
				$a = '<a class =\'title_thongtin2\' style = \'padding-left:13px;vertical-align:center\' title=\' ' . $this -> view -> translate("Click to download") . '\' href=\"./application/modules/Mp3music/externals/scripts/download.php?idsong=' . $so -> song_id . '\">' . $so -> title . '</a>';

			$html .= '<div class=\'p_8\' style=\'background:url(./application/modules/Mp3music/externals/images/music/musical.png) no-repeat scroll left center\'>' . $a . '</div>';
		}
		if (count($songs) <= 0)
		{
			$html .= $this -> view -> translate('There are no songs in this album.');
		}
		else
		{
			if (count($songs) > 8)
				$html = '<div style=\'overflow: auto ;height:200px ;margin-top:5px\'>' . $html . '</div>';

		}
		echo '{"html":"' . $html . '"}';
	}

	public function downloadsAction()
	{
	    $this -> _helper -> content
        // ->    setNoRender()
        -> setEnabled();
        
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$_SESSION['downloadlist_downloadlist'] = $user_id;
		$del = $this -> getRequest() -> getParam('delete', 0);
		if ($del > 0)
		{
			$list = Engine_Api::_() -> getItem('mp3music_list', $del);
			if ($list)
				$list -> delete();
			return $this -> _redirect('mp3-music/cart/downloadlist');
		}
		if ($this -> getRequest() -> isPost())
		{
			$values = $this -> getRequest() -> getPost();
			foreach ($values as $key => $value)
			{
				if ($key == 'delete_' . $value)
				{
					$list = Engine_Api::_() -> getItem('mp3music_list', $value);
					if ($list)
						$list -> delete();
				}
			}
		}
		$params = array_merge($this -> _paginate_params, array('user_id' => $user_id, ));
		$this -> view -> downloadlist = Mp3music_Api_Cart::getDownloadList($params);
	}

	public function viewTransactionAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$this -> _helper -> layout -> disableLayout();
		$user_id = $this -> getRequest() -> getParam('id');
		$user_name = $this -> getRequest() -> getParam('username');
		$this -> view -> user_name = $user_name;
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$params = array_merge($this -> _paginate_params, array('user_id' => $user_id));
		$this -> view -> history = $his = Mp3music_Api_Cart::getTrackingTransaction($params);
		$his -> setItemCountPerPage(1000000000000);
	}

	public function transactionAction()
	{
	    $this -> _helper -> content
        // ->    setNoRender()
        -> setEnabled();
		$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$params = array_merge($this -> _paginate_params, array('user_id' => $user_id, ));
		$this -> view -> history = Mp3music_Api_Cart::getTrackingTransaction($params);
	}

	public function checkoutAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		//$this->_helper->layout->disableLayout();
		$session_id_cart = $this -> getRequest() -> getParam('session_id');
		$is_adaptive_payment = 0;
		$this -> view -> is_adaptive_payment = $is_adaptive_payment;
		if ($is_adaptive_payment == 1)
		{
			$_SESSION['payment_sercurity'] = Mp3music_Api_Cart::getSecurityCode();
			$is_items_include = 1;
			//echo $_SESSION['payment_sercurity'];
			$session_id = $this -> getRequest() -> getParam('id');
			$method_payment = array(
				'direct' => 'Directly',
				'multi' => 'Multipartite payment'
			);
			//var_dump($res);
			$cartsec = Mp3music_Api_Shop::getCartItems();
			list($total_amount, $cartlist) = Mp3music_Api_Shop::getCartItemsInfo($cartsec);
			$couponcart = Mp3music_Api_Shop::getCouponCodeCart();
			//$paymentForm =  phpfox::getLib('url')->makeUrl('musicsharing.cart');
			$total = Mp3music_Api_Shop::getTotalAmount();
			$this -> view -> include_items = $is_items_include;
			$this -> view -> method = $method_payment;
			$this -> view -> sercurity = $_SESSION['payment_sercurity'];
			$this -> view -> cartlist = $cartlist;
			$this -> view -> total_amount = $total_amount;
			$this -> view -> total = count($cartlist);
			$this -> view -> couponcart = $couponcart;
			$this -> view -> currency = "USD";
		}
		else
		{
			$_SESSION['payment_sercurity'] = Mp3music_Api_Cart::getSecurityCode();
			$is_items_include = 1;
			$method_payment = array(
				'direct' => 'Directly',
				'multi' => 'Multipartite payment'
			);
			//echo $_SESSION['payment_sercurity'];
			$session_id = $this -> getRequest() -> getParam('id');
			$cartsec = Mp3music_Api_Shop::getCartItems();
			list($total_amount, $cartlist) = Mp3music_Api_Shop::getCartItemsInfo($cartsec);
			$couponcart = Mp3music_Api_Shop::getCouponCodeCart();
			$paymentForm = '';
			$gateway_name = "paypal";
			$gateway = Mp3music_Api_Cart::loadGateWay($gateway_name);
			$settings = Mp3music_Api_Cart::getSettingsGateWay($gateway_name);
			$params = array();
			$params = array_merge(array(
				'page' => '1',
				'req3' => 'cancel',
				'req4' => $_SESSION['payment_sercurity']
			), $params);
			$cancelUrl = Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, 'mp3music_cart', true);
			$_SESSION['url']['cancel'] = $cancelUrl;
			$returnUrl = "http://" . $_SERVER['SERVER_NAME'] . $this -> view -> baseUrl() . '/application/modules/Mp3music/externals/scripts/redirect.php?pstatus=success&index=' . $this -> view -> url(array(), 'default') . '&req4=' . $_SESSION['payment_sercurity'] . '&req5=';
			$cancelUrl = "http://" . $_SERVER['SERVER_NAME'] . $this -> view -> baseUrl() . '/application/modules/Mp3music/externals/scripts/redirect.php?pstatus=cancel&index=' . $this -> view -> url(array(), 'default') . '&req4=' . $_SESSION['payment_sercurity'] . '&req5=';
			$notifyUrl = "http://" . $_SERVER['SERVER_NAME'] . $this -> view -> baseUrl() . '/application/modules/Mp3music/externals/scripts/callback.php?action=callback&req4=' . $_SESSION['payment_sercurity'] . '&req5=';
			list($receiver, $paramsPay) = Mp3music_Api_Cart::getParamsPay($gateway_name, $returnUrl, $cancelUrl, $method_payment, $notifyUrl);
			$_SESSION['receiver'] = $receiver;
			
			$receiver = $_SESSION['receiver'];
			$carts = Mp3music_Api_Shop::getCart();
			$bill = Mp3music_Api_Shop::makeBillFromCart($carts, $receiver);
			
			$this -> view -> cartlist = $cartlist;
			$this -> view -> total_amount = $total_amount;
			$this -> view -> total = count($cartlist);
			
			//******************IMPLEMENT INTERGRATE ADV-PAYMENT*************************
		
	        $viewer = Engine_Api::_() -> user() -> getViewer();
	       	
			$this -> view -> total_pay = $total_pay = $final_price;
	        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
	
	        if ((!$gatewayTable -> getEnabledGatewayCount() && !Engine_Api::_() -> hasModuleBootstrap('yncredit'))) {
	            $message = $this -> view -> translate('There are no payment gateways.');
	            return $this -> _redirector($message);
	        }
	        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'mp3music');
			
	        if ($row = $ordersTable -> getLastPendingOrder()) {
	           $row -> delete();
	        }
	        $db = $ordersTable -> getAdapter();
	        $db -> beginTransaction();
	        try 
	        {
	            $ordersTable -> insert(array(
	            	'user_id' => $viewer -> getIdentity(), 
		            'creation_date' => new Zend_Db_Expr('NOW()'), 
		            'price' => $total_amount, 
		            'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'), 
					'security_code' => $bill -> sercurity ,
					'invoice_code' => $bill -> invoice,
				));
	            // Commit
	            $db -> commit();
	        } catch (Exception $e) {
	            $db -> rollBack();
	            throw $e;
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
			
		}

	}
	
	public function updateOrderAction() 
    {
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

        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'mp3music');
        $order = $ordersTable -> getLastPendingOrder();
        if (!$order) {
            $message = $this -> view -> translate('Can not find order.');
            return $this -> _redirector($message);
        }
        $order -> gateway_id = $gateway -> getIdentity();
        $order -> save();

        $this -> view -> status = true;
        if (!in_array($gateway -> title, array('2Checkout', 'PayPal'))) {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process-advanced', 'order_id' => $order -> getIdentity(), 'm' => 'mp3music', 'cancel_route' => 'mp3music_transaction', 'return_route' => 'mp3music_transaction', ), 'ynpayment_paypackage', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        } else {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process', 'order_id' => $order -> getIdentity(), ), 'mp3music_transaction', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        }
    }
	
	public function makebillAction()
	{
		
		$this -> _helper -> layout -> disableLayout();
		
		$receiver = $_SESSION['receiver'];
		$carts = Mp3music_Api_Shop::getCart();
		$bill = Mp3music_Api_Shop::makeBillFromCart($carts, $receiver);
	}

	public function makerequestAction()
	{
		
		$this -> _helper -> layout -> disableLayout();
		$message = $this -> getRequest() -> getParam('message');
		$request_id = $this -> getRequest() -> getParam('request_id');
		$_SESSION['request_id'] = $request_id;
		$_SESSION['message'] = $message;
	}

	public function loadSettingsCartAction()
	{
		
		$this -> _helper -> layout -> disableLayout();
		
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$user_group_id = $this -> getRequest() -> getParam('user_group_id');
		$settings = Mp3music_Api_Cart::getSettingsSelling($user_group_id);
		$settings = Mp3music_Api_Cart::setDefaultValueSelling($settings, $user_group_id);
		echo $this -> view -> partial('_selling_settings_ajax.tpl', array(
			'settings' => $settings,
			'currency' => 'USD'
		));
		return;
	}

	public function selfURL()
	{
		$server_array = explode("/", $_SERVER['PHP_SELF']);
		$server_array_mod = array_pop($server_array);
		if ($server_array[count($server_array) - 1] == "admin")
		{
			$server_array_mod = array_pop($server_array);
		}
		$server_info = implode("/", $server_array);
		return "http://" . $_SERVER['HTTP_HOST'] . $server_info . "/";
	}

	protected $_navigation;
	public function getNavigation()
	{
		$tabs = array();
		$tabs[] = array(
			'label' => 'Browse Music',
			'route' => 'mp3music_browse',
			'action' => 'browse',
			'controller' => 'index',
			'module' => 'mp3music'
		);
		$tabs[] = array(
			'label' => 'My Music',
			'route' => 'mp3music_manage_album',
			'action' => 'manage',
			'controller' => 'album',
			'module' => 'mp3music'
		);
		$tabs[] = array(
			'label' => 'My Playlists',
			'route' => 'mp3music_manage_playlist',
			'action' => 'manage',
			'controller' => 'playlist',
			'module' => 'mp3music'
		);
		$tabs[] = array(
			'label' => 'Upload Music',
			'route' => 'mp3music_create_album',
			'action' => 'create',
			'controller' => 'album',
			'module' => 'mp3music'
		);
		$tabs[] = array(
			'label' => 'My Account',
			'route' => 'mp3music_account_myaccount',
			'action' => 'index',
			'controller' => 'account',
			'module' => 'mp3music'
		);
		$tabs[] = array(
			'label' => 'Cart',
			'route' => 'mp3music_cart',
			'action' => 'index',
			'controller' => 'cart',
			'module' => 'mp3music'
		);
		$tabs[] = array(
			'label' => 'Download List',
			'route' => 'mp3music_cart_downloadlist',
			'action' => 'downloads',
			'controller' => 'cart',
			'module' => 'mp3music'
		);
		if (is_null($this -> _navigation))
		{
			$this -> _navigation = new Zend_Navigation();
			$this -> _navigation -> addPages($tabs);
		}
		return $this -> _navigation;
	}

}

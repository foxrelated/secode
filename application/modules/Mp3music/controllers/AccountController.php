<?php
class Mp3music_AccountController extends Core_Controller_Action_Standard
{
	protected $_paginate_params = array();
	public function init()
	{
		$this -> view -> navigation = $this -> getNavigation();
		$this -> _paginate_params['page'] = $this -> getRequest() -> getParam('page', 1);
		$this -> _paginate_params['limit'] = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('mp3music.songsPerPage', 10);
	}

	public function createAction()
	{
		// only members can create account
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$this -> view -> form = new Mp3music_Form_CreateAccount();
		$is_account = Mp3music_Api_Account::getCurrentInfo(Engine_Api::_() -> user() -> getViewer() -> getIdentity());
		if ($is_account['account_username'] != null)
			$result = 1;
		if ($this -> getRequest() -> isPost() && $this -> view -> form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = Engine_Api::_() -> getDbTable('paymentAccounts', 'mp3music') -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$result = $this -> view -> form -> saveValues();
				$this -> view -> result = $result;
				$db -> commit();
				if ($result)
					return $this -> _redirect('mp3-music/account/myaccount');
			}
			catch (Exception $e)
			{
				$db -> rollback();
				throw $e;
			}
		}
	}

	public function indexAction()
	{
	    $this -> _helper -> content
        // ->    setNoRender()
        -> setEnabled();
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$info_user = Mp3music_Api_Account::getCurrentInfo($user_id);
		$info_account = Mp3music_Api_Account::getCurrentAccount($user_id);
		if (strlen($info_user['status']) >= 41)
			$info_user['status'] = substr($info_user['status'], 0, 41) . "...";
		$user_group_id = Engine_Api::_() -> user() -> getViewer() -> level_id;
		$info_sellingsettings = Mp3music_Api_Account::getSellingSettings($user_group_id);
		$fee = $info_sellingsettings['comission_fee'];

		$AmountSeller = Mp3music_Api_Account::getAmountSeller($user_id);
		$list_total = $AmountSeller;
		$params = array_merge($this -> _paginate_params, array(
			'user_id' => $user_id,
			'list_total' => $list_total,
			'limit' => 20
		));
		$this -> view -> HistorySeller = $his = Mp3music_Api_Account::getHistorySeller($params);
		$min_payout = $info_sellingsettings['min_payout'];
		$max_payout = $info_sellingsettings['max_payout'];
		$allow_request = 0;
		$requested_amount = Mp3music_Api_Account::getTotalRequest($user_id);
		if ($info_account['total_amount'] >= $min_payout)
		{
			$allow_request = 1;
		}
		$rest = $info_account['total_amount'] - $requested_amount;
		$this -> view -> info_user = $info_user;
		$this -> view -> info_account = $info_account;
		$this -> view -> min_payout = $min_payout;
		$this -> view -> max_payout = $max_payout;
		$this -> view -> allow_request = $allow_request;
		$this -> view -> requested_amount = round($requested_amount, 2);
		$this -> view -> current_amount = round($rest, 2);
		$this -> view -> fee = $fee;
	}

	public function policyAction()
	{
		$settings = Mp3music_Api_Cart::getSettingsSelling(0);
		$po = $this -> getRequest() -> getParam('type');
		if ($po == '1')
		{
			$this -> view -> message = $this -> view -> translate('General Policy');
			if ($settings['policy_message'] != "")
				$this-> view -> policy = $settings['policy_message'];
			else
				$this-> view -> policy = $this->view->translate('Under the first title of the Treaty of the European<p>Communities one finds the provisions dealing with the free movement of goods. In the years between the two world wars, and leading into the Great Depression, governments around the world had employed vigorous policies of national protectionism. The erection of tariffs and customs duties on imports and sometimes the export of goods was widely seen as contributing to a fall in trade and hence the stalling of economic growth and development. Economists had long said, since Adam Smith and David Ricardo that the Wealth of Nations could only be strengthened by the long term lowering and abolition of barriers and costs to international trade. The abolition of all such barriers is the function of the treaty provisions. According to Article 28 EC,</p><p>"28. Quantitative restrictions on imports and all measures having equivalent effect shall be prohibited between Member States."Article 29 EC states the same for exports. The first thing to note is that the prohibition is simply between member states of the European Community. One of the institutions primary duties is the management of trade policy to third parties - other countries such as the United States, or China. For instance, the controversial Common Agricultural Policy is regulated under Title II EC, Article 34(1) authorising "compulsory coordination of national market organisations" with common European organisation. The second thing to note is that Article 30 sets out the exceptions to the prohibition on free movement of goods.</p><p>"30. The provisions of Articles 28 and 29 shall not preclude prohibitions or restrictions on imports, exports or goods in transit justified on grounds of public morality, public policy or public security; the protection of health and life of humans, animals or plants; the protection of national treasures possessing artistic, historic or archaeological value; or the protection of industrial and commercial property. Such prohibitions or restrictions shall not, however, constitute a means of arbitrary discrimination or a disguised restriction on trade between Member States."</p><p>So governments of member states may still justify certain trade barriers when public morality, policy, security, health, culture or industrial and commercial property might be threatened by complete abolition. One recent example of this was that during the mad cow disease crisis in the United Kingdom, France erected a barrier to imports of British beef.[31]</p>');
		}
		else
		{
			$this -> view -> message = $this -> view -> translate('Request Policy');
			if ($settings['policy_message_request'] != "")
				$this-> view -> policy = $settings['policy_message_request'];
			else
				$this-> view -> policy = $this->view->translate('Since the goal of the Treaty of Rome was to create a common market, and the Single European Act to create an internal market, it was crucial to ensure that the efforts of government could not be distorted by corporations abusing their market power. Hence under the treaties are provisions to ensure that free competition prevails, rather than cartels and monopolies sharing out markets and fixing prices. Competition law in the European Union is largely similar and inspired by United States antitrust.\r\n[edit] Collusion and cartels');
		}
	}

	public function thresholdAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
	}

	public function requestmoneyAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		//tat di layout
		$this -> _helper -> layout -> disableLayout();
		//khong su dung view
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$current_money = $this -> getRequest() -> getParam('currentmoney');
		if (!is_numeric($current_money))
			$current_money = -10;

		if (round($current_money, 2) - $current_money != 0)
		{
			$html = '<h2>' . $this -> view -> translate("Invalid request number.") . '</h2>';
			echo '{"html":"' . $html . '"}';
			return false;
		}

		$user_group_id = Engine_Api::_() -> user() -> getViewer() -> level_id;
		$info_sellingsettings = Mp3music_Api_Account::getSellingSettings($user_group_id);
		$info_account = Mp3music_Api_Account::getCurrentAccount(Engine_Api::_() -> user() -> getViewer() -> getIdentity());
		$TotalRequest = Mp3music_Api_Account::getTotalRequest(Engine_Api::_() -> user() -> getViewer() -> getIdentity());

		$min_payout = $info_sellingsettings['min_payout'];
		$max_payout = $info_sellingsettings['max_payout'];
		$allow_request = 0;

		if (round(($info_account['total_amount'] - $TotalRequest - $min_payout), 2) >= round($current_money, 2))
		{
			if ($current_money != -10 && $current_money > 0)
			{
				if ($max_payout == -1 || $max_payout >= $current_money)
				{
					$allow_request = 1;
				}
			}
		}
		else
		{
			$warning = 1;
			$minhrequest = round($info_account['total_amount'] - $TotalRequest - $min_payout, 2);
			if ($minhrequest < 0)
				$minhrequest = 0;
			$html = $this -> view -> translate("You have requested ") . round($TotalRequest, 2) . $this -> view -> translate(" USD before, so you only can request maximum is ") . $minhrequest . " USD.";
		}
		if ($allow_request == 1)
		{
			$vals = array();
			$vals['request_user_id'] = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
			$vals['request_amount'] = round($current_money, 2);
			$vals['request_date'] = time();
			$vals['request_reason'] = strip_tags($this -> getRequest() -> getParam('reason'));
			$vals['request_status'] = 0;
			$vals['request_payment_acount_id'] = $info_account['paymentaccount_id'];
			$request_id = Mp3music_Api_Account::insertRequest($vals);
			$info_account = Mp3music_Api_Account::getCurrentAccount(Engine_Api::_() -> user() -> getViewer() -> getIdentity());

			$html = "<h2>" . $this -> view -> translate("Request successfully!") . "<h2>";
			$current_request_money = round($TotalRequest + $current_money, 2);
			$current_money_money = round($info_account['total_amount'] - $TotalRequest - $current_money, 2);
		}
		else
		if ($warning != 1)
		{
			$html = "<h2>" . $this -> view -> translate("Request false!") . "</h2>";
		}
		echo '{"html":"' . $html . '","current_request_money":"' . $current_request_money . '","current_money_money":"' . $current_money_money . '"}';
	}

	public function editAction()
	{
		$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		if (isset($_POST['submit']))
		{
			$aVals = $this -> getRequest() -> getParam('val');
			$aVals['displayname'] = strip_tags($aVals['full_name']);
			$aVals['status'] = strip_tags($aVals['status']);
			$this -> view -> info = $aVals;
			$is_validate = 0;
			$is_email = 0;
			if (trim($aVals['full_name']) == "")
			{
				$is_validate = 1;
				$this -> view -> error = $this->view->translate('Please enter full name!');
			}
			if (trim($aVals['email'] == ""))
			{
				$is_validate = 1;
				$is_email = 1;
				$this -> view -> error = $this->view->translate('Please enter email!');
			}
			if (trim($aVals['account_username'] == ""))
			{
				$is_validate = 1;
				$this -> view -> error = $this->view->translate('Please enter finance account');
			}
			if ($is_email == 0)
			{
				$email = $aVals['email'];
				$regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
				if (!preg_match($regexp, $email))
				{
					$is_validate = 1;
					$this -> view -> error = $this->view->translate('Email address is not valid!');
				}
				$email = $aVals['account_username'];
				$regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
				if (!preg_match($regexp, $email))
				{
					$is_validate = 1;
					$this -> view -> error = $this->view->translate('Finance account email is not valid!');
					$this -> view -> mail_empty = 'emailAccount';
				}
			}
			if ($is_validate == 0)
			{
				$result = Mp3music_Api_Account::updateinfo($aVals);
				Mp3music_Api_Account::updateusername_account($user_id, $aVals['account_username']);
				$info_account = Mp3music_Api_Account::getCurrentAccount($user_id);
				if ($info_account != null)
				{
					if ($info_account['payment_type'] == 1)
					{
						$params['admin_account'] = $aVals['account_username'];
						$params['is_from_finance'] = 1;
						Mp3music_Api_Gateway::saveSettingGateway('paypal', $params);
					}
				}

			}
			else
				return;
		}

		$info = Mp3music_Api_Account::getCurrentInfo($user_id);
		$this -> view -> info = $info;
		$this -> view -> result = $result;
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

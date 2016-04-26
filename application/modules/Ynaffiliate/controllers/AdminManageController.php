<?php
class Ynaffiliate_AdminManageController extends Core_Controller_Action_Admin
{
	public function init()
	{
		Zend_Registry::set('admin_active_menu', 'ynaffiliate_admin_main_manage_affiliate');
		$this -> view -> headLink() -> appendStylesheet($this -> view -> baseUrl() . '/application/modules/Ynaffiliate/externals/styles/main.css');
	}

	public function indexAction()
	{
		$this -> view -> form = $form = new Ynaffiliate_Form_Admin_Manage_Affiliate();
		$page = $this -> _getParam('page', 1);
		$values = array();
		if ($form -> isValid($this -> _getAllParams()))
		{
			$values = $form -> getValues();
		}
		$limit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynaffiliate.page', 10);
		$values['limit'] = $limit;
		$this -> view -> viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> paginator = $paginator = Engine_Api::_() -> ynaffiliate() -> getAffiliatesPaginator($values);
		$this -> view -> paginator -> setCurrentPageNumber($page);
		$this -> view -> formValues = $values;
	}

	public function deleteSelectedAction()
	{
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		$this -> view -> count = count(explode(",", $ids));
		// Save values
		if ($this -> getRequest() -> isPost() && $confirm == true)
		{
			$ids_array = explode(",", $ids);
			foreach ($ids_array as $id)
			{
				$account = Engine_Api::_() -> getItem('ynaffiliate_accounts', $id);
				if ($account)
				{
					$account -> delete();
				}
			}
			$this -> _helper -> redirector -> gotoRoute(array('action' => 'index'));
		}
	}

	public function approveSelectedAction()
	{
		$this -> view -> ids = $ids = $this -> _getParam('ids1', null);
		$confirm = $this -> _getParam('confirm', false);
		$this -> view -> count = count(explode(",", $ids));
		// Save values
		if ($this -> getRequest() -> isPost() && $confirm == true)
		{
			$ids_array = explode(",", $ids);
			foreach ($ids_array as $id)
			{
				$account = Engine_Api::_() -> getItem('ynaffiliate_accounts', $id);
				if ($account -> approved == 0)
				{
					$account -> approved = 1;
					$account -> save();
				}
			}
			$this -> _helper -> redirector -> gotoRoute(array('action' => 'index'));
		}
	}

	public function denySelectedAction()
	{
		$this -> view -> ids = $ids = $this -> _getParam('ids2', null);
		$confirm = $this -> _getParam('confirm', false);
		$this -> view -> count = count(explode(",", $ids));
		// Save values
		if ($this -> getRequest() -> isPost() && $confirm == true)
		{
			$ids_array = explode(",", $ids);
			foreach ($ids_array as $id)
			{
				$account = Engine_Api::_() -> getItem('ynaffiliate_accounts', $id);
				if ($account -> approved == 0)
				{
					$account -> approved = 2;
					$account -> save();
				}
			}
			$this -> _helper -> redirector -> gotoRoute(array('action' => 'index'));
		}
	}

	public function approveAffiliateAction()
	{
		$form = $this -> view -> form = new Ynaffiliate_Form_Admin_Approve();
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$values = $form -> getValues();
			$account_id = $values['account_id'];
			$account = Engine_Api::_() -> getItem('ynaffiliate_accounts', $account_id);
			$this -> view -> account_id = $account -> account_id;
			// This is a smoothbox by default
			if (null === $this -> _helper -> ajaxContext -> getCurrentContext())
				$this -> _helper -> layout -> setLayout('default-simple');
			else// Otherwise no layout
				$this -> _helper -> layout -> disableLayout(true);
			$account -> approved = 1;
			$account -> save();
			$this -> view -> form = null;
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'messages' => array('Change Saved')
			));
		}
		if (!($account_id = $this -> _getParam('account_id')))
		{
			throw new Zend_Exception('No Affiliate specified');
		}
		//Generate form
		$form -> populate(array('account_id' => $account_id));
		//Output
		$this -> renderScript('admin-manage/form.tpl');
	}

	public function statisticsAction()
	{
		$id = $this -> _getParam('account_id', null);
		$account = Engine_Api::_() -> getItem('ynaffiliate_accounts', $id);
		$user_id = $account -> user_id;
		$this -> view -> user = Engine_Api::_() -> getItem('user', $user_id);
		$this -> view -> form = $form = new Ynaffiliate_Form_Statistic();
		$commssionTable = Engine_Api::_() -> getDbTable('commissions', 'ynaffiliate');
		$requestTable = Engine_Api::_() -> getDbTable('requests', 'ynaffiliate');
		$this -> view -> subscriptions = $commssionTable -> countCommission(null, $user_id, array(
			'ruleId' => 1,
			'notStatus' => 'denied'
		));
		$this -> view -> purchases = $commssionTable -> countCommission(null, $user_id, array(
			'notRule' => 1,
			'notStatus' => 'denied'
		));
		$this -> view -> commissionPoints = round($commssionTable -> getTotalPoints(null, $user_id, array('notStatus' => 'denied')), 2);
		$this -> view -> approvedCommissionPoints = round($commssionTable -> getTotalPoints('approved', $user_id), 2);
		$this -> view -> delayingCommissionPoints = round($commssionTable -> getTotalPoints('delaying', $user_id), 2);
		$this -> view -> waitingCommissionPoints = round($commssionTable -> getTotalPoints('waiting', $user_id), 2);
		$this -> view -> requestedPoints = round($requestTable -> getRequestedPoints($user_id), 2);
		$points_convert_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.pointrate', 1);
		$exchange_rate = Engine_Api::_()->getDbTable('exchangerates', 'ynaffiliate')->getExchangerateById($account->selected_currency)->exchange_rate;
		$this->view->selected_currency = $account->selected_currency;
		$this->view->points_convert_rate = $points_convert_rate;
		$this->view->exchange_rate = $exchange_rate;

	}

	public function denyAffiliateAction()
	{
		$form = $this -> view -> form = new Ynaffiliate_Form_Admin_Deny();
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$values = $form -> getValues();
			$account_id = $values['account_id'];
			$account = Engine_Api::_() -> getItem('ynaffiliate_accounts', $account_id);
			$this -> view -> account_id = $account -> account_id;
			// This is a smoothbox by default
			if (null === $this -> _helper -> ajaxContext -> getCurrentContext())
				$this -> _helper -> layout -> setLayout('default-simple');
			else// Otherwise no layout
				$this -> _helper -> layout -> disableLayout(true);
			$account -> approved = 2;
			$account -> save();
			$this -> view -> form = null;
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _("Change Saved"))
			));
			//         $this->_forward('success', 'utility', 'core', array(
			//             'smoothboxClose' => 1000,
			//             'parentRefresh' => 10,
			//             'messages' => array('Affiliate has been denied')));
		}
		if (!($account_id = $this -> _getParam('account_id')))
		{
			throw new Zend_Exception('No Affiliate specified');
		}
		//Generate form
		$form -> populate(array('account_id' => $account_id));
		//Output
		$this -> renderScript('admin-manage/form.tpl');
	}

	public function deleteAffiliateAction()
	{
		$form = $this -> view -> form = new Ynaffiliate_Form_Admin_Delete();
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$values = $form -> getValues();
			$account_id = $values['account_id'];
			$account = Engine_Api::_() -> getItem('ynaffiliate_accounts', $account_id);
			$this -> view -> account_id = $account -> account_id;
			// This is a smoothbox by default
			if (null === $this -> _helper -> ajaxContext -> getCurrentContext())
				$this -> _helper -> layout -> setLayout('default-simple');
			else// Otherwise no layout
				$this -> _helper -> layout -> disableLayout(true);
			// $account->deleted = 1;
			$account -> delete();
			$this -> view -> form = null;
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _("Change Saved"))
			));
		}
		if (!($account_id = $this -> _getParam('account_id')))
		{
			throw new Zend_Exception('No Affiliate specified');
		}
		//Generate form
		$form -> populate(array('account_id' => $account_id));
		//Output
		$this -> renderScript('admin-manage/form.tpl');
	}

	public function viewNetworkClientsAction()
	{
		//      $this -> _helper -> content -> setEnabled();
		$account_id = $this -> _getParam('account_id', null);
		$account = Engine_Api::_() -> getItem('ynaffiliate_accounts', $account_id);
		$user_id = $account -> user_id;
		$user = Engine_Api::_() -> getItem('user', $user_id);
		// get assoc table
		$assocTable = Engine_Api::_() -> getDbtable('assoc', 'ynaffiliate');
		// start getting data
		$data = $assocTable -> getClient($user_id);
		//		echo '<pre>',print_r($data);die;
		$this -> view -> viewer = $user;
		$this -> view -> user_id = $user_id;
		$this -> view -> client_data = $data;
		$this -> view -> loaded_clients = count($data);
		// count total client
		$totalClient = $directClient = $assocTable -> countClient($user_id);
		$totalClient = $assocTable -> countAllClient($user_id);
		$this -> view -> total_client = $totalClient;
		$this -> view -> direct_client = $directClient;
		// get level option to pass to each client, so it not retrive again when recursive is running
		$authorizationTable = Engine_Api::_() -> getDbtable('levels', 'authorization');
		$select = $authorizationTable -> select() -> where('type != ?', 'public');
		$levels = $authorizationTable -> fetchAll($select);
		$levelOptions = array();
		foreach ($levels as $level)
		{
			$levelOptions[$level -> level_id] = $level -> getTitle();
		}
		$this -> view -> levelOptions = $levelOptions;
		// get max client show to compare with direct client to show more button
		$clientLimit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynaffiliate.client.limit', 3);
		$this -> view -> client_limit = $clientLimit;
		$maxLevel = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynaffiliate.max.commission.level', 5);
		$this -> view -> max_level = $maxLevel;
	}

}

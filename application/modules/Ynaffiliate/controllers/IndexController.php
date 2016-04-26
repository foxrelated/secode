<?php

class Ynaffiliate_IndexController extends Core_Controller_Action_Standard
{

	public function init()
	{

	}

	function curPageURL()
	{
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
		{
			$pageURL .= "s";
		}
		$pageURL .= "://";

		if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80")
		{
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		}
		else
		{
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}

	public function indexAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}

		Zend_Registry::set('active_menu', null);

		$account = Engine_Api::_() -> getApi('Core', 'Ynaffiliate') -> getAccount();

		if (!is_object($account))
		{
			$url = $this -> getFrontController() -> getRouter() -> assemble(array(), 'ynaffiliate_signup', true);
			$this -> _helper -> redirector -> setPrependBase(false) -> gotoUrl($url);
		}

		if ($account -> isApproved() == 1)
		{
			$url = $this -> getFrontController() -> getRouter() -> assemble(array('controller' => 'commission-rule'), 'ynaffiliate_extended', true);
			$this -> _helper -> redirector -> setPrependBase(false) -> gotoUrl($url);
		}
		else
		{
			$this -> _forward('need-approved');
		}
	}

	public function needApprovedAction()
	{
		$account = Engine_Api::_() -> getApi('Core', 'Ynaffiliate') -> getAccount();
		$this -> view -> account = $account;
	}

	public function clickAction()
	{
		$url = $this -> getFrontController() -> getRouter() -> assemble(array(), 'home');
		$found = false;
		if (isset($_COOKIE['ynaffiliate_user_id']) && isset($_COOKIE['ynaffiliate_link_id']) && isset($_COOKIE['ynaffiliate_time']))
		{
			$found = true;
		}

		$user_id = $this -> _getParam('user_id');

		$target = $this -> _getParam('href');

		if ($target)
		{
			$target = base64_decode($target);
		}
		else
		{
			$url = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'home', true);
			$target = Engine_Api::_() -> ynaffiliate() -> getSiteUrl() . '/' . $url;
		}

		$Links = new Ynaffiliate_Model_DbTable_Links;

		if ($user_id)
		{
			$link = $Links -> getLink($user_id, $target, $this -> curPageURL());
			$link -> click_count++;
			$link -> last_click = date('Y-m-d H:i:s');
			$link -> save();
			// set affiliate table.
			$days = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynaffiliate.expireddays', 30);
			$expired = $days * 86400 + time();
			$request = Zend_Controller_Front::getInstance() -> getRequest();
			$base_url = $request -> getBaseUrl();
			setcookie('ynafuser', $user_id, $expired, '/');
			setcookie('ynaflink', $link -> getIdentity(), $expired, '/');
			setcookie('ynafftime', time(), $expired, '/');

			/*$_COOKIE['ynaffiliate_user_id'] = $user_id;
			 $_COOKIE['ynaffiliate_link_id'] = $link->getIdentity();
			 $_COOKIE['ynaffiliate_time'] = time();*/
		}

		$this -> _helper -> redirector -> setPrependBase(false) -> gotoUrl($target);
	}

	public function termsAction()
	{

		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}

		//      $affiliate = new Ynaffiliate_Plugin_Menus;
		//      if (!$affiliate->canView()) {
		//         $this->_redirect('/affiliate/index');
		//         //return $this->_helper->redirector->gotoRoute(array(), 'default', true);
		//      }
		$table = Engine_Api::_() -> getDbTable('statics', 'ynaffiliate');
		$select = $table -> select();
		$select -> where('static_name = ?', 'terms');
		$row = $table -> fetchRow($select);
		if (!count($row))
		{
			return;
		}
		//echo($row[0]->static_content);
		$this -> view -> terms = $row -> static_content;
	}

	public function loadMoreClientsAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);

		$user_id = $this->_getParam('user_id');
		if (!$user_id) {
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$user_id = $viewer -> getIdentity();
		}
		$from_level = $this->_getParam('from_level');
		$last_assoc_id = $this->_getParam('last_assoc_id');
		$search_user_id = $this->_getParam('search_user_id');

		// get amount of loaded client to decide show more button
		$loaded_clients= $this->_getParam('loaded_clients');
		$assocTable = Engine_Api::_()->getDbtable('assoc', 'ynaffiliate');
		// start getting data
		$client_data = $assocTable->getClient($user_id, $from_level, $last_assoc_id, $search_user_id);

		// increase amount of loaded client
		$loaded_clients = $loaded_clients + count($client_data);
		// setup view
		$view = Zend_Registry::get('Zend_View');
		$view = clone $view;
		$view->clearVars();

		// setup init values to pass to template
		$clientLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.client.limit', 3);
		$maxLevel = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.max.commission.level', 5);
		$authorizationTable = Engine_Api::_()->getDbtable('levels', 'authorization');
		$select = $authorizationTable->select()->where('type != ?', 'public');
		$levels = $authorizationTable->fetchAll($select);

		// get level options
		$levelOptions = array();
		foreach( $levels as $level ) {
			$levelOptions[$level->level_id] = $level->getTitle();
		}
		// count direct client to show more buttons
		$directClient = $assocTable -> countClient($user_id);

		// pass value to view
		$view -> client_limit = $clientLimit;
		$view -> levelOptions = $levelOptions;
		$view -> client_data = $client_data;
		$view -> level = $from_level;
		$view -> user_id = $user_id;
		$view -> direct_client = $directClient;
		$view -> max_level = $maxLevel;
		$view -> loaded_clients = $loaded_clients;
		$view -> search_user_id = $search_user_id;

		// render view
		$HTMLContent = $view->render('_network-clients_clients.tpl');
		$this->_helper->viewRenderer->postDispatch();
		echo $HTMLContent;
	}

	public function clientSuggestAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$table = Engine_Api::_()->getItemTable('user');

		// Get params
		$text = $this->_getParam('text', $this->_getParam('search', $this->_getParam('value')));
		$limit = (int) $this->_getParam('limit', 10);

		// Generate query
		$select = Engine_Api::_()->getItemTable('user')->select()->where('search = ?', 1);

		if( null !== $text ) {
			$select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
		}
		$select->limit($limit);

		// Retv data
		$data = array();
		foreach( $select->getTable()->fetchAll($select) as $friend ){
			$data[] = array(
				'id' => $friend->getIdentity(),
				'label' => $friend->getTitle(), // We should recode this to use title instead of label
				'title' => $friend->getTitle(),
				'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
				'type' => 'user',
				'url' => $friend->getHref(),
			);
		}

		// send data
		$data = Zend_Json::encode($data);
		$this->getResponse()->setBody($data);
	}

	public function downloadCsvAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$viewer_id = $viewer -> getIdentity();

		$user_id = (int) $this->_getParam('download_csv_user_id', $viewer_id);
		$user = Engine_Api::_()->getItem('user', $user_id);
		// start getting data
		$assocTable = Engine_Api::_()->getDbtable('assoc', 'ynaffiliate');
		$data = $assocTable->getClient($user_id, 0, 0, 0, 1);
		$clients = $this->_flattenClients($data);
		// get Level options
		$levelOptions = array();
		$authorizationTable = Engine_Api::_()->getDbtable('levels', 'authorization');
		$select = $authorizationTable->select()->where('type != ?', 'public');
		$levels = $authorizationTable->fetchAll($select);
		foreach( $levels as $level ) {
			$levelOptions[$level->level_id] = $level->getTitle();
		}
		// csv
		$filename = "/tmp/csv-" . date( "m-d-Y" ) . ".csv";
		$realPath = realpath( $filename );
		if ( false === $realPath )
		{
			touch( $filename );
			chmod( $filename, 0777 );
		}
		$filename = realpath( $filename );
		$handle = fopen( $filename, "w" );

		foreach ( $clients as $item )
		{
			$clientUser = Engine_Api::_()->getItem('user', $item['user_id']);
			if ($clientUser->getIdentity()) {
				$creationDate = $clientUser -> creation_date;
				$memberLevel = $levelOptions[$clientUser->level_id];
			} else {
				$creationDate = '';
				$memberLevel = '';
			}
			$finalData[] = array(
				$clientUser->getTitle(),
				$item['level'],
				$memberLevel,
				$creationDate,
				$item['total_client']
//				$item->getOwner()->email,
			);
		}
		$titleRow = array(
			'Name', 'Level', 'Member Level', 'Registration Date', 'Total Affiliates'
		);
		fputcsv( $handle, $titleRow);
		foreach ( $finalData as $finalRow )
		{
			fputcsv( $handle, $finalRow);
		}
		fclose($handle);
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		if ($user_id == $viewer_id) {
			$csvname = 'My Network Clients.csv';
		} else {
			$csvname = 'Network Clients of '.$user->getTitle().'.csv';
		}
		$this->getResponse()->setRawHeader( "Content-Type: application/csv; charset=UTF-8" )
			->setRawHeader( "Content-Disposition: attachment; filename=".$csvname )
			->setRawHeader( "Content-Transfer-Encoding: binary" )
			->setRawHeader( "Expires: 0" )
			->setRawHeader( "Cache-Control: must-revalidate, post-check=0, pre-check=0" )
			->setRawHeader( "Pragma: public" )
			->setRawHeader( "Content-Length: " . filesize( $filename ) )
			->sendResponse();
		// fix for print out data
		readfile($filename);
		unlink($filename);
		$this -> view -> status = TRUE;
		exit();
	}

	protected function _flattenClients($clients) {
		$result = array();
		foreach ($clients as $client) {
			$one_client = array(
				'user_id' => $client['user_id'],
				'level' => $client['level'],
				'total_client' => $client['total_client']
			);
			$sub_clients = $client['clients'];
			$result[] = $one_client;
			$result = array_merge($result, $this->_flattenClients($sub_clients));
		}
		return $result;
	}
}

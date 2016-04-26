<?php

class Ynaffiliate_MyAffiliateController extends Core_Controller_Action_Standard {

	public function init() {
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$affiliate = new Ynaffiliate_Plugin_Menus;
		if (!$affiliate -> canView()) {
			$this -> _redirect('/affiliate/index');
		}
	}

	public function indexAction() {

		$this -> _helper -> content -> setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$user_id = $viewer -> getIdentity();

		// get assoc table
		$assocTable = Engine_Api::_()->getDbtable('assoc', 'ynaffiliate');
		// start getting data
		$data = $assocTable->getClient($user_id);
//		echo '<pre>',print_r($data);die;

		$this -> view -> viewer = $viewer;
		$this -> view -> user_id = $viewer->getIdentity();
		$this -> view -> client_data = $data;
		$this -> view -> loaded_clients = count($data);

		// count total client
		$directClient = $assocTable -> countClient($user_id);
		$totalClient = $assocTable -> countAllClient($user_id);

		$this -> view -> total_client = $totalClient;
		$this -> view -> direct_client = $directClient;

		// get level option to pass to each client, so it not retrive again when recursive is running
		$authorizationTable = Engine_Api::_()->getDbtable('levels', 'authorization');
		$select = $authorizationTable->select()->where('type != ?', 'public');
		$levels = $authorizationTable->fetchAll($select);
		$levelOptions = array();
		foreach( $levels as $level ) {
			$levelOptions[$level->level_id] = $level->getTitle();
		}
		$this -> view -> levelOptions = $levelOptions;

		// get max client show to compare with direct client to show more button
		$clientLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.client.limit', 3);
		$this -> view -> client_limit = $clientLimit;
		$maxLevel = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.max.commission.level', 5);
		$this -> view -> max_level = $maxLevel;
	}
}

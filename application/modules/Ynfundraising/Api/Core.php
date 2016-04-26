<?php
class Ynfundraising_Api_Core extends Core_Api_Abstract {
	protected $_moduleName = 'Ynfundraising';
	/* ----- Override getItemTable Function----- */
	public function getItemTable($type) {
		if ($type == 'ynfundraising_campaign') {
			return Engine_Loader::getInstance ()->load ( 'Ynfundraising_Model_DbTable_Campaigns' );
		} else if ($type == 'ynfundraising_request') {
			return Engine_Loader::getInstance ()->load ( 'Ynfundraising_Model_DbTable_Requests' );
		} else {
			$class = Engine_Api::_ ()->getItemTableClass ( $type );
			return Engine_Api::_ ()->loadClass ( $class );
		}
	}
	public function getItemFromType($item) {
		$object_item = null;
		switch ($item['parent_type']) {
			case 'idea' :
				$object_item = Engine_Api::_ ()->getItem ( 'ynidea_idea', $item['parent_id']);
				break;
			case 'trophy' :
				$object_item = Engine_Api::_ ()->getItem ( 'ynidea_trophy', $item['parent_id']);
				break;
			default :
				break;
		}
		return $object_item;
	}
	/**
	 * Check Idea Box Plugin
	 */
	public function checkIdeaboxPlugin() {
		$module = 'ynidea';
		$modulesTable = Engine_Api::_ ()->getDbtable ( 'modules', 'core' );
		$mselect = $modulesTable->select ()
		->where ( 'enabled = ?', 1 )
		//->where ( 'version = ?', '4.02' )
		->where ( 'name  = ?', $module );
		$module_result = $modulesTable->fetchRow ( $mselect );
		if (count ( $module_result ) > 0) {
			return true;
		}
		return false;
	}

	/**
	 *
	 *
	 *
	 * Get parent Paginator
	 *
	 * @param array $params
	 * @return Zend_Paginator
	 */
	public function getParentPaginator($params = array()) {
		$paginator = Zend_Paginator::factory ( $this->getParents ( $params ) );
		if (! empty ( $params ['page'] )) {
			$paginator->setCurrentPageNumber ( $params ['page'] );
		}
		if (! empty ( $params ['limit'] )) {
			$paginator->setItemCountPerPage ( $params ['limit'] );
		}
		return $paginator;
	}

	/**
	 *
	 *
	 *
	 * Get getIdeas
	 *
	 * @param array $params
	 * @return Zend_Paginator
	 */
	public function getParents($param = null) {
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ();
		$array_select = array ();
		$user_id = Engine_Api::_ ()->user ()->getViewer ()->getIdentity ();
		if ($this->checkIdeaboxPlugin ()) {
			$idea_table = Engine_Api::_ ()->getDbtable ( 'ideas', 'ynidea' );
			$idea_Name = $idea_table->info ( 'name' );
			$trophy_table = Engine_Api::_ ()->getDbtable ( 'trophies', 'ynidea' );
			$trophy_Name = $trophy_table->info ( 'name' );

			$select_Idea = $idea_table->select ()->from ( $idea_Name, array (
					"parent_id" => "$idea_Name.idea_id",
					"created"   => "$idea_Name.creation_date",
					'parent_type' => new Zend_Db_Expr ( "'idea'" )
			) );
			$select_Idea->where("$idea_Name.idea_id NOT IN (SELECT parent_id FROM engine4_ynfundraising_campaigns WHERE engine4_ynfundraising_campaigns.parent_type = 'idea' AND engine4_ynfundraising_campaigns.status IN ('draft','ongoing'))")
						->where("$idea_Name.publish_status = 'publish'")
						->where("$idea_Name.idea_id NOT IN (SELECT parent_id FROM engine4_ynfundraising_requests WHERE engine4_ynfundraising_requests.parent_type = 'idea' AND engine4_ynfundraising_requests.requester_id = ? AND engine4_ynfundraising_requests.is_completed = 0)", $user_id)
						->where("$idea_Name.idea_id NOT IN (SELECT parent_id FROM engine4_ynfundraising_requests WHERE engine4_ynfundraising_requests.parent_type = 'idea' AND engine4_ynfundraising_requests.status = 'approved' AND engine4_ynfundraising_requests.is_completed = 0)");

			$select_Trophy = $trophy_table->select ()->from ( $trophy_Name, array (
					"parent_id" => "$trophy_Name.trophy_id",
					"created"   => "$trophy_Name.creation_date",
					'parent_type' => new Zend_Db_Expr ( "'trophy'" )
			) );
			$select_Trophy->where("$trophy_Name.trophy_id NOT IN (SELECT parent_id FROM engine4_ynfundraising_campaigns WHERE engine4_ynfundraising_campaigns.parent_type = 'trophy' AND engine4_ynfundraising_campaigns.status IN ('draft','ongoing'))")
						  ->where("$trophy_Name.trophy_id NOT IN (SELECT parent_id FROM engine4_ynfundraising_requests WHERE engine4_ynfundraising_requests.parent_type = 'trophy' AND engine4_ynfundraising_requests.requester_id = ? AND engine4_ynfundraising_requests.is_completed = 0)", $user_id)
						  ->where("$trophy_Name.trophy_id NOT IN (SELECT parent_id FROM engine4_ynfundraising_requests WHERE engine4_ynfundraising_requests.parent_type = 'trophy' AND engine4_ynfundraising_requests.status = 'approved' AND engine4_ynfundraising_requests.is_completed = 0)");
			if (isset ( $param ['view'] ) && $param ['view'] != "") {
				if ($param ['view'] == 0) {
					$select_Idea->where ( "user_id = ?", $user_id );
					$select_Trophy->where ( "user_id = ?", $user_id );
				} else {
					$select_Idea->where ( "user_id <> ?", $user_id );
					$select_Idea->where ( "allow_campaign = 1" );
					$select_Trophy->where ( "user_id <> ?", $user_id );
					$select_Trophy->where ( "allow_campaign = 1" );
				}
			}
			else
				{
					$select_Idea->where ( "(user_id = $user_id OR allow_campaign = 1)");
					$select_Trophy->where ("(user_id = $user_id OR allow_campaign = 1)");
				}
			if (isset ( $param ['search'] ) && $param ['search'] != "") {
				$search = $param ['search'];
				$select_Idea->where ( "title LIKE ?", "%$search%" );
				$select_Trophy->where ( "title LIKE ?", "%$search%" );
			}
			$array_select += array (
					$select_Idea,
					$select_Trophy
			);
		}
		return $select->union ( $array_select )->order("created DESC");
	}
	public function getDefaultCurrency() {
		return Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynfundraising.currency', 'USD' );
	}
	public function getDefaultCountry() {
		return Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynfundraising.country', 'VNM' );
	}

	/* ---- Get campaign paginator */
	public function getCampaignPaginator($params = array()) {
		// Get campaign paginator
		$select = $this->getCampaignSelect ( $params );

		$paginator = Zend_Paginator::factory ( $select );

		// Set current page
		if (! empty ( $params ['page'] )) {
			$paginator->setCurrentPageNumber ( $params ['page'], 1 );
		}
		// Item per page
		if (! empty ( $params ['limit'] )) {
			$paginator->setItemCountPerPage ( $params ['limit'] );
		}

		return $paginator;
	}

	public function getRequestPaginator($params = array()) {
		// Get campaign paginator
		$select = $this->getRequestSelect ( $params );
		$paginator = Zend_Paginator::factory ( $select );

		// Set current page
		if (! empty ( $params ['page'] )) {
			$paginator->setCurrentPageNumber ( $params ['page'], 1 );
		}
		if (! empty ( $params ['limit'] )) {
			$paginator->setItemCountPerPage ( $params ['limit'] );
		}

		return $paginator;
	}

	public function createPhoto($params, $file)
    {
	    if( $file instanceof Storage_Model_File )
	    {
	      $params['file_id'] = $file->getIdentity();
	    }

	    else
	    {
	      // Get image info and resize
	      $name = basename($file['tmp_name']);
	      $path = dirname($file['tmp_name']);
	      $extension = ltrim(strrchr($file['name'], '.'), '.');

	      $mainName = $path.'/m_'.$name . '.' . $extension;
	      $profileName = $path.'/p_'.$name . '.' . $extension;
	      $thumbName = $path.'/in_'.$name . '.' . $extension;
	      $iSquare = $path.'/is_'.$name . '.' . $extension;

	      $image = Engine_Image::factory();
	      $image->open($file['tmp_name'])
	          ->resize(720, 720)
	          ->write($mainName)
	          ->destroy();
	       // Resize image (profile)
	       $image = Engine_Image::factory();
	       $image->open($file['tmp_name'])
	          ->resize(240, 240)
	          ->write($profileName)
	          ->destroy();
	      $image = Engine_Image::factory();
	      $image->open($file['tmp_name'])
	          ->resize(190,190)
	          ->write($thumbName)
	          ->destroy();

	      $image = Engine_Image::factory();
	      $image->open($file['tmp_name'])
	          ->resize(48, 48)
	          ->write($iSquare)
	          ->destroy();

	      // Store photos
	      $photo_params = array(
	        'parent_id' => $params['campaign_id'],
	        'parent_type' => 'ynfundraising',
	      );
	      $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
	      $profileFile = Engine_Api::_()->storage()->create($profileName, $photo_params);
	      $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
	      $iSquare = Engine_Api::_()->storage()->create($iSquare, $photo_params);
	      $photoFile->bridge($profileFile, 'thumb.profile');
	      $photoFile->bridge($iSquare, 'thumb.icon');
	      $photoFile->bridge($thumbFile, 'thumb.normal');
	      $params['file_id'] = $photoFile->file_id; // This might be wrong
	      $params['photo_id'] = $photoFile->file_id;

	      // Remove temp files
	      @unlink($mainName);
	      @unlink($profileName);
	      @unlink($thumbName);
	      @unlink($iSquare);

	    }
	    $row = Engine_Api::_()->getDbtable('photos', 'ynfundraising')->createRow();
	    $row->setFromArray($params);
	    $row->save();
	    return $row;
  	}

	/**
  	 * @author trunglt
  	 * return select
  	 */
	public function getCampaignSelect($params = array()) {
		$campaignTbl = Engine_Api::_ ()->getDbTable ( 'campaigns', 'ynfundraising' );
		$campaignTblName = $campaignTbl->info ( 'name' );

		$donationTbl = Engine_Api::_ ()->getDbTable ( 'donations', 'ynfundraising' );
		$donationTblName = $donationTbl->info ( 'name' );

		$userTbl = Engine_Api::_ ()->getItemTable ( 'user' );
		$userTblName = $userTbl->info ( 'name' );

		$select = $campaignTbl->select ()->from($campaignTblName)->setIntegrityCheck ( false );

		$select->joinLeft($userTblName, "$userTblName.user_id = $campaignTblName.user_id", "$userTblName.displayname as owner_title");
		// Get Tagmaps table
		$tags_table = Engine_Api::_ ()->getDbtable ( 'TagMaps', 'core' );
		$tags_name = $tags_table->info ( 'name' );

		//expiry_date
		if (! empty ( $params ['expiry_date'] ) && $params ['expiry_date'] == true)
		{
			$date = date ( 'Y-m-d H:i:s' );
			$select->where("$campaignTblName.expiry_date <> '0000-00-00 00:00:00'")
				->where("$campaignTblName.expiry_date <> '1970-01-01 00:00:00'")
				->where("$campaignTblName.expiry_date <= '$date'")
				->where("$campaignTblName.status = ?",Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS);
		}

		// User id filter
		if (! empty ( $params ['user_id'] ) && is_numeric ( $params ['user_id'] )) {
			$select->where ("($campaignTblName.user_id = ?", $params ['user_id'] )
				   ->orWhere("$campaignTblName.campaign_id IN (SELECT $donationTblName.campaign_id FROM $donationTblName WHERE $donationTblName.user_id = ?))", $params ['user_id']);
		}

		// Show type filter
		if (! empty ( $params ['show'] )) {
			if ($params['show'] == 1) {
				$select->where("$campaignTblName.user_id = ?", $params ['user_id']);
			}
			else if ($params['show'] == 2) {
				$select->where("$campaignTblName.campaign_id IN (SELECT $donationTblName.campaign_id FROM $donationTblName WHERE $donationTblName.user_id = ?)", $params ['user_id']);
			}
			//$str = ( string ) (is_array ( $params ['users'] ) ? "'" . join ( "', '", $params ['users'] ) . "'" : $params ['users']);
			//$select->where ( $campaignTblName . '.owner_id in (?)', new Zend_Db_Expr ( $str ) );
		}

		// Tag filter
		if (! empty ( $params ['tag'] )) {
			$select
			->joinLeft ( $tags_name, "$tags_name.resource_id = $campaignTblName.campaign_id", "" )
			->where ( $tags_name . '.resource_type = ?', 'ynfundraising_campaign' )
			->where ( $tags_name . '.tag_id = ?', $params ['tag'] );
		}

		// Search filter
		if (!empty($params['search'])) {
			$select -> where("($campaignTblName.title LIKE ? OR $campaignTblName.short_description LIKE ?", '%' . $params['search'] . '%')
				    ->orWhere("$campaignTblName.user_id IN (SELECT engine4_users.user_id FROM engine4_users WHERE engine4_users.username LIKE ? OR engine4_users.displayname LIKE ?))", '%' . $params['search'] . '%'); // campaign owner
		}

		// Published Campaigns filter
		if (!empty($params['published'])) {
			$select -> where($campaignTblName . ".published = ?",$params['published']);
		}

		// Past Campaigns filter
		if (!empty($params['past'])) {
			$select -> where($campaignTblName . ".status <> ?", Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS);
		}

		// Title filter
		if (! empty ( $params ['title'] )) {
			$select->where ("($campaignTblName.title LIKE ?", '%' . $params ['title'] . '%' )
			->orWhere("$campaignTblName.user_id IN (SELECT engine4_users.user_id FROM engine4_users WHERE engine4_users.username LIKE ? OR engine4_users.displayname LIKE ?))", '%' . $params['title'] . '%'); // campaign owner
		}

		// From date
		if (!empty($params['start_date'])) {
			$fromdate = $this->getFromDateSearch($params['start_date']);
			if (!$fromdate) {
				$select->where("false");
				return $select;
			}
			$select->where("($campaignTblName.creation_date >= ?)", $fromdate);
		}

		// To date
		if (!empty($params['end_date'])) {
			$todate = $this->getToDateSearch($params['end_date']);
			if (!$todate) {
				$select->where("false");
				return $select;
			}
			//$select = $this->_selectEventsToDate($select, $todate);
			$select->where("($campaignTblName.creation_date <= ?)", $todate);

		}

		if (! empty ( $params ['parent_type'] )) {
			$parent_type = $params ['parent_type'];
			$select->where ( $campaignTblName . ".parent_type IN ($parent_type)");
		}

		// Type filter
		if (! empty ( $params ['type'] )) {
			$select->where ( $campaignTblName . ".parent_type = ?", $params ['type'] );
		}

		// Status filter
		if (! empty ( $params ['status'] )) {
			$select->where ( $campaignTblName . ".status = ?", $params ['status'] );
		}

		// Feature campaign filter
		if (isset ( $params ['featured'] ) && $params ['featured'] != '') {
			$select->where ( "$campaignTblName.is_featured = ?", $params ['featured'] );
		}
		
		// Activated campaign filter
		if (!isset($params['mycontest'])){			
			$select->where("activated = 1");		
		}

		// Order by filter
		// Select campaign
		if (! isset ( $params ['direction'] ))
			$params ['direction'] = "DESC";
		if (! isset ( $params ['orderby'] ))
			$params ['orderby'] = "campaign_id";
		$select->order ( ! empty ( $params ['orderby'] ) ?  $params ['orderby'] . ' ' . $params ['direction'] : 'campaign_id ' . $params ['direction'] );


		// Limit option
		if (! empty ( $params ['limit'] )) {
			$select->limit ( $params ['limit'] );
		}

		

		return $select;
	}

	/**
  	 * @author trunglt
  	 * return request select
  	 */
	public function getRequestSelect($params = array()) {
		$requestTbl = Engine_Api::_ ()->getDbTable ( 'requests', 'ynfundraising' );
		$requestTblName = $requestTbl->info ( 'name' );

		$idea_table = Engine_Api::_ ()->getDbtable ( 'ideas', 'ynidea' );
		$idea_Name = $idea_table->info ( 'name' );

		$trophy_table = Engine_Api::_ ()->getDbtable ( 'trophies', 'ynidea' );
		$trophy_Name = $trophy_table->info ( 'name' );

		$userTbl = Engine_Api::_ ()->getItemTable ( 'user');
		$userTblName = $userTbl->info ( 'name' );

		$select = $requestTbl->select ();

		if (! isset ( $params ['direction'] )) {
			$params ['direction'] = "DESC";
		}

		// Campaign filter
		if (!empty ( $params ['parent_id'] ) && is_numeric ( $params ['parent_id'] ) && !empty ( $params ['parent_type'] )) {
			$select->where ( $requestTblName . '.parent_id = ?', $params ['parent_id'] );
			$select->where ( $requestTblName . '.parent_type = ?', $params ['parent_type'] );
		}
		// Request does not completed
		if (!empty($params ['is_completed']) && ($params ['is_completed'] == 0)) {
			$select->where ( $requestTblName . '.is_completed = ?', 0 );
		}
		// User id filter
		if (! empty ( $params ['requester_id'] ) && is_numeric ( $params ['requester_id'] )) {
			// on My Requests page
			$select->where ( $requestTblName . '.requester_id = ?', $params ['requester_id'] );
		}
		elseif(!empty($params['is_manage'])) {
			// on Manage Requests page
			$select->where("$requestTblName.visible = 1");
		}

		// Title filter
		if (! empty ( $params ['title'] )) {
			if ( empty ( $params ['requester_id'] ) && (!empty($params['is_manage']))) {
				$select->where("(($requestTblName.parent_type = 'idea' AND $requestTblName.parent_id IN (SELECT idea_id FROM  $idea_Name WHERE $idea_Name.title like ? AND $idea_Name.user_id = {$params['user_id']} ))", '%' . $params['title'] . '%');
				$select->orwhere("($requestTblName.parent_type = 'trophy' AND $requestTblName.parent_id IN (SELECT trophy_id FROM  $trophy_Name WHERE $trophy_Name.title like ? AND $trophy_Name.user_id = {$params['user_id']})))",  '%' . $params['title'] . '%');
			}
			else {
				$select->where("(($requestTblName.parent_type = 'idea' AND $requestTblName.parent_id IN (SELECT idea_id FROM  $idea_Name WHERE $idea_Name.title like ?))", '%' . $params['title'] . '%');
				$select->orwhere("($requestTblName.parent_type = 'trophy' AND $requestTblName.parent_id IN (SELECT trophy_id FROM  $trophy_Name WHERE $trophy_Name.title like ?)))",  '%' . $params['title'] . '%');
			}
		}
		else {
			if ( empty ( $params ['requester_id'] ) && (!empty($params['is_manage']))) {
				$select->where("(($requestTblName.parent_type = 'idea' AND $requestTblName.parent_id IN (SELECT idea_id FROM  $idea_Name WHERE $idea_Name.user_id = {$params['user_id']} ))", '%' . $params['title'] . '%');
				$select->orwhere("($requestTblName.parent_type = 'trophy' AND $requestTblName.parent_id IN (SELECT trophy_id FROM  $trophy_Name WHERE $trophy_Name.user_id = {$params['user_id']})))",  '%' . $params['title'] . '%');
			}
		}

		// From date
		if (!empty($params['start_date'])) {
			$fromdate = $this->getFromDateSearch($params['start_date']);
			if (!$fromdate) {
				$select->where("false");
				return $select;
			}
			$select->where("($requestTblName.request_date >= ?)", $fromdate);
		}

		// To date
		if (!empty($params['end_date'])) {
			$todate = $this->getToDateSearch($params['end_date']);
			if (!$todate) {
				$select->where("false");
				return $select;
			}
			$select->where("($requestTblName.request_date <= ?)", $todate);

		}

		// Status filter
		if (! empty ( $params ['status'] )) {
			$select->where ( $requestTblName . ".status = ?", $params ['status'] );
		}

		$select->order("$requestTblName.request_id DESC");
		// Return query
		//echo $select; die;
		return $select;
	}

	public function getDateSearch($month, $year) {

		$user_tz = date_default_timezone_get();
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity()) {
			$user_tz = $viewer->timezone;
		}
		$oldTz = date_default_timezone_get();
		date_default_timezone_set($user_tz);
		$first_date = $year . '-' . $month . '-01';
		$firstDateObject = new Zend_Date(strtotime($first_date));
		$lastDateObject = new Zend_Date(strtotime($first_date));
		$lastDateObject->add('1', Zend_Date::MONTH);
		$lastDateObject->sub('1', Zend_Date::SECOND);
		date_default_timezone_set($oldTz);

		// convert to server time zone to search in database
		$firstDateObject->setTimezone(date_default_timezone_get());
		$lastDateObject->setTimezone(date_default_timezone_get());
		$first_date = $firstDateObject->get('yyyy-MM-dd HH:mm:ss');
		$last_date = $lastDateObject->get('yyyy-MM-dd HH:mm:ss');
		$date_search = array($first_date, $last_date);
		return $date_search;
		//          var_dump($date_search);
		//          die();
	}

	public function getFromDateSearch($day) {
		$day = $day . " 00:00:00";

		$user_tz = date_default_timezone_get();
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity()) {
			$user_tz = $viewer->timezone;
		}
		$oldTz = date_default_timezone_get();
		date_default_timezone_set($user_tz);
		$start = strtotime($day);

		date_default_timezone_set($oldTz);
		$fromdate = date('Y-m-d H:i:s', $start);

		//        echo $fromdate;
		//        die;

		return $fromdate;
	}

	public function getToDateSearch($day) {
		$user_tz = date_default_timezone_get();
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity()) {
			$user_tz = $viewer->timezone;
		}
		$oldTz = date_default_timezone_get();
		//user time zone
		date_default_timezone_set($user_tz);
		$d_temp = strtotime($day);
		if ($d_temp == false) {
			return null;
		}
		$toDateObject = new Zend_Date(strtotime($day));

		$toDateObject->add('1', Zend_Date::DAY);
		$toDateObject->sub('1', Zend_Date::SECOND);
		date_default_timezone_set($oldTz);
		$toDateObject->setTimezone(date_default_timezone_get());
		return $todate = $toDateObject->get('yyyy-MM-dd HH:mm:ss');
	}
	public function getTotalCampaigns($status = null)
	{
		$campaignTbl = Engine_Api::_ ()->getDbTable ( 'campaigns', 'ynfundraising' );
		$campaignTblName = $campaignTbl->info ( 'name' );
		$select = $campaignTbl->select()->from($campaignTblName, "Count(*) as total");
		if($status)
			$select->where('status = ?',$status);
		$total = $campaignTbl->fetchRow($select);
		if($total)
			return $total['total'];
		else
			return 0;
	}
	public function insertSupporter($backID = 0, $campaign_id = 0)
	{
		if($backID > 0)
		{
			$supporterTbl = Engine_Api::_ ()->getDbTable ( 'supporters', 'ynfundraising' );
			$supporterTblName = $supporterTbl->info ( 'name' );
			$select = $supporterTbl->select()->from($supporterTblName)
					->where("user_id = ?", $backID)
					->where("campaign_id = ?", $campaign_id);
			$supporter = $supporterTbl->fetchRow($select);
			if($supporter)
			{
				$supporter->click_count ++;
				$supporter->save();
			}
			else {
				$supporter = $supporterTbl->createRow();
				$supporter->user_id = $backID;
				$supporter->campaign_id = $campaign_id;
				$supporter->click_count = 1;
				$supporter->save();
				$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
				$action = @Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($supporter, $campaign, 'ynfundraising_new_supporter');
				if($action != null )
				{
					 Engine_Api::_()->getDbtable('actions','activity')->attachActivity($action, $campaign);
				}
			}
		}
	}
	/**
	 * Return select
	 *
	 */
	 public function getSupporterSelect($params = array())
	 {
	 	$supporterTbl = Engine_Api::_ ()->getDbTable ( 'supporters', 'ynfundraising' );
		$supporterTblName = $supporterTbl->info ( 'name' );
		$select = $supporterTbl->select()->from($supporterTblName);
		$select->setIntegrityCheck ( false );
		$select->join("engine4_users","engine4_users.user_id = $supporterTblName.user_id","");
		if(! empty ( $params ['campaign'] ))
			$select->where("campaign_id = ?", $params ['campaign']);
		if(! empty ( $params ['name'] ) && $params ['name'] != "")
		{
			$name = $params ['name'];
			$select->where("(username like '%$name%' OR  displayname like '%$name%')");
		}
		return $select;
	 }
	 /* ---- Get supporter paginator */
	 public function getSupporterPaginator($params = array()) {
		// Get campaign paginator
		$select = $this->getSupporterSelect ( $params );
		$paginator = Zend_Paginator::factory ( $select );

		// Set current page
		if (! empty ( $params ['page'] )) {
			$paginator->setCurrentPageNumber ( $params ['page'], 1 );
		}
		if (! empty ( $params ['limit'] )) {
			$paginator->setItemCountPerPage ( $params ['limit'] );
		}
		return $paginator;
	}
	/**
	 * Return select
	 *
	 */
	 public function getDonorSelect($params = array())
	 {
	 	$donationTbl = Engine_Api::_ ()->getDbTable ( 'donations', 'ynfundraising' );
		$donationTblName = $donationTbl->info ( 'name' );
		$select = $donationTbl->select()->distinct()->from($donationTblName, "$donationTblName.user_id, guest_email, guest_name, sum(amount) as total_amount");
		$select->setIntegrityCheck ( false );
		if(! empty ( $params ['campaign'] ))
			$select->where("campaign_id = ?", $params ['campaign']);
		if(! empty ( $params ['top'] ))
		{
			$select->join("engine4_users","engine4_users.user_id = $donationTblName.user_id","");
			$select->where("$donationTblName.user_id > 0");
		}
		else
			$select->joinLeft("engine4_users","engine4_users.user_id = $donationTblName.user_id","");
		if(! empty ( $params ['name'] ) && $params ['name'] != "")
		{
			$name = $params ['name'];
			$select->where("(username like '%$name%' OR  displayname like '%$name%')");
		}
		$select->where("$donationTblName.status = 1");
		//$select->where("$donationTblName.user_id > 0");
		$select->order("sum(amount) DESC");
		$select->group("$donationTblName.user_id");
		$select->group("$donationTblName.guest_email");
		$select->group("$donationTblName.guest_name");

		return $select;
	 }
	 /* ---- Get supporter paginator */
	 public function getDonorPaginator($params = array()) {
		// Get campaign paginator
		$select = $this->getDonorSelect ( $params );
		$paginator = Zend_Paginator::factory ( $select );

		// Set current page
		if (! empty ( $params ['page'] )) {
			$paginator->setCurrentPageNumber ( $params ['page'], 1 );
		}
		if (! empty ( $params ['limit'] )) {
			$paginator->setItemCountPerPage ( $params ['limit'] );
		}
		return $paginator;
	}
	public function getAvgUserRating($user_id = 0)
	{
		$campaignRatingTbl = Engine_Api::_ ()->getDbTable ( 'campaignRatings', 'ynfundraising' );
		$camapignRatingTblName = $campaignRatingTbl->info ( 'name' );
		$campaignTbl = Engine_Api::_ ()->getDbTable ( 'campaigns', 'ynfundraising' );
		$campaignTblName = $campaignTbl->info ( 'name' );
		$select = $campaignRatingTbl->select()->from($camapignRatingTblName, "SUM(rate_number)/Count(*) as avg")
			->joinLeft("$campaignTblName","$campaignTblName.campaign_id = $camapignRatingTblName.campaign_id","")
			->where("$campaignTblName.user_id = ?",$user_id);

		$avg = $campaignRatingTbl->fetchRow($select);
		if($avg['avg'])
		{
			return $avg['avg'];
		}
		else
			return 0;

	}
	public function getAvgCampaignRating($campaign_id = 0)
	{
		$campaignRatingTbl = Engine_Api::_ ()->getDbTable ( 'campaignRatings', 'ynfundraising' );
		$camapignRatingTblName = $campaignRatingTbl->info ( 'name' );
		$select = $campaignRatingTbl->select()->from($camapignRatingTblName, "SUM(rate_number)/Count(*) as avg")->where("campaign_id = ?",$campaign_id);
		$avg = $campaignRatingTbl->fetchRow($select);
		if($avg['avg'])
		{
			return $avg['avg'];
		}
		else
			return 0;

	}
	public function getTotalRating($user_id = 0)
	{
		$campaignRatingTbl = Engine_Api::_ ()->getDbTable ( 'campaignRatings', 'ynfundraising' );
		$camapignRatingTblName = $campaignRatingTbl->info ( 'name' );
		$campaignTbl = Engine_Api::_ ()->getDbTable ( 'campaigns', 'ynfundraising' );
		$campaignTblName = $campaignTbl->info ( 'name' );
		$select = $campaignRatingTbl->select()->from($camapignRatingTblName, "Count(*) as total")
			->joinLeft("$campaignTblName","$campaignTblName.campaign_id = $camapignRatingTblName.campaign_id","")
			->where("$campaignTblName.user_id = ?",$user_id);

		$avg = $campaignRatingTbl->fetchRow($select);
		if($avg['total'])
		{
			return $avg['total'];
		}
		else
			return 0;

	}
	public function getTotalRatingCampaign($campaign_id = 0)
	{
		$campaignRatingTbl = Engine_Api::_ ()->getDbTable ( 'campaignRatings', 'ynfundraising' );
		$campaignRatingTblName = $campaignRatingTbl->info ( 'name' );
		$select = $campaignRatingTbl->select()->from($campaignRatingTblName, "Count(*) as total")->where("campaign_id = ?",$campaign_id);
		$avg = $campaignRatingTbl->fetchRow($select);
		if($avg['total'])
		{
			return $avg['total'];
		}
		else
			return 0;

	}
	public function getTotalCampaign($user_id = 0)
	{
		$campaignTbl = Engine_Api::_ ()->getDbTable ( 'campaigns', 'ynfundraising' );
		$campaignTblName = $campaignTbl->info ( 'name' );
		$select = $campaignTbl->select()->from($campaignTblName, "Count(*) as total")->where("user_id = ?",$user_id);
		$avg = $campaignTbl->fetchRow($select);
		if($avg['total'])
		{
			return $avg['total'];
		}
		else
			return 0;

	}

	public function checkCampaignRating($campaign_id = 0, $viewer_id = 0)
	{
		$campaignRatingTbl = Engine_Api::_ ()->getDbTable ( 'campaignRatings', 'ynfundraising' );
		$campaignRatingTblName = $campaignRatingTbl->info ( 'name' );
		$select = $campaignRatingTbl->select()->from($campaignRatingTblName)->where("campaign_id = ?",$campaign_id)
								->where('poster_id = ?', $viewer_id);
		$rate = $campaignRatingTbl->fetchRow($select);
		if($rate)
		{
			return false;
		}
		else
			return true;

	}
	/**
	 * Return select
	 *
	 */
	 public function getNewsSelect($params = array())
	 {
	 	$newsTbl = Engine_Api::_ ()->getDbTable ('news', 'ynfundraising');
		$newsTblName = $newsTbl->info ( 'name' );
		$select = $newsTbl->select()->from($newsTblName);
		if(! empty ( $params ['campaign_id'] ))
			$select->where("campaign_id = ?", $params ['campaign_id']);
		$select->order('creation_date DESC');
		return $select;
	 }
	 /* ---- Get news paginator */
	 public function getNewsPaginator($params = array()) {
		// Get news paginator
		$select = $this->getNewsSelect($params);
		$paginator = Zend_Paginator::factory ( $select );

		// Set current page
		if (! empty ( $params ['page'] )) {
			$paginator->setCurrentPageNumber ( $params ['page'], 1 );
		}
		if (! empty ( $params ['limit'] )) {
			$paginator->setItemCountPerPage ( $params ['limit'] );
		}
		return $paginator;
	}


	public function getFollow($user_id = null, $campaign_id = null)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $followTable = Engine_Api::_()->getDbtable('follows', 'ynfundraising');
        $select = $followTable->select()
        ->where('campaign_id = ?', $campaign_id)
        ->where('user_id = ?',$user_id);
        $row = $followTable->fetchRow($select);
        return $row;
    }
	/*
	 * Check if there is a request that satifies the condition
	 * return 0 if no row founds
	 */
	public function checkOtherRequestStatus($request, $status) {
		$requestTbl = Engine_Api::_ ()->getItemTable ( 'ynfundraising_request' );
		$select = $requestTbl->select ()->where ( 'request_id <> ?', $request->getIdentity () )
		->where ( 'parent_type = ?', $request->parent_type )
		->where ( 'parent_id = ?', $request->parent_id )
		->where('status = ?', $status);

		if (count($requestTbl->fetchAll($select)) == 0) {
			return true;
		}
		return false;
	}

	//get all request with status approved and (not exists campaign or campaign not published)
	public function getAllRequestsTimeOut()
	{
		// should or should not
		date_default_timezone_set('UTC');
		$requestTbl = Engine_Api::_ ()->getItemTable ( 'ynfundraising_request' );
		$requestTblName = $requestTbl->info ( 'name' );
		$date = date ( 'Y-m-d H:i:s' );
		$time_out = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfundraising.timeout', '');
		if($time_out)
		{
			$select = $requestTbl->select ($requestTblName);
			$select->where("$requestTblName.status = ?", Ynfundraising_Plugin_Constants::REQUEST_APPROVED_STATUS)
					->where("DATE_SUB('$date', INTERVAL ? HOUR) > $requestTblName.approved_date", $time_out);
			return $requestTbl->fetchAll($select);
		}
		else
			return array();
	}
	/**
	 *
	 * get Latest Anonymous
	 */
	 public function getLatestAnonymous($user_id = 0, $campaign_id = 0)
	{
		$donationTbl = Engine_Api::_ ()->getItemTable ( 'ynfundraising_donation' );
		$donationTblName = $donationTbl->info ( 'name' );

		$select = $donationTbl->select()->where("user_id = ?", $user_id)
						->where('campaign_id = ?', $campaign_id)->order("donation_date DESC");
		return $donationTbl->fetchRow($select);
	}
	/**
	 *
	 * get Guest Anonymous
	 */
	 public function getGuestAnonymous($guest_name = "", $guest_email = "", $campaign_id = 0)
	{
		$donationTbl = Engine_Api::_ ()->getItemTable ( 'ynfundraising_donation' );
		$donationTblName = $donationTbl->info ( 'name' );

		$select = $donationTbl->select()->where("guest_email = ?", $guest_email)
								->where("guest_name = ?", $guest_name)
								->where('campaign_id = ?', $campaign_id)->order("donation_date DESC");
		return $donationTbl->fetchRow($select);
	}
	public function getTotalCampaignForDonor($user_id = 0)
	{
		$donationTbl = Engine_Api::_ ()->getItemTable ( 'ynfundraising_donation' );
		$donationTblName = $donationTbl->info ( 'name' );

		$select = $donationTbl->select()->distinct()->from($donationTblName,"$donationTblName.campaign_id")
		->where("$donationTblName.user_id = ?", $user_id)
		->where("$donationTblName.status = 1");
		$campaignIds = $donationTbl->fetchAll($select);
		return count($campaignIds);
	}

	/**
	 * get followers
	 */

	public function getFollowers($campaign_id) {
		$followTbl = Engine_Api::_()->getItemTable('ynfundraising_follow');
		$followTblName = $followTbl->info('name');
		$select = $followTbl->select()->where("campaign_id = ?", $campaign_id);

		return $followTbl->fetchAll($select);

	}

	public function getDonationPaginator($params = array()) {
		// Get campaign paginator
		$select = $this->getDonationSelect ( $params );
		$paginator = Zend_Paginator::factory ( $select );

		// Set current page
		if (! empty ( $params ['page'] )) {
			$paginator->setCurrentPageNumber ( $params ['page'], 1 );
		}
		if (! empty ( $params ['limit'] )) {
			$paginator->setItemCountPerPage ( $params ['limit'] );
		}

		return $paginator;
	}

	/**
	 * @author trunglt
	 * return select
	 */
	public function getDonationSelect($params = array()) {
		$userTbl = Engine_Api::_ ()->getDbTable ( 'users', 'user' );
		$userTblName = $userTbl->info ( 'name' );
		$donationTbl = Engine_Api::_ ()->getDbTable ( 'donations', 'ynfundraising' );
		$donationTblName = $donationTbl->info ( 'name' );

		$campaignTbl = Engine_Api::_ ()->getDbTable ( 'campaigns', 'ynfundraising' );
		$campaignTblName = $campaignTbl->info ( 'name' );

		//print_r($params);die;
		$select = $donationTbl->select ();
		$select->from($donationTblName);
		$select->setIntegrityCheck ( false )
		->joinLeft ( $userTblName, "$userTblName.user_id = $donationTblName.user_id","$userTblName.displayname as owner_title")
		->joinLeft ( $campaignTblName, "$campaignTblName.campaign_id = $donationTblName.campaign_id","$campaignTblName.title as campaign_title");

		$select->where("$donationTblName.status = ?", 1);
		// Search filter
		if (!empty($params['search'])) {
			// search email
			$text = '%'.preg_replace('/\s+/', '%',$params['search']).'%';
			$select->where("($donationTblName.payer_email LIKE ?", $text) // payer email
				   ->orWhere("$userTblName.username LIKE ? OR $userTblName.displayname LIKE ?", $text) // donor using username and displayname
				   ->orWhere("$donationTblName.guest_name LIKE ?", $text) // donor using guest name if has any
				   ->orWhere("$donationTblName.transaction_id LIKE ?", $text) // transaction id
				   ->orWhere("$campaignTblName.user_id IN (
				   		SELECT $userTblName.user_id FROM $userTblName WHERE $userTblName.username LIKE ? OR  $userTblName.displayname LIKE ? )"
				   		, $text); // owner of campaign
			if (is_numeric($params['search'])) {
				// will be remove later
				$select->orWhere("$donationTblName.amount = ?", $params['search']);
			}
			$select->orWhere("$donationTblName.campaign_id IN (
				   		SELECT engine4_ynfundraising_campaigns.campaign_id FROM engine4_ynfundraising_campaigns WHERE engine4_ynfundraising_campaigns.title LIKE ?))", $text
				   	); // campaign title
		}



		// From date
		if (!empty($params['start_date'])) {
			$fromdate = $this->getFromDateSearch($params['start_date']);
			if (!$fromdate) {
				$select->where("false");
				return $select;
			}
			$select->where("($donationTblName.donation_date >= ?)", $fromdate);
		}

		// To date
		if (!empty($params['end_date'])) {
			$todate = $this->getToDateSearch($params['end_date']);
			if (!$todate) {
				$select->where("false");
				return $select;
			}
			//$select = $this->_selectEventsToDate($select, $todate);
			$select->where("($donationTblName.donation_date <= ?)", $todate);

		}

		// User id filter
		if (! empty ( $params ['donation_id'] ) && is_numeric ( $params ['donation_id'] )) {
			$select->where ( $donationTblName . '.donation_id = ?', $params ['donation_id'] );
		}
		if (! empty ( $params ['user_id'] ) && is_numeric ( $params ['user_id'] )) {
			//$select->where ( $donationTblName . '.user_id = ?', $params ['user_id'] );
		}
		if (!empty($params['campaign_id']) && is_numeric ( $params ['campaign_id'])) {
			$select->where ( $donationTblName . '.campaign_id = ?', $params ['campaign_id'] );
		}

		// Order by filter
		// Select transaction
		if (! isset ( $params ['direction'] ))
			$params ['direction'] = "DESC";
		if (! isset ( $params ['orderby'] ))
			$params ['orderby'] = "donation_id";
		$select->order ( ! empty ( $params ['orderby'] ) ? $params ['orderby'] . ' ' . $params ['direction'] :  'donation_id ' . $params ['direction'] );


		// Limit option
		if (! empty ( $params ['limit'] )) {
			$select->limit ( $params ['limit'] );
		}
		//echo $select;
		return $select;
	}
	public static function partialViewFullPath($partialTemplateFile)
	 {
		$ds = DIRECTORY_SEPARATOR;
		return "application{$ds}modules{$ds}Ynfundraising{$ds}views{$ds}scripts{$ds}{$partialTemplateFile}";
  	 }

  	 public function shortenTitle($str, $length = 64)
  	 {
  	 	$str = preg_replace('/<br\s*\/>/', ' - ', $str);
  	 	$str = preg_replace('/<a\s+[^>]+>(.*?)<\/a>/im', '', $str);
  	 	$str = strip_tags($str);
  	 	$str = trim($str, ' - ');
  	 	$str = str_replace('--', '-', $str);
  	 	$result = (strlen($str) > $length) ? substr($str, 0, $length - 3) : $str;
  	 	if (strlen($str) > $length) {
  	 		$result = preg_replace('/ [^ ]*$/', ' ...', $result);
  	 	}
  	 	return $result;
  	 }

}
?>

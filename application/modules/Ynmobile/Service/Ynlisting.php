<?php

class Ynmobile_Service_Ynlisting extends Ynmobile_Service_Base{
	
	/**
     * main module name.
     * @var string
     */
    protected $module = 'ynlistings';
    /**
     * @main item type 
     */
    protected $mainItemType = 'ynlistings_listing';
	
	
	
	public function fetch($aData){
		extract($aData);
		
		$searchParams = $this->mapListingSearchFields($aData);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		if($aData['sView'] == 'my'){
			
			$searchParams['user_id'] = $viewer -> getIdentity();
        	$searchParams['direction'] = 'DESC';
		}
		
		$select = Engine_Api::_() -> getItemTable('ynlistings_listing') -> getListingsSelect($searchParams);

		return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields=array('listing'));
	}
	
	public function detail($aData){
		extract($aData);
		
		$iListingId = intval($iListingId);
		
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $iListingId);
		
		
		if(!$listing){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Listing not found!")
			);
		}
		
		return Ynmobile_AppMeta::_export_one($listing, array('infos'));
		
	}	
	
	function __getCategoryOptions(){
		return Engine_Api::_() -> getDbTable('categories', 'ynlistings')->getCategories();
	}
	
	public function addPhotoOptions(){
		return $this->getPrivacyOptions($this->mainItemType, 'add_photos');
	}
	
	public function addVideoOptions(){
		return $this->getPrivacyOptions($this->mainItemType, 'add_videos');
	}
	
	public function addDiscussionOptions(){
		return $this->getPrivacyOptions($this->mainItemType, 'add_discussions');
	}
	
	public function printingOptions(){
		return $this->getPrivacyOptions($this->mainItemType, 'printing');
	}
	
	public function sharingOptions(){
		return $this->getPrivacyOptions($this->mainItemType, 'sharing');
	}
	
	
	public function formadd($aData){
		extract($aData);
		
		$auth =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');
		
		$canCreate  =  $auth ->setAuthParams('ynjobposting_job', null, 'create')->checkRequire();
		
		if(!$canCreate){
			return array(
				'error_code'=>1,
				'error_message'=> Zend_Registry::get('Zend_Translate')->_("You don't have permission to create listing"),
			);
		}
		
		$categoryOptions  = $this->categories();
		
		array_shift($categoryOptions);
		
		return array(
			'categoryOptions'=>$categoryOptions,
			'currencyOptions'=>$this->currencies(),
			'viewOptions' => $this -> viewOptions(),
			'commentOptions' => $this -> commentOptions(),
			'photoOptions'=>$this->addPhotoOptions(),
			'videoOptions'=>$this->addVideoOptions(),
			'discussionOptions'=>$this->addDiscussionOptions(),
			'printingOptions'=>$this->addVideoOptions(),
			'sharingOptions'=>$this->sharingOptions(),
		);
	}

	public function formedit($aData){
		extract($aData);
		$iListingId =  intval($iListingId);
		
		$listing = Engine_Api::_()->getItem('ynlistings_listing', $iListingId);
		$translate  =  Zend_Registry::get('Zend_Translate');
		
		if(!$listing){
			return array(
				'error_code'=>1,
				'error_message'=>$translate->_('The page you have attempted to access could not be found'),
			);
		}
        
        if (!$listing->isEditable()) {
        	return array(
				'error_code'=>1,
				'error_message'=>$translate->_('You do not have permission to edit this listing.'),
			);
        }

		$categoryOptions  = $this->categories();
		
		array_shift($categoryOptions);

		
		return array(
			'aItem'=>Ynmobile_AppMeta::_export_one($listing, array('edit')),
			'categoryOptions'=>$categoryOptions,
			'currencyOptions'=>$this->currencies(),
			'viewOptions' => $this -> viewOptions(),
			'commentOptions' => $this -> commentOptions(),
			'photoOptions'=>$this->addPhotoOptions(),
			'videoOptions'=>$this->addVideoOptions(),
			'discussionOptions'=>$this->addDiscussionOptions(),
			'printingOptions'=>$this->addVideoOptions(),
			'sharingOptions'=>$this->sharingOptions(),
		);
	}

	public function viewOptions(){
		//view_listings
		return $this->getPrivacyOptions($this->mainItemType, 'view_listings');
	}
	
	public function formsearch($aData){
		$categoryFields = Engine_Api::_()->fields()
			->getFieldsObjectsByAlias('ynlistings_listing', 'profile_type');
    
	    if( count($categoryFields) !== 1 || !isset($categoryFields['profile_type']) ) {
	    	return array();
		}

	    $categoryField= $categoryFields['profile_type'];
	    
	    $options = $categoryField->getOptions();
		
		return array(
			'categoryOptions'=>$this->categories(),
			'currencyOptions'=>$this->currencies(),
		);
	}
	
	public function categories(){
		$categoryOptions =  array();
		
		foreach($this->__getCategoryOptions() as $row){
			$categoryOptions[] = array(
				'id'=>$row['category_id'],
				'title'=>str_repeat("-- ", $row['level'] - 1).$row['title'],
			);
		}

		return $categoryOptions;
	}
	
	public function currencies(){
		
		$currencyOptions =  array();
		foreach($this->getCurrencyOptions() as $k=>$v){
			$currencyOptions[] = array(
				'id'=>$k,
				'title'=>$v,
				'is_default'=>($k == 'USD')?1:0,
			);
		}
		return $currencyOptions;
	}
	
	public function getCurrencyOptions(){
		//populate currency
        $supportedCurrencies = array();
        $gateways = array();
        $gatewaysTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        foreach ($gatewaysTable->fetchAll() as $gateway) {
            $gateways[$gateway -> gateway_id] = $gateway -> title;
            $gatewayObject = $gateway -> getGateway();
            $currencies = $gatewayObject -> getSupportedCurrencies();
            
            if (empty($currencies)) {
                continue;
            }
            $supportedCurrencyIndex[$gateway -> title] = $currencies;
            if (empty($fullySupportedCurrencies)) {
                $fullySupportedCurrencies = $currencies;
            }
            else {
                $fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
            }
            $supportedCurrencies = array_merge($supportedCurrencies, $currencies);
        }
        $supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);
		
		return array_merge(array_combine($fullySupportedCurrencies,$fullySupportedCurrencies), array_combine($supportedCurrencies,$supportedCurrencies));
	}

	public function mapAddListingFields($aData){
		
		/**
		 * bIsEnd: "1",
			iCategoryId: 2,
			sAboutUs: "about",
			sAuthView: "everyone",
			sCurrencyId: "SEK",
			sDescription: "des",
			sPrice: 2,
			sShortDescription: "shortdes"
			sTitle: "title",
			end_date: "2015-03-10",
			end_time: "15:07:00",
			sLocation
		 */
		$keys =  array(
			'iCategoryId'=>'category_id',
			'sAboutUs'=>'about_us',
			'sDescription'=>'description',
			'sTitle'=> 'title',
			'sPrice'=> 'price',
			'sShortDescription'=> 'short_description',
			'sLocation'=>'location',
			'sLat'=>'latitude',
			'sLong'=>'longitude',
			'sAuthView'=>'view',
			'sCurrencyId'=>'currency', // data URI
			'bIsEnd' =>'is_end',// data URI,
			'sTags'=>'tags',
		);
		
		$values = array();
		foreach($keys as $from=>$to){
			if(isset($aData[$from])){
				$values[$to] =  $aData[$from];
			}else{
				$values[$to] = "";
			}
		}
		
		if($values['is_end'] == '0'){
			$values['end_date'] = null;
		}else{
			$values['end_date'] =  $aData['end_date'] . ' '. $aData['end_time'];	
		}
		
		$values['description'] =  html_entity_decode($values['description']);
		$values['about_us'] =  html_entity_decode($values['about_us']);
		$values['short_description'] =  html_entity_decode($values['short_description']);
		
		
		return $values;
	}

	
	public function add($aData){

		extract($aData);
		$viewer =  $this->getViewer();
		
		$auth =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');
		
		$canCreate  =  $auth ->setAuthParams('ynjobposting_job', null, 'create')->checkRequire();
		
		if(!$canCreate){
			return array(
				'error_code'=>1,
				'error_message'=> Zend_Registry::get('Zend_Translate')->_("You don't have permission to create listing"),
			);
		}
		
		$tableCategory = Engine_Api::_()->getItemTable('ynlistings_category');
        
        // Check max of listings can be add.
        $table = Engine_Api::_() -> getDbtable('listings', 'ynlistings');
        $select = $table->select()->where('user_id = ?', $viewer->getIdentity());
        $count_listings = count($table->fetchAll($select));
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max_listings_auth = $permissionsTable->getAllowed('ynlistings_listing', $viewer->level_id, 'max_listings');
        if ($max_listings_auth == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynlistings_listing')
                ->where('name = ?', 'max_listings'));
            if ($row) {
                $max_listings_auth = $row->value;
            }
        }
        $categories = Engine_Api::_() -> getItemTable('ynlistings_category') -> getCategories();
 		$firstCategory = $categories[1];
		
		$category_id =  intval($iCategoryId);
		
		if(!$category_id){
			$category_id =  $firstCategory->category_id;
		}
		        
        $can_select_theme = $auth->setAuthParams('ynlistings_listing', null, 'select_theme') -> checkRequire();
		
        
        //check max of listings can be add
        if ($max_listings_auth > 0 && $count_listings >= $max_listings_auth) {
			return array(
				'error_code'=> 1,
				'error_message' => Zend_Registry::get('Zend_Translate') 
					->_('Number of your listings is maximum. Please delete some listings for creating new.'),
			);
       
        }
        
		// Populate category list.
		$categories = Engine_Api::_() -> getItemTable('ynlistings_category') -> getCategories();
		unset($categories[0]);
		
		//populate category
		if(!$category_id)
		{
			return array(
				'error_code'=> 1,
				'error_message' => Zend_Registry::get('Zend_Translate') 
					->_('Create listing require at least one category. Please contact admin for more details.'),
			);
		}
		
		// Check method and data validity.
		
		
		$values = $this->mapAddListingFields($aData);
		$values['user_id'] = $viewer -> getIdentity();

		if ($values['is_end'] == '1')
		{
			$oldTz = date_default_timezone_get();
			date_default_timezone_set($viewer -> timezone);
			$end = strtotime($values['end_date']);
			date_default_timezone_set($oldTz);
			$values['end_date'] = date('Y-m-d H:i:s', $end);
			$now = date('Y-m-d H:i:s');
			if (strtotime($now) > strtotime($values['end_date']))
			{
				return array(
					'error_code'=>1,
					'error_message'=> Zend_Registry::get('Zend_Translate')->_('End date must be greater than today!'),
				
				);
			}
		}
		$db = Engine_Api::_() -> getDbtable('listings', 'ynlistings') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			// Create listing
			$table = Engine_Api::_() -> getDbtable('listings', 'ynlistings');
			$listing = $table -> createRow();
			$listing -> setFromArray($values);
			$listing -> status = 'draft';
			
			// $listing -> video_id = $values['toValues'];
			$listing -> approved_status = 'pending';
			
			if ($values['is_end'] === '1')
			{
				$listing -> end_date = $values['end_date'];
			}
			
			$listing -> save();

			// Add tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$listing -> tags() -> addTagMaps($viewer, $tags);

			$search_table = Engine_Api::_() -> getDbTable('search', 'core');
			$select = $search_table -> select() -> where('type = ?', 'ynlistings_listing') -> where('id = ?', $listing -> getIdentity());
			$row = $search_table -> fetchRow($select);
			if ($row)
			{
				$row -> keywords = $values['tags'];
				$row -> save();
			}
			else
			{
				$row = $search_table -> createRow();
				$row -> type = 'ynlistings_listing';
				$row -> id = $listing -> getIdentity();
				$row -> title = $listing -> title;
				$row -> description = $listing -> description;
				$row -> keywords = $values['tags'];
				$row -> save();
			}

			// Set photo
			if (!empty($values['photo']))
			{
				// $listing -> setPhoto($form -> photo);
			}
			
			// Add fields
			$db -> commit();

		    $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
            $auth_arr = array('view', 'comment', 'share', 'upload_photos', 'discussion', 'print');
            if(Engine_Api::_()->hasItemType('video')) {
                array_push($auth_arr, 'upload_videos'); 
            }
            foreach ($auth_arr as $elem) {
                $auth_role = $values[$elem];
                if ($auth_role) {
                    $roleMax = array_search($auth_role, $roles);
                    foreach ($roles as $i=>$role) {
                       $auth->setAllowed($listing, $role, $elem, ($i <= $roleMax));
                    }
                }    
            }
			
			if (Engine_Api::_() -> hasModuleBootstrap("yncredit"))
            {
                Engine_Api::_()->yncredit()-> hookCustomEarnCredits($listing -> getOwner(), $listing -> title, 'ynlistings_new', $listing);
			}
        }
		catch( Engine_Image_Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Could not complete your request."),
				'error_debug'=> $e->getMessage(),
			);
			// $form -> addError(Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'));
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Could not complete your request."),
				'error_debug'=> $e->getMessage(),
			);
		}
		
		
		return array(
			'error_code'=>0,
			'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Successfully."),
			'iListingId'=>$listing->getIdentity(),
		);
		
		
	}
	
	public function edit($aData){
		extract($aData);
		
		$auth =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
        
		// Check authorization to edit listing.
		if (!$auth -> setAuthParams('ynlistings_listing', null, 'edit')){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You do not have permission to edit this listing."),
			);
		}
		 
        
		$listing_id = intval($iListingId);
		
		$listing = Engine_Api::_()->getItem('ynlistings_listing', $listing_id);
		
		$iCategoryId = intval($iCategoryId);
        
        if (!$listing || !$listing->isEditable()) {
            return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You do not have permission to edit this listing."),
			);    
        }
        
	    $tableCategory = Engine_Api::_()->getItemTable('ynlistings_category');
		$category_id = $iCategoryId?$iCategoryId:$listing->category_id;
		// Create Form
		//get current category
		$category = Engine_Api::_() -> getItem('ynlistings_category', $category_id);
		if(!$category)
		{
			$categories = Engine_Api::_() -> getItemTable('ynlistings_category') -> getCategories();
			unset($categories[0]);
			$category = $categories[1];
		}
		
		// Populate category list.
		$categories = Engine_Api::_() -> getItemTable('ynlistings_category') -> getCategories();
		unset($categories[0]);
		
		//populate end date?
		$end_date = $listing -> end_date;
		$end_date = strtotime($end_date);
        
		$values =  $this->mapAddListingFields($aData);

		if ($values['is_end'] == '1')
		{
			$oldTz = date_default_timezone_get();
			date_default_timezone_set($viewer -> timezone);
			$end = strtotime($values['end_date']);
			date_default_timezone_set($oldTz);
			$values['end_date'] = date('Y-m-d H:i:s', $end);
			$now = date('Y-m-d H:i:s');
			if (strtotime($now) > strtotime($values['end_date']))
			{
				return array(
					'error_code'=>1,
					'error_message'=>Zend_Registry::get('Zend_Translate') -> _("End date must be greater than today!"),
				);
			}
		}
		
		$db = Engine_Api::_() -> getDbtable('listings', 'ynlistings') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			//Check if it edit category
			if($listing -> category_id != $category_id)
			{
				$old_category_id = $listing -> category_id;
				$isEditCategory = true;
			}
			
			// Edit listing
			$listing -> setFromArray($values);
			
			if ($values['is_end'] == '1')
			{
				$listing -> end_date = $values['end_date'];
			}
			else{
				$listing -> end_date = NULL;
			}
			$listing -> save();

			// Add tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$listing -> tags() -> addTagMaps($viewer, $tags);

			$search_table = Engine_Api::_() -> getDbTable('search', 'core');
			$select = $search_table -> select() -> where('type = ?', 'ynlistings_listing') -> where('id = ?', $listing -> getIdentity());
			$row = $search_table -> fetchRow($select);
			if ($row)
			{
				$row -> keywords = $values['tags'];
				$row -> save();
			}
			else
			{
				$row = $search_table -> createRow();
				$row -> type = 'ynlistings_listing';
				$row -> id = $listing -> getIdentity();
				$row -> title = $listing -> title;
				$row -> description = $listing -> description;
				$row -> keywords = $values['tags'];
				$row -> save();
			}
			
			// Remove old data custom fields if edit category
			if($isEditCategory)
			{
				$old_category = Engine_Api::_()->getItem('ynlistings_category', $old_category_id);
				$tableMaps = Engine_Api::_() -> getDbTable('maps','ynlistings');
				$tableValues = Engine_Api::_() -> getDbTable('values','ynlistings');
				$tableSearch = Engine_Api::_() -> getDbTable('search','ynlistings');
				if($old_category)
				{
					$fieldIds = $tableMaps->fetchAll($tableMaps -> select()-> where('option_id = ?',  $old_category->option_id));
					$arr_ids = array();
					if(count($fieldIds) > 0)
					{
						//clear values in search table
						$searchItem  = $tableSearch->fetchRow($tableSearch -> select() -> where('item_id = ?', $listing->getIdentity()) -> limit(1));
						foreach($fieldIds as $id)
						{
							try{
								$column_name = 'field_'.$id -> child_id;
								$searchItem -> $column_name = NULL;
								$arr_ids[] = $id -> child_id;
							}
							catch(exception $e)
							{
								continue;
							}
						}
						$searchItem -> save();
						//delele in values table
						if(count($arr_ids) > 0)
						{
							$valueItems = $tableValues->fetchAll($tableValues -> select() -> where('item_id = ?', $listing->getIdentity()) -> where('field_id IN (?)', $arr_ids));
							foreach($valueItems as $item)
							{
								$item -> delete();
							}
						}
					}
				}
			}
			$db -> commit();
            
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
            $auth_arr = array('view', 'comment', 'share', 'upload_photos', 'discussion', 'print');
            if(Engine_Api::_()->hasItemType('video')) {
                array_push($auth_arr, 'upload_videos'); 
            }
            foreach ($auth_arr as $elem) {
                $auth_role = $values[$elem];
                if ($auth_role) {
                    $roleMax = array_search($auth_role, $roles);
                    foreach ($roles as $i=>$role) {
                       $auth->setAllowed($listing, $role, $elem, ($i <= $roleMax));
                    }
                }    
            }

		}
		catch( Engine_Image_Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Could not complete your request."),
				'error_debug'=> $e->getMessage(),
			);
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Could not complete your request."),
				'error_debug'=> $e->getMessage(),
			);
		}
		
		
		return array(
			'error_code'=>0,
		);
	}
	
	public function delete($aData){
		extract($aData);
		$iListingId =  intval($iListingId);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $iListingId);
		
		if (!$listing){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Listing doesn't exists."),
			);
		}
		
		if (!$listing->isDeletable()) {
            return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this listing."),
			);    
        }
		
		$db = $listing -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			if ($listing -> photo_id)
			{
				Engine_Api::_() -> getItem('storage_file', $listing -> photo_id) -> remove();
			}
			$listing -> delete();
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Could not complete your request."),
				'error_debug'=>$e->getMessage(),
			);
		}
		
		return array(
			'error_code'=>0,
			'message'=>Zend_Registry::get('Zend_Translate') -> _('This listing has been deleted.'),
		);
	}
	
	public function open($aData){
		extract($aData);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$iListingId =  intval($iListingId);
		
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $iListingId);
		
		if (!$listing){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Listing doesn't exists."),
			);
		}
		
		if (!$listing -> isOwner($viewer)){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don't have permission to open this listing."),
			);
		}
		
		if ( $listing -> approved_status != 'approved') {
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("This listing is not approved."),
			);
		}

		if(($listing -> status != 'closed' && $listing -> status != 'expired')){
			
			if($listing -> status != 'closed'){
				return array(
					'error_code'=>1,
					'error_message'=>Zend_Registry::get('Zend_Translate') -> _("This listing is not closed."),
				);
			}else{
				return array(
					'error_code'=>1,
					'error_message'=>Zend_Registry::get('Zend_Translate') -> _("This listing is not expired."),
				);	
			}
			
		}
		
		$listing -> status = 'open';
		$listing -> save();
		
		return array(
			'error_code'=>0,
			'message'=>Zend_Registry::get('Zend_Translate') -> _('Listing has been open.'),
			'aItem'=> Ynmobile_AppMeta::_export_one($listing, array('infos')),
		);
	}
	
	public function close($aData){
		extract($aData);
		$iListingId =  intval($iListingId);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $iListingId);
		
		if (!$listing){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Listing doesn't exists."),
			);
		}
		
		if (!$listing -> isOwner($viewer)){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don't have permission to close this listing."),
			);
		}
		
		if ( $listing -> approved_status != 'approved') {
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("This listing is not approved."),
			);
		}
		
		if ($listing -> status != 'open'){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Listing is not opened."),
			);
		}
		
		$listing -> status = 'closed';
		$listing -> save();
		
		return array(
			'error_code'=>0,
			'message'=>Zend_Registry::get('Zend_Translate') -> _('Listing has been closed.'),
			'aItem'=> Ynmobile_AppMeta::_export_one($listing, array('infos')),
		);
	}

	public function transfer_owner($aData){
		extract($aData);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$iListingId =  intval($iListingId);
		$iUserId = intval($iUserId);
		
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $iListingId);
		
		$user  =  Engine_Api::_()->getItem('user', $iUserId);
		
		if(!$user){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Invalid user to transfer"),
			);
		}
		
		
		if (!$listing){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Listing doesn't exists."),
			);
		}
		
		if (!$listing -> isOwner($viewer)){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don't have permission to open this listing."),
			);
		}
		
		if($viewer->getIdentity() != $user->getIdentity()){
			$listing -> user_id = $user->getIdentity();
                $listing -> save();
                $activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
                $action = $activityApi -> addActivity($user, $listing, 'ynlistings_listing_transfer');
                if ($action) {
                    $action -> attach($listing);
                }
		}
		
		return array(
			'error_code'=>0,
			'message'=>Zend_Registry::get('Zend_Translate') -> _('This listing is tranferred successfully.'),
			'aItem'=> Ynmobile_AppMeta::_export_one($listing, array('infos')),
		);
	}
	
	
	public function fetch_review($aData){
		extract($aData);
		
		
		$iPage =  @$iPage?intval($iPage):1;
		$iLimit = @$iLimit?intval($iLimit): 10;
		$iOffset = @($iPage -1 )* $iLimit;
		$iListingId =  intval($iListingId);
		$viewer = $this->getViewer();
		
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $iListingId);
		
		if(!$listing){
			return array();
		}
		
		$table = Engine_Api::_()->getItemTable('ynlistings_review');
		
		if(!$viewer || ! $listing->getOwner()){
			return array();
		}
		
		$result = array();
		if($iPage == 1 && $viewer != null){
		
			$select = $table->select()
	            ->where('listing_id = ?', $listing->getIdentity())
	            ->where('user_id = '.$viewer->getIdentity())
	            ;
			foreach($table->fetchAll($select) as $row){
				$result[] =  	Ynmobile_AppMeta::_export_one($row, array('listing'));
			}
		}
		
		
		$select = $table->select()
            ->where('listing_id = ?', $listing->getIdentity())
            ->where('user_id <> '.$viewer->getIdentity())
            ->where('user_id <> '.$listing->getOwner()->getIdentity())
			->limit($iLimit, $iOffset)
            ->order('modified_date');
			
        foreach($table->fetchAll($select) as $row){
			$result[] =  	Ynmobile_AppMeta::_export_one($row, array('listing'));
		}
		
		return $result;
	}
	
	public function form_add_review($aData){
		return array();
	}
	
	public function add_review($aData){
		extract($aData);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$iListingId =  intval($iListingId);
		
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $iListingId);
		
		if (!$listing){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Listing doesn't exists."),
			);
		}
		
		$auth =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');
		
		if($listing->isOwner($viewer) || !$auth -> setAuthParams('ynlistings_listing', null, 'rate')->checkRequire()){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You do not have permission to add review to this listing."),
			);
		}
		
		$rated = $listing->checkRated();
        if ($rated) {
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You have added a review already.")
			);
        }
		
		$values  = array(
			'review_body'=>  (string)$sContent,
			'review_rating'=> intval($iRateValue),
		);
		
        $db = Engine_Api::_()->getDbtable('reviews', 'ynlistings')->getAdapter();
		
        $db->beginTransaction();
        try {
            $table = Engine_Api::_()->getDbtable('reviews', 'ynlistings');
            $review = $table->createRow();
            $review->listing_id = $listing->getIdentity();
            $review->user_id = $viewer->getIdentity();
            $review->body = strip_tags($values['review_body']);
            $review->rate_number = $values['review_rating'];
            $review->save();
            
            // Add activity and notification
            $activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
            $action = $activityApi -> addActivity($viewer, $listing, 'ynlistings_review_create');
            if ($action) {
                $action -> attach($listing);
            }
            
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $notifyApi -> addNotification($listing->getOwner(), $viewer, $listing, 'ynlistings_listing_add_review');
        }
        catch( Exception $e ) {
            $db->rollBack();
            return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Could not complete your request."),
				'error_debug'=> $e->getMessage(),
			);
        }       

        $db->commit();
		
		return array(
			'error_code'=> 0,
			'message'=>Zend_Registry::get('Zend_Translate') -> _('Your review has been created.'),
			'aItem'=> Ynmobile_AppMeta::_export_one($review, array('infos')),
			'iReviewId'=>$review->getIdentity(),
		);
	}
	
	public function delete_review($aData){
		extract($aData);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$iReviewId =  intval($iReviewId);
		
		$review = Engine_Api::_() -> getItem('ynlistings_review', $iReviewId);
		
		if(!$review){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Review doesn't exists."),
			);
		}
		
		$listing =  Engine_Api::_() -> getItem('ynlistings_listing', intval($review->listing_id));
		
		if(!$review->isDeletable()){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this review."),
			);	
		}
		if (!$listing){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Listing doesn't exists."),
			);
		}
		
		$db = $review -> getTable() -> getAdapter();
        $db -> beginTransaction();

        try {
            $review -> delete();
            $db -> commit();
        }
        catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }
		
		return array(
			'error_code'=> 0,
			'message'=>Zend_Registry::get('Zend_Translate') -> _('This review has been deleted.'),
		);
	}
	
	public function form_edit_review($aData){
		extract($aData);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$iReviewId =  intval($iReviewId);
		
		$review = Engine_Api::_() -> getItem('ynlistings_review', $iReviewId);
		
		if(!$review){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Review doesn't exists."),
			);
		}
		
		if(!$review->isEditable()){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this review."),
			);	
		}
	
		return array(
			'aItem'=>Ynmobile_AppMeta::_export_one($review, array('infos')),
		);
	}

	public function edit_review($aData){
		extract($aData);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$iReviewId =  intval($iReviewId);
		
		$review = Engine_Api::_() -> getItem('ynlistings_review', $iReviewId);
		
		if(!$review){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Review doesn't exists."),
			);
		}
		
		$listing =  Engine_Api::_() -> getItem('ynlistings_listing', intval($review->listing_id));
		
		if(!$review->isEditable()){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this review."),
			);	
		}
		if (!$listing){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Listing doesn't exists."),
			);
		}
		
		$values  = array(
			'review_body'=>  (string)$sContent,
			'review_rating'=> intval($iRateValue),
		);
		
        $db = Engine_Api::_()->getDbtable('reviews', 'ynlistings')->getAdapter();
        $db->beginTransaction();
        
        try {
            $review->body = $values['review_body'];
            $review->rate_number = $values['review_rating'];
            $review->save();
        }
        catch( Exception $e ) {
            $db->rollBack();
            return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Could not complete your request."),
				'error_debug'=> $e->getMessage(),
			);
        }       

        $db->commit();
        
		return array(
			'error_code'=> 0,
			'message'=>Zend_Registry::get('Zend_Translate') -> _('This review has been edited.'),
			'aItem'=>Ynmobile_AppMeta::_export_one($review, array('infos')),
		);
	}
	
	public function follow_seller($aData){
		extract($aData);
		
		$iListingId  = intval($iListingId);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		$listing = Engine_Api::_()->getItem('ynlistings_listing', $iListingId);
		$translate  =  Zend_Registry::get('Zend_Translate');
		
		if(!$listing){
			return array(
				'error_code'=>1,
				'error_message'=>$translate->_('The page you have attempted to access could not be found'),
			);
		}
        
        $owner =  $listing->getOwner();
		$owner_id =  $owner ->getIdentity();
		if(!$owner){
			return array(
				'error_code'=>1,
				'error_message'=>$translate->_('Could not complete your request.'),
			);
		}
		
		$followTable = Engine_Api::_() -> getItemTable('ynlistings_follow');
		$row = $followTable -> getRow($viewer->getIdentity(), $owner_id);
		
		
		if($row)
		{
			$row -> status = 1;
			$row -> save();
		}
		else 
		{
			$new_row = $followTable -> createRow();
			$new_row -> user_id = $viewer->getIdentity();
			$new_row -> owner_id = $owner_id;
			$new_row -> status = 1; 
			$new_row -> save();
            $owner = Engine_Api::_()->getItem('user', $owner_id);
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $notifyApi -> addNotification($owner, $viewer, $owner, 'ynlistings_listing_follow_owner');
		}

		return array(
			'error_code'=>0,
			// 'message'=>$translate->_('Successfully.'),
		); 
	}
	
	public function unfollow_seller($aData){
		extract($aData);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$iListingId =  intval($iListingId);
		
		$listing = Engine_Api::_()->getItem('ynlistings_listing', $iListingId);
		$translate  =  Zend_Registry::get('Zend_Translate');
		
		if(!$listing){
			return array(
				'error_code'=>1,
				'error_message'=>$translate->_('The page you have attempted to access could not be found'),
			);
		}
        
        $owner =  $listing->getOwner();
		$owner_id =  $owner ->getIdentity();
		if(!$owner){
			return array(
				'error_code'=>1,
				'error_message'=>$translate->_('Could not complete your request.'),
			);
		}
		
		$followTable = Engine_Api::_() -> getItemTable('ynlistings_follow');
		$row = $followTable -> getRow($viewer->getIdentity(), $owner_id);
		
		if($row)
		{
			$row -> status = 0;
			$row -> save();
		}
		
		return array(
			'error_code'=>0,
			// 'message'=>$translate->_('Successfully.'),
		); 
	}

	
	public function fetch_album($aData){
		extract($aData);
		
		$table =  Engine_Api::_() -> getItemTable('ynlistings_album');
		
		$select = $table -> getAlbumsPaginator(array(
			'listing_id'=>intval($iListingId),
		));
		
		return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, array('listing'));
	}
	
	public function fetch_video($aData){
		extract($aData);
		
		// Get paginator
	    $mappingTable = Engine_Api::_()->getItemTable('ynlistings_mapping');
	    $select = $mappingTable->getWidgetVideosPaginator(array(
	    	'listing_id'=>intval($iListingId)
		));
		
		return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, array('listing'));
	}
	
	//listing_title=&order=listing.listing_id&location=&within=&category=all&lat=&long=&tag=&search_button%5Bsearch%5D=
	public function mapListingSearchFields($aData){
		$maps = array(
			'iCategoryId'=>array(
				'def'=>'all',
				'key'=>'category',
			),
			'sSearch'=>array(
				'def'=>'',
				'key'=>'listing_title',
			),
			'sLat'=>array(
				'def'=>'',
				'key'=>'lat',
			),
			'sLong'=>array(
				'def'=>'',
				'key'=>'long',
			),
			'sLocation'=>array(
				'def'=>'',
				'key'=>'location',
			),
			'sWithin'=>array(
				'def'=>'',
				'key'=>'within',
			),
		);
		
		$result  = array();
		
		foreach($maps as $k=>$opt){
			if(isset($aData[$k])){
				$result[$opt['key']] =  $aData[$k];
			}else{
				$result[$opt['key']] =  $opt['def'];
			}
		}
		
		switch($aData['sOrder']){
			
			case 'most_viewed':
				$result['order'] =  'listing.view_count';
				break;
			case 'most_liked':
				$result['order'] =  'listing.like_count';
				break;
			case 'most_discussed':
				$result['order'] =  'discuss_count';
				break;
			case 'title':
				$result['order'] =  'listing.title';
				break;
			case 'most_recent':
			default:
				$result['order'] =  'listing.listing_id';
		}
		
		return $result;
	}
	
	public function discussions($aData)
	{
		extract($aData);
		
		$iListingId = intval($iListingId);  
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$listing = Engine_Api::_()->getItem('ynlistings_listing', $iListingId);
		
		if (!$listing){
			return array();
		}
		
		// Get paginator
		$table = Engine_Api::_()->getItemTable('ynlistings_topic');
		
		$select = $table->select()
		->where('listing_id = ?', $iListingId)
		->order('sticky DESC')
		->order('modified_date DESC');
		
		return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, array('listing'));
	}

	public function create_topic($aData)
	{
		extract($aData);
		
		$iListingId =  intval($iListingId);
		
        $table = Engine_Api::_()->getItemTable('ynlistings_listing');
		$listing = $table->findRow($iListingId);
		
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if (!Engine_Api::_()->authorization()->isAllowed($listing, $viewer, 'comment')){
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You do not have any permission to create topic!")
			);
		}
	
		if (!isset($sTitle) || empty($sTitle)){
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing topic title!")
			);
		}
		
		if (!isset($sBody) || empty($sBody)){
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing topic body!")
			);
		}
		
		// Process
		$values = array(
			'title' => $sTitle, 
			'body' => $sBody,
			'watch' => (isset($iWatch) && ($iWatch == '0' || $iWatch == '1')) ? $iWatch : 1,  	
		);
		
		$values['user_id'] = $viewer->getIdentity();
		$values['listing_id'] = $listing->getIdentity();
	
		$topicTable = Engine_Api::_()->getItemTable('ynlistings_topic');
        $topicWatchesTable = $this->getWorkingTable('topicWatches','ynlistings');
        $postTable = $this->getWorkingTable('posts','ynlistings');
		
	
		$db = $listing->getTable()->getAdapter();
		$db->beginTransaction();
	
		try
		{
			// Create topic
			$topic = $topicTable->createRow();
			$topic->setFromArray($values);
			$topic->save();
	
			// Create post
			$values['topic_id'] = $topic->topic_id;
	
			$post = $postTable->createRow();
			$post->setFromArray($values);
			$post->save();
	
			// Create topic watch
			$topicWatchesTable->insert(array(
					'resource_id' => $listing->getIdentity(),
					'topic_id' => $topic->getIdentity(),
					'user_id' => $viewer->getIdentity(),
					'watch' => (bool) $values['watch'],
			));
	
			// Add activity
			$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
			$action = $activityApi->addActivity($viewer, $listing, $this->getActivityType('ynlistings_topic_create'), null, array('child_id' => $topic->getIdentity()));
			if( $action ) {
				$action->attach($topic, Activity_Model_Action::ATTACH_DESCRIPTION);
			}
	
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get("Zend_Translate")->_("Created topic successfully!"),
				'iTopicId' => $topic->getIdentity()
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 3,
					'error_message' => $e->getMessage(),
			);
		}
	}

	public function view_topic($aData)
	{
		extract($aData);
		
		$viewer = Engine_Api::_()->user()->getViewer();
		
        $table = Engine_Api::_()->getItemTable('ynlistings_post');
		
		$select = $table->select()
			->where('topic_id = ?', $iTopicId)
			->order('creation_date ASC');
			
		return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, array('listing'));
	}
	
	public function topic_info($aData)
	{
		extract($aData);
		
		$iTopicId = intval($iTopicId);
		
		$topic = Engine_Api::_()->getItem('ynlistings_topic', $iTopicId);
		
		if (!$topic){
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing Topic identity!")
			);
		}
        
		return Ynmobile_AppMeta::_export_one($topic, array('infos'));   
	}
	
	public function post_reply($aData)
	{
		extract($aData);
		
		$iTopicId  = intval($iTopicId);
		
		$topic = Engine_Api::_()->getItem('ynlistings_topic', $iTopicId);
		
		if (!$topic){
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		
		if (!isset($sBody)){
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("sBody is required and can't be empty")
			);
		}
				
		
        $topicWatchesTable = $this->getWorkingTable('topicWatches','ynlistings');
		
		$listing = $topic->getParentListing();
		
		if( $topic->closed ) {
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("This has been closed for posting.")
			);
		}
		
		$allowHtml = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('ynlistings_html', 0);
		$allowBbcode = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('ynlistings_bbcode', 0);
		
		if( isset($iQuoteId) && !empty($iQuoteId) ) {
		    $quote = $this->getWorkingItem('ynlistings_post', $iQuoteId);
			
			if($quote->user_id == 0) {
				$owner_name = Zend_Registry::get('Zend_Translate')->_('Deleted Member');
			} else {
				$owner_name = $quote->getOwner()->__toString();
			}
			$sBody = "[blockquote][b]" . "[i]{$owner_name}[/i] said:" . "[/b]\r\n" . htmlspecialchars_decode($quote->body, ENT_COMPAT) . "[/blockquote]\r\n" . $sBody;
			
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
        
		if( !$allowHtml ) 
		{
			$filter = new Engine_Filter_HtmlSpecialChars();
		} 
		else 
		{
			$filter = new Engine_Filter_Html();
			$filter->setForbiddenTags();
			$allowed_tags = array_map('trim', explode(',', Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'advgroup', 'commentHtml')));
            
			$filter->setAllowedTags($allowed_tags);
		}
        
        $sBody = $filter->filter($sBody);
        
		if ($sBody == ''){
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Post content is invalid!")
			);
		}
		
		// Process
		$viewer = Engine_Api::_()->user()->getViewer();
		$topicOwner = $topic->getOwner();
		$isOwnTopic = $viewer->isSelf($topicOwner);
		
		$postTable = $this->getWorkingTable('posts','ynlistings');
        $topicWatchesTable = $this->getWorkingTable('topicWatches','ynlistings');
		
		$userTable = Engine_Api::_()->getItemTable('user');
		$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
		$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
		
		$values['body'] = html_entity_decode($sBody, ENT_QUOTES, 'UTF-8');
		$values['user_id'] = $viewer->getIdentity();
		$values['listing_id'] = $listing->getIdentity();
		$values['topic_id'] = $topic->getIdentity();
		$values['watch'] =  (isset($iWatch) && $iWatch == '1') ? 1 : 0;
		
		$watch = (bool) $values['watch'];
		$isWatching = $topicWatchesTable
		->select()
		->from($topicWatchesTable->info('name'), 'watch')
		->where('resource_id = ?', $listing->getIdentity())
		->where('topic_id = ?', $topic->getIdentity())
		->where('user_id = ?', $viewer->getIdentity())
		->limit(1)
		->query()
		->fetchColumn(0)
		;
		
		$db = $listing->getTable()->getAdapter();
		$db->beginTransaction();
		
		try
		{
			// Create post
			$post = $postTable->createRow();
			$post->setFromArray($values);
			$post->save();
		
			// Watch
			if( false === $isWatching ) {
				$topicWatchesTable->insert(array(
						'resource_id' => $listing->getIdentity(),
						'topic_id' => $topic->getIdentity(),
						'user_id' => $viewer->getIdentity(),
						'watch' => (bool) $watch,
				));
			} else if( $watch != $isWatching ) {
				$topicWatchesTable->update(array(
						'watch' => (bool) $watch,
				), array(
						'resource_id = ?' => $listing->getIdentity(),
						'topic_id = ?' => $topic->getIdentity(),
						'user_id = ?' => $viewer->getIdentity(),
				));
			}
		
			// Activity
			$action = $activityApi->addActivity($viewer, $listing, $this->getActivityType('ynlistings_topic_reply'), null, array('child_id' => $topic->getIdentity()));
			if( $action ) 
			{
				$action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);
			}
		
		
			// Notifications
			$notifyUserIds = $topicWatchesTable->select()
			->from($topicWatchesTable->info('name'), 'user_id')
			->where('resource_id = ?', $listing->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('watch = ?', 1)
			->query()
			->fetchAll(Zend_Db::FETCH_COLUMN)
			;
		
			$view = Zend_Registry::get("Zend_View");
			
			foreach( $userTable->find($notifyUserIds) as $notifyUser ) 
			{
				if( $notifyUser->isSelf($viewer) ) 
				{
					continue;
				}
				if( $notifyUser->isSelf($topicOwner) ) 
				{
					$type = 'ynlistings_discussion_response';
				} else 
				{
					$type = 'ynlistings_discussion_reply';
				}
				$notifyApi->addNotification($notifyUser, $viewer, $topic, $this->getActivityType($type), array(
						'message' => $view->BBCode($post->body),
				));
			}
		
			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => Zend_Registry::get('Zend_Translate') -> _("Posted reply successfully!"),
					'iPostId' => $post->getIdentity(),
					'iTopicId' => $iTopicId,
			);
		}
		
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage()
			);
		}
	}

	public function topic_watch($aData)
	{
		extract($aData);
		
		$iTopicId = intval($iTopicId);
		
		$topic = $this->getWorkingItem('ynlistings_topic', $iTopicId);
		
		if (!$topic){
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		
		if (!isset($iWatch)){
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iWatch!")
			);
		}
			
		$listing = $topic->getParentListing();
        
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$watch = ( isset($iWatch) && $iWatch == '1' ) ? true : false;

		$topicWatchesTable = $this->getWorkingTable('topicWatches','ynlistings');
		
		$db = $topicWatchesTable->getAdapter();
		$db->beginTransaction();
		
		try
		{
			$isWatching = $topicWatchesTable
			->select()
			->from($topicWatchesTable->info('name'), 'watch')
			->where('resource_id = ?', $listing->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('user_id = ?', $viewer->getIdentity())
			->limit(1)
			->query()
			->fetchColumn(0)
			;
		
			if( false === $isWatching ) {
				$topicWatchesTable->insert(array(
						'resource_id' => $listing->getIdentity(),
						'topic_id' => $topic->getIdentity(),
						'user_id' => $viewer->getIdentity(),
						'watch' => (bool) $watch,
				));
			} else if( $watch != $isWatching ) {
				$topicWatchesTable->update(array(
						'watch' => (bool) $watch,
				), array(
						'resource_id = ?' => $listing->getIdentity(),
						'topic_id = ?' => $topic->getIdentity(),
						'user_id = ?' => $viewer->getIdentity(),
				));
			}
		
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => ($watch) 
					? Zend_Registry::get('Zend_Translate')->_("Set watching successfully")
					: Zend_Registry::get('Zend_Translate')->_("Unset watching successfully")
			);
		}
		
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
		
	}

	public function topic_delete($aData)
	{
		extract($aData);
		$iTopicId = intval($iTopicId);
		
		$topic = $this->getWorkingItem('ynlistings_topic', $iTopicId);
		if (!$topic){
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
	
		$viewer = Engine_Api::_()->user()->getViewer();
	
		try{
			$topic->delete();
			return array(
				'error_code' => 0,
				'error_message' => Zend_Registry::get('Zend_Translate')->_("Deleted topic successfully")
			);
		}
	
		catch( Exception $e ){
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	
	}
	
	public function post_info($aData)
	{
		extract($aData);
		
		$iPostId =  intval($iPostId);
		
		$post  =  $this->getWorkingItem('ynlistings_post',$iPostId);
		
		if (!$post){
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPostId!")
			);
		}
	    
		return Ynmobile_AppMeta::_export_one($post, array('infos'));
	}
	
	public function edit_post($aData)
	{
		extract($aData);
		
		$iPostId = intval($iPostId);
		
		$post = $this->getWorkingItem('ynlistings_post', $iPostId);
		
		if (!$post){
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPostId!")
			);
		}
		
		if (!isset($sBody) || $sBody == ""){
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("sBody is required and can't be empty")
			);
		}
		
		
		
		$listing = $post->getParentListing();
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if( !$listing->isOwner($viewer) && !$post->isOwner($viewer) && !$listing->authorization()->isAllowed($viewer, 'topic.edit') )
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this post")
			);
		}
		
		// Process
		$table = $post->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
		
		try
		{
			$post->modified_date = date('Y-m-d H:i:s');
			$post->body = html_entity_decode($sBody, ENT_QUOTES, 'UTF-8');
			$post->save();
		
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate') -> _("Edited post successfully!"),
				'iPostId' => $post->getIdentity(),
			);
		}
		
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	}
	
	public function delete_post($aData)
	{
		extract($aData);
		
		
		$iPostId = intval($iPostId);
		
		$post = $this->getWorkingItem('ynlistings_post', $iPostId);
		
		if (!$post){
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPostId!")
			);
		}
		
		$listing = $post->getParentListing();
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if( !$listing->isOwner($viewer) && !$post->isOwner($viewer) && !$listing->authorization()->isAllowed($viewer, 'topic.edit') ){
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this post")
			);
		}
		
		// Process
		$table = $post->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		$topic_id = $post->topic_id;
	
		try
		{
			$post->delete();
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate') -> _("Deleted post successfully!"),
				'iTopicId' => $topic_id,
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	}
	
	public function albumcreate($aData){
		//Check viewer and subject requirement
		
		extract($aData);
		$iListingId =  intval($iListingId);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$listing  = Engine_Api::_()->getItem('ynlistings_listing',$iListingId);
		
		if(!$listing){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Listing not found."),
			);
		}
		
		if (!$viewer || !$viewer -> getIdentity())
		{
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You do not have permission to create album"),
			);
		}
		
		$canUpload = $listing->canUploadPhotos();
		
        if (!$canUpload) {
            return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You do not have permission to create album"),
			);
        }
		
		
		$values = array(
			'title'=>$sTitle,
			'description'=>$sDescription,
		);
		
		$table = Engine_Api::_() -> getItemTable('ynlistings_album');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$album = $table -> createRow();
			$album -> listing_id = $listing -> listing_id;
			$album -> user_id = $viewer -> user_id;
			$album -> title = $values['title'];
			$album -> description = $values['description'];

			$album -> save();
			$db -> commit();
			
			return array(
				'error_code'=>0,
				'iAlbumId'=> $album->getIdentity(),
			);
		}
		catch(Exception $e)
		{
			$db -> rollBack();
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Could not complete your request."),
				'error_debug'=>$e->getMessage(),
			);
		}
	}

	public function upload_photo_to_listing($aData){
		extract($aData);
		
		$listing_id =  intval($iParentId);
		
		$viewer =  Engine_Api::_() -> user() ->getViewer();
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $listing_id);
		
		if(!$listing){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Listing not found")
			);
		}
		
		if(!$listing -> canUploadPhotos()){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don\t have permission to upload photo to this listing.")
			);
		}
		
		$album = $listing -> getSingletonAlbum();
		
		$params = array(
			'listing_id' => $listing -> getIdentity(), 
			'user_id' => $viewer -> getIdentity(), 
			'album_id'=>$album->getIdentity(),
			'collection_id'=>$album->getIdentity(),
			'image_title'=>'',
			'image_description'=>'',
		);
		
		$table  =  Engine_Api::_()->getItemTable('ynlistings_photo');
		$photo =  $table->fetchNew();
		$db =  $table->getAdapter();
		
		$db->beginTransaction();
		
		try{

			$photo->setFromArray($params);
			
			$photo -> save();
	
			$photo = Engine_Api::_() -> ynmobile() -> setPhoto($photo, $_FILES['image']);
	
			$photo -> save();
		
			if($listing->photo_id == 0){
				$listing->photo_id =  $photo->file_id;
				$listing->save();
			}
			
			$db->commit();
			return array(
				'error_code'=>0,
			);
		}catch(Exception $ex){
			
			$db->rollBack();
			
			
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Could not upload photo"),
				'error_debug'=>$ex->getMessage(),
			);
		}
	}

	public function upload_photo($aData)
	{
		extract($aData);
		$album_id = (int) $iAlbumId;
		
		if(!$album_id){
			return $this->upload_photo_to_listing($aData);
		}
		
		$album = Engine_Api::_() -> getItem('ynlistings_album', $album_id);
		$listing_id =  (int)$album->listing_id;
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $listing_id);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		if (!$listing)
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This listing is not existed!")
			);
		}
		if( !$listing->canUploadPhotos() ) 
		{
			return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You do not have permission to upload photos to this listing!")
			);
		}
		
		if (!isset($_FILES['image']))
		{
			return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("No file!"),
					'result' => 0
			);
		}
	
		
		$table = Engine_Api::_() -> getItemTable('ynlistings_photo');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

	
		try 
		{
			
			$photo = $table -> createRow();
			
			$photo -> setFromArray(array(
				'listing_id'=>$listing->getIdentity(),
				'user_id' => $viewer -> getIdentity(),
				'album_id'=>$album->getIdentity(),
				'collection_id'=>$album->getIdentity(),
				'image_title'=> isset($aData['sTitle']) ? $aData['sTitle'] : '',
				'image_description'=>isset($aData['sDescription']) ? $aData['sDescription'] : '',
			));
			
			$photo -> save();

			$photo = Engine_Api::_() -> ynmobile() -> setPhoto($photo, $_FILES['image']);

			$photo -> save();
            
            if($album && $album->photo_id ==0){
                $album->photo_id =  $photo->file_id;
                $album->modified_date =  date('Y-m-d H:i:s');
                $album->save();
            }

			$db -> commit();
			
			return array(
				'error_code'=>0,
				'iPhotoId'=>$photo->getIdentity(),
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 3,
				'error_message' => $e->getMessage(),
			);
		}
	}

}

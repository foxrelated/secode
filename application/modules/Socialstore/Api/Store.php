<?php  
class Socialstore_Api_Store extends Core_Api_Abstract {
	
  const IMAGE_WIDTH = 720;
  const IMAGE_HEIGHT = 720;
  const THUMB_WIDTH = 200;
  const THUMB_HEIGHT = 150;
  
  	public function getStoreByUserId($user_id) {
		$table = new Socialstore_Model_DbTable_SocialStores();
		$rName = $table -> info('name');
		$select = $table -> select();
		$select -> where('owner_id = ?', $user_id);
		//$select -> where('is_delete = 0');
		return $table -> fetchAll($select)->current();
  	}
	public function canRate($store,$user_id)
    {
    	if ($store->owner_id == $user_id && Engine_Api::_()->getApi('settings', 'core')->getSetting('store.rate', 0) == 0) {
    		return 0;
    	}
    	else {
    		if ($store) {
	    		$rateTable = Engine_Api::_()->getDbtable('rates','Socialstore');
		       	$select = $rateTable->select()
		       	->where('item_id = ?', $store->getIdentity())
		       	->where('user_id = ?', $user_id);
		       	return (count($rateTable->fetchAll($select)) > 0)?0:1;
    		}
    	}
    }
    
	public function getAVGrate($store_id)
  	{
        $rateTable = Engine_Api::_()->getDbtable('rates','Socialstore');
        $select = $rateTable->select()
        ->from($rateTable->info('name'), 'AVG(rate_number) as rates')
        ->group("item_id")
        ->where('item_id = ?', $store_id);
        $row = $rateTable->fetchRow($select);
        return ((count($row) > 0)) ? $row->rates : 0;
    }
    
  	public function getStoresPaginator($params = array())
	{
	    $paginator = Zend_Paginator::factory($this->getStoresSelect($params));
	   
	    if( !empty($params['page']) )
	    {
	      $paginator->setCurrentPageNumber($params['page']);
	    }
	    if( !empty($params['limit']) )
	    {
	      $paginator->setItemCountPerPage($params['limit']);
	    }
	    return $paginator;
	}
	  
	public function getStoresSelect($params = array())
	{
	  	$table = new Socialstore_Model_DbTable_SocialStores();
	    $rName = $table->info('name');
	    $select = $table->select()->from($rName)->setIntegrityCheck(false);

		$select->where('deleted = 0');
	    // by search
	    
	    if(@$params['search'] && $search = trim($params['search']))
	    {
	      $select->where($rName.".title LIKE ? OR ".$rName.".description LIKE ?", '%'.$search.'%');
	    }
	  
	    if( isset($params['featured']) && $params['featured'] != ' ')
	    {
	      $select->where($rName.".featured = ? ",$params['featured']);
	    }
	    // by where
	    if(isset($params['where']) && $params['where'] != "")
	    	$select->where($params['where']);
	    // by User
	    if(!empty($params['user_id']) && is_numeric($params['user_id']))
	    	$select->where("$rName.owner_id = ?",$params['user_id']);
	    // by Buyer
	    
	    if(!empty($params['location_id']) && $params['location_id'] > 1)
	    {
	    	$select->joinLeft('engine4_socialstore_locations', "$rName.location_id = engine4_socialstore_locations.location_id", 'engine4_socialstore_locations.name as location_title');
	    	$Model = new Socialstore_Model_DbTable_Locations;
	    	$item = $Model->find($params['location_id'])->current();
	    	$ids = $item->getDescendantIds();
	    	$select->where("$rName.location_id in (?) ", $ids);
	    }
		if(!empty($params['category_id']) && $params['category_id'] > 0)
	    {
	   		$Model = new Socialstore_Model_DbTable_Storecategories;
	    	$item = $Model->find($params['category_id'])->current();
	    	$ids_object = $item->getDescendantIds();
	    	$ids_array = array();
	    	foreach ($ids_object as $id_ob) {
	    		$ids_array[] = $id_ob->storecategory_id;
	    	}
	    	$ids_array[] = $item->storecategory_id;
	    	$select->where("$rName.category_id in (?)", $ids_array);
	    }
		
	    // by status
	    
		if(isset($params['approve_status']) && $params['approve_status'] != ''){
			$select->where("$rName.approve_status = ?", $params['approve_status']);
		}
		
	  	if(isset($params['view_status']) && $params['view_status'] != ''){
			$select->where("$rName.view_status = ?", $params['view_status']);
		}
		
	    if(isset($params['orderby']) && $params['orderby']) {
	       	if ($params['orderby'] == 'featured') {
	       		$select->where("$rName.featured = 1");
	       	}
	       	else {
	    		$select->order($params['orderby'].' DESC');
	       	}
	    }
		elseif (!empty($params['order']) && $params['order'] != '') {
	    	$select->order($params['order'].' '.$params['direction']);
		}
	    else 
	    {
	        $select->order("$rName.creation_date DESC");
	    }
		
	    	    
		//echo $select;die;
	    if(getenv('DEVMODE') == 'localdev'){
			print_r($params);
			echo $select;	
		}
		
	    return $select;
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
	      $thumbName = $path.'/t_'.$name . '.' . $extension;
	      $thumbName1 = $path.'/t1_'.$name . '.' . $extension;
	
	      $image = Engine_Image::factory();
	      $image->open($file['tmp_name'])
	          ->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
	          ->write($mainName)
	          ->destroy();
	       // Resize image (profile)
	       $image = Engine_Image::factory();
	       $image->open($file['tmp_name'])
	          ->resize(500, 400)
	          ->write($profileName)
	          ->destroy();
	      $image = Engine_Image::factory();
	      $image->open($file['tmp_name'])
	          ->resize(339,195)
	          ->write($thumbName1)
	          ->destroy();
	      
	      $image = Engine_Image::factory();
	      $image->open($file['tmp_name'])
	          ->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
	          ->write($thumbName)
	          ->destroy();
	
	      // Store photos
	      $photo_params = array(
	        'parent_id' => $params['store_id'],
	        'parent_type' => 'social_store',
	      );
	      $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
	      $profileFile = Engine_Api::_()->storage()->create($profileName, $photo_params);
	      $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
	      $thumbFile1 = Engine_Api::_()->storage()->create($thumbName1, $photo_params);
	      $photoFile->bridge($profileFile, 'thumb.profile');
	      $photoFile->bridge($thumbFile, 'thumb.normal');
	      $photoFile->bridge($thumbFile1, 'thumb.normal1');
	      $params['file_id'] = $photoFile->file_id; // This might be wrong
	      $params['photo_id'] = $photoFile->file_id;
	
	      // Remove temp files
	      @unlink($mainName);
	      @unlink($profileName);
	      @unlink($thumbName);
	      @unlink($thumbName1);
	      
	    }
	    $row = Engine_Api::_()->getDbtable('storephotos','Socialstore')->createRow();
	    $row->setFromArray($params);
	    $row->save();
	    return $row;
	}


}
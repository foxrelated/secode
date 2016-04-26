<?php  
class Socialstore_Api_Product extends Core_Api_Abstract {  

  const IMAGE_WIDTH = 720;
  const IMAGE_HEIGHT = 720;
  const THUMB_WIDTH = 170;
  const THUMB_HEIGHT = 140;

	public function getProductsPaginator($params = array())
  	{
	    $paginator = Zend_Paginator::factory($this->getProductsSelect($params));
	   
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
	public function getStoreSearchProductsPaginator($params = array())
  	{
	    $paginator = Zend_Paginator::factory($this->getStoreSearchProductsSelect($params));
	   
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
	public function canRate($product,$user_id)
    {
    	if ($product->owner_id == $user_id && Engine_Api::_()->getApi('settings', 'core')->getSetting('store.product.rate', 0) == 0) {
    		return 0;
    	}
    	else {
    		$rateTable = Engine_Api::_()->getDbtable('rates','Socialstore');
	       	$select = $rateTable->select()
	       	->where('item_id = ?', $product->getIdentity())
	       	->where('user_id = ?', $user_id);
	       	return (count($rateTable->fetchAll($select)) > 0)?0:1;
    	}
    }
    
	public function getAVGrate($product_id)
  	{
        $rateTable = Engine_Api::_()->getDbtable('rates','Socialstore');
        $select = $rateTable->select()
        ->from($rateTable->info('name'), 'AVG(rate_number) as rates')
        ->group("item_id")
        ->where('item_id = ?', $product_id);
        $row = $rateTable->fetchRow($select);
        return ((count($row) > 0)) ? $row->rates : 0;
    }
  
	public function getProductsSelect($params = array())
	{
		$table = new Socialstore_Model_DbTable_Products();
	    $rName = $table->info('name');
	    $select = $table->select()->from($rName)->setIntegrityCheck(false);
		$select->joinLeft('engine4_socialstore_storecategories', "$rName.category_id = engine4_socialstore_storecategories.storecategory_id", 'engine4_socialstore_storecategories.name as category_title');
		$select->where('deleted = 0');
	    // by search
	   if (@$params['store_id'] && $params['store_id'] != '') {
	   	  $select->where($rName.".store_id = ? ", $params['store_id']);
	   } 
	   if(@$params['search'] && $search = trim($params['search']))
	   {
	      $select->where($rName.".title LIKE ? OR ".$rName.".description LIKE ?", '%'.$search.'%');
	   }
	   if(@$params['title'] && $title = trim($params['title']))
	   {
	     $select->where($rName.".title LIKE ? ", '%'.$title.'%');
	   }
	   if( isset($params['featured']) && $params['featured'] != ' ')
	   {
	      $select->where($rName.".featured = ? ",$params['featured']);
	   }

	    // by User
	    if(!empty($params['user_id']) && is_numeric($params['user_id']))
	    	$select->where("$rName.owner_id = ?",$params['user_id']);
	    // by Buyer
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
	    	$select->where("$rName.storecategory_id in (?)", $ids_array);
	    }
		if(!empty($params['from']) && $params['from'] != '')
	    {
	    	$select->where("$rName.pretax_price >= ?", $params['from']);
	    }

		if(!empty($params['to']) && $params['to'] != '')
	    {
	    	$select->where("$rName.pretax_price <= ?", $params['to']);
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
	    
	    if(getenv('DEVMODE') == 'localdev'){
			print_r($params);
			echo $select;	
		}
	    return $select;
	}
	
	public function getStoreSearchProductsSelect($params = array())
	{
		$table = new Socialstore_Model_DbTable_Products();
	    $rName = $table->info('name');
	    $select = $table->select()->from($rName)->setIntegrityCheck(false);
		$select->joinLeft('engine4_socialstore_customcategories', "$rName.category_id = engine4_socialstore_customcategories.customcategory_id", 'engine4_socialstore_customcategories.name as category_title');
		$select->where('deleted = 0');
	    // by search
	   if (@$params['store_id'] && $params['store_id'] != '') {
	   	  $select->where($rName.".store_id = ? ", $params['store_id']);
	   } 
	   if(@$params['search'] && $search = trim($params['search']))
	   {
	      $select->where($rName.".title LIKE ? OR ".$rName.".description LIKE ?", '%'.$search.'%');
	   }
	   if(@$params['title'] && $title = trim($params['title']))
	   {
	     $select->where($rName.".title LIKE ? ", '%'.$title.'%');
	   }
	   if( isset($params['featured']) && $params['featured'] != ' ')
	   {
	      $select->where($rName.".featured = ? ",$params['featured']);
	   }

	    // by User
	    if(!empty($params['user_id']) && is_numeric($params['user_id']))
	    	$select->where("$rName.owner_id = ?",$params['user_id']);
	    // by Buyer
	    
	    if(!empty($params['category_id']) && $params['category_id'] > 0)
	    {
	   		$Model = new Socialstore_Model_DbTable_Customcategories;
	    	$item = $Model->find($params['category_id'])->current();
	    	$ids_object = $item->getDescendantIds();
	    	$ids_array = array();
	    	foreach ($ids_object as $id_ob) {
	    		$ids_array[] = $id_ob->customcategory_id;
	    	}
	    	$ids_array[] = $item->customcategory_id;
	    	$select->where("$rName.category_id in (?)", $ids_array);
	    }
		
		if(!empty($params['from']) && $params['from'] != '')
	    {
	    	$select->where("$rName.pretax_price >= ?", $params['from']);
	    }

		if(!empty($params['to']) && $params['to'] != '')
	    {
	    	$select->where("$rName.pretax_price <= ?", $params['to']);
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
	    
	    if(getenv('DEVMODE') == 'localdev'){
			print_r($params);
			echo $select;	
		}
	    return $select;
	}
	
	public function getSoldProductsPaginator($params = array())
  	{
	    $paginator = Zend_Paginator::factory($this->getSoldProductsSelect($params));
	   
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
	
	public function getSoldProductsSelect($params = array())
	{
		$table = new Socialstore_Model_DbTable_Products();
	    $rName = $table->info('name');
	    $select = $table->select()->from($rName)->setIntegrityCheck(false);
		$select->join(array('orderitems' => 'engine4_socialstore_orderitems'), 
							"$rName.product_id = orderitems.object_id 
							AND orderitems.object_type='shopping-cart'", 
							array('orderitem_id'=>'orderitems.orderitem_id',
								  'order_id'=>'orderitems.order_id',
								  'shippingaddress_id'=>'orderitems.shippingaddress_id',
								  'options' => 'orderitems.options',
								  'quantity'=>'orderitems.quantity',
								  'delivery_status'=>'orderitems.delivery_status',
							));
		$select->join(array('orders'=>'engine4_socialstore_orders'),
						"orders.order_id = orderitems.order_id",
						array('order_date' => 'orders.creation_date',
							  'buyer_id' => 'orders.owner_id',
							  'guest_id' => 'orders.guest_id',
						));
		/*$select->join(array('users'=>'engine4_users'),
						"users.user_id = orders.owner_id",'');*/				
	    // by search
	   $select->where('orders.payment_status = ?' ,'completed');
		if(@$params['title'] && $title = trim($params['title']))
	   	{
	    	$select->where($rName.".title LIKE ? ", '%'.$title.'%');
	   	}
	    if(!empty($params['order_id']) && $params['order_id'] != '')
	    	$select->where("orders.order_id = ?",$params['order_id']);
		 if (isset($params['user_id']) && $params['user_id']) {
	    	$select->where("$rName.owner_id = ?", $params['user_id']);
		 }
		
	   	if(!empty($params['owner_name']) && $params['owner_name'] != "") {
	    	$select->where("users.username LIKE ?",'%'.$params['owner_name'].'%');
   	    }	    	
	    if(isset($params['order']) && $params['order']) {
	       	if ($params['order'] == 'quantity') {
	       		$select->order("orderitems.quantity ".$params['direction']);
	       	}
	       	elseif ($params['order'] == 'order_date') {
	       		$select->order("orders.creation_date ".$params['direction']);
	       	}
	       	elseif ($params['order'] == 'delivery_status') {
	       		$select->order('orderitems.delivery_status '.$params['direction']);
	       	}
	    }
	    else
	    {
	        $select->order("orders.creation_date DESC");
	    }
	    
	    
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
	      $iconName = $path.'/t2_'.$name.'.'.$extension;
	
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
		
	      $image = Engine_Image::factory();
	      $image->open($file['tmp_name'])
	          ->resize(52,52)
	          ->write($iconName)
	          ->destroy();    
	      // Store photos
	      $photo_params = array(
	        'parent_id' => $params['product_id'],
	        'parent_type' => 'social_product',
	      );
	      $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
	      $profileFile = Engine_Api::_()->storage()->create($profileName, $photo_params);
	      $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
	      $thumbFile1 = Engine_Api::_()->storage()->create($thumbName1, $photo_params);
	      $iconFile = Engine_Api::_()->storage()->create($iconName, $photo_params);
	      $photoFile->bridge($profileFile, 'thumb.profile');
	      $photoFile->bridge($thumbFile, 'thumb.normal');
	      $photoFile->bridge($thumbFile1, 'thumb.normal1');
	      $photoFile->bridge($iconFile, 'thumb.normal2');
	      $params['file_id'] = $photoFile->file_id; // This might be wrong
	      $params['photo_id'] = $photoFile->file_id;
	
	      // Remove temp files
	      @unlink($mainName);
	      @unlink($profileName);
	      @unlink($thumbName);
	      @unlink($thumbName1);
	      @unlink($iconName);
	      
	    }
	    $row = Engine_Api::_()->getDbtable('productphotos','Socialstore')->createRow();
	    $row->setFromArray($params);
	    $row->save();
	    return $row;
	}
	
	/**
	 * Deal Request
	 * @param array $params
	 */
	public function getGDARequestsPaginator($params = array())
    {
        $paginator = Zend_Paginator::factory($this->getGDARequestsSelect($params));
       
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
public function getGDARequestsSelect($params = array())
    {
        $table = new Socialstore_Model_DbTable_Gdarequests();
        $rName = $table->info('name');
        $select = $table->select()->from($rName);
        if(isset($params['store_id']))
            $select->where("$rName.store_id = ?",$params['store_id']);
        if(isset($params['user_id']))
            $select->where("$rName.user_id = ?",$params['user_id']);
        if(isset($params['status']) && $params['status'])
            $select->where("$rName.status = ?",$params['status']);
        if(isset($params['product_title']) && $params['product_title'])
        {
             $title =  $params['product_title'];
             $select->joinLeft('engine4_socialstore_products', "$rName.product_id = engine4_socialstore_products.product_id", '');
             $select->where('engine4_socialstore_products.title LIKE ?',"%$title%"); 
        }
        if(isset($params['deal_title']) && $params['deal_title'])
        {
             $title =  $params['deal_title'];
             $select->joinLeft('engine4_groupbuy_deals', "$rName.deal_id = engine4_groupbuy_deals.deal_id", '');
             $select->where('engine4_groupbuy_deals.title LIKE ?',"%$title%"); 
        }
        if(isset($params['owner_name']) && $params['owner_name'])
        {
             $title =  $params['owner_name'];
             $select->joinLeft('engine4_users', "$rName.user_id = engine4_users.user_id", '');
             $select->where('engine4_users.displayname LIKE ?',"%$title%")
                    ->where('deal_id > 0'); 
        }
        if(isset($params['order']) && $params['order'])
            $select->order($params['order'].' '.$params['direction']);
        else
        {
            $select->order('product_id DESC')
                ->order ('creation_date DESC');
        }
        //echo $select;
        //die;
        return $select;
    }
}
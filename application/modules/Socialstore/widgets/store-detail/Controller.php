<?php

class Socialstore_Widget_StoreDetailController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    
		$viewer = Engine_Api::_()->user()->getViewer();
		if (Zend_Registry::isRegistered('store_detail_id')) {
			$store_id = Zend_Registry::get('store_detail_id');
		}
		else {
			$this->setNoRender(true);
			return;
		}
		$store = Engine_Api::_()->getItem('social_store', $store_id);
		if (!$store) {
			$this->setNoRender(true);
			return;
		}
		if($store->photo_id) {
        	$this->view->main_photo = $store->getPhoto($store->photo_id);
      	}
      	if ($viewer->getIdentity() == 0) {
      		
      		$this->view->isAdmin = 0;
      	}
      	else {
			$level = Engine_Api::_()->getItem('authorization_level', $viewer->level_id);
	    	if( in_array($level->type, array('admin', 'moderator')) ) {
	      		$this->view->isAdmin = 1;
	    	}
	    	else {
	    		$this->view->isAdmin = 0;
	    	}
      	}
    	$this->view->store = $store;
    	$this->view->viewer = $viewer;
		$this->view->viewer_id = $viewer->getIdentity();
    	if (!$viewer->getIdentity()){
    		$this->view->can_rate = $can_rate = 0;
    	}        
    	else{
    		$this->view->can_rate = $can_rate = Engine_Api::_()->getApi('store','Socialstore')->canRate($store,$viewer->getIdentity());
    	}
    	$route = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
    	$this->view->route = $route;
	 	$owner = Engine_Api::_()->getItem('user', $store->owner_id);
    	if( !$owner->isSelf($viewer) ) {
    		$store->view_count++;
    		$store->save();
     }
    	      // album material
		$this->view->album = $album = $store->getSingletonAlbum();
		$this->view->paginator = $paginator = $album->getCollectiblesPaginator();
		$paginator->setCurrentPageNumber($this->_getParam('page', 1));
		$paginator->setItemCountPerPage(100);
		$view = $this->view;
      	$view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
      	$this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($store);
		$ShippingMethod = new Socialstore_Model_DbTable_ShippingMethods;
      	$methods = $ShippingMethod->getMethods($store_id);
		$this->view->methods = $methods;
		$temp_rules = array();
		$active = true;
		foreach ($methods as $method) {
			if ($temp_rules == null) {
				$temp_rules = $method->getShippingRule($active);
			}
			else {
				if (count($method->getShippingRule()) > 0) {
					$temp_rules = array_merge($temp_rules, $method->getShippingRule($active));
				}
			}
		}
		$rules = array();
		foreach ($temp_rules as $temp_rule) {
			$country_id = $temp_rule['country_id'];
			unset($temp_rule['country_id']);
			$category_id = $temp_rule['category_id'];
			unset($temp_rule['category_id']);
			if (!array_key_exists($temp_rule['shippingrule_id'], $rules)) {
				$temp_rule['country_id'] = array($country_id);
				$temp_rule['category_id'] = array($category_id);
				$rules[$temp_rule['shippingrule_id']] = $temp_rule;
			}
			else {
				if (!in_array($country_id,$rules[$temp_rule['shippingrule_id']]['country_id'])) {
					$rules[$temp_rule['shippingrule_id']]['country_id'][] = $country_id;
				}
				if (!in_array($category_id, $rules[$temp_rule['shippingrule_id']]['category_id'])) {
					$rules[$temp_rule['shippingrule_id']]['category_id'][] = $category_id;
				}
			}
		}
		$this->view->rules = $rules;
	}
}

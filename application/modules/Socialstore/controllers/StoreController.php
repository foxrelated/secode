<?php

// public page
class Socialstore_StoreController extends Core_Controller_Action_Standard{
	public function init(){
		// set active menu on navigation
		Zend_Registry::set('active_menu','socialstore_main_store');
		$this->view->headScript()
    	->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places');
		
	}
	public function changeMultiLevelAction() {
		
		$category_id = $this->_getParam('id');
		$model_class =  $this->_getParam('model');
		$name =  $this->_getParam('name');	
		$level = $this->_getParam('level');
		$model =  new $model_class;
		$item =  $model->find((string)$category_id)->current();		
		
		if(!is_object($item)){
			return ;
		}
		if ($level != ($item->level - 1)) {
			return;
		}
		$options =  $model->getMultiOptions($item->getIndexTree($item->getLevel()));		
		if(count($options)<2){
			return ;
		}
		$route = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
	 	$element = new Zend_Form_Element_Select(
				sprintf("%s_%s",$name, $level+1),
				array(
					'multiOptions'=> $options,
					'onchange'=>"en4.store.changeCategory($(this),'".$name."','".$model_class."','".$route."')",
				)
			);
			
			
		echo $element->renderViewHelper();
		
	}
  	
  	
	public function rateStoreAction()
	{
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        if( !$this->_helper->requireUser()->isValid() ) return;

        $store_id = (int) $this->_getParam('store_id');
        $rates = (int)  $this->_getParam('rates');

        $viewer = Engine_Api::_()->user()->getViewer();

        if ($rates == 0 || $store_id == 0)
        {
            return;
        }
        
        $store = Engine_Api::_()->getItem('social_store', $store_id);
        $can_rate = Engine_Api::_()->getApi('store','Socialstore')->canRate($store,$viewer->getIdentity());
        // Check user rated
        if (!$can_rate)
        {
            return;
        }            
        $rateTable = Engine_Api::_()->getDbtable('rates', 'Socialstore');
        $db = $rateTable->getAdapter();
        $db->beginTransaction();
        try
        {
            $rate = $rateTable->createRow();
            $rate->user_id = $viewer->getIdentity();
            $rate->item_id = $store_id;
            $rate->rate_number  = $rates;
            $rate->save();
            $rates = Engine_Api::_()->getApi('store','Socialstore')->getAVGrate($store_id);
            $store->rate_ave = $rates;
            $store->save();
            // Commit
            $db->commit();
        }

        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }
        $route = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
        return $this->_redirect($route."/detail/".$store_id."/".$store->makeSlug());
	}

	public function editStoreAction(){
		
	}
	
	public function editProductAction(){
		
	}

	public function storeDetailAction() 
	{
		$store_id = $this->_getParam('store_id');
		$store = Engine_Api::_()->getItem('social_store', $store_id);
		if($store)
		{
			if (!Engine_Api::_() -> core() -> hasSubject($store -> getType()))
			{
				Engine_Api::_() -> core() -> setSubject($store);
			}
		}
		Zend_Registry::set('store_detail_id', $store_id);
		$this->_helper->content
         ->setNoRender()
           ->setEnabled();
	}
	
	public function frontAction() 
	{
		$store_id = $this->_getParam('store_id');
		$store = Engine_Api::_()->getItem('social_store', $store_id);
		if($store)
		{
			if (!Engine_Api::_() -> core() -> hasSubject($store -> getType()))
			{
				Engine_Api::_() -> core() -> setSubject($store);
			}
		}
		Zend_Registry::set('store_detail_id', $store_id);
		$this->_helper->content
         ->setNoRender()
           ->setEnabled();
	}
}

<?php

/**
 * @author Nam Nguyen <namnv@younetco.com>
 * @since 4.10
 */
 
 class Ynmobile_Helper_Ultimatenews_Feed extends Ynmobile_Helper_Base{
 	
	public $contentParams = array(
		'limit'=>5,
	);
	
	
	function getYnmobileApi(){
        return Engine_Api::_()->getApi('ultimatenews','ynmobile');
    }
	
	public function field_id(){
		$this->data['iFeedId'] = $this->entry->getIdentity();
	}
	
	public function field_topContents(){
		$this->data['aTopContents'] =  array();
		
		$params  =  Zend_Registry::get('Ynmobile_Helper_Ultimatenews_Feed_Params');
		$params['category_id'] = $this->entry->getIdentity();
		
		foreach($this->entry->getTopContents($params) as $content){
			$this->data['aTopContents'][]  = Ynmobile_AppMeta::_export_one($content, array('listing'));
		}
	}
	
	public function field_subscribe(){
		$this->data['bIsSubscribe'] =  $this->entry->isSubscribe() == 'subscribe'?0:1;
	}
	
	public function field_logo(){
		if(isset($this->entry->logo) && $this->entry->logo){
			if(strpos($this->entry->logo, 'http') !== false){
				$this->data['imgIcon'] =  trim($this->entry->logo, '.');	
			}else{
				$this->data['imgIcon'] = self::getBaseUrl() .  trim($this->entry->logo, '.');
			}
		}else {
			$this->data['imgIcon'] = "";
		}
	}
	
	public function field_keyword(){
		$this->data['sKeywords'] =  $this->entry->getKeywords();
	}
	
	public function field_listing(){
		
		$this->field_id();
		$this->field_type();
		$this->field_title();
		$this->field_desc();
		$this->field_logo();
		$this->field_subscribe();
		$this->field_keyword();
		$this->field_href();
		$this->field_category();
	}
	
	public function field_category(){
		$this->data['iCategoryId'] =  $this->entry->category_parent_id;
		
		if($this->entry->category_parent_id){
			if($category =  $this->getYnmobileApi()->getCategoryById($this->entry->category_parent_id)){
				$this->data['sCategoryTitle'] =  $category->category_name;
				
			}
		}
	}
	
	public function field_infos(){
		$this->field_listing();
		
			
	}
 }

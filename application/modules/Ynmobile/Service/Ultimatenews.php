<?php

/**
 * @author Nam Nguyen <namnv@younetco.com>
 * @since 4.10 
 */
 class Ynmobile_Service_Ultimatenews extends Ynmobile_Service_Base{
 	
	
	protected $module = 'ultimatenews';
    protected $mainItemType = 'ultimatenews_content';
	
	/**
	 * fetch for home pages
	 */
	public function browse_feed($aData){
		extract($aData);
		$iLimit = $iLimit?intval($iLimit):10;
		$iPage = $iPage?intval($iPage):1;
		$iContentLimit  = @$iContentLimit?$iContentLimit:6;
		$iCategoryId  =  isset($iCategoryId)?$iCategoryId:-1;
		$sSearch =  @$sSearch?"%{$sSearch}%":"";
		
		if($iPage >1){
			return array();
		}
		
		$select = Engine_Api::_()->ultimatenews()->getCategoriesSelect(true, array(
            'category_id' => $iFeedId, 
            'number_article' => $iContentLimit,
            // 'search' => $sSearch, 
            //'order' => 'pubDate DESC', 
            'is_active' => 1, 
            'getcommment' => true,
            'start_date' => $sStartDate, 
            'end_date' => $sEndDate, 
            'group' => 'feed', 
            // 'limit' => 100, 
            'category_parent' => $iCategoryId,
        ));
		
		Zend_Registry::set('Ynmobile_Helper_Ultimatenews_Feed_Params', array(
			'limit'=>$iContentLimit,
			'search'=> $sSearch,
			'start_date'=>$sStartDate,
			'end_date'=>$sEndDate,
			'order'=>'pubDate DESC',
			'getcommment'=>true,
		));
		
		return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, array('listing','topContents'));
	}
	
	public function formsearch_content($aData){
		return array(
			'categoryOptions'=>$this->categoryOptions(),
			'feedOptions'=>$this->feedOptions(),
		);
	}
	
	public function fetch_feed($aData){
		extract($aData);
		
		$table  =  Engine_Api::_()->getDbtable('categories', 'ultimatenews');
		
		$select =  $table->select();
		$select->where('is_active=?',1)->order('category_name');
		
		if($iPage >1 ){
			return array();
		}
		
		$items =  Ynmobile_AppMeta::_exports_by_page($select, $iPage, 10000, array('listing'));
		
		if($sView == 'my'){
			$return = array();
			
			foreach($items as $item){
				if($item['bIsSubscribe']){
					$return[] = $item;
				}
			}
			return $return;
		}
		return $items;
	}
	
	public function getFeedById($iFeedId){

		$table  =  Engine_Api::_()->getDbtable('categories', 'ultimatenews');
		
		$select =  $table->select()->where('category_id=?', intval($iFeedId));
		
		return $table->fetchRow($select);
	}

	public function getCategoryById($iCategoryId){
		$table  =  Engine_Api::_()->getDbtable('categoryparents', 'ultimatenews');
		
		$select =  $table->select()->where('category_id=?', $iCategoryId);
		
		return $table->fetchRow($select);
	}
	
	public function feed_info($aData){
		extract($aData);
		
		$feed =  $this->getFeedById($iFeedId);
		
		return Ynmobile_AppMeta::_export_one($feed, array('infos'));
	}
	
	public function categoryOptions(){
		$table  =  Engine_Api::_()->getDbtable('categoryparents', 'ultimatenews');
		
		$select =  $table->select();
		$select->where('is_active=?',1)->order('category_name');
		
		$result  =  array(
			array(
				'id'=>-1,
				'title'=>'All Categories',
				'aFeeds'=>$this->feedOptionsByCategoryId(-1),
			)
		);
		
		foreach($table->fetchAll($select) as $row){
			$result[] = array(
				'id'=>$row->category_id,
				'title'=>$row->category_name,
				'aFeeds'=> $this->feedOptionsByCategoryId($row->category_id),
			);
		}

		$result[] =  array(
			'id'=>0,
			'title'=>'Other',
			'aFeeds'=> $this->feedOptionsByCategoryId(0),
		);
		
		return $result;
	}
	
	public function feedOptionsByCategoryId($id){
		$table  =  Engine_Api::_()->getDbtable('categories', 'ultimatenews');
		
		$select =  $table->select();
		$select->where('is_active=?',1)->order('category_name');
		
		if($id == -1){
			
		}
		if($id == 0){
			$select->where('category_parent_id=?',0);
		}else
		if($id >0){
			$select->where('category_parent_id=?',$id);
		}
		
		$result  =  array();
		
		foreach($table->fetchAll($select) as $row){
			$result[] = array(
				'id'=>$row->category_id,
				'title'=>$row->category_name,
			);
		}
		
		return $result;
	}

	
	public function feedOptions(){
		$table  =  Engine_Api::_()->getDbtable('categories', 'ultimatenews');
		
		$select =  $table->select();
		$select->where('is_active=?',1)->order('category_name');
		
		$result  =  array();
		
		foreach($table->fetchAll($select) as $row){
			$result[] = array(
				'id'=>$row->category_id,
				'title'=>$row->category_name,
			);
		}
		
		return $result;
	}
	
	public function fetch_content($aData){
		
		extract($aData);
		
		$table = Engine_Api::_()->getDbtable('contents', 'ultimatenews');
        $select = $table->select();
		
		
		$select->where('category_id=?', intval($iFeedId))->order('pubDate desc');
		
		return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, array('infos'));
	}
	
	public function content_info($aData){
		extract($aData);
		
		$iContentId  = intval($iContentId);
		
		$content  =  Engine_Api::_()->getItem('ultimatenews_content',$iContentId);
		
		if(!$content){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Content not found.")
			);
		}
		
		return Ynmobile_AppMeta::_export_one($content, array('listing','infos'));
	}
	
	public function subscribe($aData){
		
		extract($aData);
		
		$feed =  $this->getFeedById($iFeedId);
		
		if(!$feed){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Feed not found.")
			);
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (empty($viewer)) {
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Logged required.")
			);
		}

		$users = Zend_Json::decode($feed -> subscribe);
		$users[$viewer -> getIdentity()] = $viewer -> getIdentity();
		$feed -> subscribe = Zend_Json::encode($users);
		$feed -> save();
		
		return array(
			'error_code'=>0,
			'message'=>Zend_Registry::get('Zend_Translate') -> _("Subscribe successfully"),
			'aItem'=>Ynmobile_AppMeta::_export_one($this->getFeedById($iFeedId), array('infos')),
		);
	}
	
	public function unsubscribe($aData){
		
		extract($aData);
		
		$feed =  $this->getFeedById($iFeedId);
		
		if(!$feed){
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Feed not found.")
			);
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (empty($viewer)) {
			return array(
				'error_code'=>1,
				'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Logged required.")
			);
		}

		$users = Zend_Json::decode($feed -> subscribe);
		
		if(isset($users[$viewer -> getIdentity()])){
			unset($users[$viewer -> getIdentity()]);
			$feed -> subscribe = Zend_Json::encode($users);
			$feed -> save();
		}
		
		
		return array(
			'error_code'=>0,
			'message'=>Zend_Registry::get('Zend_Translate') -> _("Un-subscribe successfully"),
			'aItem'=>Ynmobile_AppMeta::_export_one($this->getFeedById($iFeedId), array('infos')),
		);
	}
	
 }

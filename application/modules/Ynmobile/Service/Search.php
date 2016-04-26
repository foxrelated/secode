<?php

class Ynmobile_Service_Search extends Ynmobile_Service_Base {
    
    protected $module = 'none';
	
	protected $_types;
	
	public function getAvailableTypes()
  	{
    	if( null === $this->_types ) {
      	$this->_types = Engine_Api::_()->getDbtable('search', 'core')->getAdapter()
	        ->query('SELECT DISTINCT `type` FROM `engine4_core_search`')
	        ->fetchAll(Zend_Db::FETCH_COLUMN);
	      $this->_types = array_intersect($this->_types, Engine_Api::_()->getItemTypes());
		  
		  $this->_types = array_intersect($this->_types, array (
			  'advgroup_album',
			  'advgroup_photo',
			  'advgroup_poll',
			  'advgroup_post',
			  'group_topic',
			  'group_album',
			  'group_photo',
			  'group_poll',
			  // 'group_post',
			  'group_topic',
			  'album',
			  'album_photo',
			  'advalbum_album',
			  'advalbum_photo',
			  'blog',
			  'classified',
			  'classified_album',
			  // 10 => 'core_link',
			  'event',
			  'event_photo',
			  'ynevent',
			  'ynevent_photo',
			  
			  // 'event_post',
			  'event_topic',
			  'forum',
			  'forum_post',
			  'forum_topic',
			  'advforum_forum',
			  'advforum_post',
			  'advforum_topic',
			  'group',
			  'music_playlist',
			  // 'music_playlist_song',
			  'poll',
			  'ultimatenews_content',
			  'user',
			  'video',
			  // 28 => 'ynchat_file',
			  // 29 => 'ynforum_category',
			  'ynjobposting_company',
			  // 'ynjobposting_industry',
			  'ynjobposting_job',
			  'ynlistings_listing',
			));
	    }
		
		// echo var_export($this->_types); exit;

	    return $this->_types;
	}
    
    function available_types(){
        
        $availableTypes  = $this->getAvailableTypes();
        
        $view  = Zend_Registry::get('Zend_View');
        
        // require to reorder by alphabet ascendent
        $maps = array();
        $options  = array();
        
        
        foreach($availableTypes as $type){
            $maps[$type] = $view->translate(strtoupper('ITEM_TYPE_' . $type)); 
        }
        asort($maps, SORT_LOCALE_STRING);
        
        foreach($maps as $id=>$title){
            $options[] =array(
                'id'=>$id,
                'title'=>$title,
            );
        } 
        
        // sort($options);
        
        return $options;
    }
    
    /**
     *
     */
    function fetch($aData) {
        extract($aData);
        
        $fields = array('id','title','desc','type');
        
        $sSearch = isset($sSearch) ? $sSearch : '';
        $iPage = isset($iPage) ? $iPage : 1;
        $iLimit =  isset($iLimit)?$iLimit:10;
        $sType =  isset($sFilterBy)?$sFilterBy: 'user';

        if (empty($sSearch)) {
            return array();
        }

        $searchApi = Engine_Api::_() -> getApi('search', 'core');

        // Get available types
        $availableTypes = $this -> getAvailableTypes();


        $paginator = $searchApi -> getPaginator($sSearch, $sType);
        
        $paginator -> setCurrentPageNumber($iPage);
        $paginator -> setItemCountPerPage($iLimit);
        
        if ($iPage > $paginator -> count()) {
            return array();
        }

        $return = array();

        $view = Zend_Registry::get('Zend_View');
        
        // $appMeta = Ynmobile_AppMeta::getInstance();
        
        // $fields = array('simple_array');

        foreach ($paginator as $entry) {
            
            $item = Engine_Api::_() -> getItem($entry -> type, $entry -> id);

            if (!$item){
                continue;
            }   

            try {
                $img = $view->itemPhoto($item, 'thumb.icon');
                $sImage = '';
                if(preg_match("#src=\"([^\"]+)\"#", $img, $match)){
                    $sImage  = Engine_Api::_()->ynmobile()->finalizeUrl($match[1]);
                }
                
                // $result =  $appMeta->getModelHelper($item)->toArray($fields);;
                // $result['imgIcon'] =  $sImage;

                $id = $item->getIdentity();
                $type = $item->getType();

                // return topic in stead of post
                if ($type == 'forum_post' || $type == 'advgroup_post' || $type ==  'group_post') {
                    $parent = $item->getParent();
                    $id = $parent->getIdentity();
                    $type = $parent->getType();
                }

                $id = $item->getIdentity();
                $type = $item->getType();

                // return topic in stead of post
                if ($type == 'forum_post' || $type == 'advgroup_post' || $type ==  'group_post') {
                    $parent = $item->getParent();
                    $id = $parent->getIdentity();
                    $type = $parent->getType();
                }

                $return[] = array(
                    'id'=>$id,
                    'type'=>$type,
                    'title'=>$item->getTitle(),
                    'description'=>$item->getDescription(),
                    'img'=>$sImage,
                );

            } catch(Exception $ex) {
                return array('error_code'=>1,'error_message'=>$ex->getMessage());
            }
        }

        return $return;
    }

}

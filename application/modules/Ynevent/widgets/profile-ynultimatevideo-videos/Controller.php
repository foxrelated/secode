<?php

class Ynevent_Widget_ProfileYnultimatevideoVideosController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	
	public function indexAction() {
		// Don't render if module not available
		if( !Engine_Api::_()->hasModuleBootstrap('ynultimatevideo')) {
			return $this->setNoRender();
		}
		// Don't render this if not authorized
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_()->core()->hasSubject()) {
			return $this->setNoRender();
		}
		
		// Get subject and check auth
		$subject = Engine_Api::_()->core()->getSubject('event');
		if (!$subject->authorization()->isAllowed($viewer, 'view')) {
			return $this->setNoRender();
		}

		// Prepare data
		$this->view->event = $event = $subject;
			
	    //Get viewer, event, search form
	   	$viewer = Engine_Api::_() -> user() -> getViewer();
	   	$this -> view -> event = $event = Engine_Api::_() -> core() -> getSubject();

		if ( !$event->authorization()->isAllowed($viewer, 'view') ) { return; }
	   
	    // Check create video authorization
		$canCreate = $event -> authorization() -> isAllowed($viewer, 'video');
		$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('event', $viewer, 'video');
		
		if ($canCreate && $levelCreate) {
			$this -> view -> canCreate = true;
		} else {
			$this -> view -> canCreate = false;
		}
	
	    //Prepare data filer
	    $params = array();
	    $params = $this->_getAllParams();
	    $params['title'] = '';
	    $params['parent_type'] = 'event';
	    $params['parent_id'] = $event->getIdentity();
	    $params['search'] = 1;
	    $params['status'] = 1;
	    $params['limit'] = 12;

		//Get data from table video
		$tableVideo = Engine_Api::_()->getItemTable('ynultimatevideo_video');
		$select = $tableVideo -> select() 
			-> from($tableVideo -> info('name'), new Zend_Db_Expr("`video_id`"))
			-> where('parent_type = "event"') 
			-> where('parent_id = ?', $event -> getIdentity());
		$video_ids = $tableVideo -> fetchAll($select);
		//Merge ids
		foreach($video_ids as $video_id)
		{
			$params['ids'][] = $video_id -> video_id;
		}

	    //$paginator = Engine_Api::_() -> ynvideo() -> getVideosPaginator($params);
	    $paginator = $event -> getYnultimatevideoVideosPaginator($params);
	    $this -> view -> paginator = $paginator;
	    
		// Add count to title if configured
	    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
	      $this->_childCount = $paginator->getTotalItemCount();
	    }
	}
	
	public function getChildCount()
	{
	    return $this->_childCount;
	}
}
<?php

class Ynidea_Widget_IdeasProfileAuthorsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Don't render this if not authorized
	    $viewer = Engine_Api::_()->user()->getViewer();
	    if( !Engine_Api::_()->core()->hasSubject() ) {
	      return $this->setNoRender();
	    }
	
	    // Get subject and check auth
	    $subject = Engine_Api::_()->core()->getSubject('ynidea_idea');
	    $this->view->idea = $subject; 
		$params = array();
		$params['limit'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('idea.page', 10);
		$params['page'] = $this->_getParam('page', 1);
		$params['idea_id'] = $subject->idea_id; 
		$this->view->coauthors = $coauthors = Engine_Api::_()->getApi('core', 'ynidea')->getCoauthorsPaginator($params);
		if( $coauthors->getTotalItemCount() <= 0 ) {
      		return $this->setNoRender();
    	}                      
    }

}

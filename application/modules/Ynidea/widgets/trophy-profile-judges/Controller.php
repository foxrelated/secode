<?php

class Ynidea_Widget_TrophyProfileJudgesController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Don't render this if not authorized
	    $viewer = Engine_Api::_()->user()->getViewer();
	    if( !Engine_Api::_()->core()->hasSubject() ) {
	      return $this->setNoRender();
	    }
	
	    // Get subject and check auth
	    $subject = Engine_Api::_()->core()->getSubject('ynidea_trophy');
	    $this->view->trophy = $subject;      
		$params = array();
		$params['limit'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('idea.page', 10);
		$params['page'] = $this->_getParam('page', 1);
		$params['trophy_id'] = $subject->trophy_id;
		$this->view->judges = $judges = Engine_Api::_()->getApi('core', 'ynidea')->getJudgesPaginator($params);
		                    
    }

}

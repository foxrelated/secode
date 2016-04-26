<?php

class Ynidea_Widget_TrophyProfileNomineesController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
    	$headScript = new Zend_View_Helper_HeadScript();
   		$headScript -> appendFile('application/modules/Ynidea/externals/scripts/TabContent.js');
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
		$params['trophy_id'] = $subject->trophy_id;
        $params['orderby'] = 'title';
		$params['direction'] = 'ASC';
        $this->view->alphabetic = Engine_Api::_()->getApi('core', 'ynidea')->getIdeaPaginator($params);
		
		$params['orderby'] = 'score';
		$params['direction'] = 'DESC';
        $this->view->ranking = Engine_Api::_()->getApi('core', 'ynidea')->getIdeaPaginator($params);
		
		$params['orderby'] = 'ideal_score';
		$params['direction'] = 'DESC';
        $this->view->public_ranking = Engine_Api::_()->getApi('core', 'ynidea')->getIdeaPaginator($params);
		
		
		/*
		$params['orderby'] = 'vote_ave';
		$params['direction'] = 'ASC';
        $this->view->vote = Engine_Api::_()->getApi('core', 'ynidea')->getIdeaPaginator($params); 
		 */                  
    }

}

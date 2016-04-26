<?php

class Ynidea_Widget_IdeasProfileAwardsController extends Engine_Content_Widget_Abstract
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
		$awards = $subject->getAwards();
		if(count($awards) <= 0)
			$this->setNoRender();
		$this->view->awards =  $awards;                  
    }

}

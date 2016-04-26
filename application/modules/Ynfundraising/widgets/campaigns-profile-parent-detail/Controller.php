<?php

class Ynfundraising_Widget_CampaignsProfileParentDetailController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Don't render this if not authorized
	    $viewer = Engine_Api::_()->user()->getViewer();
	    if( !Engine_Api::_()->core()->hasSubject() ) {
	      return $this->setNoRender();
	    }
	
	    // Get subject and check auth
	    $subject = Engine_Api::_()->core()->getSubject('ynfundraising_campaign');
	    $this->view->campaign = $subject;     
		$this->view->parent = $parent = Engine_Api::_()->getApi('core', 'ynfundraising')->getItemFromType($subject);
		if(!$parent)
		{
			$this->setNoRender();
		}    
    }

}

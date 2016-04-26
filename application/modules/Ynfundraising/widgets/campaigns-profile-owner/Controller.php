<?php

class Ynfundraising_Widget_CampaignsProfileOwnerController extends Engine_Content_Widget_Abstract
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
		$this->view->owner = $subject->getOwner();
		$this->view->avgrating = Engine_Api::_()->getApi('core', 'ynfundraising')->getAvgUserRating($subject->getOwner()->getIdentity());
		$this->view->totalRates = Engine_Api::_()->getApi('core', 'ynfundraising')->getTotalRating($subject->getOwner()->getIdentity());
		$this->view->totalCampaigns = Engine_Api::_()->getApi('core', 'ynfundraising')->getTotalCampaign($subject->getOwner()->getIdentity());
    }

}

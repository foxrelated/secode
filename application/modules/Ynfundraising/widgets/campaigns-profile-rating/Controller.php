<?php

class Ynfundraising_Widget_CampaignsProfileRatingController extends Engine_Content_Widget_Abstract
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
		$this->view->avgrating = Engine_Api::_()->getApi('core', 'ynfundraising')->getAvgCampaignRating($subject->getIdentity());
		$this->view->totalRates = Engine_Api::_()->getApi('core', 'ynfundraising')->getTotalRatingCampaign($subject->getIdentity());
		if(($viewer->getIdentity() > 0 && $viewer->getIdentity() != $subject->getOwner()->getIdentity() && $subject->checkDonor($viewer->getIdentity())) || ($viewer->getIdentity() == $subject->getOwner()->getIdentity() && Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfundraising.rate', 0)))
			$this->view->can_rate = Engine_Api::_()->getApi('core', 'ynfundraising')->checkCampaignRating($subject->getIdentity(),$viewer->getIdentity());
		else {
			$this->view->can_rate = false;
		}
    }

}

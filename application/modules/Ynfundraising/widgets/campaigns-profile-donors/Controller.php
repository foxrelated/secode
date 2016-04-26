<?php

class Ynfundraising_Widget_CampaignsProfileDonorsController extends Engine_Content_Widget_Abstract
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
		$limit = 5;
        if($this->_getParam('number') != '' && $this->_getParam('number') >= 0)
        {
            $limit = $this->_getParam('number');
        }
		$values = array("campaign" => $subject->getIdentity(), 'limit' => $limit);
		$this->view->donors = $donors = Engine_Api::_()->getApi('core', 'ynfundraising')->getDonorPaginator($values);
		if(!$donors->getTotalItemCount())
		{
			$this->setNoRender();
		}  
		$this->view->total = $donors->getTotalItemCount();             
    }

}

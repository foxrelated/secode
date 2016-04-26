<?php

class Ynfundraising_Widget_CampaignsProfileSupportersController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Don't render this if not authorized
	    $viewer = Engine_Api::_()->user()->getViewer();
	    if( !Engine_Api::_()->core()->hasSubject() ) {
	      return $this->setNoRender();
	    }
		$limit = 9;
        if($this->_getParam('number') != '' && $this->_getParam('number') >= 0)
        {
            $limit = $this->_getParam('number');
        }
	    // Get subject and check auth
	    $subject = Engine_Api::_()->core()->getSubject('ynfundraising_campaign');
	    $this->view->campaign = $subject;    
		$params = array("campaign" => $subject->getIdentity(), "limit" => $limit); 
		$this->view->supporters = $supporters = Engine_Api::_()->getApi('core', 'ynfundraising')->getSupporterPaginator($params);
		if(!$supporters->getTotalItemCount())
		{
			$this->setNoRender();
		}  
		$this->view->total = $supporters->getTotalItemCount();       
    }

}

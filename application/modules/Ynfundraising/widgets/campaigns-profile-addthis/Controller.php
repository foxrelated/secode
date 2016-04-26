<?php

class Ynfundraising_Widget_CampaignsProfileAddthisController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
    	$request = Zend_Controller_Front::getInstance ()->getRequest ();
        // Don't render this if not authorized
	    $viewer = Engine_Api::_()->user()->getViewer();
	    if( !Engine_Api::_()->core()->hasSubject() ) {
	      return $this->setNoRender();
	    }
	
	    // Get subject and check auth
	    $subject = Engine_Api::_()->core()->getSubject('ynfundraising_campaign');
	    $this->view->campaign = $subject;  
		
		$backId = $request->getParam('user');
		if(isset($backId) && !empty($backId)) {
            if($backId != $viewer->getIdentity()) {
            	if(Engine_Api::_ ()->getItem ( 'user', $backId ))
            		Engine_Api::_()->getApi('core', 'ynfundraising')->insertSupporter($backId,$subject->campaign_id);
                $subject->click_count ++;
				$subject->save();
            }
        }
		
		$this->view->shares = $shares =  $subject->share_count; 
		$this->view->clicks = $clicks =  $subject->click_count; 
		$this->view->viralLift = $viralLift  = ($shares!=0)?round(($clicks*100)/$shares,2):'0';         
		$this->view->token = $token = md5($viewer->getIdentity());	
		$this->view->user_id = $viewer->getIdentity();
		$this->view->pubid = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfundraising.pubid',"");
    }

}

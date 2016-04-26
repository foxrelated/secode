<?php

class Ynfundraising_Widget_CampaignsProfileFundraisingGoalController extends Engine_Content_Widget_Abstract
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
		$this->view->goal = $goal =  $subject->goal; 
		$this->view->total_amount = $amount =  $subject->total_amount?$subject->total_amount:'0'; 
		$this->view->percent = $percent  = ($goal!=0)?round(($amount*100)/$goal,2):'0';     
		$this->view->percent = ($percent>100)?100:$percent;
		$this->view->sponsor_levels = $subject->getSponsorLevels();         
    }

}

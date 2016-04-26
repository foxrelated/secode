<?php
class Ynfundraising_Widget_CampaignsForParentController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		 if( !Engine_Api::_()->core()->hasSubject() ) {
	      return $this->setNoRender();
	    }
	
	    // Get subject and check auth
	    $subject = Engine_Api::_()->core()->getSubject();
		if(!$subject->checkExistCampaign())
			 return $this->setNoRender();
		$this->view->campaign = $campaign = $subject->checkExistCampaign();
		$values = array("campaign" => $campaign->getIdentity(), 'limit' => 5);
		$this->view->donors = $donors = Engine_Api::_()->getApi('core', 'ynfundraising')->getDonorPaginator($values);
		$this->view->goal = $goal =  $campaign->goal; 
		$this->view->total_amount = $amount =  $campaign->total_amount?$campaign->total_amount:'0'; 
		$percent  = ($goal!=0)?round(($amount*100)/$goal,2):'0';   
		$this->view->percent = ($percent>100)?100:$percent;
		$view = $this->view;
    	$view->addHelperPath(APPLICATION_PATH . '/application/modules/Ynfundraising/views/helpers','Ynfundraising_View_Helper');
	}
}
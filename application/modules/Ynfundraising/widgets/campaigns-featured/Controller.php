<?php
class Ynfundraising_Widget_CampaignsFeaturedController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$headScript = new Zend_View_Helper_HeadScript();
   		$headScript -> appendFile('application/modules/Ynfundraising/externals/scripts/jquery-1.9.1.min.js');
   		$headScript -> appendFile('application/modules/Ynfundraising/externals/scripts/jquery.divslideshow-1.2-min.js');
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$limit = 5;
        if($this->_getParam('number') != '' && $this->_getParam('number') >= 0)
        {
            $limit = $this->_getParam('number');
        }
		$params = array('limit' => $limit, 'featured' => '1', 'status' => Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS);
		$campaigns = Engine_Api::_()->getApi('core', 'ynfundraising')->getCampaignPaginator($params);
		$this->view->campaigns = $campaigns;
		if(!$campaigns->getTotalItemCount())
			$this->setNoRender();	
		$view = $this->view;
    	$view->addHelperPath(APPLICATION_PATH . '/application/modules/Ynfundraising/views/helpers','Ynfundraising_View_Helper');
	}
}
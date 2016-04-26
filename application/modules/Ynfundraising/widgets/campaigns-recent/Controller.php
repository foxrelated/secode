<?php
class Ynfundraising_Widget_CampaignsRecentController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$limit = 8;
        if($this->_getParam('number') != '' && $this->_getParam('number') >= 0)
        {
            $limit = $this->_getParam('number');
        }
		$params = array('limit' => $limit, 'orderby' => 'campaign_id', 'direction' => 'DESC', 'status' => Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS);
		$campaigns = Engine_Api::_()->getApi('core', 'ynfundraising')->getCampaignPaginator($params);
		$this->view->campaigns = $campaigns;
		if(!$campaigns->getTotalItemCount())
			$this->setNoRender();
		$view = $this->view;
    	$view->addHelperPath(APPLICATION_PATH . '/application/modules/Ynfundraising/views/helpers','Ynfundraising_View_Helper');
	}
}
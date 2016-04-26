<?php
class Ynfundraising_Widget_ProfileCampaignsController extends Engine_Content_Widget_Abstract 
{
	protected $_childCount;		
	public function indexAction()
	{
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$limit = 8;
        if($this->_getParam('number') != '' && $this->_getParam('number') >= 0)
        {
            $limit = $this->_getParam('number');
        }
		$subject = Engine_Api::_()->core()->getSubject();
		
		$params = array('user_id' => $subject->getIdentity(), 'limit' => $limit, 'orderby' => 'campaign_id', 'direction' => 'DESC', 'status' => Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS);
		$campaigns = Engine_Api::_()->getApi('core', 'ynfundraising')->getCampaignPaginator($params);
		$this->view->campaigns = $campaigns;
		$view = $this->view;
    	$view->addHelperPath(APPLICATION_PATH . '/application/modules/Ynfundraising/views/helpers','Ynfundraising_View_Helper');
		 // Add count to title if configured
        if( $this->_getParam('titleCount', false) && $campaigns->getTotalItemCount() > 0 ) 
        {
          $this->_childCount = $campaigns->getTotalItemCount();
        }
		
		if(!$campaigns->getTotalItemCount())
			$this->setNoRender();
	}
	public function getChildCount()
	{
	   return $this->_childCount;
	}
}
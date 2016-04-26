<?php
class Ynfundraising_Widget_MenuStatisticsChartlistController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		// Data preload
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$params = array ();

		$request = Zend_Controller_Front::getInstance ()->getRequest ();

		$params = $request->getParams ();

		$campaign = Engine_Api::_()->getItem('ynfundraising_campaign', $params['campaign_id']);
		if (!$campaign) {
			return $this->setNoRender();
		}
		$this->view->campaign = $campaign;

	}
}
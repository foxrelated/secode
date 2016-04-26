<?php
class Groupbuy_Content_Widget_Listing extends Engine_Content_Widget_Abstract {
	
	public function init(){
		$this -> setScriptPath('application/modules/Groupbuy/widgets/latest-deals');
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->user_id = $viewer->getIdentity();
	}
	/**
	 * @return  int limit [5,10]
	 */
	public function getLimit() {		
		$limit = (int)$this -> _getParam('max');
		$limit = $limit < 1 ? 5 : $limit;
		return $limit > 10 ? 5 : $limit;
	}

}

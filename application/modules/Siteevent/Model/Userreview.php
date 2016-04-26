<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Userreview.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_Userreview extends Core_Model_Item_Abstract {
protected $_parent_type = 'user';
	/**
	* Return rich content for feed items
	**/
	public function getRichContent() {
			$view = Zend_Registry::get('Zend_View');
			$view = clone $view;
			$view->clearVars();
			$view->addScriptPath('application/modules/Siteevent/views/scripts/');

			// Render the thingy
			$view->review = $this;
			$view->event_id = $this->event_id;
			$view->user_id = $this->user_id;
			$view->ratingValue = Engine_Api::_()->getDbtable('userreviews', 'siteevent')->myRatings(array('viewer_id' => $this->viewer_id, 'event_id' => $this->event_id, 'user_id' => $this->user_id));
			return $view->render('activity-feed/_userreview.tpl');
	}
  
      public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => "siteevent_user_review",
            'reset' => true,
            'event_id' => $this->event_id,
            // 'review_id' => $this->review_id,
            'user_id' => $this->user_id,
                //'tab' => $content_id,
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }
}
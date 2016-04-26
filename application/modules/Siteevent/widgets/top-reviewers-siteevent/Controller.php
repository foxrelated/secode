<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_TopReviewersSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //GET SETTINGS
        $params = array();
        $params['limit'] = $this->_getParam('itemCount', 3);
        $this->view->type = $params['type'] = $this->_getParam('type', 'user');

        $params['resource_type'] = 'siteevent_event';

        //GET RESULTS
        $this->view->reviewers = Engine_Api::_()->getDbtable('reviews', 'siteevent')->topReviewers($params);

        //DON'T RENDER IF NO DATA
        if (Count($this->view->reviewers) <= 0) {
            return $this->setNoRender();
        }
    }

}

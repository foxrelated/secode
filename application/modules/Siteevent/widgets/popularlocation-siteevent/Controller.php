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
class Siteevent_Widget_PopularlocationSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {


        $params = array();
        $params['limit'] = $this->_getParam('itemCount', 10);

        $params['eventType'] = $this->_getParam('eventType', 'upcoming');

        //DONT RENDER IF LOCATION IS DIS-ABLED BY ADMIN
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)) {
            return $this->setNoRender();
        }

        $this->view->category_id = 0;
        $this->view->subcategory_id = 0;
        $this->view->subsubcategory_id = 0;

        $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id');
        $this->view->subcategory_id = $params['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
        $this->view->subsubcategory_id = $params['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');

        //GET SITEEVENT SITEEVENT FOR MOST RATED
        $this->view->siteeventLocation = Engine_Api::_()->getDbTable('events', 'siteevent')->getPopularLocation($params);

        //DONT RENDER IF SITEEVENT COUNT IS ZERO
        if (!(count($this->view->siteeventLocation) > 0)) {
            return $this->setNoRender();
        }

        $this->view->searchLocation = null;
        if (isset($_GET['location']) && !empty($_GET['location'])) {
            $this->view->searchLocation = $_GET['location'];
        }
    }

}
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
class Siteevent_Widget_CategoriesHomeBreadcrumbController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $category_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);

        if (empty($category_id)) {
            return $this->setNoRender();
        }

        //GET USER SUBJECT    
        $this->view->category = Engine_Api::_()->getItem('siteevent_category', $category_id);

        if (empty($this->view->category)) {
            return $this->setNoRender();
        }
    }

}
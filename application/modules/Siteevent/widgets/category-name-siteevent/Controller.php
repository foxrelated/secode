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
class Siteevent_Widget_CategoryNameSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $siteeventCategoriesName = Zend_Registry::isRegistered('siteeventCategoriesName') ? Zend_Registry::get('siteeventCategoriesName') : null;

        $category_id = $request->getParam('subsubcategory_id', null);
        if (empty($category_id)) {
            $category_id = $request->getParam('subcategory_id', null);
            if (empty($category_id)) {
                $category_id = $request->getParam('category_id', null);
            }
        }

        if (empty($siteeventCategoriesName))
            return $this->setNoRender();

        if (empty($category_id)) {
            return $this->setNoRender();
        }

        //GET USER SUBJECT    
        $this->view->category = Engine_Api::_()->getItem('siteevent_category', $category_id);
    }

}
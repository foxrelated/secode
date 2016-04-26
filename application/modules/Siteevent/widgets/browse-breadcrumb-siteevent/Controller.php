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
class Siteevent_Widget_BrowseBreadcrumbSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->formValues = $values = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        //GET EVENT CATEGORY TABLE
        $this->view->categoryTable = $tableCategory = Engine_Api::_()->getDbTable('categories', 'siteevent');
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->category_id = $this->view->subcategory_id = $this->view->subsubcategory_id = 0;
        $this->view->category_name = $this->view->subcategory_name = $this->view->subsubcategory_name = '';

        $category_id = $request->getParam('category_id', null);

        $siteeventBrowseBreadcrumb = Zend_Registry::isRegistered('siteeventBrowseBreadcrumb') ? Zend_Registry::get('siteeventBrowseBreadcrumb') : null;
        if (empty($siteeventBrowseBreadcrumb))
            return $this->setNoRender();

        if (!empty($category_id)) {
            $this->view->category_id = $category_id;
            $this->view->category_name = $tableCategory->getCategory($category_id)->category_name;

            $subcategory_id = $request->getParam('subcategory_id', null);

            if (!empty($subcategory_id)) {
                $this->view->subcategory_id = $subcategory_id;
                $this->view->subcategory_name = $tableCategory->getCategory($subcategory_id)->category_name;

                $subsubcategory_id = $request->getParam('subsubcategory_id', null);

                if (!empty($subsubcategory_id)) {
                    $this->view->subsubcategory_id = $subsubcategory_id;
                    $this->view->subsubcategory_name = $tableCategory->getCategory($subsubcategory_id)->category_name;
                }
            }
        }

        if (((isset($values['tag']) && !empty($values['tag']) && isset($values['tag_id']) && !empty($values['tag_id'])))) {
            $current_url = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
            $current_url = explode("?", $current_url);
            if (isset($current_url[1])) {
                $current_url1 = explode("&", $current_url[1]);
                foreach ($current_url1 as $key => $value) {
                    if (strstr($value, "tag=") || strstr($value, "tag_id=")) {
                        unset($current_url1[$key]);
                    }
                }
                $this->view->current_url2 = implode("&", $current_url1);
            }
        }

        if (empty($category_id) || (isset($values['tag']) && empty($values['tag']))) {
            return $this->setNoRender();
        }
    }

}

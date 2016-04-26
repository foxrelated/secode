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
class Siteevent_Widget_LocationSearchController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        return $this->setNoRender();
        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $action = $front->getRequest()->getActionName();
        $controller = $front->getRequest()->getControllerName();

        if ($module != 'siteevent' || $controller != 'index' || $action != 'map') {
            return $this->setNoRender();
        }

        $valueArray = array('street' => $this->_getParam('street', 1), 'city' => $this->_getParam('city', 1), 'country' => $this->_getParam('country', 1), 'state' => $this->_getParam('state', 1));
        $list_street = serialize($valueArray);

//        $widgetSettings = array(
//            'priceFieldType' => $this->_getParam('priceFieldType', 'slider'),
//            'minPrice' => $this->_getParam('minPrice', 0),
//            'maxPrice' => $this->_getParam('maxPrice', 999)
//        );        
        // Make form
        $this->view->form = $form = new Siteevent_Form_Locationsearch(array('value' => $list_street, 'type' => 'siteevent_event'));

        if (!empty($_POST)) {
            $this->view->category_id = $_POST['category_id'];
            $this->view->subcategory_id = $_POST['subcategory_id'];
            $this->view->subsubcategory_id = $_POST['subsubcategory_id'];
            $this->view->category_name = $_POST['categoryname'];
            $this->view->subcategory_name = $_POST['subcategoryname'];
            $this->view->subsubcategory_name = $_POST['subsubcategoryname'];
        }

        if (!empty($_POST)) {
            $this->view->advanced_search = $_POST['advanced_search'];
        }

        // Process form
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        // $form->isValid($params);
        $values = $form->getValues();
        unset($values['or']);
        $this->view->formValues = array_filter($values);
        $this->view->assign($values);

        $siteeventLocationSearch = Zend_Registry::isRegistered('siteeventLocationSearch') ? Zend_Registry::get('siteeventLocationSearch') : null;
        $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name', 'category_slug'), null, 0, 0, 1);
        if (empty($siteeventLocationSearch))
            return $this->setNoRender();

        $categories_slug[0] = "";
        if (count($categories) != 0) {
            foreach ($categories as $category) {
                $categories_slug[$category->category_id] = $category->getCategorySlug();
            }
        }
        $this->view->categories_slug = $categories_slug;
    }

}
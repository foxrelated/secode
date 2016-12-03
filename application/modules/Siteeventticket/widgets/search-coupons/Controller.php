<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Widget_SearchCouponsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        
        //IF TICKET SETTING DISABLED FROM ADMIN SIDE
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1)) {
            return $this->setNoRender();
        }              

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.couponprivate', 0)) {
            return $this->setNoRender();
        }
        
        //GET THE COLUMN WHICH YOU WANT TO SHOW IN THE SEARCH WIDGET
        $this->view->showTabArray = $showTabArray = $this->_getParam("search_column", array("0" => "1", "1" => "2", "2" => "3", "3" => "4"));        

        $this->view->form = $form = new Siteeventticket_Form_Coupon_Search();

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $orderby = $request->getParam('orderby', null);
        if (empty($orderby) && !empty($form->orderby)) {
            $form->orderby->setValue('creation_date');
        }

        $category = $request->getParam('category_id', null);
        $subcategory = $request->getParam('subcategory_id', null);
        $categoryname = $request->getParam('categoryname', null);
        $subcategoryname = $request->getParam('subcategoryname', null);
        $subsubcategory = $request->getParam('subsubcategory_id', null);
        $subsubcategoryname = $request->getParam('subsubcategoryname', null);
        $cattemp = $request->getParam('category', null);

        if (!empty($cattemp)) {
            $this->view->category_id = $_GET['category'] = $request->getParam('category');
            $row = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategory($this->view->category_id);
            if (!empty($row->category_name)) {
                $categoryname = $this->view->category_name = $_GET['categoryname'] = $row->category_name;
            }

            $categorynametemp = $request->getParam('categoryname', null);
            $subcategorynametemp = $request->getParam('subcategoryname', null);
            if (!empty($categorynametemp)) {
                $categoryname = $this->view->category_name = $_GET['categoryname'] = $categorynametemp;
            }
            if (!empty($subcategorynametemp)) {
                $subcategoryname = $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategorynametemp;
            }
        } else {
            if ($categoryname)
                $this->view->category_name = $_GET['categoryname'] = $categoryname;
            if ($category) {
                $this->view->category_id = $_GET['category_id'] = $category;
                $row = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategory($this->view->category_id);
                if (!empty($row->category_name)) {
                    $this->view->category_name = $_GET['categoryname'] = $categoryname = $row->category_name;
                }
            }
        }

        $siteeventticketSearchCoupon = Zend_Registry::isRegistered('siteeventticketSearchCoupon') ? Zend_Registry::get('siteeventticketSearchCoupon') : null;
        $subcattemp = $request->getParam('subcategory', null);

        if (!empty($subcattemp)) {
            $this->view->subcategory_id = $_GET['subcategory_id'] = $request->getParam('subcategory');
            $row = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategory($this->view->subcategory_id);
            if (!empty($row->category_name)) {
                $this->view->subcategory_name = $row->category_name;
            }
        } else {
            if ($subcategoryname)
                $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategoryname;
            if ($subcategory) {
                $this->view->subcategory_id = $_GET['subcategory_id'] = $subcategory;
                $row = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategory($this->view->subcategory_id);
                if (!empty($row->category_name)) {
                    $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategoryname = $row->category_name;
                }
            }
        }
        
        if(empty($siteeventticketSearchCoupon)) {
          return $this->setNoRender();
        }

        $subsubcattemp = $request->getParam('subsubcategory', null);

        if (!empty($subsubcattemp)) {
            $this->view->subsubcategory_id = $_GET['subsubcategory_id'] = $request->getParam('subsubcategory');
            $row = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategory($this->view->subsubcategory_id);
            if (!empty($row->category_name)) {
                $this->view->subsubcategory_name = $row->category_name;
            }
        } else {
            if ($subsubcategoryname)
                $this->view->subsubcategory_name = $_GET['subsubcategoryname'] = $subsubcategoryname;

            if ($subsubcategory) {
                $this->view->subsubcategory_id = $_GET['subsubcategory_id'] = $subsubcategory;
                $row = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategory($this->view->subsubcategory_id);
                if (!empty($row->category_name)) {
                    $this->view->subsubcategory_name = $_GET['subsubcategoryname'] = $subsubcategoryname = $row->category_name;
                }
            }
        }

        if (empty($categoryname)) {
            $_GET['category'] = $this->view->category_id = 0;
            $_GET['subcategory'] = $this->view->subcategory_id = 0;
            $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
            $_GET['categoryname'] = $categoryname;
            $_GET['subcategoryname'] = $subcategoryname;
            $_GET['subsubcategoryname'] = $subsubcategoryname;
        }

        if (!empty($_GET)) {
            $form->populate($_GET);
        }

        $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name', 'category_slug'), null, 0, 0, 1);
        $categories_slug[0] = "";
        if (count($categories) != 0) {
            foreach ($categories as $category) {
                $categories_slug[$category->category_id] = $category->getCategorySlug();
            }
        }
        $this->view->categories_slug = $categories_slug;
    }

}

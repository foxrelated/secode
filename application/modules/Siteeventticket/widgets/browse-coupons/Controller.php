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
class Siteeventticket_Widget_BrowseCouponsController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //IF TICKET SETTING DISABLED FROM ADMIN SIDE
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1)) {
            return $this->setNoRender();
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.couponprivate', 0)) {
            return $this->setNoRender();
        }

        $this->view->statistics = $this->_getParam('statistics', array("startdate", "enddate", "couponcode", 'discount', 'expire', 'viewCount', 'likeCount', 'commentCount'));
        $siteeventticketBrowseCoupon = Zend_Registry::isRegistered('siteeventticketBrowseCoupon') ? Zend_Registry::get('siteeventticketBrowseCoupon') : null;
        $this->view->truncation = $this->_getParam('truncation', 64);

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer->getIdentity();

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $category = $request->getParam('category_id', null);
        $category_id = $request->getParam('category', null);
        $subcategory = $request->getParam('subcategory_id', null);
        $subcategory_id = $request->getParam('subcategory', null);
        $categoryname = $request->getParam('categoryname', null);
        $subcategoryname = $request->getParam('subcategoryname', null);
        $subsubcategory = $request->getParam('subsubcategory_id', null);
        $subsubcategory_id = $request->getParam('subsubcategory', null);
        $subsubcategoryname = $request->getParam('subsubcategoryname', null);

        if ($category)
            $_GET['category'] = $category;
        if ($subcategory)
            $_GET['subcategory'] = $subcategory;
        if ($categoryname)
            $_GET['categoryname'] = $categoryname;
        if ($subcategoryname)
            $_GET['subcategoryname'] = $subcategoryname;

        if ($subsubcategory)
            $_GET['subsubcategory'] = $subsubcategory;
        if ($subcategoryname)
            $_GET['subsubcategoryname'] = $subsubcategoryname;

        if ($category_id)
            $_GET['category'] = $values['category'] = $category_id;
        if ($subcategory_id)
            $_GET['subcategory'] = $values['subcategory'] = $subcategory_id;
        if ($subsubcategory_id)
            $_GET['subsubcategory'] = $values['subsubcategory'] = $subsubcategory_id;

        //GET VALUE BY POST TO GET DESIRED COUPONS
        $values = array();
        if (!empty($_GET)) {
            $values = $_GET;
        }

        if (($category) != null) {
            $this->view->category = $values['category'] = $category;
            $this->view->subcategory = $values['subcategory'] = $subcategory;
            $this->view->subsubcategory = $values['subsubcategory'] = $subsubcategory;
        } else {
            $values['category'] = 0;
            $values['subcategory'] = 0;
            $values['subsubcategory'] = 0;
        }

        if (($category_id) != null) {
            $this->view->category_id = $values['category'] = $category_id;
            $this->view->subcategory_id = $values['subcategory'] = $subcategory_id;
            $this->view->subsubcategory_id = $values['subsubcategory'] = $subsubcategory_id;
        } else {
            $values['category'] = 0;
            $values['subcategory'] = 0;
            $values['subsubcategory'] = 0;
        }

        $form = new Siteeventticket_Form_Coupon_Search();

        $values['orderby'] = 'creation_date';

        if (isset($_GET['orderby'])) {
            $values['orderby'] = $_GET['orderby'];
        }

        $this->view->assign($values);
        $this->view->formValues = $values;

        if (empty($categoryname)) {
            $_GET['category'] = $this->view->category_id = 0;
            $_GET['subcategory'] = $this->view->subcategory_id = 0;
            $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
            $_GET['categoryname'] = $categoryname;
            $_GET['subcategoryname'] = $subcategoryname;
            $_GET['subsubcategoryname'] = $subsubcategoryname;
        }

        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('coupons', 'siteeventticket')->getSiteEventTicketCouponsPaginator($values);
        $paginator->setItemCountPerPage($this->_getParam('itemCount', 20));
        $this->view->paginator = $paginator->setCurrentPageNumber($request->getParam('page'));
        
        if(empty($siteeventticketBrowseCoupon)) {
          return $this->setNoRender();
        }
    }

}

<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminCouponController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_AdminCouponController extends Core_Controller_Action_Admin {

    //ACTINO FOR GLOBAL SETTINGS
    public function indexAction() {
        //TAB CREATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteeventticket_admin_main_ticket');

        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteeventticket_admin_main_ticket', array(), 'siteeventticket_admin_main_coupon');

        $this->view->form = $form = new Siteeventticket_Form_Admin_Coupon_Global();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();

            include APPLICATION_PATH . '/application/modules/Siteeventticket/controllers/license/license2.php';
        }
    }

    public function manageAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteeventticket_admin_main_coupon');

        //CREATE NAVIGATION TABS
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteeventticket_admin_main_coupon', array(), 'siteeventticket_admin_main_coupon_manage');

        //FORM GENERATION
        $this->view->formFilter = $formFilter = new Siteeventticket_Form_Admin_Manage_Filter();

        //FETCH COUPON DATAS
        $tableUser = Engine_Api::_()->getItemTable('user')->info('name');
        $tableSiteevent = Engine_Api::_()->getItemTable('siteevent_event')->info('name');
        $table = Engine_Api::_()->getDbtable('coupons', 'siteeventticket');
        $rName = $table->info('name');
        $select = $table->select()
                ->setIntegrityCheck(false)
                ->from($rName)
                ->joinLeft($tableUser, "$rName.owner_id = $tableUser.user_id", 'username')
                ->joinLeft($tableSiteevent, "$rName.event_id = $tableSiteevent.event_id", 'title AS siteevent_title');

        $values = array();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        if (isset($_POST['search'])) {
            if (!empty($_POST['owner'])) {
                $this->view->owner = $_POST['owner'];
                $select->where($tableUser . '.username  LIKE ?', '%' . $_POST['owner'] . '%');
            }

            if (!empty($_POST['title'])) {
                $this->view->title = $_POST['title'];
                $select->where($rName . '.title  LIKE ?', '%' . $_POST['title'] . '%');
            }

            if (!empty($_POST['siteevent_title'])) {
                $this->view->siteevent_title = $_POST['siteevent_title'];
                $select->where($tableSiteevent . '.title  LIKE ?', '%' . $_POST['siteevent_title'] . '%');
            }

            if (!empty($_POST['coupon_code'])) {
                $this->view->coupon_code = $_POST['coupon_code'];
                $select->where($rName . '.coupon_code  LIKE ?', '%' . $_POST['coupon_code'] . '%');
            }
        }

        $values = array_merge(array(
            'order' => 'coupon_id',
            'order_direction' => 'DESC',
                ), $values);

        $this->view->assign($values);
        $select->order((!empty($values['order']) ? $values['order'] : 'coupon_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator->setItemCountPerPage(50);
        $this->view->paginator = $paginator->setCurrentPageNumber(1);
    }

    //ACTION FOR MULTI DELETE COUPONS
    public function multiDeleteAction() {

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $coupon_id = (int) $value;
                    Engine_Api::_()->getItem('siteeventticket_coupon', $coupon_id)->delete();
                }
            }
        }

        return $this->_redirect('admin/siteeventticket/coupon/manage');
    }

    public function approvalAction() {

        $couponId = $this->_getParam('id');

        if (empty($couponId))
            $this->_redirect('admin/siteeventticket/coupon/manage');

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $siteEventTicketCoupon = Engine_Api::_()->getItem('siteeventticket_coupon', $couponId);
            $siteEventTicketCoupon->approved = !$siteEventTicketCoupon->approved;
            $siteEventTicketCoupon->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->_redirect('admin/siteeventticket/coupon/manage');
    }

    //VIEW COUPON DETAILS
    public function detailAction() {

        //GET COUPON ID
        $couponId = $this->_getParam('id');

        //FETCH THE BADGE DETAIL
        $this->view->siteeventcouponDetail = Engine_Api::_()->getItem('siteeventticket_coupon', $couponId);
    }

    //ACTION FOR DELETE THE COUPONS
    public function deleteAction() {

        //RENDER DEFAULT LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET COUPON ID
        $this->view->coupon_id = $coupon_id = $this->_getParam('id');

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                Engine_Api::_()->getItem('siteeventticket_coupon', $coupon_id)->delete();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Coupon has been deleted successfully !!')
            ));
        }

        $this->renderScript('admin-coupon/delete.tpl');
    }

}

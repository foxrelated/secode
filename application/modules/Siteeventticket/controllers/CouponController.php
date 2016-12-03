<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: CouponController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_CouponController extends Seaocore_Controller_Action_Standard {

    public function init() {

        //TICKET SETTING DISABLED FROM ADMIN SIDE
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        
        $siteeventticketEventType = Zend_Registry::isRegistered('siteeventticketEventType') ? Zend_Registry::get('siteeventticketEventType') : null;
        if(empty($siteeventticketEventType)) {
          return $this->_forward('notfound', 'error', 'core');
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //MUST BE ABLE TO VIEW EVENTS
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "view")) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        
    }

    //NONE USER SPECIFIC METHODS
    public function indexAction() {

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.couponprivate', 0)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $this->_helper->content
                ->setContentName("siteeventticket_coupon_index")
                ->setNoRender()
                ->setEnabled();
    }

    //ACTION FOR MANAGING COUPONS
    public function manageAction() {

        //LOGGED IN USER VALIDATON 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $params = array();
        $params['event_id'] = $event_id = $this->_getParam('event_id');

        if (empty($event_id)) {
            return;
        }

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT ID AND EVENT OBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $this->view->event_id = $event_id;
        if ($siteevent && !Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            Engine_Api::_()->core()->setSubject($siteevent);
        }

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', $viewer, "ticket_create")->isValid()) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $totalCoupons = Engine_Api::_()->getDbtable('coupons', 'siteeventticket')->getEventCouponCount($event_id);

        $this->view->can_create_coupons = Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, 'coupon_creation');
        if (!$this->view->can_create_coupons && $totalCoupons <= 0) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET NAVIAGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteevent_dashboard');

        $params['show_all_coupons'] = 1;

        //MAKE PAGINATOR
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('siteeventticket_coupon')->getSiteEventTicketCouponsPaginator($params);
        $this->view->paginator = $paginator->setItemCountPerPage(100)->setCurrentPageNumber($this->_getParam('page', 1));
    }

    //ACTION FOR CREATING COUPON
    public function createAction() {

        //LOGGED IN USER VALIDATON 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, 'coupon_creation')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $event_id = $this->_getParam('event_id');

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (empty($siteevent)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', $viewer, "ticket_create")->isValid()) {
            //CHECK FOR CREATION PRIVACY    
            return $this->_forward('notfound', 'error', 'core');
        }
        
        // SET COUPON INFORMATION
        $setCouponInfo = Engine_Api::_()->getDbtable('coupons', 'siteeventticket')->setCouponInfo();
        if(empty($setCouponInfo)) {
          return $this->_forward('notfound', 'error', 'core');
        }

        $this->_helper->content
                ->setContentName("siteeventticket_coupon_create")
                ->setEnabled();

        $this->view->eventProfileCouponTabId = Engine_Api::_()->siteeventticket()->getContentId(array('page_name' => 'siteevent_index_view', 'widget_name' => 'siteeventticket.event-profile-coupons'));

        $this->view->form = $form = new Siteeventticket_Form_Coupon_Create();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            if ((!empty($_POST['discount_type']) && $_POST['price'] <= 0) || (empty($_POST['discount_type']) && ($_POST['rate'] <= 0 || $_POST['rate'] > 100))) {
                $error = Zend_Registry::get('Zend_Translate')->_('Discount value should be grater than zero.');
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            $isCouponCodeExist = Engine_Api::_()->getDbtable('coupons', 'siteeventticket')->getCouponInfo(array('coupon_code' => $_POST['coupon_code'], 'fetchColumn' => 1), array('coupon_id'));
            if ($isCouponCodeExist) {
                $error = Zend_Registry::get('Zend_Translate')->_('Entered coupon code is not available.');
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            $values = $form->getValues();

            $values['discount_amount'] = !empty($values['discount_type']) ? $values['price'] : $values['rate'];

            if ($values['end_settings'] == 0) {
                $values['end_time'] = '0000-00-00 00:00:00';
            }

            $siteeventticketcouponsTable = Engine_Api::_()->getDbtable('coupons', 'siteeventticket');
            $db = $siteeventticketcouponsTable->getAdapter();
            $db->beginTransaction();
            try {

                //CREATE COUPON
                $siteeventticketcoupon = $siteeventticketcouponsTable->createRow();
                $siteeventticketcoupon->setFromArray($values);
                $siteeventticketcoupon->discount_amount = $values['discount_amount'];
                $siteeventticketcoupon->event_id = $event_id;
                $siteeventticketcoupon->owner_id = $viewer->getIdentity();
                $siteeventticketcoupon->approved = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.couponapproval', 1);
                if (!empty($values['ticket_ids'])) {
                    $siteeventticketcoupon->ticket_ids = implode(',', $values['ticket_ids']);
                }

                $siteeventticketcoupon->coupon_code = strtoupper($values['coupon_code']);
                $siteeventticketcoupon->save();

                //ADD PHOTO
                if (!empty($values['photo'])) {
                    $siteeventticketcoupon->setPhoto($form->photo);
                }

                //COMMENT PRIVACY
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                $auth_comment = "everyone";
                $commentMax = array_search($auth_comment, $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($siteeventticketcoupon, $role, 'comment', ($i <= $commentMax));
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('user_id' => $siteeventticketcoupon->owner_id, 'coupon_id' => $siteeventticketcoupon->coupon_id, 'slug' => $siteeventticketcoupon->getCouponSlug()), "siteeventticketcoupon_view", true);
        }
    }

    //ACTION FOR EDIT COUPON
    public function editAction() {

        // CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET COUPON OBJECT
        $this->view->coupon_id = $this->_getParam('coupon_id');
        $this->view->siteeventticketcoupon = $siteeventticketcoupon = Engine_Api::_()->getItem('siteeventticket_coupon', $this->_getParam('coupon_id'));

        if (empty($siteeventticketcoupon)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        //GET EVENT OBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $siteeventticketcoupon->event_id);

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        
        $setCouponInfo = Engine_Api::_()->getDbtable('coupons', 'siteeventticket')->setCouponInfo();
        if(empty($setCouponInfo)) {
          return $this->_forward('notfound', 'error', 'core');
        }

        $this->_helper->content
                ->setContentName("siteeventticket_coupon_edit")
                ->setEnabled();

        $this->view->eventProfileCouponTabId = Engine_Api::_()->siteeventticket()->getContentId(array('page_name' => 'siteevent_index_view', 'widget_name' => 'siteeventticket.event-profile-coupons'));

        //FORM GENERATION
        $this->view->form = $form = new Siteeventticket_Form_Coupon_Edit();

        if (isset($form->coupon_code)) {
            $form->removeElement('coupon_code');
        }

        $date = (string) date('Y-m-d');
        $siteeventticketcoupon->end_time = $siteeventticketcoupon->end_time;

        if ($siteeventticketcoupon->end_settings == 0) {
            $date = (string) date('Y-m-d');
            $siteeventticketcoupon->end_time = $date . ' 00:00:00';
        }

        $form->populate($siteeventticketcoupon->toArray());
        $form->price->setValue($siteeventticketcoupon->discount_amount);
        $form->rate->setValue($siteeventticketcoupon->discount_amount);

        if (!empty($form->ticket_ids)) {
            $tempMappedIds = explode(',', $siteeventticketcoupon->ticket_ids);
            $selectedTicketIds = array();
            foreach ($tempMappedIds as $ticket_id) {
                $ticket = Engine_Api::_()->getItem('siteeventticket_ticket', $ticket_id);

                if (empty($ticket) || $ticket->price <= 0)
                    continue;

                $selectedTicketIds[] = $ticket_id;
            }

            $form->ticket_ids->setValue($selectedTicketIds);
        }

        //IF NOT POST OR FORM NOT VALID THAN RETURN
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //IF NOT POST OR FORM NOT VALID THAN RETURN
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        if ((!empty($_POST['discount_type']) && $_POST['price'] <= 0) || (empty($_POST['discount_type']) && ($_POST['rate'] <= 0 || $_POST['rate'] > 100))) {
            $error = Zend_Registry::get('Zend_Translate')->_('Discount value should be grater than zero.');
            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }

        //GET FORM VALUES
        $values = $form->getValues();

        $values['discount_amount'] = !empty($values['discount_type']) ? $values['price'] : $values['rate'];

        if ($values['end_settings'] == 0) {
            $values['end_time'] = '0000-00-00 00:00:00';
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $siteeventticketcoupon->discount_amount = $values['discount_amount'];
            $siteeventticketcoupon->setFromArray($values);
            if (!empty($values['ticket_ids'])) {
                $siteeventticketcoupon->ticket_ids = implode(',', $values['ticket_ids']);
            }

            $siteeventticketcoupon->save();

            //ADD PHOTO
            if (!empty($values['photo'])) {
                $siteeventticketcoupon->setPhoto($form->photo);
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_helper->redirector->gotoRoute(array('user_id' => $siteeventticketcoupon->owner_id, 'coupon_id' => $siteeventticketcoupon->coupon_id, 'slug' => $siteeventticketcoupon->getCouponSlug()), "siteeventticketcoupon_view", true);
    }

    //ACTION FOR DELETE COUPON
    public function deleteAction() {

        // CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET COUPON ID AND COUPON OBJECT
        $this->view->coupon_id = $coupon_id = $this->_getParam('coupon_id');
        $siteeventticketcoupon = Engine_Api::_()->getItem('siteeventticket_coupon', $coupon_id);

        //GET EVENT OBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $siteeventticketcoupon->event_id);

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $siteeventticketcoupon->delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $url = $this->_helper->url->url(array('action' => 'manage', 'event_id' => $siteevent->event_id), 'siteeventticket_coupon', true);

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRedirect' => $url,
                'parentRedirectTime' => '15',
                'format' => 'smoothbox',
                'messages' => Zend_Registry::get('Zend_Translate')->_('Coupon has been deleted successfully.')
            ));
        }
    }

    //ACTION FOR ENABLE/DISABLE THE COUPON
    public function enableDisableAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET EVENT ID AND EVENT OBJECT
        $coupon_id = $this->_getParam('coupon_id', null);

        $this->view->siteeventticketcoupon = $siteeventticketcoupon = Engine_Api::_()->getItem('siteeventticket_coupon', $coupon_id);

        if (empty($siteeventticketcoupon)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT OBJECT
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $siteeventticketcoupon->event_id);
        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if ($this->getRequest()->isPost()) {

            $siteeventticketcoupon->status = !$siteeventticketcoupon->status;
            $siteeventticketcoupon->save();

            $this->_forwardCustom('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Coupon status has been changed successfully.')
            ));
        }
    }

    //ACTION FOR APPLYING COUPON ON TICKET BUY
    public function applyCouponAction() {

        //LOGGED IN USER VALIDATON 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $coupon_code = $this->_getparam('coupon_code');
        $cart_event_id = $this->_getparam('event_id');
        $cart_info = json_decode($this->_getparam('cart_info'), true);
        $totalQuantityArray = $this->_getParam('totalQuantityArray');
        $dynamic_subtotal = $this->_getParam('dynamic_subtotal');

        if (empty($coupon_code) || empty($cart_info)) {
            $this->view->coupon_error_msg = $this->view->translate("Please enter correct coupon code.");
            return;
        }

        $coupon_detail = Engine_Api::_()->getDbtable('coupons', 'siteeventticket')->getCouponInfo(array('coupon_code' => $coupon_code, 'fetchRow' => 1), array('coupon_id', 'event_id', 'ticket_ids', 'discount_type', 'discount_amount', 'start_time', 'end_time', 'end_settings', 'status', 'approved', 'coupon_code', 'min_quantity', 'min_amount'));
        $isCouponExist = @COUNT($coupon_detail);

        if (empty($isCouponExist) || empty($coupon_detail->approved) || empty($coupon_detail->status) || (date("Y-m-d H:i:s") < $coupon_detail->start_time) || ($coupon_detail->end_settings == 1 && $coupon_detail->end_time < date("Y-m-d H:i:s")) || (!empty($cart_event_id) && $coupon_detail->event_id != $cart_event_id)) {
            $this->view->coupon_error_msg = $this->view->translate("Please enter a different coupon code as %s is either invalid or has expired.", $coupon_code);
            return;
        }
        
        $setCouponInfo = Engine_Api::_()->getDbtable('coupons', 'siteeventticket')->setCouponInfo();
        if(empty($setCouponInfo)) {
            $this->view->coupon_error_msg = $this->view->translate("Please enter a different coupon code as %s is either invalid or has expired.", $coupon_code);
        }

        $cart_info = $cart_info[$coupon_detail->event_id];
        if (empty($cart_info)) {
            $this->view->coupon_error_msg = $this->view->translate("Please enter a different coupon code as %s is either invalid or has expired.", $coupon_code);
            return;
        }

        $eventTicketIdsPrice = $cart_info['ticket_ids'];

        if (!empty($coupon_detail->ticket_ids)) {
            $mappedTicketIds = explode(',', $coupon_detail->ticket_ids);
        }

        $finalTicektIds = $intersectTicektIds = array_intersect($mappedTicketIds, $eventTicketIdsPrice);
        $successMessageTitle = '';
        foreach ($intersectTicektIds as $key => $ticket_id) {
            $ticket = Engine_Api::_()->getItem('siteeventticket_ticket', $ticket_id);

            $validTicketId = false;
            if (!empty($ticket) && $ticket->status != 'hidden' && $ticket->status != 'closed' && date("Y-m-d H:i:s") > $ticket->sell_starttime && date("Y-m-d H:i:s") < $ticket->sell_endtime) {
                $validTicketId = true;
                $successMessageTitle .= "'" . trim($ticket->getTitle()) . "',";
            }

            if (empty($validTicketId)) {
                unset($finalTicektIds[$key]);
            }
        }

        $successMessageTitle = rtrim($successMessageTitle, ',');

        if (Count($finalTicektIds) <= 0) {
            $this->view->coupon_error_msg = $this->view->translate("Please enter a different coupon code as %s is either invalid or has expired.", $coupon_code);
            return;
        }

        if (!empty($coupon_detail->discount_type) && ($dynamic_subtotal - $coupon_detail->discount_amount) <= 0) {
            $this->view->coupon_error_msg = $this->view->translate("Discount amount is not valid for this purchasing amount.", $coupon_code);
            return;
        }

        $totalAmount = $totalQuantity = 0;
        foreach ($finalTicektIds as $eventTicketId) {
            $coupon_details_array[$eventTicketId] = array('coupon_id' => $coupon_detail->coupon_id, 'coupon_amount' => $coupon_detail->discount_amount, 'event_id' => $coupon_detail->event_id, 'coupon_type' => $coupon_detail->discount_type, 'coupon_code' => $coupon_detail->coupon_code);
            $totalQuantity = $totalQuantity + $totalQuantityArray[$eventTicketId];
            $totalAmount = $totalAmount + ($totalQuantityArray[$eventTicketId] * $cart_info['unitPrice'][$eventTicketId]);
        }
        
        if($totalQuantity <= 0) {
            $this->view->coupon_error_msg = $this->view->translate("Please buy at least one ticket to apply this coupon.");
            return;
        }                 

        if (!empty($coupon_detail->min_quantity) && $coupon_detail->min_quantity > $totalQuantity) {
            $this->view->coupon_error_msg = $this->view->translate("You need to purchase %1s ticket(s) to avail this coupon. (Applicable for %2s)", $coupon_detail->min_quantity, $successMessageTitle);
            return;
        }

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyValue = $view->locale()->toCurrency($coupon_detail->min_amount, $currency);
        if (!empty($coupon_detail->min_amount) && $coupon_detail->min_amount > $totalAmount) {
            $this->view->coupon_error_msg = $this->view->translate("Your ticket order should be of atleast %1s to avail this coupon. (Applicable for %2s)", $currencyValue, $successMessageTitle);
            return;
        }
        
        if($totalAmount <= 0) {
            $this->view->coupon_error_msg = $this->view->translate("Your ticket order should be of atleast %1s to avail this coupon. (Applicable for %2s)", $currencyValue, $successMessageTitle);
        }         

        $session = new Zend_Session_Namespace('siteeventticket_cart_coupon');
        $session->siteeventticketCouponDetail = serialize($coupon_details_array);
        $this->view->cart_coupon_applied = true;
        $this->view->ticketIds = $coupon_details_array;

        if ($coupon_detail->discount_type) {
            if ($totalQuantity) {
                $this->view->coupon_success_msg = $this->view->translate("This coupon has been applied. (Applicable for %s)", $successMessageTitle);
            } else {
                $this->view->coupon_error_msg = $this->view->translate("To avail this discount you must to purchase %s type ticket.", $successMessageTitle);
            }
        } else {
            $this->view->coupon_success_msg = $this->view->translate("This coupon has applied for: %s", $successMessageTitle);
        }
    }

    //ACTION FOR VIEW COUPON
    public function viewAction() {

        $siteeventticketcoupon = Engine_Api::_()->getItem('siteeventticket_coupon', $this->getRequest()->getParam('coupon_id'));

        if (empty($siteeventticketcoupon)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        if ($siteeventticketcoupon) {
            Engine_Api::_()->core()->setSubject($siteeventticketcoupon);
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT OBJECT
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $siteeventticketcoupon->event_id);
        $canEdit = $siteevent->authorization()->isAllowed($viewer, 'edit');

        if (!$canEdit && (empty($siteeventticketcoupon->status) || empty($siteeventticketcoupon->approved))) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $this->_helper->content
                ->setNoRender()
                ->setEnabled()
        ;
    }

    //ACTION FOR PRINTING THE COUPON
    public function printAction() {

        $this->_helper->layout->setLayout('default-simple');

        //GET COUPON ID AND COUPON OBJECT
        $coupon_id = $this->_getParam('coupon_id', null);
        $this->view->siteeventcoupon = $siteeventcoupon = Engine_Api::_()->getItem('siteeventticket_coupon', $coupon_id);

        if (empty($siteeventcoupon)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $this->view->siteevent = Engine_Api::_()->getItem('siteevent_event', $siteeventcoupon->event_id);
    }

    public function resendCouponAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->coupon_id = $coupon_id = $this->_getParam('coupon_id', null);

        if (!empty($coupon_id)) {
            $this->view->siteeventcoupon = Engine_Api::_()->getItem('siteeventticket_coupon', $coupon_id);
        }
    }

    public function sendCouponAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->_helper->layout->setLayout('default-simple');
        $this->view->coupon_id = $coupon_id = $this->_getParam('coupon_id');

        $siteeventcoupon = Engine_Api::_()->getItem('siteeventticket_coupon', $coupon_id);

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $siteeventcoupon->event_id);

        $httpHost = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

        $data = array();

        $data['browse_coupon'] = $this->view->htmlLink($httpHost .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'siteeventticket_coupon', true), $this->view->translate('View More Coupons'), array('style' => 'color:#3b5998;text-decoration:none;margin-left:10px; ', 'target' => '_blank'));

        $data['share_coupon'] = $this->view->htmlLink($httpHost .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('user_id' => $siteeventcoupon->owner_id, 'coupon_id' => $siteeventcoupon->coupon_id, 'slug' => $siteeventcoupon->getcouponSlug($siteeventcoupon->title), 'share_coupon' => 1), 'siteeventticketcoupon_view', true), $this->view->translate('Share Coupon'), array('style' => 'text-decoration:none;font-weight:bold;color:#fff;font-size:11px;', 'target' => '_blank'));

        $data['like_event'] = $this->view->htmlLink($httpHost .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $siteevent->event_id, 'slug' => $siteevent->getSlug()), 'siteevent_entry_view', true), $this->view->translate('Like') . ' ' . $siteevent->getTitle(), array('style' => 'color:#3b5998;text-decoration:none;margin-right:10px;margin-left:10px;', 'target' => '_blank'));

        if ($siteevent->photo_id) {
            $data['event_photo_path'] = $siteevent->getPhotoUrl('thumb.icon');
        } else {
            $data['event_photo_path'] = 'application/modules/Siteevent/externals/images/nophoto_siteevent_thumb_icon.png';
        }

        $data['event_title'] = $this->view->htmlLink($httpHost .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $siteevent->event_id, 'slug' => $siteevent->getSlug()), 'siteevent_entry_view', true), $siteevent->getTitle(), array('style' => 'color:#3b5998;text-decoration:none;'));

        if ($siteeventcoupon->photo_id) {
            $data['coupon_photo_path'] = $siteeventcoupon->getPhotoUrl('thumb.icon');
        } else {
            $data['coupon_photo_path'] = 'application/modules/Siteeventticket/externals/images/coupon_thumb.png';
        }

        $data['site_title'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1);

        $data['coupon_title'] = $this->view->htmlLink($httpHost .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('user_id' => $siteeventcoupon->owner_id, 'coupon_id' => $siteeventcoupon->coupon_id, 'slug' => $siteeventcoupon->getcouponSlug($siteeventcoupon->title)), 'siteeventticketcoupon_view', true), $siteeventcoupon->title, array('style' => 'color:#3b5998;text-decoration:none;'));
        $data['coupon_code'] = $siteeventcoupon->coupon_code;
        $data['coupon_description'] = $siteeventcoupon->description;
        $data['coupon_time'] = gmdate('M d, Y', strtotime($siteeventcoupon->end_time));
        $data['coupon_time_setting'] = $siteeventcoupon->end_settings;
        $data['claim_owner_name'] = Engine_Api::_()->user()->getViewer()->username;
        $data['enable_mailtemplate'] = Engine_Api::_()->hasModuleBootstrap('sitemailtemplates');

        //INITIALIZE THE STRING TO BE SEND IN THE CLAIM MAIL
        $template_header = "";
        $template_footer = "";
        $string = $this->view->couponmail($data);

        $this->view->event_title = $siteevent->title;

        $subject = $this->view->translate('Your %1s coupon from %2s', $data['site_title'], $siteevent->title);

        $email = Engine_Api::_()->user()->getViewer()->email;
        $email_admin = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'SITEEVENTTICKET_GET_COUPON', array(
            'subject' => $subject,
            'template_header' => $template_header,
            'message' => $string,
            'template_footer' => $template_footer,
            'email' => $email_admin,
            'queue' => false));
    }

    public function couponCodeValidationAction() {

        $coupon_code = $this->_getParam('coupon_code');

        $staticBaseUrl = Zend_Registry::get('Zend_View')->layout()->staticBaseUrl;

        if (empty($coupon_code)) {
            echo Zend_Json::encode(array('success' => 0, 'error_msg' => '<span style="color:red;"><img src="' . $staticBaseUrl . 'application/modules/Siteevent/externals/images/cross.png"/>' . $this->view->translate('Coupon Code is not available.') . '</span>'));
            exit();
        }

        $isCouponCodeExist = Engine_Api::_()->getDbtable('coupons', 'siteeventticket')->getCouponInfo(array('coupon_code' => $coupon_code, 'fetchColumn' => 1), array('coupon_id'));

        if (!empty($isCouponCodeExist)) {
            echo Zend_Json::encode(array('success' => 0, 'error_msg' => '<span style="color:red;"><img src="' . $staticBaseUrl . 'application/modules/Siteevent/externals/images/cross.png"/>' . $this->view->translate('Coupon Code is not available.') . '</span>'));
            exit();
        } else {
            $success_message = Zend_Registry::get('Zend_Translate')->_("Coupon Code is Available.");
            echo Zend_Json::encode(array('success' => 1, 'success_msg' => '<span style="color:green;"><img src="' . $staticBaseUrl . 'application/modules/Siteevent/externals/images/tick.png"/>' . $success_message . '</span>'));
            exit();
        }
    }

}

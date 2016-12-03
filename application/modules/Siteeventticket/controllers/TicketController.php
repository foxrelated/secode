<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: TicketController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_TicketController extends Seaocore_Controller_Action_Standard {

    protected $_get_ticket_id;

    public function init() {

        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //TICKET SETTING DISABLED FROM ADMIN SIDE
        $ticket = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.ticket.enabled', 1);
        if (empty($ticket)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $siteeventpaidHiddenTicket = Zend_Registry::isRegistered('siteeventpaidHiddenTicket') ? Zend_Registry::get('siteeventpaidHiddenTicket') : null;
        if (empty($siteeventpaidHiddenTicket)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //LOGGED IN USER VALIDATON 
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SET SUBJECT
        global $get_ticket_id;
        $event_id = $this->_getParam('event_id', null);

        if ($event_id) {
            $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
            if ($siteevent && !Engine_Api::_()->core()->hasSubject('siteevent_event')) {
                Engine_Api::_()->core()->setSubject($siteevent);
            }
        }
        //END - SET SUBJECT
        //AUTHORIZATION CHECK - Payment approved, expiration etc.
        $this->_get_ticket_id = !empty($get_ticket_id) ? $get_ticket_id : 0;
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $can_view = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "view");
        if ($can_view != 2 && ((!empty($siteevent->closed) || !empty($siteevent->draft) || empty($siteevent->approved)) && ($siteevent->owner_id != $viewer_id) || (Engine_Api::_()->siteevent()->hasPackageEnable() && (isset($siteevent->expiration_date) && $siteevent->expiration_date !== "2250-01-01 00:00:00" && strtotime($siteevent->expiration_date) < time())))) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }
        //  END - CHECK
        //IF TAX IS MANDATORY AND NOT SET THEN DISPLAY A TIP
        $taxMandatory = Engine_Api::_()->siteeventticket()->isTaxRateMandatoryMessage($event_id);
        if ($taxMandatory) {
            $this->view->taxMandatoryMessage = 'true';
        }
        //END
    }

    //ACTION TO DISPLAY ALL TICKETS
    public function manageAction() {

        //GET EVENT ID EVENT OBJECT AND THEN CHECK VALIDATIONS
        $this->view->event_id = $event_id = $this->_getParam('event_id');
        if (empty($event_id) || empty($this->_get_ticket_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (empty($siteevent) || empty($this->_get_ticket_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        //ORDER PLACED FOR THIS EVENT
        $this->view->isAllowTicketCreation = $isAllowTicketCreation = true;
        $isEventOrderPlaced = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->fetchRow(array('event_id = ?' => $event_id));

        //LEVEL CHECK FOR CREATE
        $this->view->isAllowTicketCreation = $isAllowTicketCreation = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "ticket_create");

        if (!$isAllowTicketCreation && empty($isEventOrderPlaced)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //END VALIDATIONS
        $values['event_id'] = $event_id = $this->_getParam('event_id');
        $values['hiddenTickets'] = true;
        $values['columns'] = array('status', 'price', 'title', 'quantity', 'ticket_id');
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('tickets', 'siteeventticket')->getTicketsPaginator($values);
        $this->view->count = $paginator->getTotalItemCount();
    }

    //ACTION TO ADD NEW TICKET
    public function addAction() {

        //START - VALIDATIONS
        //GET EVENT ID EVENT OBJECT AND THEN CHECK VALIDATIONS
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->event_id = $event_id = $this->_getParam('event_id');
        if (empty($event_id) || empty($this->_get_ticket_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (empty($siteevent) || empty($this->_get_ticket_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //CHECK FOR CREATION PRIVACY  
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', $viewer, "ticket_create")->isValid() || empty($this->_get_ticket_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid() || empty($this->_get_ticket_id)) {
            return;
        }
        //END - VALIDATIONS        
        $seaoSmoothbox = $this->_getParam('seaoSmoothbox');
        $this->view->form = $form = new Siteeventticket_Form_Ticket_Add(array('seaoSmoothbox' => $seaoSmoothbox, 'event_id' => $event_id));

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $values = $form->getValues();
        $ticketUploadedByHost = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $siteeventticketCouponTypeInfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticketcoupon.type.info', null);
        $siteeventticketGetShowViewType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.getshow.viewtype', null);
        $isEnabled = Engine_Api::_()->siteevent()->isEnabled();

        //PAYMENT GATEWAYS ENABLED CHECK FOR PAID TICKETS ONLY
        if ($values['price'] > '0.00') {

            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
            // DIRECT PAYMENT TO SELLER ENABLED
            if (empty($isPaymentToSiteEnable)) {
                $eventEnabledgateway = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($event_id, 'event_gateway');

                if (!empty($eventEnabledgateway)) {
                    $siteAdminEnablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.allowed.payment.gateway', array('paypal', 'cheque', 'cod'));
                    $eventEnabledgateway = Zend_Json_Decoder::decode($eventEnabledgateway);

                    foreach ($eventEnabledgateway as $gatewayName => $gatewayTableId) {
                        if (in_array($gatewayName, $siteAdminEnablePaymentGateway)) {
                            $finalEventEnableGateway[] = $gatewayName;
                        }
                    }
                }

                // IF NO PAYMENT GATEWAY ENABLE
                if (empty($eventEnabledgateway) || empty($finalEventEnableGateway)) {
                    $no_payment_gateway_enable = true;
                }
            } else {
                $gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
                $enable_gateway = $gateway_table->select()
                    ->from($gateway_table->info('name'), array('gateway_id', 'title', 'plugin'))
                    ->where('enabled = 1')
                    ->query()
                    ->fetchAll();

                $admin_payment_gateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.admin.gateway', array('cheque'));

                // IF NO PAYMENT GATEWAY ENABLE BY THE SITEADMIN
                if (empty($enable_gateway) && empty($admin_payment_gateway)) {
                    $no_payment_gateway_enable = true;
                }
            }

            if (!empty($no_payment_gateway_enable) && $values['status'] != 'hidden') {
                if (empty($isPaymentToSiteEnable)) {
                    $error = Zend_Registry::get('Zend_Translate')->_("You need to enable payment gateways to create paid ticket. If you still want to create this ticket, then you may create it with 'Hidden' status and after enabling the payment gateways you can change it to 'Open' status.");
                } else {
                    $error = Zend_Registry::get('Zend_Translate')->_("You cannot create paid tickets as Site Admin has not enabled payment gateways for this site. Please contact your site admin for further queries. But, If you still want to create this ticket, then you may create it with the 'Hidden' status and can change it to 'Open' status once site Asmin enables the payment gateways.");
                }
            }
            
            if (Engine_Api::_()->siteevent()->hasPackageEnable()) {
                $package = Engine_Api::_()->getItemTable('siteeventpaid_package')->fetchRow(array('package_id = ?' => $siteevent->package_id));
                if (empty($package->ticket_type)) {
                    $error = Zend_Registry::get('Zend_Translate')->_("You can only create free tickets under selected package of your event.");
                }
            }            
        }

        if (!empty($ticketUploadedByHost))
            $ticketUploadedByHost = @md5($ticketUploadedByHost);

        //BUY LIMIT MIN, MAX VALIDATIONS
        if ($values['buy_limit_min'] > $values['buy_limit_max']) {
            $error = Zend_Registry::get('Zend_Translate')->_('"Minimum Buying Limit" should be lesser than the "Maximum Buying Limit".');
        } elseif ($values['buy_limit_min'] > $values['quantity']) {
            $error = Zend_Registry::get('Zend_Translate')->_('"Minimum Buying Limit" should be lesser than the "Available Quantity".');
        } elseif ($values['buy_limit_max'] > $values['quantity']) {
            $error = Zend_Registry::get('Zend_Translate')->_('"Maximum Buying Limit" should be lesser than the "Available Quantity".');
        }
        if (isset($error) && !empty($error)) {
            $form->addError($error);
            return;
        }

        $values['event_id'] = $event_id;
        $values['owner_id'] = $viewer->getIdentity();
        // Convert times
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($viewer->timezone);
        $start = strtotime($values['sell_starttime']);
        $end = strtotime($values['sell_endtime']);
        date_default_timezone_set($oldTz);
        $values['sell_starttime'] = date('Y-m-d H:i:s', $start);
        $values['sell_endtime'] = date('Y-m-d H:i:s', $end);

        $db = Engine_Api::_()->getDbtable('tickets', 'siteeventticket')->getAdapter();
        $db->beginTransaction();

        try {
            if (!empty($siteeventticketGetShowViewType) || empty($isEnabled) || ($siteeventticketCouponTypeInfo == $ticketUploadedByHost)) {
                //SAVE TICKETS DETAILS IN TICKET TABLE.
                $table = Engine_Api::_()->getDbtable('tickets', 'siteeventticket');
                $ticket = $table->createRow();
                $ticket->setFromArray($values);
                $ticket->save();

                //SAVE TICKETS DETAILS IN EVENT_OCCURRENCES TABLE.
                Engine_Api::_()->getDbtable('occurrences', 'siteevent')->setTicketDetails($event_id, $ticket->ticket_id);

                if ($ticket->status != 'hidden') {
                    // Add action
                    $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                    $action = $activityApi->addActivity($viewer, $siteevent, 'siteeventticket_new', null, array('ticket' => array($ticket->getType(), $ticket->getIdentity())));
                    if ($action) {
                        $activityApi->attachActivity($action, $siteevent);
                    }
                }
            } else {
                Engine_Api::_()->getApi('settings', 'core')->setSetting('siteeventticket.getview.type', 0);
                Engine_Api::_()->getApi('settings', 'core')->setSetting('siteeventticket.getinfo.type', 0);
            }
            // Commit
            $db->commit();

            // Redirect
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your event ticket has been successfully created.'))
            ));
        } catch (Engine_Image_Exception $e) {
            $db->rollBack();
            $form->addError(Zend_Registry::get('Zend_Translate')->_('Unable to add this ticket. The Details you have filled are inappropriate.'));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR TICKET DETAIL
    public function detailAction() {

        $this->view->viewer = Engine_Api::_()->user()->getViewer();

        $ticket_id = $this->_getParam('ticket_id');
        if (empty($ticket_id) || empty($this->_get_ticket_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $this->view->ticket = Engine_Api::_()->getItem('siteeventticket_ticket', $ticket_id);
    }

    //ACTION FOR DELETE TICKET
    public function deleteAction() {
        //START - VALIDATIONS
        //GET EVENT ID EVENT OBJECT AND THEN CHECK VALIDATIONS 
        $viewer = Engine_Api::_()->user()->getViewer();
        $event_id = $this->_getParam('event_id');
        if (empty($event_id) || empty($this->_get_ticket_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (empty($siteevent) || empty($this->_get_ticket_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //CHECK FOR CREATION PRIVACY  
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', $viewer, "ticket_create")->isValid() || empty($this->_get_ticket_id)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid() || empty($this->_get_ticket_id)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END - VALIDATIONS

        $ticket_id = $this->getRequest()->getParam('ticket_id');
        $siteeventticket = Engine_Api::_()->getItem('siteeventticket_ticket', $ticket_id);

        $this->view->form = $form = new Siteeventticket_Form_Ticket_Delete();

        $this->view->isTicketSold = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket')->fetchRow(array('ticket_id = ?' => $ticket_id));

        if (!$siteeventticket) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Ticket entry doesn't exist or not authorized to delete");
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        $db = $siteeventticket->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $siteeventticket->delete();

            //REMAINING - DELETE TICKET INFORMATION FROM OCCURRENCES TABLE
            Engine_Api::_()->getDbtable('occurrences', 'siteevent')->deleteTicketDetails($event_id, $ticket_id);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your ticket entry has been deleted.');
        // Redirect
        return $this->_helper->redirector->gotoRoute(array('action' => 'manage', 'event_id' => $event_id), "siteeventticket_ticket", true);
    }

    //ACTION FOR EDIT TICKET
    public function editAction() {

        //START - VALIDATIONS
        //GET EVENT ID EVENT OBJECT AND THEN CHECK VALIDATIONS
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->event_id = $event_id = $this->_getParam('event_id');
        $this->view->ticket_id = $ticket_id = $this->_getParam('ticket_id');
        if (empty($event_id) || empty($this->_get_ticket_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (empty($siteevent) || empty($this->_get_ticket_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //CHECK FOR CREATION PRIVACY 
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', $viewer, "ticket_create")->isValid() || empty($this->_get_ticket_id)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid() || empty($this->_get_ticket_id)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END - VALIDATIONS

        $siteeventticket = Engine_Api::_()->getItem('siteeventticket_ticket', $ticket_id);
        $seaoSmoothbox = $this->_getParam('seaoSmoothbox');

        $this->view->form = $form = new Siteeventticket_Form_Ticket_Edit(array('seaoSmoothbox' => $seaoSmoothbox, 'event_id' => $event_id));

        // Populate form
        $form->populate($siteeventticket->toArray());

        // Check post/form
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();

        //PAYMENT GATEWAYS ENABLED CHECK FOR PAID TICKETS ONLY
        if ($values['price'] > '0.00') {

            if (Engine_Api::_()->siteevent()->hasPackageEnable()) {
                $package = Engine_Api::_()->getItemTable('siteeventpaid_package')->fetchRow(array('package_id = ?' => $siteevent->package_id));
                if (empty($package->ticket_type)) {
                    $error = Zend_Registry::get('Zend_Translate')->_("You can only create free tickets under selected package of your event.");
                }
            }

            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
            // DIRECT PAYMENT TO SELLER ENABLED
            if (empty($isPaymentToSiteEnable)) {
                $eventEnabledgateway = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($event_id, 'event_gateway');

                if (!empty($eventEnabledgateway)) {
                    $siteAdminEnablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.allowed.payment.gateway', array('paypal', 'cheque', 'cod'));
                    $eventEnabledgateway = Zend_Json_Decoder::decode($eventEnabledgateway);

                    foreach ($eventEnabledgateway as $gatewayName => $gatewayTableId) {
                        if (in_array($gatewayName, $siteAdminEnablePaymentGateway)) {
                            $finalEventEnableGateway[] = $gatewayName;
                        }
                    }
                }

                // IF NO PAYMENT GATEWAY ENABLE
                if (empty($eventEnabledgateway) || empty($finalEventEnableGateway)) {
                    $no_payment_gateway_enable = true;
                }
            } else {
                $gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
                $enable_gateway = $gateway_table->select()
                    ->from($gateway_table->info('name'), array('gateway_id', 'title', 'plugin'))
                    ->where('enabled = 1')
                    ->query()
                    ->fetchAll();

                $admin_payment_gateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.admin.gateway', array('cheque'));

                // IF NO PAYMENT GATEWAY ENABLE BY THE SITEADMIN
                if (empty($enable_gateway) && empty($admin_payment_gateway)) {
                    $no_payment_gateway_enable = true;
                }
            }

            if (!empty($no_payment_gateway_enable) && $values['status'] != 'hidden') {
                if (empty($isPaymentToSiteEnable)) {
                    $error = Zend_Registry::get('Zend_Translate')->_("You need to enable payment gateways to create paid ticket. If you still want to create this ticket, then you may create it with 'Hidden' status and after enabling the payment gateways you can change it to 'Open' status.");
                } else {
                    $error = Zend_Registry::get('Zend_Translate')->_("You cannot create paid tickets as Site Admin has not enabled payment gateways for this site. Please contact your site admin for further queries. But, If you still want to create this ticket, then you may create it with the 'Hidden' status and can change it to 'Open' status once site Admin enables the payment gateways.");
                }
            }
        }

        //CALCULATES MAXIMUM SOLD OF EACH TICKET - while ticket edit, owner can not set the quantity less than this count.
        $occurrenceTable = Engine_Api::_()->getDbtable('occurrences', 'siteevent');
        $maxEachTicketSoldCount = $occurrenceTable->maxEachTicketSoldCount(array('event_id' => $event_id, 'ticket_id' => $ticket_id));

        if ($values['quantity'] < $maxEachTicketSoldCount) {
            $error = $this->view->translate('Available Quantity cannot be less than %1s, as %2s tickets have already been purchased for this event.', $maxEachTicketSoldCount, $maxEachTicketSoldCount);
        } elseif ($values['buy_limit_min'] > $values['buy_limit_max']) {
            $error = Zend_Registry::get('Zend_Translate')->_('"Minimum Buying Limit" should be lesser than the "Maximum Buying Limit".');
        } elseif ($values['buy_limit_min'] > $values['quantity']) {
            $error = Zend_Registry::get('Zend_Translate')->_('"Minimum Buying Limit" should be lesser than the "Available Quantity".');
        } elseif ($values['buy_limit_max'] > $values['quantity']) {
            $error = Zend_Registry::get('Zend_Translate')->_('"Maximum Buying Limit" should be lesser than the "Available Quantity".');
        }
        if (isset($error) && !empty($error)) {
            $form->addError($error);
            return;
        }
        // Process
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $siteeventticket->setFromArray($values);
            $siteeventticket->modified_date = date('Y-m-d H:i:s');
            $siteeventticket->save();

            // Add Activity
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($viewer, $siteeventticket, 'ticket_create');
            if ($action) {
                $activityApi->attachActivity($action, $siteeventticket);
            }

            $db->commit();
            // Redirect
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your event ticket has been updated successfully.'))
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR BUY TICKET
    public function buyAction() {

        $this->view->headLink()
            ->prependStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteeventprofile.css');

        $this->_helper->content
            ->setNoRender()
            ->setEnabled();
    }

    //ACTION TO SET TERMS & CONDITION
    public function termsOfUseAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id');
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit') || empty($this->_get_ticket_id)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //SELECTED TAB
        $this->view->TabActive = "termsofuse";

        //MAKE FORM
        $this->view->form = $form = new Siteeventticket_Form_Ticket_Termsofuse();

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'siteevent');

        //SAVE THE VALUE
        if ($this->getRequest()->isPost()) {
            $tableOtherinfo->update(array('terms_of_use' => $_POST['terms_of_use']), array('event_id = ?' => $event_id));
            $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }

        //POPULATE FORM
        $values['terms_of_use'] = $tableOtherinfo->getColumnValue($event_id, 'terms_of_use');
        $form->populate($values);
    }

    public function memberListAction() {

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //GET USER LEVEL ID
        if ($viewer->getIdentity()) {
            $this->view->level_id = $viewer->level_id;
        } else {
            $this->view->level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $subject = Engine_Api::_()->core()->getSubject('siteevent_event');
        $this->view->friendsonly = $friendsonly = $this->_getParam('friendsonly', 0);

        $this->view->canEdit = $subject->authorization()->isAllowed($viewer, "edit");
        $this->view->list = $list = $subject->getLeaderList();
        $this->view->datesInfo = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($subject->event_id);
        $this->view->page = $page = $this->_getParam('page', 1);
        $this->view->search = $search = $this->_getParam('search');
        $this->view->occurrence_id = $occurrence_id = $this->_getParam('occurrence_id', 'all');

        if (empty($occurrence_id)) {
            $this->view->occurrence_id = $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
        }

        $this->view->event = $event = Engine_Api::_()->core()->getSubject();

        $orderTable = Engine_Api::_()->getDbTable('orders', 'siteeventticket');
        $userTable = Engine_Api::_()->getItemTable('user');
        $userTableName = $userTable->info('name');
        $orderTableName = $orderTable->info('name');

        $select = $userTable->select()
            ->setIntegrityCheck(false)
            ->from($userTableName, array("user_id", "displayname", "username", 'photo_id'))
            ->join($orderTableName, "$orderTableName.user_id = $userTableName.user_id", array(""))
            ->where("$orderTableName.event_id = ?", $event->getIdentity())
            ->where("(payment_status = 'active' OR (non_payment_seller_reason = 0 && non_payment_admin_reason = 0))")
            ->where("$orderTableName.is_private_order = ?", 0)
            ->group("$orderTableName.user_id")
            ->order("$orderTableName.creation_date DESC");

        if (!empty($occurrence_id) && $occurrence_id != 'all') {
            $select->where("occurrence_id=?", $occurrence_id);
        } else {
            $this->view->occurrence_id = '';
        }

//        //IF REQUEST IS ONLY TO SHOW VIEWER FRIENDS THEN ALSO PUT THE JOIN WITH USER MEMBERSHIP TABLE.
//        if ($friendsonly) {
//            $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
//            $membershipEventTableName = 'engine4_siteevent_orders';
//            $membershipTableName = $membershipTable->info('name');
//            $select->join($membershipTableName, "$membershipTableName.resource_id = $membershipEventTableName.user_id", null)
//                    ->where($membershipTableName . '.user_id = ?', $viewer->getIdentity())
//                    ->where($membershipTableName . '.active = ?', 1)
//                    ->where('engine4_users.verified = ?', 1)
//                    ->where('engine4_users.enabled = ?', 1);
//        }

        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 20));
        $paginator->setCurrentPageNumber($this->_getParam('page', $page));
        $this->view->totalMembers = $paginator->getTotalItemCount();
    }
    
    public function exportExcelAction() {

        $this->_helper->layout->setLayout('default-simple');

        $this->view->event = $event = Engine_Api::_()->getItem('siteevent_event', $this->_getParam('event_id'));
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $canEdit = $event->authorization()->isAllowed($viewer, "edit");

        if (!$event->isOwner($viewer) && $viewer->level_id != 1 && !$canEdit) {
            return;
        }        

        $this->view->occurrence_id = $occurrence_id = $this->_getParam('occurrence_id', null);
   
        $this->view->eventDates = $event->getStartEndDate($occurrence_id);

        $orderTable = Engine_Api::_()->getDbTable('orders', 'siteeventticket');    
        $orderTableName = $orderTable->info('name');        
        
        $userTable = Engine_Api::_()->getDbtable('users', 'user');
        $userTableName = $userTable->info('name');

        $select = $orderTable->select()
                ->setIntegrityCheck(false)
                ->from($orderTableName, array('order_id'))
                ->join($userTableName, $userTableName . '.user_id = ' . $orderTableName . '.user_id', array('username', 'displayname'))
                ->where("(payment_status = 'active' OR (non_payment_seller_reason = 0 && non_payment_admin_reason = 0))")
                ->where("$orderTableName.event_id =?", $event->getIdentity())
                ->order('order_id');
                //->group("$orderTableName.user_id");                 

        if ($occurrence_id) {
            $select->where($orderTableName . '.occurrence_id = ?', $occurrence_id);
        }

        $this->view->memberDetails = $orderTable->fetchAll($select);   
 
        $this->view->buyerDetails = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.detail.step', 1);
    }    

}

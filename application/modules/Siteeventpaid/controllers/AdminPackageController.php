<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminPackageController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventpaid_AdminPackageController extends Core_Controller_Action_Admin {

    //ACTION FOR MANAGE PACKAGE EVENTS
    public function indexAction() {

        //TAB CREATION
        $this->view->navigation = $this->_navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteeventticket_admin_main_ticket');

        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteeventticket_admin_main_ticket', array(), 'siteeventpaid_admin_main_package');

        $this->view->canCreate = $canCreate = 1;

        if (Engine_Api::_()->hasModuleBootstrap('payment')) {
            $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
            $this->view->isEnabled2Checkout = $gatewayTable->select()
                    ->from($gatewayTable, 'enabled')
                    ->where('plugin = ?', 'Payment_Plugin_Gateway_2Checkout')
                    ->query()
                    ->fetchColumn();

            //TEST CURL SUPPORT
            if (!function_exists('curl_version') ||
                    !($info = curl_version())) {
                $this->view->error = 'The PHP extension cURL' .
                        'does not appear to be installed, which is required' .
                        'for interaction with payment gateways. Please contact your' .
                        'hosting provider.';
            }
            //TEST CURL SSL SUPPORT
            else if (!($info['features'] & CURL_VERSION_SSL) ||
                    !in_array('https', $info['protocols'])) {
                $this->view->error = 'The installed version of' .
                        'the cURL PHP extension does not support HTTPS, which is required' .
                        'for interaction with payment gateways. Please contact your' .
                        'hosting provider.';
            }
            //CHECK FOR ENABLE PAYMENT GATEWAYS
            else if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
                $this->view->error = $this->view->translate('There are currently no enabled payment gateways. You must %1$senable payment gatways%2$s before creating a paid package.', '<a href="' .
                        $this->view->escape($this->view->url(array('module' => 'payment', 'controller' => 'gateway'))) .
                        '"  target="_blank" >', '</a>');
            }
        } else {
            $this->view->canCreate = $canCreate = 0;
            $this->view->error = 'You have not install or enable "Payment" module. Please install or enable "Payment" module to create or edit package.';
        }

        //INITILIZE SELECT
        $table = Engine_Api::_()->getDbtable('packages', 'siteeventpaid');
        $select = $table->select();

        //FILTER FORM
        $this->view->formFilter = $formFilter = new Siteeventpaid_Form_Admin_Package_Filter();

        //PROCESS FORM
        if ($formFilter->isValid($this->_getAllParams())) {
            $filterValues = $formFilter->getValues();
        }
        if (empty($filterValues['order'])) {
            $select->order("order");
            $filterValues['order'] = 'package_id';
        }
        if (empty($filterValues['direction'])) {

            $filterValues['direction'] = 'DESC';
        }
        $this->view->filterValues = $filterValues;
        $this->view->order = $filterValues['order'];
        $this->view->direction = $filterValues['direction'];

        //ADD FILTER VALUES
        if (!empty($filterValues['query'])) {
            $select->where('title LIKE ?', '%' . $filterValues['query'] . '%');
        }

        if (isset($filterValues['enabled']) && '' != $filterValues['enabled']) {
            $select->where('enabled = ?', $filterValues['enabled']);
        }

        if (!empty($filterValues['order'])) {
            if (empty($filterValues['direction'])) {
                $filterValues['direction'] = 'ASC';
            }
            $select->order($filterValues['order'] . ' ' . $filterValues['direction']);
        }

        include APPLICATION_PATH . '/application/modules/Siteeventticket/controllers/license/license2.php';

        //GET EVENTS TOTALS FOR EACH PACKAGE
        $memberCounts = array();
        foreach ($paginator as $item) {
            $memberCounts[$item->package_id] = Engine_Api::_()->getDbtable('events', 'siteevent')
                    ->select()
                    ->from('engine4_siteevent_events', new Zend_Db_Expr('COUNT(*)'))
                    ->where('package_id = ?', $item->package_id)
                    ->query()
                    ->fetchColumn();
        }
        $this->view->memberCounts = $memberCounts;
    }

    //ACTION FOR PACKAGE CREATE
    public function createAction() {

        if (!Engine_Api::_()->hasModuleBootstrap('payment')) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //TAB CREATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteeventpaid_admin_main_package');

        //FORM GENERATION
        $this->view->form = $form = new Siteeventpaid_Form_Admin_Package_Create();

        //GET SUPPORTED BILLING CYCLES
        $gateways = array();
        $supportedBillingCycles = array();
        $partiallySupportedBillingCycles = array();
        $fullySupportedBillingCycles = null;
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        foreach ($gatewaysTable->fetchAll() as $gateway) {
            $gateways[$gateway->gateway_id] = $gateway;
            $supportedBillingCycles[$gateway->gateway_id] = $gateway->getGateway()->getSupportedBillingCycles();
            $partiallySupportedBillingCycles = array_merge($partiallySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
            if (null == $fullySupportedBillingCycles) {
                $fullySupportedBillingCycles = $supportedBillingCycles[$gateway->gateway_id];
            } else {
                $fullySupportedBillingCycles = array_intersect($fullySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
            }
        }
        $partiallySupportedBillingCycles = array_diff($partiallySupportedBillingCycles, $fullySupportedBillingCycles);

        $multiOptions = array_combine(array_map('strtolower', $fullySupportedBillingCycles), $fullySupportedBillingCycles);
        $form->getElement('recurrence')
                ->setMultiOptions($multiOptions);
        $form->getElement('recurrence')->options['forever'] = 'One-time';
        //$form->getElement('recurrence')->options['day'] = 'Day';

        $form->getElement('duration')
                ->setMultiOptions($multiOptions);
        $form->getElement('duration')->options/* ['Fully Supported'] */['forever'] = 'Forever';

        //FORM VALDIATION
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $values = $form->getValues();

        $tmp = $values['recurrence'];
        unset($values['recurrence']);
        if (empty($tmp) || !is_array($tmp)) {
            $tmp = array(null, null);
        }

        $values['recurrence'] = (int) $tmp[0];
        $values['recurrence_type'] = $tmp[1];

        if (!isset($values['twitter'])) {
            $values['twitter'] = 0;
        }

        if ($values['price'] > 0) {

            //FOR NOT ENABLE GATEWAYS
            if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
                $form->getDecorator('errors')->setOption('escape', false);

                $error = $this->view->translate('You have not enabled a payment gateway yet. Please %1$senable payment gateways%2$s  before creating a paid package.', '<a href="' . $this->view->baseUrl() . '/admin/payment/gateway" ' . " target='_blank'" . '">', '</a>');
                $this->view->status = false;
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                return $form->addError($error);
            }
        }

        //MEMBER LEVEL SETTING WORK
        if (@in_array('0', $values['level_id'])) {
            $values['level_id'] = 0;
        } else {
            $values['level_id'] = implode(',', $values['level_id']);
        }

        $tmp = $values['duration'];
        unset($values['duration']);
        if (empty($tmp) || !is_array($tmp)) {
            $tmp = array(null, null);
        }
        $values['duration'] = (int) $tmp[0];
        $values['duration_type'] = $tmp[1];
        if (isset($values['modules']))
            $values['modules'] = serialize($values['modules']);
        else
            $values['modules'] = serialize(array());

        $profileFields = array();
        if ($values['profile'] == 2) {
            foreach ($_POST as $key => $value) {
                if (@strstr($key, '_profilecheck_') != null && $value) {
                    $tc = @explode("_profilecheck_", $key);
                    $profileFields[] = "1_" . $tc[0] . "_" . $value;
                }
            }
        }
        $values['profilefields'] = serialize($profileFields);

        //IF SITEEVENTTICKET MODULE IS ENABLED-TICKET SETTINGS SAVE IN SERIALIZED FORMAT
        if (Engine_Api::_()->siteevent()->hasTicketEnable()) {
            $ticketValues = array();
            $ticketValues['commission_handling'] = $values['commission_handling'];
            $ticketValues['commission_fee'] = $values['commission_fee'];
            $ticketValues['commission_rate'] = $values['commission_rate'];
            if(Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', '0')) {
                $ticketValues['transfer_threshold'] = $values['transfer_threshold'];
            }
            $values['ticket_settings'] = @serialize($ticketValues);
            unset($values['commission_handling']);
            unset($values['commission_fee']);
            unset($values['commission_rate']);
            if(Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', '0')) {
                unset($values['transfer_threshold']);
            }
        }

        $packageTable = Engine_Api::_()->getDbtable('packages', 'siteeventpaid');
        $db = $packageTable->getAdapter();
        $db->beginTransaction();

        try {
            $package = $packageTable->createrow();
            $package->setFromArray($values);
            $package->save();

            //CREATE PACKAGE IN GATEWAYS?
            if (!$package->isFree()) {
                $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
                foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
                    $gatewayPlugin = $gateway->getGateway();
                    //CHECK BILLING CYCLE SUPPORT
                    if (!$package->isOneTime()) {
                        $sbc = $gateway->getGateway()->getSupportedBillingCycles();
                        if (!in_array($package->recurrence_type, array_map('strtolower', $sbc))) {
                            continue;
                        }
                    }
                    if (method_exists($gatewayPlugin, 'createProduct')) {
                        $gatewayPlugin->createProduct($package->getGatewayParams());
                    }
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //REDIRECT
        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    //ACTION FOR PACKAGE EDIT
    public function editAction() {

        if (!Engine_Api::_()->hasModuleBootstrap('payment')) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //TAB CREATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteeventpaid_admin_main_package');

        //GET PACKAGES
        if (null == ($packageIdentity = $this->_getParam('package_id')) ||
                !($package = Engine_Api::_()->getDbtable('packages', 'siteeventpaid')->find($packageIdentity)->current())) {
            throw new Engine_Exception('No package found');
        }

        //FORM GENERATION
        $this->view->form = $form = new Siteeventpaid_Form_Admin_Package_Edit();
        $this->view->package = $package;
        $values = $package->toArray();

        //TICKET SETTINGS POPULATE
        if (Engine_Api::_()->siteevent()->hasTicketEnable() && !empty($values['ticket_settings'])) {
            $getTicketSettings = @unserialize($values['ticket_settings']);
            unset($values['ticket_settings']);
            $values = @array_merge($values, $getTicketSettings);
        }

        $values['recurrence'] = array($values['recurrence'], $values['recurrence_type']);

        $values['duration'] = array($values['duration'], $values['duration_type']);
        $is_packageedit = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventpaid.edit.package', null);

        unset($values['recurrence_type']);

        unset($values['duration_type']);
        $values['level_id'] = explode(',', $values['level_id']);

        $otherValues = array(
            'price' => $values['price'],
            'recurrence' => $values['recurrence'],
            'duration' => $values['duration'],
        );
        if (isset($values['modules']))
            $values['modules'] = unserialize($values['modules']);
        $form->populate($values);
        $profileFields = array();
        if ($values['profile'] == 2) {
            $profileFields = unserialize($values['profilefields']);
        }
        $session = new Zend_Session_Namespace('profileFields');
        $session->profileFields = $profileFields;

        //CHECK METHOD DATA
        if (!$this->getRequest()->isPost() || !empty($is_packageedit)) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        if (isset($session->profileFields)) {
            unset($session->profileFields);
        }

        //HACK EM UP
        $form->populate($otherValues);

        //PROCESS
        $values = $form->getValues();

        //for member level seting work
        if (@in_array('0', $values['level_id'])) {
            $values['level_id'] = 0;
        } else {
            $values['level_id'] = implode(',', $values['level_id']);
        }

        unset($values['price']);
        unset($values['recurrence']);
        unset($values['recurrence_type']);
        unset($values['duration']);
        unset($values['duration_type']);
        unset($values['trial_duration']);
        unset($values['trial_duration_type']);
        if (isset($values['modules']))
            $values['modules'] = serialize($values['modules']);
        else
            $values['modules'] = serialize(array());
        $profileFields = array();
        if ($values['profile'] == 2) {
            $i = 0;
            foreach ($_POST as $key => $value) {
                if (@strstr($key, '_profilecheck_') != null && $value) {
                    $tc = @explode("_profilecheck_", $key);
                    $profileFields[] = "1_" . $tc[0] . "_" . $value;
                }
            }
        }

        $values['profilefields'] = serialize($profileFields);

        //IF SITEEVENTTICKET MODULE IS ENABLED-TICKET SETTINGS SAVE IN SERIALIZED FORMAT
        if (Engine_Api::_()->siteevent()->hasTicketEnable()) {
            $ticketValues = array();
            $ticketValues['commission_handling'] = $values['commission_handling'];
            $ticketValues['commission_fee'] = $values['commission_fee'];
            $ticketValues['commission_rate'] = $values['commission_rate'];
            $ticketValues['transfer_threshold'] = $values['transfer_threshold'];
            $values['ticket_settings'] = @serialize($ticketValues);
            unset($values['commission_handling']);
            unset($values['commission_fee']);
            unset($values['commission_rate']);
            unset($values['transfer_threshold']);
        }

        $packageTable = Engine_Api::_()->getDbtable('packages', 'siteeventpaid');
        $db = $packageTable->getAdapter();
        $db->beginTransaction();

        try {
            $package->setFromArray($values);
            $package->save();

            //CREATE PACKAGE IN GATEWAYS
            if (!$package->isFree()) {
                $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
                foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
                    $gatewayPlugin = $gateway->getGateway();

                    //CHECK BILLING CYCLE SUPPORT
                    if (!$package->isOneTime()) {
                        $sbc = $gateway->getGateway()->getSupportedBillingCycles();
                        if (!in_array($package->recurrence_type, array_map('strtolower', $sbc))) {
                            continue;
                        }
                    }
                    if (!method_exists($gatewayPlugin, 'createProduct') ||
                            !method_exists($gatewayPlugin, 'editProduct') ||
                            !method_exists($gatewayPlugin, 'detailVendorProduct')) {
                        continue;
                    }

                    //IF IT THROWS AN EXCEPTION, OR RETURNS EMPTY, ASSUME IT DOESN'T EXIST?
                    try {
                        $info = $gatewayPlugin->detailVendorProduct($package->getGatewayIdentity());
                    } catch (Exception $e) {
                        $info = false;
                    }
                    //CREATE
                    if (!$info) {
                        $gatewayPlugin->createProduct($package->getGatewayParams());
                    }
                    //EDIT
                    else {
                        $gatewayPlugin->editProduct($package->getGatewayIdentity(), $package->getGatewayParams());
                    }
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //REDIRECT
        return $this->_helper->redirector->gotoRoute(array('module' => 'siteeventpaid', 'controller' => 'package', 'action' => 'index'), "admin_default", true);
    }

    //ACTION FOR SHOW THE PACKAGE DETAILS
    public function packgeDetailAction() {

        $id = $this->_getParam('id');
        if (empty($id)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
        $this->view->package = $package = Engine_Api::_()->getItem('siteeventpaid_package', $id);
        $this->view->overview = $coreSettingsApi->getSetting('siteevent.overview', 1);
        $this->view->package_description = $coreSettingsApi->getSetting('siteevent.package.description', 1);
        $this->view->viewer = Engine_Api::_()->user()->getViewer();
    }

    //ACTION FOR PACKAGE UPDATION
    public function updateAction() {

        //CHECK POST
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            $values = $_POST;
            try {
                foreach ($values['order'] as $key => $value) {

                    $package = Engine_Api::_()->getItem('siteeventpaid_package', (int) $value);
                    if (!empty($package)) {
                        $package->order = $key + 1;
                        $package->save();
                    }
                }
                $db->commit();
                $this->_helper->redirector->gotoRoute(array('action' => 'index'));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    //ACTION FOR MAKE PACKAGES ENABLE/DISABLE
    public function enabledAction() {
        $id = $this->_getParam('id');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        $package = Engine_Api::_()->getItem('siteeventpaid_package', $id);
        if ($package->enabled == 0) {
            try {
                $package->enabled = 1;
                $package->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_redirect('admin/siteeventpaid/package/index');
        } else {
            if ($this->getRequest()->isPost()) {
                try {
                    $package->enabled = 0;
                    $package->save();
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 10,
                    'parentRefresh' => 10,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
                ));
            }
        }
    }

    //ACTION FOR DELETE THE PACKAGE
    public function deleteAction() {

        $this->_helper->layout->setLayout('admin-simple');

        // Get package
        if (null === ($packageIdentity = $this->_getParam('id')) ||
                !($package = Engine_Api::_()->getDbtable('packages', 'siteeventpaid')->find($packageIdentity)->current())) {
            throw new Engine_Exception('No package found');
        }

        if ($this->getRequest()->isPost()) {
            $packageTable = Engine_Api::_()->getDbtable('packages', 'siteeventpaid');
            $db = $packageTable->getAdapter();
            $db->beginTransaction();

            try {

                // Delete package in gateways?
                $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
                foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
                    $gatewayPlugin = $gateway->getGateway();
                    if (method_exists($gatewayPlugin, 'deleteProduct')) {
                        try {
                            $gatewayPlugin->deleteProduct($package->getGatewayIdentity());
                        } catch (Exception $e) {
                            
                        } // Silence?
                    }
                }

                // Delete package
                $package->delete();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Deleted Succesfully.')
            ));
        }
        $this->renderScript('admin-package/delete.tpl');
    }

}

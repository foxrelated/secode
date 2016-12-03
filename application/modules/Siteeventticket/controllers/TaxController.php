<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: TaxController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_TaxController extends Core_Controller_Action_Standard {

    // COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->event_id = $event_id = $this->_getParam('event_id', null);
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', $viewer, "ticket_create")->isValid()) {
            //CHECK FOR CREATION PRIVACY
            return $this->_forward('requireauth', 'error', 'core');
        }

        $taxEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.tax.enabled', 0);

        if (empty($taxEnabled)) {
            return $this->_forward('notfound', 'error', 'core');
        }
    }

    public function indexAction() {

        $this->view->event_id = $params['event_id'] = $this->_getParam('event_id', null);
        $this->view->siteevent = Engine_Api::_()->getItem('siteevent_event', $params['event_id']);
        $this->view->form = $form = new Siteeventticket_Form_Tax();

        $this->view->taxMandatory = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.tax.mandatory', 0);

        $params['columns'] = array('is_tax_allow', 'tax_rate', 'tax_id_no');
        $taxValuesColumns = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getOtherinfoColumns($params);
        
        //IF TAX MANDATORY THEN DISABLE THE TAX CHECKBOX
        if ($this->view->taxMandatory) {
            $form->is_tax_allow->setAttrib('disable', true);
            $form->is_tax_allow->setAttrib('value', 1);
            $taxValuesColumns['is_tax_allow'] = 1;
        }

        $values = array('is_tax_allow' => $taxValuesColumns['is_tax_allow'], 'tax_rate' => $taxValuesColumns['tax_rate'], 'tax_id_no' => $taxValuesColumns['tax_id_no']);
        
        $values['tax_rate'] = !empty($values['tax_rate']) ? $values['tax_rate'] : null;
        $form->populate($values);
    }

    public function saveVatDetailAction() {

        @parse_str($_POST['eventVatValues'], $eventVatValues);
        $event_id = $this->_getParam('event_id');

        if ($eventVatValues['tax_rate'] >= 0 && $eventVatValues['tax_rate'] <= 100) {
            $tax_rate = round($eventVatValues['tax_rate'], 2);
        } else {
            $this->view->VATinvalidRateMessage = true;
            return;
        }

        $taxMandatory = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.tax.mandatory', 0);
        if ($taxMandatory) {
            $eventVatValues['is_tax_allow'] = 1;
        }
        if ($taxMandatory && $eventVatValues['tax_rate'] <= 0) {
            $this->view->VATMandatoryMessage = true;
            return;
        }

        if (empty($eventVatValues['tax_id_no'])) {
            $this->view->TINMandatoryMessage = true;
            return;
        }

        //SAVE DETAILS IN OTHERINFO TABLE  
        $table = Engine_Api::_()->getDbTable('otherinfo', 'siteevent');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $otherinfo_obj = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getOtherinfo($event_id);
            $otherinfo_obj->is_tax_allow = $eventVatValues['is_tax_allow'];
            $otherinfo_obj->tax_rate = $tax_rate;
            $otherinfo_obj->tax_id_no = $eventVatValues['tax_id_no'];
            $otherinfo_obj->save();
            $db->commit();
            $this->view->VATSuccessMessage = true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

}

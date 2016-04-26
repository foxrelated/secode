<?php

class Ynaffiliate_AdminCommissionController extends Core_Controller_Action_Admin {

    public function init() {
        Zend_Registry::set('admin_active_menu', 'ynaffiliate_admin_main_commission');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/application/modules/Ynaffiliate/externals/scripts/datepicker.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/application/modules/Ynaffiliate/externals/scripts/ynaffiliate_date.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/application/modules/Ynaffiliate/externals/styles/datepicker_jqui/datepicker_jqui.css');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/application/modules/Ynaffiliate/externals/styles/main.css');
    }

    public function indexAction() {
        $page = $this -> _getParam('page', 1);
        $this->view->form = $form = new Ynaffiliate_Form_Admin_Manage_Commission();
        $values = array();
        $req = $this -> getRequest();
        if($form -> isValid($this->_getAllParams())) {
            $values = $form -> getValues();
        }
        $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.page', 10);
        $values['limit'] = $limit;

        $paginator = $this -> view -> paginator = Engine_Api::_()->ynaffiliate()->getCommissionsPaginator($values);
        $this->view->paginator->setCurrentPageNumber($page);
        $this->view->formValues = $values;
    }

    public function acceptAction() {
        $table = new Ynaffiliate_Model_DbTable_Commissions;
        $id = $this -> _getParam('id', 0);
        $item = $table -> find($id) -> current();
        if(!is_object($item)) {
            return;
        }
        // calculate time pass since creation to approve or delay
        $now = new DateTime();
        $delayingPeriod = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.delay', 30);
        $creation_date = new DateTime($item->creation_date);
        $diff = date_diff($creation_date, $now);

        if ($diff->format('%a') < $delayingPeriod) {
            $item->approve_stat = 'delaying';
        } else {
            $item->approve_stat = 'approved';
            $item->approved_date = date('Y-m-d H:i:s');
            $owner = $item->getOwner();
            $client = $item->getClient();
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $notifyApi -> addNotification($owner, $client, $item, 'ynaffiliate_commission_approved');
        }
        $item -> save();

        $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Approve commission successfully.')));
    }

    public function acceptSelectedAction() {
        $table = new Ynaffiliate_Model_DbTable_Commissions;
        $ids = $this -> _getParam('ids', 0);
        $ids_array = explode(",", $ids);
        $now = new DateTime();
        $delayingPeriod = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.delay', 30);
        foreach ($ids_array as $id) {
            $item = $table -> find($id) -> current();
            if(!is_object($item)) {
                continue;
            }
            if ($item->approve_stat == 'waiting') {
                $creation_date = new DateTime($item->creation_date);
                $diff = date_diff($creation_date, $now);
                if ($diff->format('%a') < $delayingPeriod) {
                    $item->approve_stat = 'delaying';
                } else {
                    $item->approve_stat = 'approved';
                    $item->approved_date = date('Y-m-d H:i:s');
                    // create notification
                    $owner = $item->getOwner();
                    $client = $item->getClient();
                    $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
                    $notifyApi -> addNotification($owner, $client, $item, 'ynaffiliate_commission_approved');
                }
            }
            $item -> save();
        }

        $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    public function denyAction() {
        $table = new Ynaffiliate_Model_DbTable_Commissions;
        $id = $this -> _getParam('id', 0);
        $item = $table -> find($id) -> current();
        if(!is_object($item)) {
            return;
        }

        $this->view->form = $form = new Ynaffiliate_Form_Admin_Commission_Deny(array(
        ));

        if( !$this->getRequest()->isPost() ) {
            return;
        }
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }

        $values = $form->getValues();

        $item -> reason = $values['reason'];
        $item -> approve_stat = 'denied';
        $item -> approved_date = date('Y-m-d H:i:s');
        $item -> save();

        // send notification
        $owner = $item->getOwner();
        $client = $item->getClient();
        $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
        $notifyApi -> addNotification($owner, $client, $item, 'ynaffiliate_commission_denied');

        $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Deny commission successfully.')));
    }

    public function denySelectedAction() {
        $table = new Ynaffiliate_Model_DbTable_Commissions;
        $ids = $this -> _getParam('ids', 0);

        $ids_array = explode(",", $ids);

        foreach ($ids_array as $id) {
            $item = $table->find($id)->current();
            if (!is_object($item)) {
                continue;
            }
            if ($item->approve_stat == 'waiting') {
                $item->approve_stat = 'denied';
                $item->approved_date = date('Y-m-d H:i:s');

                // notification
                $owner = $item->getOwner();
                $client = $item->getClient();
                $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
                $notifyApi -> addNotification($owner, $client, $item, 'ynaffiliate_commission_denied');

                $item->save();
            }
        }
        $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    public function rejectAction() {
        $table = new Ynaffiliate_Model_DbTable_Commissions;
        $id = $this -> _getParam('id', 0);
        $item = $table -> find($id) -> current();
        if(!is_object($item)) {
            return;
        }

        $this->view->form = $form = new Ynaffiliate_Form_Admin_Commission_Reject(array(
        ));

        if( !$this->getRequest()->isPost() ) {
            return;
        }
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }

        $values = $form->getValues();

        $item -> reason = $values['reason'];
        $item -> approve_stat = 'denied';
        $item -> approved_date = date('Y-m-d H:i:s');
        $item -> save();

        $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Reject commission successfully.')));
    }
}

<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PriceInfoController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_PriceInfoController extends Core_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        $front = Zend_Controller_Front::getInstance();
        $action = $front->getRequest()->getActionName();
        if ($action == 'redirect') {
            return;
        }

        //ONLY LOGGED IN USER CAN ADD PRICE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //IF WHERE TO BUY IS NOT ALLOWED
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.wheretobuy', 0)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //AUTHENTICATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if ($action != 'redirect') {
            $viewer = Engine_Api::_()->user()->getViewer();

            if ($action == 'edit' || $action == 'delete') {
                $priceinfo_id = $this->_getParam('id', null);
                $priceInfo = Engine_Api::_()->getDbTable('priceinfo', 'siteevent')->getPriceInfo($priceinfo_id);
                $siteevent = Engine_Api::_()->getItem('siteevent_event', $priceInfo->event_id);
            } else {
                $siteevent = Engine_Api::_()->getItem('siteevent_event', $this->_getParam('id'));
            }

            if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }
    }

    public function indexAction() {

        //GET EVENT ID
        $event_id = $this->_getParam('id', null);
        $this->view->includeDiv = $this->_getParam('includeDiv', 1);

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $this->view->TabActive = 'priceinfo';
        $this->view->show_price = (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.wheretobuy', 0) == 1) ? 0 : 1;
        $this->view->priceInfos = Engine_Api::_()->getDbTable('priceinfo', 'siteevent')->getPriceDetails($event_id);
    }

    public function addAction() {

        //LAYOUT
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {
            $this->_helper->layout->disableLayout(true);
        }

        //ONLY LOGGED IN USER CAN VIEW THIS PAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET EVENT ID
        $event_id = $this->_getParam('id', null);

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Priceinfo_Add();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $table = Engine_Api::_()->getDbTable('priceinfo', 'siteevent');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {

                $values = array_merge($form->getValues(), array('event_id' => $event_id));
                if ($values['wheretobuy_id'] != 1) {
                    unset($values['title']);
                    unset($values['address']);
                    unset($values['contact']);
                } elseif (empty($values['title'])) {
                    $error = $this->view->translate('Please complete Title field - it is required.');
                    $error = Zend_Registry::get('Zend_Translate')->_($error);
                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    return;
                }
                $priceInfo = $table->createRow();
                $priceInfo->setFromArray($values);
                $priceInfo->save();

                $preg_match = preg_match('/\s*[a-zA-Z0-9]{2,5}:\/\//', $priceInfo->url);

                if (empty($preg_match)) {
                    $priceInfo->url = "http://" . $priceInfo->url;
                    $priceInfo->save();
                }

                //COMMIT
                $db->commit();

                $this->view->responseHTML = $this->view->action('index', 'price-info', 'siteevent', array(
                    'includeDiv' => 0,
                    'id' => $event_id,
                    'format' => 'html',
                ));
                $this->_helper->contextSwitch->initContext();

                if (empty($this->view->responseHTML)) {
                    $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'format' => 'smoothbox',
                        'messages' => Zend_Registry::get('Zend_Translate')->_("New 'Where to Buy' option has been added successfully.")
                    ));
                }
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    public function editAction() {

        //LAYOUT
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {
            $this->_helper->layout->disableLayout(true);
        }

        //ONLY LOGGED IN USER CAN VIEW THIS PAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PRICE INFO ID
        $priceinfo_id = $this->_getParam('id', null);

        $priceInfo = Engine_Api::_()->getDbTable('priceinfo', 'siteevent')->getPriceInfo($priceinfo_id);

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Priceinfo_Edit();
        $form->populate($priceInfo->toArray());

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            if ($values['wheretobuy_id'] != 1) {
                unset($values['title']);
            } elseif (empty($values['title'])) {
                $error = $this->view->translate('Please complete Title field - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }
            $table = Engine_Api::_()->getDbTable('priceinfo', 'siteevent');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {

                $priceInfo->setFromArray($values);
                $priceInfo->save();

                $preg_match = preg_match('/\s*[a-zA-Z0-9]{2,5}:\/\//', $priceInfo->url);
                if (empty($preg_match)) {
                    $priceInfo->url = "http://" . $priceInfo->url;
                    $priceInfo->save();
                }

                //COMMIT
                $db->commit();

                $this->view->responseHTML = $this->view->action('index', 'price-info', 'siteevent', array(
                    'includeDiv' => 0,
                    'id' => $priceInfo->event_id,
                    'format' => 'html',
                ));
                $this->_helper->contextSwitch->initContext();

                if (empty($this->view->responseHTML)) {
                    $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'format' => 'smoothbox',
                        'messages' => Zend_Registry::get('Zend_Translate')->_("'Where to Buy' option has been edited successfully.")
                    ));
                }
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    public function deleteAction() {

        //LAYOUT
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {
            $this->_helper->layout->disableLayout(true);
        }

        //ONLY LOGGED IN USER CAN VIEW THIS PAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PRICE INFO ID
        $priceinfo_id = $this->_getParam('id', null);

        $priceInfo = Engine_Api::_()->getDbTable('priceinfo', 'siteevent')->getPriceInfo($priceinfo_id);
        $event_id = $priceInfo->event_id;

        if (!$this->getRequest()->isPost())
            return;

        //DELTE PRICE INFO
        Engine_Api::_()->getDbTable('priceinfo', 'siteevent')->delete(array('priceinfo_id = ?' => $priceinfo_id));

        $this->view->responseHTML = $this->view->action('index', 'price-info', 'siteevent', array(
            'includeDiv' => 0,
            'id' => $event_id,
            'format' => 'html',
        ));
        $this->_helper->contextSwitch->initContext();

        if (empty($this->view->responseHTML)) {
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'format' => 'smoothbox',
                'messages' => Zend_Registry::get('Zend_Translate')->_("'Where to Buy' option has been deleted successfully.")
            ));
        }
    }

    public function redirectAction() {

        $url = $this->_getParam('url', null);
        if (empty($url)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        header('Location: ' . @base64_decode($url));

        exit(0);
    }

}

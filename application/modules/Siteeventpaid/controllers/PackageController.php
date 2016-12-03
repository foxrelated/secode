<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PackageController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventpaid_PackageController extends Core_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //USER VALIDATON
        if (!$this->_helper->requireUser()->isValid())
            return;

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            return;

        $siteeventpaidListPackage = Zend_Registry::isRegistered('siteeventpaidListPackage') ? Zend_Registry::get('siteeventpaidListPackage') : null;
        if (empty($siteeventpaidListPackage))
            return;
    }

    //ACTION FOR SHOW PACKAGES
    public function indexAction() {

        //EVENT CREATION PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "create")->isValid())
            return;

        $package_show = $this->_getParam('package', 0);
        $parent_type = $this->_getParam('parent_type');
        $parent_id = $this->_getParam('parent_id');

        if ($package_show == 1) {

            $packageCount = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getPackageCount();

            if (!Engine_Api::_()->siteevent()->hasPackageEnable()) {
                //REDIRECT
                if ($parent_type && $parent_id) {
                    return $this->_helper->redirector->gotoRoute(array('action' => 'create', 'parent_id' => $parent_id, 'parent_type' => $parent_type), "siteevent_general", true);
                } else {
                    return $this->_helper->redirector->gotoRoute(array('action' => 'create'), "siteevent_general", true);
                }
            }

            if ($packageCount == 1) {
                $package = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getEnabledPackage();
                if (($package->price == '0.00')) {
                    if ($parent_type && $parent_id) {
                        return $this->_helper->redirector->gotoRoute(array('action' => 'create', 'id' => $package->package_id, 'parent_id' => $parent_id, 'parent_type' => $parent_type), "siteevent_general", true);
                    } else {
                        return $this->_helper->redirector->gotoRoute(array('action' => 'create', 'id' => $package->package_id), "siteevent_general", true);
                    }
                }
            }
        }

        $this->_helper->content
                ->setContentName("siteeventpaid_package_index")
                ->setNoRender()
                ->setEnabled();
    }

    //ACTION FOR PACKAGE DETAIL
    public function detailAction() {

        //EVENT CREATION PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "create")->isValid())
            return;

        //PACKAGE ENABLE VALIDATION
        if (!Engine_Api::_()->siteevent()->hasPackageEnable()) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $id = $this->_getParam('id');
        if (empty($id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->overview = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 0);

        $this->view->viewer = Engine_Api::_()->user()->getViewer();

        $this->view->package = Engine_Api::_()->getItem('siteeventpaid_package', $id);

        //WIDGET SETTINGS ARRAY - INFO ARRAY WHICH IS TO BE SHOWN IN PACKAGE DETAILS.
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->packageInfoArray = $settings->getSetting('siteevent.package.information', array("price", "billing_cycle", "duration", "featured", "sponsored", "rich_overview", "videos", "photos", "description", "ticket_type"));
    }

    //ACTION FOR PACKAGE UPDATION
    public function updatePackageAction() {

        //PACKAGE ENABLE VALIDATION
        if (!Engine_Api::_()->siteevent()->hasPackageEnable()) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //GET EVENT ID EVENT OBJECT AND THEN CHECK VALIDATIONS
        $this->view->event_id = $event_id = $this->_getParam('event_id');
        if (empty($event_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (empty($siteevent)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }

        $this->view->package_view = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.package.view', 1);
        $this->view->overview = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 0);
        $this->view->package_description = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.package.description', 0);

        //WIDGET SETTINGS ARRAY - INFO ARRAY WHICH IS TO BE SHOWN IN PACKAGE DETAILS.
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->packageInfoArray = $settings->getSetting('siteevent.package.information', array("price", "billing_cycle", "duration", "featured", "sponsored", "rich_overview", "videos", "photos", "description", "ticket_type"));

        $this->view->viewer = Engine_Api::_()->user()->getViewer();
        $this->view->TabActive = "package";
        $this->view->siteevents_view_menu = 16;

        $this->view->show_editor = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.tinymceditor', 0);

        $this->view->package = Engine_Api::_()->getItem('siteeventpaid_package', $siteevent->package_id);
        $paginator = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getPackageResult($siteevent);
        $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page'));

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("siteevent_main");
        $this->view->is_ajax = $this->_getParam('is_ajax', '');
    }

    //ACTION FOR PACKAGE UPGRADE CONFIRMATION
    public function updateConfirmationAction() {

        //PACKAGE ENABLE VALIDATION
        if (!Engine_Api::_()->siteevent()->hasPackageEnable()) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //GET EVENT ID, EVENT OBJECT AND THEN CHECK VALIDATIONS
        $this->view->event_id = $event_id = $this->_getParam('event_id');
        if (empty($event_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (empty($siteevent)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->package_id = $this->_getParam('package_id');
        $package_chnage = Engine_Api::_()->getItem('siteeventpaid_package', $this->view->package_id);

        if (empty($package_chnage) || !$package_chnage->enabled || (!empty($package_chnage->level_id) && !in_array($siteevent->getOwner()->level_id, explode(",", $package_chnage->level_id)))) {
            return $this->_forward('notfound', 'error', 'core');
        }

        if ($this->getRequest()->getPost()) {

            if (!empty($_POST['package_id'])) {
                $table = $siteevent->getTable();
                $db = $table->getAdapter();
                $db->beginTransaction();

                try {
                    $is_upgrade_package = true;

                    //APPLIED CHECKS BECAUSE CANCEL SHOULD NOT BE CALLED IF ALREADY CANCELLED 
                    if ($siteevent->status == 'active')
                        $siteevent->cancel($is_upgrade_package);

                    $siteevent->package_id = $_POST['package_id'];
                    $package = Engine_Api::_()->getItem('siteeventpaid_package', $siteevent->package_id);

                    $siteevent->featured = $package->featured;
                    $siteevent->sponsored = $package->sponsored;
                    $siteevent->pending = 1;
                    $siteevent->expiration_date = new Zend_Db_Expr('NULL');
                    $siteevent->status = 'initial';
                    if (($package->isFree())) {
                        $siteevent->approved = $package->approved;
                    } else {
                        $siteevent->approved = 0;
                    }

                    if (!empty($siteevent->approved)) {
                        $siteevent->pending = 0;
                        $expirationDate = $package->getExpirationDate();
                        if (!empty($expirationDate))
                            $siteevent->expiration_date = date('Y-m-d H:i:s', $expirationDate);
                        else
                            $siteevent->expiration_date = '2250-01-01 00:00:00';

                        if (empty($siteevent->approved_date)) {
                            $siteevent->approved_date = date('Y-m-d H:i:s');
                            if ($siteevent->draft == 0 && $siteevent->search && time() >= strtotime($siteevent->creation_date)) {
                                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($siteevent->getOwner(), $siteevent, 'siteevent_new');
                                if ($action != null) {
                                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $siteevent);
                                }
                            }
                        }
                    }
                    $siteevent->save();
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'format' => 'smoothbox',
                'parentRedirect' => $this->view->url(array('action' => 'update-package', 'event_id' => $siteevent->event_id), "siteevent_package", true),
                'parentRedirectTime' => 15,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The package for your Event has been successfully changed.'))
            ));
        }
    }

    //ACTION FOR PACKAGE PAYMENT
    public function paymentAction() {

        //PACKAGE ENABLE VALIDATION
        if (!Engine_Api::_()->siteevent()->hasPackageEnable()) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->show_editor = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.tinymceditor', 0);

        //GET EVENT ID, EVENT OBJECT AND THEN CHECK VALIDATIONS
        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode'))
            $event_id = $_POST['event_id_session'];
        else
            $event_id = $this->_getParam('event_id');

        if (empty($event_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (empty($siteevent)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $package = Engine_Api::_()->getItem('siteeventpaid_package', $siteevent->package_id);

        if ((!$package->isFree())) {
            $session = new Zend_Session_Namespace('Payment_Siteevent');
            $session->event_id = $event_id;

            return $this->_helper->redirector->gotoRoute(array(), "siteevent_payment", true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "siteevent_general", true);
        }
    }

    //ACTION FOR PACKAGE CANCEL
    public function cancelAction() {

        //PACKAGE ENABLE VALIDATION
        if (!Engine_Api::_()->siteevent()->hasPackageEnable()) {
            return $this->_forward('notfound', 'error', 'core');
        }

        if (!($package_id = $this->_getParam('package_id')) ||
                !($package = Engine_Api::_()->getItem('siteeventpaid_package', $package_id))) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'index', 'package_id' => null));
        }

        $this->view->package_id = $package_id;
        $event_id = $this->_getParam('event_id');

        $this->view->form = $form = new Siteeventpaid_Form_Package_Cancel();

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Try to cancel
        $this->view->form = null;
        try {
            Engine_Api::_()->getItem('siteevent_event', $event_id)->cancel();
            $this->view->status = true;
        } catch (Exception $e) {
            $this->view->status = false;
            $this->view->error = $e->getMessage();
        }
    }

}

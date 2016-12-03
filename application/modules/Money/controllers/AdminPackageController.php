<?php
/**
 * SocialEnginePro
 *
 * @category   Application_Extensions
 * @package    E-money
 * @author     Azim
 */

/**
 * @category   Application_Extensions
 * @package    E-money
 */
class Money_AdminPackageController extends Core_Controller_Action_Admin
{
    public function indexAction() {
        // Test curl support
        if (!function_exists('curl_version') ||
                !($info = curl_version())) {
            $this->view->error = $this->view->translate('The PHP extension cURL ' .
                    'does not appear to be installed, which is required ' .
                    'for interaction with payment gateways. Please contact your ' .
                    'hosting provider.');
        }
        // Test curl ssl support
        else if (!($info['features'] & CURL_VERSION_SSL) ||
                !in_array('https', $info['protocols'])) {
            $this->view->error = $this->view->translate('The installed version of ' .
                    'the cURL PHP extension does not support HTTPS, which is required ' .
                    'for interaction with payment gateways. Please contact your ' .
                    'hosting provider.');
        }
        // Check for enabled payment gateways
        else if (Engine_Api::_()->getDbtable('gateways', 'money')->getEnabledGatewayCount() <= 0) {
            $this->view->error = $this->view->translate('There are currently no ' .
                    'enabled payment gateways. You must %1$sadd one%2$s before this ' .
                    'page is available.', '<a href="' .
                    $this->view->escape($this->view->url(array('controller' => 'gateway'))) .
                    '">', '</a>');
        }

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('money_admin_main', array(), 'money_admin_main_plans');

        // Initialize select
        $table = Engine_Api::_()->getDbtable('packages', 'money');
        $select = $table->select();



        // Make paginator
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    }

    public function createAction() {
        // Make form
        $this->view->form = $form = new Money_Form_Admin_Package_Create();

        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }


        // Process
        $values = $form->getValues();



        $packageTable = Engine_Api::_()->getDbtable('packages', 'money');
        $db = $packageTable->getAdapter();
        $db->beginTransaction();

        try {



            // Create package
            $package = $packageTable->createRow();
            $package->setFromArray($values);
            $package->save();


            $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'money');
            foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
                $gatewayPlugin = $gateway->getGateway();
                // Check billing cycle support



                if (method_exists($gatewayPlugin, 'createProduct')) {
                    $gatewayPlugin->createProduct($package->getGatewayParams());
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // Redirect
        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    public function editAction() {
        // Get package
        if (null === ($packageIdentity = $this->_getParam('package_id')) ||
                !($package = Engine_Api::_()->getDbtable('packages', 'money')->find($packageIdentity)->current())) {
            throw new Engine_Exception('No package found');
        }

        // Make form
        $this->view->form = $form = new Money_Form_Admin_Package_Edit();

        // Populate form
        $values = $package->toArray();



        $otherValues = array(
            'price' => $values['price'],
        );

        $form->populate($values);



        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Hack em up
        $form->populate($otherValues);

        // Process
        $values = $form->getValues();


        unset($values['price']);
        

        


        $packageTable = Engine_Api::_()->getDbtable('packages', 'money');
        $db = $packageTable->getAdapter();
        $db->beginTransaction();

        try {


            // Update package
            $package->setFromArray($values);
            $package->save();

            

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $form->addNotice('Your changes have been saved.');
    }

    public function deleteAction() {
        
        // Get package
        if (null === ($packageIdentity = $this->_getParam('package_id')) ||
                !($package = Engine_Api::_()->getDbtable('packages', 'money')->find($packageIdentity)->current())) {
            throw new Engine_Exception('No package found');
        }



        $this->view->form = $form = new Money_Form_Admin_Package_Delete();



        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }



        // Process

        $packageTable = Engine_Api::_()->getDbtable('packages', 'payment');
        $db = $packageTable->getAdapter();
        $db->beginTransaction();

        try {

            // Delete package in gateways?
            $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'money');
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

        $this->view->status = true;
    }

}
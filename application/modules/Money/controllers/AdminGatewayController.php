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
class Money_AdminGatewayController extends Core_Controller_Action_Admin {

    public function gatewayAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('money_admin_main', array(), 'money_admin_main_gateway');

        $table = Engine_Api::_()->getDbtable('gateways', 'money');
        $select = $table->select();

        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    }

    public function editAction() {
        $gateway = Engine_Api::_()->getDbtable('gateways', 'money')->find($this->_getParam('gateway_id'))->current();
        $this->view->form = $form = $gateway->getPlugin()->getAdminGatewayForm();
      
        // Populate form
        $form->populate($gateway->toArray());
        if (is_array($gateway->config)) {
            $form->populate($gateway->config);
        }
        
        // Check method/valid
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }


        // Process
        $values = $form->getValues();
        
        $enabled = (bool) $values['enabled'];
        //$testMode = !empty($values['test_mode']);
        unset($values['enabled']);
        //unset($values['test_mode']);
        // Validate gateway config
        if ($enabled) {
            $gatewayObject = $gateway->getGateway();

            try {
             
                $gatewayObject->setConfig($values);
                $response = $gatewayObject->test();
            } catch (Exception $e) {
                $enabled = false;
                $form->populate(array('enabled' => false));
                $form->addError(sprintf('Gateway login failed. Please double check ' .
                                'your connection information. The gateway has been disabled. ' .
                                'The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
            }
        } else {
            $form->addError('Gateway is currently disabled.');
        }

        // Process
        $message = null;
        try {
            $values = $gateway->getPlugin()->processAdminGatewayForm($values);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $values = null;
        }

        if (null !== $values) {
            $gateway->setFromArray(array(
                'enabled' => $enabled,
                'config' => $values,
            ));
            
            $gateway->save();

            $form->addNotice('Changes saved.');
        } else {
            $form->addError($message);
        }
    }

}
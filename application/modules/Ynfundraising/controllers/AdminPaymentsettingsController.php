<?php

/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Fundraising
 * @copyright  Copyright 2013 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminPaymentsettingsController.php
 */
class Ynfundraising_AdminPaymentsettingsController extends Core_Controller_Action_Admin
{

    protected $_paginate_params = array();

    public function init ()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('ynfundraising_admin_main', array(), 'ynfundraising_admin_main_paymentsettings');
    }

    public function indexAction ()
    {
        $select = Engine_Api::_()->getDbtable('gateways', 'ynfundraising')->select();
        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->paginator = $paginator;
    }

    public function editAction ()
    {
        $this->_helper->layout->setLayout ( 'admin-simple' );
        $this->view->form = $form = new Ynfundraising_Form_Admin_Paymentsettings();
        $params = $this->getRequest()->getParams();

        $gateway = Engine_Api::_ ()->getItem ( 'ynfundraising_gateway', $params['gateway_id'] );
        // Generate and assign form
        $gateway_settings = Zend_Json::decode($gateway['params']);

        $form -> populate($gateway_settings);
        //Check post method
        if($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
            $gateway->is_active = 1;
            $gateway->params = Zend_Json::encode($params);
            $gateway->save();
            $this->_forward ( 'success', 'utility', 'core', array (
                    'smoothboxClose' => true,
                    'parentRefresh' => true,
                    'format' => 'smoothbox',
                    'messages' => array (
                            $this->view->translate ( 'Save successfully.' )
                    )
            ) );
        }

    }
}
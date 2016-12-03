<?php

class Money_Form_Admin_Gateway_Webmoney extends Money_Form_Admin_Gateway_Abstract {

    public function init() {
        parent::init();

        $this->setTitle('Money Gateway: Webmoney');

        //$description = $this->getTranslator()->translate('PAYMENT_FORM_ADMIN_GATEWAY_PAYPAL_DESCRIPTION');
//        $this->setDescription('Result URL: http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
//                    'module' => 'money',
//                    'controller' => 'subscription',
//                    'action' => 'return'
//                        ), 'default', true) .
//                '<br>Success URL: http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
//                    'module' => 'money',
//                    'controller' => 'subscription',
//                    'action' => 'return',
//                    'state'=>'success'
//                        ), 'default', true) .
//                '<br>Fail URL: http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
//                    'module' => 'money',
//                    'controller' => 'subscription',
//                    'action' => 'return'
//                        ), 'default', true) );

        // Decorators
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Text', 'wmid', array(
            'label' => 'WMID',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
            'allowEmpty' => false,
            'required' => true,
        ));

        $this->addElement('Text', 'purse', array(
            'label' => 'Purse',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
            'allowEmpty' => false,
            'required' => true,
        ));

        $this->addElement('Text', 'secretkey', array(
            'label' => 'Secret Key',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));

    }

}
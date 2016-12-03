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
class Money_Form_Recharge extends Engine_Form
{

    public function init() {
        $this
                ->setTitle('Add funds to your account')
                ->setDescription('_MONEY_FORM_DESCRIPTION');

        $this->setAttrib('enctype', 'multipart/form-data')
                ->setAttrib('id', 'form-upload')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));



        $options = array('' => '');
        $table = Engine_Api::_()->getDbtable('gateways', 'money');
        $gateway = $table->getEnabledGateways();

        foreach ($gateway as $value) {
            $options[$value['gateway_id']] = $value['title'];
        }

        $this->addElement('Select', 'gateway', array(
            'label' => 'Method of payment',
            'required' => true,
            'allowEmpty' => false,
            'multioptions' => $options,
            'onchange' => 'updateRadioButton(this)'
        ));

        $package = Engine_Api::_()->getDbtable('packages', 'money')->getEnabledPackage();

        $options = array(0 => 'others');
        foreach ($package as $option) {
            $options[$option['package_id']] = $option['price'];
        }
        
        
        //sort($options);
        $this->addElement('Radio', 'plan', array(
            'label' => 'Plan',
            'required' => true,
            'allowEmpty' => false,
            'multioptions' => $options,
            'onchange' => "updateTextFields(this)",
        ));
 
        $this->addElement('Text', 'amount', array(
            'label' => 'Amount',
            'allowEmpty' => false,
            'validators' => array(
                new Engine_Validate_Callback(array($this, 'validateAmount')),
            ),
            'onchange' => "commission(this.value)"
        ));


        $this->addElement('Button', 'upload', array(
            'label' => 'Send',
            'type' => 'submit',
            'onclick' => 'saveForm(this)',
            'order' => '99'
        ));
    }

    public function validateAmount($value) {

        if ($this->getValue('plan') == 0) {
            if ($value == '') {
                $this->amount->getValidator('Engine_Validate_Callback')->setMessage('Please enter numeric.');
                return false;
            }
        }
        if (!is_numeric($value) && $value != '') {
            $this->amount->getValidator('Engine_Validate_Callback')->setMessage('Please enter numeric.');
            return false;
        }
        if ($value <= 0 && $value != '') {
            $this->amount->getValidator('Engine_Validate_Callback')->setMessage('Big 0.');
            return false;
        }
        return true;
    }
}
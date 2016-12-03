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
class Money_Form_Admin_Search extends Engine_Form
{
    public function init()
    {
        $this
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

        $this
            ->setAttribs(array(
            'id' => 'filter_form',
            'class' => 'global_form_box',
        ))
            ->setMethod('GET');
        $this->addElement('Text', 'text', array(
            'label' => 'User Name'
        ));

        $this->addElement('Select', 'type', array(
            'label' => 'Type',
            'multioptions' => array(
                '0' => '',
                '1' => 'add funds paypal',
                '2' => 'add funds webmoney',
                '3' => 'add funds 2checkout',
                '4' => 'withdraw paypal',
                '5' => 'withdraw web money',
                '6' => 'send friend',
                '7' => 'received  friend',
            )
        ));

        $table = Engine_Api::_()->getDbtable('gateways', 'money');
        $gateway = $table->getEnabledGateways();

        $option = array(0 => '');

        foreach ($gateway as $value) {
            $option[$value['gateway_id']] = $value['title'];
        }

        $this->addelement('Select', 'gateway', array(
            'Label' => 'Gateway',
            'multioptions' => $option
        ));

        $this->addElement('button', 'submit', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true
        ));
    }
}
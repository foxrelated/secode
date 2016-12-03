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
class Money_Form_Admin_Edit extends Engine_Form
{
    public function init()
    {

        $this->setTitle('Edit');

        $this->addElement('text', 'money', array(
            'label' => 'money'
        ));

        $this->addElement('Button', 'execute', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'decorators' => array('ViewHelper'),
            'order' => 10001,
            'ignore' => true,
        ));

        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'prependText' => ' or ',
            'link' => true,

            'decorators' => array('ViewHelper'),
            'order' => 10002,
            'ignore' => true,
        ));

        // DisplayGroup: buttons
        $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
            'order' => 10003,
        ));
    }

}
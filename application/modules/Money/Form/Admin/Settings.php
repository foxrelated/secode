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
class Money_Form_Admin_Settings extends Engine_Form
{
    public function init()
    {
        $this->setTitle('Global Settings');

        $this->addElement('Select', 'money_currency', array(
            'label' => 'Pay Currency',
            'value' => 'USD',
            'description' => '-',
        ));
        $this->getElement('money_currency')->getDecorator('Description')->setOption('placement', 'APPEND');

        $this->addElement('Select', 'money_site_currency', array(
            'label' => 'Site Currency',
            'value' => 'USD',
            'description' => 'Site Currency',
        ));
        $this->getElement('money_site_currency')->getDecorator('Description')->setOption('placement', 'APPEND');

        $this->addElement('Text', 'money_commission', array(
            'Label' => 'Commission recharge',
            'description' => 'Commission recharge',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('money.commission', 0.8),
        ));

        $this->addElement('Text', 'money_commissionissue', array(
            'Label' => 'Commission issue',
            'description' => 'Commission issue',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('money.commissionissue', 0.8),
        ));

        $this->addElement('Text', 'money_conversion', array(
            'Label' => 'Conversion',
            'description' => 'Pay Currency - Site Currency ',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('money.conversion', 1),
        ));

        $this->addElement('Text', 'money_page', array(
            'label' => 'Listings Per Page',
            'description' => 'How many contents will be shown per page? (Enter a number between 1 and 999)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('money.page', 30),
        ));

        $this->addElement('Button', 'execute', array(
            'label' => 'Save Changes',
            'type' => 'submit',
        ));
    }

}
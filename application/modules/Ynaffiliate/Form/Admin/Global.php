<?php

class Ynaffiliate_Form_Admin_Global extends Engine_Form {

    public function init() {

        $this->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');

        $currency = Engine_Api::_()->getApi('settings', 'core')->payment['currency'];
        $this->addElement('Radio', 'ynaffiliate_mode', array(
            'label' => '*Enable Test Mode?',
            'description' => 'Allow admin to test Store by using development mode?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.mode', 1),
        ));
        $this->addElement('radio', 'ynaffiliate_autoapprove', array(
            'label' => 'Auto Approve',
            'description' => 'Do you want to approve member automatically?',
            'required' => true,
            'multiOptions' => array(
                '1' => 'Yes ',
                '0' => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.autoapprove', 1)
        ));

        // add js radio
        $this->addElement('radio', 'ynaffiliate_invitation', array(
            'label' => ' Intergration with invitation',
            'description' => 'Do you want to intergrate with invitations?',
            'required' => true,
            'multiOptions' => array(
                '1' => 'Yes ',
                '0' => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.invitation', 1),
        ));

        $this->addElement('Text', 'ynaffiliate_max_commission_level', array(
            'label' => 'Number of Commission Levels',
            'title' => 'Number of Commission Levels',
            'description' => '',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.max.commission.level', 5),
        ));

        $this->addElement('Text', 'ynaffiliate_client_limit', array(
            'label' => 'Number of users per level on Network Clients page',
            'title' => 'Number of users per level on Network Clients page',
            'description' => '',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.client.limit', 3),
        ));

        $this->addElement('Text', 'ynaffiliate_minrequest', array(
            'label' => 'Minimum Request Points',
            'title' => 'Minimum Request Points',
            'description' => '',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.minrequest', 100),
        ));

        $this->addElement('Text', 'ynaffiliate_maxrequest', array(
            'label' => 'Maximum Request Points',
            'title' => 'Maximum Request Points',
            'description' => '',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.maxrequest', 1000),
        ));

        $descText = Zend_Registry::get('Zend_View')->translate('Please fill in the point convert rate for 1 %s', $currency);

        $this->addElement('Text', 'ynaffiliate_pointrate', array(
            'label' => 'Convert Rate',
            'allowEmpty' => false,
            'description' => $descText,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Float', true),
            ),
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.pointrate', 1),
        ));

        $this->addElement('Text', 'ynaffiliate_delay', array(
            'label' => 'Delay time for refunds and disputes (days)',
            'title' => 'Delay time for refunds and disputes (days)',
            'description' => 'Each commission of new transaction will have a delay time to allow for refunds and disputes.',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.delay', 30),
        ));

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $baseUrl = sprintf('%s://%s', $request->getScheme(), $request->getHttpHost());
        $this->addElement('Text', 'ynaffiliate_baseUrl', array(
            'label' => 'Base URL',
            'description' => 'Base URL for Dynamic Links',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),

            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.baseUrl', $baseUrl),
        ));

        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
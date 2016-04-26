<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: NotificationSettings.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitegroupmember_Form_NotificationSettings extends Engine_Form {

  public function init() {

      $this->setTitle('Notification Settings')
					->setDescription('What do you want to get notified about?')
					->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
					//->setAttrib('name', 'notification');
					
		$this->addElement('Checkbox', 'email', array(
				'description' => 'Email Notifications',
				'label' => 'Send email notifications to me when people post an update, or create various content on this Group.',
				'onclick' => 'showEmailAction()',
				'value' => 0,
		));
		

    $this->addElement('Radio', 'emailposted', array(
			//'label' => 'People post updates on this group',
			'description' => 'People post updates on this group',
			'multiOptions' => array(
				'1' => 'All People',
				'2' => 'Only my friends',
				'0' => 'Turn off',
			),
			'value' => 0,
    ));
		
		
		$this->addElement('Radio', 'emailcreated', array(
			//'label' => 'People post updates on this group',
			'description' => 'People create various contents on this group',
			'multiOptions' => array(
				'1' => 'All People',
				'2' => 'Only my friends',
				'0' => 'Turn off',
			),
			'value' => 0,
    ));

	  $this->addElement('Checkbox', 'notification', array(
			'description' => 'Notification settings',
			'label' => 'Send notifications to me when people post an update, or create various content on this Group.',
			'onclick' => 'showNotificationAction()',
			'value' => 0,
		));
		
		
		$this->addElement('Radio', 'notificationposted', array(
			//'label' => 'People post updates on this group',
			'description' => 'People post updates on this group',
			'multiOptions' => array(
				'1' => 'All People',
				'2' => 'Only my friends',
				'0' => 'Turn off',
			),
			'value' => 0,
    ));
    
		$this->addElement('Radio', 'notificationcreated', array(
			//'label' => 'People post updates on this group',
			'description' => 'People create various contents on this group',
			'multiOptions' => array(
				'1' => 'All People',
				'2' => 'Only my friends',
				'0' => 'Turn off',
			),
			'value' => 0,
    ));
		
    $this->addElement('Radio', 'notificationfollow', array(
			//'label' => 'People post updates on this group',
			'description' => 'People follow this group',
			'multiOptions' => array(
				'1' => 'All People',
				'2' => 'Only my friends',
				'0' => 'Turn off',
			),
			'value' => 0,
    ));
		
		
				
    $this->addElement('Radio', 'notificationlike', array(
			//'label' => 'People post updates on this group',
			'description' => 'People like this group',
			'multiOptions' => array(
				'1' => 'All People',
				'2' => 'Only my friends',
				'0' => 'Turn off',
			),
			'value' => 0,
    ));
		
		$this->addElement('Radio', 'notificationcomment', array(
			//'label' => 'People post updates on this group',
			'description' => 'People comment this group',
			'multiOptions' => array(
				'1' => 'All People',
				'2' => 'Only my friends',
				'0' => 'Turn off',
			),
			'value' => 0,
    ));

		$this->addElement('Radio', 'notificationjoin', array(
			'description' => 'People Join this group',
			'multiOptions' => array(
				'1' => 'All People',
				'2' => 'Only my friends',
				'0' => 'Turn off',
			),
			'value' => 0,
    ));
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addDisplayGroup(array(
      'submit',
      'cancel'
    ), 'buttons');

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))->setMethod('POST');
    
  }
}
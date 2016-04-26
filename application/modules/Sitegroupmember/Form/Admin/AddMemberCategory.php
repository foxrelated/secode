<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddMemberCategory.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitegroupmember_Form_Admin_AddMemberCategory extends Engine_Form {

  protected $_field;

  public function init() {
  
    $this->setMethod('post');

	  $this->addElement('Text', 'category_name', array(
			'label' => 'Member Role',
			'allowEmpty' => false,
			'required' => true,
			'description' => 'Enter the member role name.',
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Add Member Role',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: JoinedMoreGroups.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitegroupmember_Form_JoinedMoreGroups extends Engine_Form {

  protected $_field;

  public function init() {
  
    $this->setMethod('post');
    $this->setTitle('Joined More Groups')
        ->setDescription('Joined More Groups');

    $label = new Zend_Form_Element_Text('title');
    $label->setLabel('Group Name')
					->addValidator('NotEmpty')
					->setRequired(true)
					->setAttrib('class', 'text')
					->setAttrib('style', 'width:300px;');

    // init to
    $this->addElement('Hidden', 'group_id', array());

    $this->addElements(array(
        $label,
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Join Group',
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
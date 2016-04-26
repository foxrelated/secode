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
    $this->setTitle('Join More Groups');
        //->setDescription('Enter the name of the group below.');

    $label = new Zend_Form_Element_Text('title');
    $label->setLabel('Enter the name of the group which you want to join.')
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
    
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
			$URL = $view->url(array('action'=>'index'), 'sitegroup_packages');
		} else {
			$URL = $view->url(array('action'=>'create'), 'sitegroup_general');
		}
		
		$click = Zend_Registry::get('Zend_Translate')->_("<a href='" . $URL ."' class='buttonlink sitegroup_quick_create' target='_parent'>Click here</a>");
		
    $customBlocks = sprintf(Zend_Registry::get('Zend_Translate')->_("%s to create a new group."), $click);

		
    $this->addElement('Dummy', 'new_group', array(
      'description' => $customBlocks,
    ));
    $this->getElement('new_group')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

  }
}
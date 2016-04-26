<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Signup.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Suggestion_Form_Admin_Signup_Invite extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttrib('enctype', 'multipart/form-data');

    $step_table = Engine_Api::_()->getDbtable('signup', 'user');
    $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'Suggestion_Plugin_Signup_Invite'));
    $count = $step_row->order;
    $title = $this->getView()->translate('Step %d: Import & Invite Friends (Suggestions Plugin)', $count);
    $this->setTitle($title)->setDisableTranslator(true);


    $enable = new Engine_Form_Element_Radio('enable');
    $enable->setLabel("Import Contacts & Invite Friends");
    $enable->setDescription("Do you want members to invite their friends during the signup process? (They will be able to import contacts from multiple services and also add email addresses manually.)");
    $enable->addMultiOptions(
      array(
        1=>'Yes, include the "Invite Friends" step during signup.',
        0=>'No, do not include this step.'
    ));
    $enable->setValue($step_row->enable);

    $this->addElements(array($enable));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));

  }
}
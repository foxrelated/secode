<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Addicon.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Form_Admin_Settings_Addicon extends Engine_Form
{
  public function init()
  {
		$this
		->setTitle('Add Icon');

		$this->addElement('File', 'photo', array(
			'label' => 'Upload the icon. The recommended dimension for the icon of categories is: 24 x 24 pixels and of sub-categories and 3rd level categories is: 16 x 16 pixels.',
			'allowEmpty' => false,
			'required' => true,
		));
		$this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg,JPG,PNG,GIF,JPEG');

    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
			'onclick' => 'javascript:parent.Smoothbox.close()',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
	}
}
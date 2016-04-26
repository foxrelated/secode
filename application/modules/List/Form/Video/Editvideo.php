<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Editvideo.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Form_Video_Editvideo extends Engine_Form
{
  public function init()
  {
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
   
    $this->addElement('Button', 'button', array(
      'label' => 'Save Changes',
      'type' => 'submit',
    ));
  }
}
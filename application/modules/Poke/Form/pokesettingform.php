<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: connectionsettingform.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Poke_Form_pokesettingform extends Engine_Form
{
  public function init()
  {$this
    ->setTitle('My Poke Settings')
    ->setDescription('Do you want to allow other users to poke you?');
    // Get current user id.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    // Check id in "userconnection table"
    $connection_value = Engine_Api::_()->getItem('poke_setting', $user_id);
    if(empty($connection_value)) {
    	$connection_value = 0;
    }
    else {
    	$connection_value = 1;
    }
    $this->addElement('Radio', 'connection', array(
      'value' => $connection_value,
      'multiOptions' => array(
        0 => 'Yes, allow others to poke me.',
        1 => 'No, do not allow others to poke me.'
      )
    ));
    
     $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
    ));
  }
}
?>
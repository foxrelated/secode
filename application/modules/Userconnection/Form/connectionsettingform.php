<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: connectionsettingform.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Userconnection_Form_connectionsettingform extends Engine_Form
{
  public function init()
  {$this
    ->setTitle('My Connection Settings')
    ->setDescription('Enable or Disable your visibility in connection paths.');
    // Get current user id.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    // Check id in "userconnection table"
    $connection_value = Engine_Api::_()->getItem('userconnection', $user_id);
    if(empty($connection_value)) {
    	$connection_value = 0;
    }
    else {
    	$connection_value = 1;
    }
    $this->addElement('Radio', 'connection', array(
      'value' => $connection_value,
      'multiOptions' => array(
        0 => 'Yes, show me in Connection Paths.',
        1 => 'No, hide me in Connection Paths.'
      )
    ));
    
     $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
    ));
  }
}
?>
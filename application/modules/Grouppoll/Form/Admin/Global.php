<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2010-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
    ->setTitle('Global Settings')
    ->setDescription('These settings affect all members in your community.');

		$this->addElement('Text', 'grouppoll_maxoptions', array(
      'label' => 'Maximum Options',
      'description' => 'How many possible poll answers do you want to  allow in poll creation ?',
      'required' => true,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.maxoptions', 4),
    ));

   $this->addElement('Text', 'grouppoll_title_turncation', array(
      'label' => 'Title Truncation Limit',
      'description' => 'What maximum limit should be applied to the number of characters in the titles of items in the widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
      'required' => true,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.title.turncation', 16),

    ));

    $this->addElement('Radio', 'grouppoll_canchangevote', array(
      'label' => 'Change Vote?',
      'description' => 'Do you want to permit your members to change their vote?',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
      ),
      'value' => (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.canchangevote', false),
    ));
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
?>
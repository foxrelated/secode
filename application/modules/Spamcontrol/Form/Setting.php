<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Global.php 8798 2011-04-06 00:43:24Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Spamcontrol_Form_Setting extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ;
    
    $this->addElement('Text', 'spamcontrol_lengthname', array(
        'label' => 'How many numbers allowed in username?',
        'description' =>'Depending on the setting, will show the potential spammer.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('spamcontrol.lengthname', 5),
    ));
    
    $this->addElement('Text', 'spamcontrol_lengthemail',    array(
        'label' => 'How many numbers allowed in email?',
        'description' =>'Depending on the setting, will show the potential spammer.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('spamcontrol.lengthemail', 5),
    ));
    
    $this->addElement('Text', 'spamcontrol_page', array(
      'label' => 'Listings Per Page',
      'description' => 'How many contents will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('spamcontrol.page', 30),
    ));

   
    
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Global.php 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Shoutbox_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('Settings for your Shoutbox Plugin.');


    $this->addElement('Text', 'shoutbox_shouts', array(
      'label' => 'Visible shouts',
      'description' => 'How many shouts will be shown in widget?',
      'required' => true,
      'value' => 10
    ));
    
    $this->addElement('Radio', 'shoutbox_autorefresh', array(
      'label' => 'Autorefresh?',
      'required' => true,
      'description' => 'The application connects to your server (using AJAX) every 5 seconds to check if there are any new shouts to display.',
      'multiOptions' => array(
        1 => 'Enable',
        0 => 'Disable'
      ),
      'value' => 1
    ));
    
    $this->addElement('Select', 'shoutbox_timer', array(
      'label' => 'Update Frequency',
      'description' => 'Set timer for autorefresh in seconds. Default is 5 seconds',
      'value' => 5000,
      'multiOptions' => array(
        1000   => '1 second',
        2000   => '2 seconds',
        3000   => '3 seconds',
        4000   => '4 seconds',
        5000   => '5 seconds',
        10000  => '10 seconds',
        15000  => '15 seconds',
        20000  => '20 seconds',
        25000  => '25 seconds',
        30000  => '30 seconds',
        45000  => '45 seconds',
        60000  => '1 minute',
        120000 => "2 minutes"
      )
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}
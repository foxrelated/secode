<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Level.php 7486 2010-09-28 03:00:23Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Shoutbox_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below');

    // Element: view
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Members to view Shoutbox widget?',
      'description' => 'Do you want to let members to view shoutbox widget?',
      'multiOptions' => array(
        1 => 'Yes, allow members to view shoutbox widget.',
        0 => 'No, do not allow members to view shoutbox widget.'
      ),
      'value' => 1,
    ));
    
    // Element: create
    $this->addElement('Radio', 'create', array(
      'label' => 'Allow Members to write shouts?',
      'description' => 'Do you want to let members to write shouts?',
      'multiOptions' => array(
        1 => 'Yes, allow members to write shouts.',
        0 => 'No, do not allow members to write shouts.'
      ),
      'value' => 1,
    ));

  }
}
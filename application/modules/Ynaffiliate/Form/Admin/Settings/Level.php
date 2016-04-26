<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Level.php 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynaffiliate_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("YNAFFILIATE_FORM_ADMIN_LEVEL_DESCRIPTION");

    // Element: view
//    $this->addElement('Radio', 'auto_approve', array(
//      'label' => 'Auto approve',
//      'description' => 'Do you want to approve member automatically?',
//      'multiOptions' => array(
//        1 => 'Yes',
//        0 => 'No',
//      ),
//     
//    ));
    }
  
}
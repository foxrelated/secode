<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Global.php 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynidea_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'idea_page', array(
      'label' => 'Entries Per Page',
      'description' => 'How many idea entries will be shown per page? (Enter a number between 1 and 100)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('idea.page', 10),
      'validators'=>array(
	     	'Int',
		 	array('Between',true,array('min'=>1,'max'=>100))
		 ),
    ));
    $this->addElement('Text','idea_sitelogo',array(
        'label'=>'Logo URL',
        'description'=>'Site logo for PDF idea',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('idea.sitelogo', ''),
    ));
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
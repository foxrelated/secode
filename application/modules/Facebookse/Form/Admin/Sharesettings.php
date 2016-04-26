<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Sharesettings.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Facebookse_Form_Admin_Sharesettings extends Engine_Form
{
  public function init()
  {
  	
    $this
      ->setTitle('Share Button Settings')
      ->setDescription('Select the settings for the Share button on the main pages of the various content types.');

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));
    
    
  }

  public function show_shares ($pagelevel_id) {
  //GETTING ALL PLUGINS FOR SETTING UP FACEBOOK LIKE AND SHARE BUTTON BY ADMIN
  $plugin = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    
	$share_array1=  array(0 => 'No', 1 => 'Yes');
	    
    foreach ($plugin as $plugins){
			if ($plugins == 'blog' || $plugins == 'album' || $plugins == 'classified' || $plugins == 'forum' || $plugins == 'poll' || $plugins == 'video' || $plugins == 'music' || $plugins == 'group' || $plugins == 'event') {
				$plugins_array[$plugins]= ucfirst($plugins);
				
			}
    }

	 $plugins_array['user_profile']= 'User Profile';
   //$plugins_array['home'] = 'Home Page';
  asort($plugins_array);
	array_unshift($plugins_array, "Select");
	$this->addElement('Select', 'pagelevel_id', array(
      'label' => 'Content Type',
      'multiOptions' => $plugins_array,
      'onchange' => 'javascript:fetchShareSettings(this.value);',
      'ignore' => true
    ));

	if (!empty($pagelevel_id) && ($pagelevel_id == 'blog' || $pagelevel_id == 'album' || $pagelevel_id == 'classified' || $pagelevel_id == 'forum' || $pagelevel_id == 'poll' || $pagelevel_id == 'video' || $pagelevel_id == 'music' || $pagelevel_id == 'group' || $pagelevel_id == 'event' || $pagelevel_id == 'user_profile')) {
    $this->addElement('Select', $pagelevel_id . '_1', array(
			'label' => 'Show Share Button',
			'multiOptions' => $share_array1,
			'value' => 1
		));
		$this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'ignore' => true
    ));

		//MAKING A SUBFORM FOR GENERATING THE FACEBOOK LIKE BUTTON CODE.
		//$url = Zend_Registry::get('Zend_Translate')->_('URL to Like <a target="_blank" href="#" id="url_help">(?)</a>');
		$this->addElement('Text', 'share_url', array(
				'label' => 'Url to Share',
				'description' => '',
				'value' =>'',
			));

		$this->addElement('Select', 'share_type', array(
			'label' => 'Type ',
			'multiOptions' => array('' => '', 'box_count' => 'box_count', 'button_count' => 'button_count', 'button' => 'button', 'icon' => 'icon', 'icon_link' => 'icon_link', )
		));

		$this->addElement('Button', 'Get_Code', array(
			'label' => 'Get Code',
			'type' => 'button',
			'onclick' => 'javascript:get_code();',
			'ignore' => true
		));
	 }
  }
}

?>
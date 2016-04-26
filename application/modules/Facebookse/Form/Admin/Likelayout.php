<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Likelayout.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Facebookse_Form_Admin_Likelayout extends Engine_Form
{
  public function init()
  {
  	$this
      ->setTitle('Facebook Like Button Configurator')
      ->setDescription("Below, you can generate code for Facebook Like Button to be placed at any URL page of your choice.")
      ->setAttrib('id', 'likebutton_config');
  }
  
  public function show_likes ($pagelevel_id) {
		
    $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
      if (!empty($enable_fboldversion)) {
        $socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('socialdna');
        $socialdnaversion = $socialdnamodule->version;
        if ($socialdnaversion >= '4.1.1') {
          $enable_fboldversion = 0;
        }
      }
      if (empty($enable_fboldmodule)) {
        $this->addElement('Checkbox', 'share_button', array(
          'label' => 'Share Button',             
          'value' => 1
        ));
      }	
      
		$this->addElement('Select', 'layout_style', array(
			'label' =>'Layout Style',
			'description' => 'Select the Layout Style. This determines the size and amount of social context next to the button.',
			'multiOptions' =>array('standard' => 'standard', 'button_count' => 'button_count','box_count' => 'box_count')
			
		));
			
		$this->addElement('Checkbox', 'show_faces', array(
			'label' => 'Show Faces',
			'description' => '',
			'value' => 1
		));

		$this->addElement('Text', 'like_width', array(
			'label' => 'Width',
			'description' => 'Specify the width of the Facebook Like Button plugin in pixels.',
			'value' => 450
		));

		$this->addElement('Select', 'like_verb_display', array(
			'label' => 'Verb to display',
			'description' => "Select the verb to display in the button. You may select from 'like' or 'recommend'.",
			'multiOptions' => array('like' => 'like', 'recommend' => 'recommend')
		));

	  $this->addElement('Select', 'like_font', array(
			'label' => 'Font ',
			'description' => 'Select the font of the Facebook Like Button.',
			'multiOptions' => array('' => '', 'arial' => 'arial', 'lucida grande' => 'lucida grande', 'segoe ui' => 'segoe ui', 'tahoma' => 'tahoma', 'trebuchet ms' => 'trebuchet ms', 'verdana' => 'verdana')
		));

		$this->addElement('Select', 'color_scheme', array(
			'label' => 'Color Scheme ',
			'description' => 'Select the color scheme of the Facebook Like Button.',
			'multiOptions' => array('light' => 'light', 'dark' => 'dark')
		));

		$this->addElement('Button', 'Get_Code', array(
			'label' => 'Get Code',
			'type' => 'button',
			'onclick' => 'javascript:get_code();',
			'ignore' => true
		));
	}
}
?>
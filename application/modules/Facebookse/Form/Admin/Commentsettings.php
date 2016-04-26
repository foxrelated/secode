<?php
  /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Likesettings.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Facebookse_Form_Admin_Commentsettings extends Engine_Form
{
  public function init()
  {
  	$this
      ->setTitle("Facebook Comments Box Settings")
      ->setDescription("The Facebook Comments Box enables users to leave a comment on your site as their Facebook profile or any Facebook Page they administer. Users can choose to have their comment posted to their Facebook wall (Profile or Page, depending on how they made the comment). With this, the comment also publishes to the News Feed of their friends / fans, thus being easily shared with them. Such sharing on Facebook links back to your website. Friends and fans can respond to user's comment either directly from News Feed on Facebook, or from Comments Box on your site. Responses on Facebook and in the Comments Box on your site are synced, thus prompting more people to get engaged with your website.Below, you can configure the settings for Facebook Comments Box on your site for the various content types.<br />Facebook Comments Box integration on your site can be made more effective by enabling Open Graph for the respective content types. Then, as Site Admin you will be able to moderate comments for it. You should have entered your Facebook User ID in the field: 'Admin\'s Facebook User ID' in Global Settings. Please note that if you enable Facebook Comments Box for a content type, then content creators for its entities will not become admins of the corresponding Facebook Page (i.e., the Open Graph Setting: 'Admin of corresponding Facebook Page' for that content type will be No.)");

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));
    $this->getDecorator('Description')->setOption('escape', false);
  }

  public function show_comments ($pagelevel_id) {

	// prepare user levels
    
    $comment_array = array(1 => 'Yes, enable and replace the SocialEngine Comment Box.', 2 => 'Yes, enable and show both SocialEngine Comment Box and Facebook Comments Box.', 0 => 'No, do not enable Facebook Comments Box.');
    $plugins_array = Engine_Api::_()->getDbtable( 'mixsettings' , 'facebookse' )->getMixLikeItems(1);
    
    if (isset($plugins_array['sitealbum']))
        unset($plugins_array['sitealbum']);
    if (isset($plugins_array['user']))
        unset($plugins_array['user']);
    if (isset($plugins_array['home']))
        unset($plugins_array['home']);
    if (isset($plugins_array['albums']))
        unset($plugins_array['albums']);

    if (!empty($plugins_array)) {
    	asort($plugins_array);
  		array_unshift($plugins_array, "Select");
  
  		$this->addElement('Select', 'pagelevel_id', array(
  			'label' => 'Content Type',
  			'multiOptions' => $plugins_array,
  			'onchange' => 'javascript:fetchCommentSettings(this.value);',
  			'ignore' => true,
  			'value' => $pagelevel_id
  		));
    }
   
    if ( !empty($pagelevel_id) && array_key_exists($pagelevel_id, $plugins_array)) { 
      $description = Zend_Registry::get('Zend_Translate')->_('Do you want to enable the Facebook Comments Box for the main pages of this content type?');
      $description = sprintf($description,$plugins_array[$pagelevel_id]);
      $this->addElement('Radio', 'enable', array(
			'label' => 'Enable Facebook Comments Box',
      'description' => $description,
			'multiOptions' => $comment_array,
			'value' => 0
			));


       $description_commentboxprivacy = Zend_Registry::get('Zend_Translate')->_("Do you want the Facebook Comments Box for this content type to inherit the privacy settings and checks from the SocialEngine core and settings of this content plugin? (If you choose No, then you should also uncheck all the Comment privacy options from the setting of this content plugin so that users are not able to choose privacy for commenting on their content of this type. In that case, everyone will be able to comment with their Facebook accounts, irrespective of their login status on the site and privacy. If you choose Yes, then your site's privacy settings will apply for commenting, and users will not be able to see comments also if they are not logged-in on the site.)");
      $this->addElement('Radio', 'commentbox_privacy', array(
				'label' => 'Inheriting Core Comments Privacy',
				'description' => $description_commentboxprivacy,
				'multiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => 1
			));

			$this->addElement('Text', 'commentbox_width', array(
				'label' => 'Width',
				'description' => 'Specify the width of the Facebook Comments Box for this content type in pixels.',
				'value' =>'500',
			));


			$this->addElement('Select', 'commentbox_color', array(
				'label' => 'Color Scheme ',
				'description' => 'Select the color scheme of the Facebook Comments Box for this content type.',
				'multiOptions' => array('light' => 'light', 'dark' => 'dark')
			));

			$this->addElement('Button', 'submit', array(
				'label' => 'Save Settings',
				'type' => 'submit',
				'style' => 'float:left;margin-right:10px;',
				'ignore' => true,
				'decorators' => array(
					'ViewHelper'
				)
			));
			$buttons[] = 'submit';
      $this->addElement('Button', 'preview', array(
				'label' => 'Preview',
				'type' => 'button',
				'onclick' => 'javascript:show_commentboxpreview();',
				'ignore' => true,
        'decorators' => array(
					'ViewHelper'
				)
			));
      $buttons[] = 'preview';
			$this->addDisplayGroup($buttons, 'buttons');
			$button_group = $this->getDisplayGroup('buttons');
		}
  }
}
?>
<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Widgetsettings.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Facebookse_Form_Admin_Widgetsettings extends Engine_Form
{
//  public function init()
//  {
//    $url_layouteditor =  'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/content';
//    $url = '<a href="' . $url_layouteditor . '" > here </a>';
//    $site_desc = Zend_Registry::get('Zend_Translate')->_("Facebook Social Plugins enable you to turn user interactions into more engaging experiences throughout your site. The plugins are personalized for all users of your site who are logged into Facebook — even if the users haven't yet signed up for your site. This plugin integrates various Facebook Social Plugins on your site like: Recommendations, Like Box, Activity Feed, etc. To activate these, please enable their respective widgets from the Layout Editor over <a href= %s >here </a> Below, you can configure the settings for these plugins on your site.");
//    $site_desc = sprintf($site_desc, $url_layouteditor);
//   	$this
//      ->setTitle('Facebook Social Plugins Settings')
//      ->setDescription($site_desc);
//
//    $this->loadDefaultDecorators();
//    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND', 'escape' => false));
//  }
//  
//  public function show_likes ($pagelevel_id) {
//		$widget_type =  $pagelevel_id;
//		$color_array = array('light' => 'light', 'dark' => 'dark');
//		$widgets_array = array('activity_feed' => 'Activity Feed', 'facepile' => 'Facepiles', 'likebox' => 'Like Box', 'recommendation' => 'Recommendations' ); 
//		array_unshift($widgets_array, "Select");
//		if (empty($widget_type)) {
//			$this->addElement('Select', 'widget_type', array(
//				'label' => 'Social Plugin',
//				'description' => "Select the Facebook Social Plugin to configure.",
//				'multiOptions' => $widgets_array,
//				'onchange' => 'javascript:fetchWidgetSettings(this.value);'
//				
//			));
//		}
//
//    if (!empty($widget_type)) {
//      //SHOWING FORM FOR FACEBOOK RECENT ACTIVITY FEED WIDGET.
//			if ($widget_type == 'activity_feed') {
//				$this->addElement('Select', 'widget_type', array(
//					'label' => 'Social Plugin',
//					'description' => "The Activity Feed Social Plugin displays stories when users like content on your site using the Facebook Like Button. If a user is logged into Facebook, the plugin will be personalized to highlight content from their Facebook friends. If the user is logged out, the activity feed will show recommendations from your site, and give the user the option to log in to Facebook.",
//					'multiOptions' => $widgets_array,
//					'onchange' => 'javascript:fetchWidgetSettings(this.value);'
//				));
//
//				$this->addElement('Text', 'fb_width', array(
//					'label' => 'Width',
//					'description' => 'The width of the plugin in pixels.',
//					'value' =>'',
//				));
//
//				$this->addElement('Text', 'fb_height', array(
//					'label' => 'Height',
//					'description' => 'The height of the plugin in pixels.',
//					'value' =>'',
//				));
//
//        $this->addElement('Checkbox', 'show_header', array(
//					'label' => "Show the Facebook header on the plugin.",
//					'description' => "Header",
//					'value' => 1,
//				));
//
//				$this->addElement('Select', 'widget_color_scheme', array(
//					'label' => 'Color Scheme ',
//					'description' => 'The color scheme of the plugin.',
//					'multiOptions' => array('light' => 'light', 'dark' => 'dark')
//				));
//
//				$this->addElement('Select', 'widget_font', array(
//					'label' => 'Font ',
//					'description' => 'The font of the plugin.',
//					'multiOptions' => array('' => '', 'arial' => 'arial', 'lucida grande' => 'lucida grande', 'segoe ui' => 'segoe ui', 'tahoma' => 'tahoma', 'trebuchet ms' => 'trebuchet ms', 'verdana' => 'verdana')
//				));
//		
//				$this->addElement('Text', 'widget_border_color', array(
//					'label' => 'Border Color',
//					'decorators' => array(array('ViewScript', array(
//						'viewScript' => '_formImagerainbow.tpl',
//						'class'      => 'form element'
//					)))
//				));
//
//				$this->addElement('Checkbox', 'recommend', array(
//						'label' => "Show recommendations. [The social plugin is filled with activity from the user's friends.If there isn't enough friend activity to fill the plugin, it is backfilled with recommendations. If you set the recommendations param to true, the plugin is split in half, showing friends activity in the top half, and recommendations in the bottom half. If there is not enough friends activity to fill half of the plugin, it will include more recommendations.]",
//						'description' => "Recommendations",
//						'value' => 1,
//				));
//			}
//			//SHOWING FORM FOR FACEBOOK FACEPILES WIDGET.
//			elseif ($widget_type == 'facepile') {
//        $this->addElement('Select', 'widget_type', array(
//					'label' => 'Social Plugin',
//					'description' => "The Facepile Social Plugin shows the Facebook profile pictures of the user's Facebook friends who have already signed up for your site.",
//					'multiOptions' => $widgets_array,
//					'onchange' => 'javascript:fetchWidgetSettings(this.value);'
//				
//				));
//
//				$this->addElement('Text', 'connection', array(
//					'label' => 'Num rows',
//					'description' => 'The maximum number of rows of profile pictures to show.',
//					'value' =>'1',
//				));
//			
//				$this->addElement('Text', 'fb_width', array(
//					'label' => 'Width',
//					'description' => 'The width of the plugin in pixels.',
//					'value' =>'',
//				));
//			}
//			//SHOWING FORM FOR FACEBOOK LIKEBOX WIDGET.
//			else if ($widget_type == 'likebox') {
//				
//				$this->addElement('Select', 'widget_type', array(
//					'label' => 'Social Plugin',
//					'description' => "The Like Box is a Social Plugin that enables Facebook Page owners to attract and gain Likes from their own website. The Like Box enables users to: 1) See how many users already like this page, and which of their friends like it too. 2) Read recent posts from the page. 3) Like the page with one click, without needing to visit the page.",
//					'multiOptions' => $widgets_array,
//					'onchange' => 'javascript:fetchWidgetSettings(this.value);'
//				
//				));
//      
//        $this->addElement('Text', 'fbpageurl', array(
//					'label' => 'Facebook Page URL',
//					'description' => "The URL of the Facebook Page for this Like Box.",
//					'value' => '',
//          'required' => true,
//					'allowEmpty' => false,
//				));
//
//				$this->fbpageurl->getDecorator('Description')->setOptions(array('escape' => false,'placement' => 'PREPEND'));
//
//				$this->addElement('Text', 'fb_width', array(
//					'label' => 'Width',
//					'description' => 'The width of the plugin in pixels.The minimum supported plugin width is 292 pixels.',
//					'value' =>'',
//				));
//				
//				$this->addElement('Text', 'fb_height', array(
//				  'label' => 'Height',
//					'description' => 'The height of the plugin in pixels.',
//					'value' =>'',
//				));
//
//				$this->addElement('Select', 'widget_color_scheme', array(
//					'label' => 'Color Scheme ',
//					'description' => 'The color scheme of the plugin.',
//					'multiOptions' => array('light' => 'light', 'dark' => 'dark')
//				));
//
//				$this->addElement('Text', 'connection', array(
//					'label' => 'Connections',
//					'description' => 'Show a sample of this many users who have liked this Page.',
//					'value' =>'',
//				));
//			
//				$this->addElement('Checkbox', 'show_stream', array(
//					'label' => "Show the Facebook Page profile stream for the public profile.",
//					'description' => "Stream",
//					'value' => 1,
//				));
//
//				$this->addElement('Checkbox', 'show_header', array(
//					'label' => "Show the 'Find us on Facebook' bar at top. Only shown when either stream or connections are present.",
//					'description' => "Header",
//					'value' => 1,
//				));
//
//			} 
//			elseif ($widget_type == 'recommendation') {
//				
//				$this->addElement('Select', 'widget_type', array(
//					'label' => 'Social Plugin',
//					'description' => "The Recommendations Social Plugin shows personalized recommendations to your users. The plugin can display personalized recommendations whether or not the user has logged into your site. To generate the recommendations, the plugin considers all the social interactions with URLs from your site. For a logged in Facebook user, the plugin will give preference to and highlight objects her Facebook friends have interacted with on your site.",
//					'multiOptions' => $widgets_array,
//					'onchange' => 'javascript:fetchWidgetSettings(this.value);'
//				
//				));
//         $base_url = '<a href="http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl(); 
//        $this->addElement('Text', 'fbpageurl', array(
//					'label' => 'Domain',
//					'description' => "The domain for which to show recommendations.",
//					'value' => $base_url
//				));
//				$this->addElement('Text', 'fb_width', array(
//					'label' => 'Width',
//					'description' => 'The width of the plugin in pixels.',
//					'value' =>'',
//				));
//
//				$this->addElement('Text', 'fb_height', array(
//					'label' => 'Height',
//					'description' => 'The height of the plugin in pixels.',
//					'value' =>'',
//				));
//			
//				$this->addElement('Checkbox', 'show_header', array(
//					'label' => "Show the Facebook header on the plugin",
//					'description' => "Header",
//					'value' => 1,
//				));
//
//				$this->addElement('Select', 'widget_color_scheme', array(
//					'label' => 'Color Scheme ',
//					'description' => 'The color scheme of the plugin.',
//					'multiOptions' => array('light' => 'light', 'dark' => 'dark')
//				));
//
//				$this->addElement('Select', 'widget_font', array(
//					'label' => 'Font ',
//					'description' => 'The font of the plugin.',
//					'multiOptions' => array('' => '', 'arial' => 'arial', 'lucida grande' => 'lucida grande', 'segoe ui' => 'segoe ui', 'tahoma' => 'tahoma', 'trebuchet ms' => 'trebuchet ms', 'verdana' => 'verdana')
//				));
//			
//				$this->addElement('Text', 'widget_border_color', array(
//					'label' => 'Border Color',
//					'decorators' => array(array('ViewScript', array(
//						'viewScript' => '_formImagerainbow_recommend.tpl',
//						'class'      => 'form element'
//					)))
//				));
//			}  
//			$this->addElement('Button', 'submit', array(
//				'label' => 'Save Settings',
//				'type' => 'submit',
//				'ignore' => true,
//        'decorators' => array('ViewHelper')
//			));
//      $buttons[] = 'submit';
//      $this->addElement('Button', 'preview', array(
//				'label' => 'Preview',
//				'type' => 'button',
//				'onclick' => 'javascript:show_preview();',
//				'ignore' => true,
//        'decorators' => array(
//					'ViewHelper'
//				)
//			));
//      $buttons[] = 'preview';
//			$this->addDisplayGroup($buttons, 'buttons');
//			$button_group = $this->getDisplayGroup('buttons');
//			$this->widget_type->getDecorator("Description")->setOption("placement", "append");
//    }
//  }
}
?>
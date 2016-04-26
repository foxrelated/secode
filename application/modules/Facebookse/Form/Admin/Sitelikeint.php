<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Sitelikeint.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Facebookse_Form_Admin_Sitelikeint extends Engine_Form
{
  public function init()
  {
    //CHECK IF LIKE MODULE IS ENABLE OR NOT.WE ARE DOING THIS FOR INTEGRATING FACEBOOK LIKE TO SITE LIKE.
		$enable_likemodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike');
    $description_sitetab = 'Here, you can integrate the Facebook Like Buttons on your site with the Likes system of your site, such that, if a user clicks on the Facebook Like Button for an item, the item will also be liked on your site. Thus, this will also lead to the action being propagated across your site, in the various widgets, likes pages, activity feeds, etc.';
    if (!empty($enable_likemodule)) {
			$description_sitetab .= '<br /><span style="font-size:11px;"><strong style="font-weight:bold;">Note:</strong> The vice-versa will not happen, i.e., if the user clicks the Like button of the site, then the item will not get liked on Facebook. If the user clicks the Facebook Like Button to unlike an item, then that item will not get unliked on site.</span>';
    }
  	$this
      ->setTitle("Integrating Facebook Like Buttons with site's Likes system")
      ->setDescription($description_sitetab);
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND', 'escape' => false));
    $like_array = array(1 => 'Yes', 0 => 'No');

    //CHECK IF LIKE MODULE IS ENABLE OR NOT.WE ARE DOING THIS FOR INTEGRATING FACEBOOK LIKE TO SITE LIKE.
		$enable_likemodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike');
    if (!empty($enable_likemodule)) {
			$this->addElement('Radio', 'fb_site_likeint', array(
				'label' => 'Integrate FB Like with Likes on site',
				'description' => 'Do you want to integrate the Facebook Like Buttons on your site with the Likes system of your site?',
				'multiOptions' => $like_array,
				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('fb.site.likeint', 1),
         'onclick' => 'javascript:show_fbinstruct();'
				));

				$this->addElement('Button', 'submit', array(
					'label' => 'Save Setting',
					'type' => 'submit',
					'ignore' => true
				));
			}
      else {
       
        $description = Zend_Registry::get('Zend_Translate')->_("This integration feature is dependent on the <a href='http://www.socialengineaddons.com/socialengine-likes-plugin-and-widgets' target='_blank'>Likes Plugin and Widgets</a> and requires it to be installed on your site. Please install this plugin after downloading it from your Client Area on SocialEngineAddOns. You may purchase this plugin over <a href='http://www.socialengineaddons.com/socialengine-likes-plugin-and-widgets' target='_blank'>here:</a>");
              
				$this->addElement('Dummy', 'message', array(
				'content' => $description,
			
				));
			}
		}

}
?>
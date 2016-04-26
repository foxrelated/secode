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
class Facebookse_Form_Admin_Likesettings extends Engine_Form {

    public function init() {

        $fbLikeButton = Engine_Api::_()->getApi("settings", "core")->getSetting('fblike.type', 'default');
        if ($fbLikeButton == 'default') {
            $desc = "The Facebook Like Button lets users share pages and content from your site back to their Facebook profile, and with their Facebook friends with one click! When a user clicks the FB Like Button on your site, a story appears in the user's friends' News Feed with a link back to your website. Below, you can enable / disable the Like buttons on the user profiles and the main pages of the various content types on your site. You can also configure various settings for these Like buttons. The Facebook Like Button Configurator at the right side of this page allows you to generate code for Like button to be placed at any URL page of your choice.<br /> The Like button integration on your site can be made more effective by enabling Open Graph implementation on the respective pages. You can do so from the 'Open Graph Settings tab.";
        } else {

            $desc = "The Facebook Like Button lets users share pages and content from your site back to their Facebook profile, and with their Facebook friends with one click! When a user clicks the FB Like Button on your site, a story appears in the user's friends' News Feed with a link back to your website. Below, you can enable / disable the Like buttons on the user profiles and the main pages of the various content types on your site. You can also configure various settings for these Like buttons.<br /> The Like button integration on your site can be made more effective by enabling Open Graph implementation on the respective pages. You can do so from the 'Open Graph Settings tab.";
        }
        $this
                ->setTitle('Facebook Like Button Settings')
                ->setDescription($desc)
                ->setAttrib('id', 'likesetting_form');

        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));
        $this->getDecorator('Description')->setOption('escape', false);
    }

    public function show_likes($pagelevel_id) {

        // Modules which are taking by us.
        $plugins_array = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getMixLikeItems(1);

        //GET THE FB LIKE BUTTON TYPE ADMIN HAS ENABLED
        $fbLikeButton = Engine_Api::_()->getApi("settings", "core")->getSetting('fblike.type', 'default');

//		foreach ($plugins_array as $key => $plugins) {
//			if ($key == 'blog' || $key == 'album' || $key == 'classified' || $key == 'forum' || $key == 'poll' || $key == 'video' || $key == 'music' || $key == 'group' || $key == 'event' || $key == 'document' || $key == 'list' || $key == 'sitepage' || $key == 'recipe' || $key == 'sitebusiness') {
//       $plugins_array_temp[$key]= $plugins;
//			}
//			else if (strstr($key, 'sitepage') !== false || strstr($key, 'sitebusiness') !== false) {
//			  $plugins_array_temp[$key]= $plugins;
//			}
//		
//    }
        $plugins_array['user'] = 'User Profile';
        $plugins_array['home'] = 'Site Homepage';
        if (isset($plugins_array['albums']))
            unset($plugins_array['albums']);
        $plugins_array = array_merge(array("Select"), $plugins_array);
        if (isset($plugins_array['sitestore_store']))
            unset($plugins_array['sitestore_store']);
        $this->addElement('Select', 'pagelevel_id', array(
            'label' => 'Content Type',
            'multiOptions' => $plugins_array,
            'onchange' => 'javascript:fetchLikeSettings(this.value);',
            'ignore' => true
        ));

        $pagelevel_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('level_id', null);
        if (!empty($pagelevel_id)) {
            $description = Zend_Registry::get('Zend_Translate')->_('Do you want to show the Facebook Like Button on the main pages of ' . $plugins_array[$pagelevel_id] . " ?.");
            $this->addElement('Radio', $pagelevel_id . '_1', array(
                'label' => 'Show FB Like Button',
                'description' => $description,
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 1
            ));

            $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
            if (!empty($enable_fboldversion)) {
                $socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('socialdna');
                $socialdnaversion = $socialdnamodule->version;
                if ($socialdnaversion >= '4.1.1') {
                    $enable_fboldversion = 0;
                }
            }
            if (empty($enable_fboldmodule)) {
                $this->addElement('MultiCheckbox', $pagelevel_id . '_4', array(
                    'label' => 'Share Button',
                    'description' => 'Show Share Button alongside the Like Button. Share Button allows users to add a personal message and customize who they share with.',
                    'multiOptions' => array(
                        1 => 'Share Button'
                    ),
                    'value' => array(1)
                ));
            }


            if ($fbLikeButton == 'default') {

                $this->addElement('Select', 'layout_style', array(
                    'label' => 'Layout Style',
                    'description' => 'Select the Layout Style. This determines the size and amount of social context next to the button.',
                    'multiOptions' => array('standard' => 'standard', 'button_count' => 'button_count', 'box_count' => 'box_count')
                ));

                $this->addElement('Text', $pagelevel_id . '_5', array(
                    'label' => 'Width',
                    'description' => 'Specify the width of the Facebook Like Button plugin in pixels.',
                    'value' => 450
                ));

                $this->addElement('Select', $pagelevel_id . '_2', array(
                    'label' => 'Verb to display',
                    'description' => "Select the verb to display in the button. You may select from 'like' or 'recommend'.",
                    'multiOptions' => array(
                        'like' => 'like',
                        'recommend' => 'recommend'
                    ),
                    'value' => array('like')
                ));

                $this->addElement('Select', 'likefont', array(
                    'label' => 'Font ',
                    'description' => 'Select the font of the Facebook Like Button.',
                    'multiOptions' => array('' => '', 'arial' => 'arial', 'lucida grande' => 'lucida grande', 'segoe ui' => 'segoe ui', 'tahoma' => 'tahoma', 'trebuchet ms' => 'trebuchet ms', 'verdana' => 'verdana')
                ));


                $this->addElement('Select', 'like_color', array(
                    'label' => 'Color Scheme',
                    'description' => 'Select the color scheme of the Facebook Like Button.',
                    'multiOptions' => array('light' => 'light', 'dark' => 'dark')
                ));
                
                //FACEBOOK URL ELEMENT
                if ($pagelevel_id == 'home') {
                    $this->addElement('Text', 'facebook_home_url', array(
                        'label' => 'Facebook Url',
                        'description' => 'Enter your facebook page url that you want users to like on facebook.',
                        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('facebook.home.url')
                    ));
                }
            }

            $this->addElement('MultiCheckbox', $pagelevel_id . '_3', array(
                'label' => 'Show Friends\' Faces',
                'description' => 'Show Facebook profile pictures below the button of those who Like the item.',
                'multiOptions' => array(
                    1 => 'Show faces'
                ),
                'value' => array(1)
            ));


            if ($fbLikeButton == 'custom') {
                $this->addElement('Select', 'action_type', array(
                    'label' => 'Action Type',
                    'description' => 'Select the Action Type which you want to associate with this content type. The default action type will be "og.likes".',
                    'multiOptions' => array('og.likes' => 'default', 'custom' => 'custom'),
                    'onchange' => 'javascript:setActionType(this);',
                ));

                $this->addElement('Text', 'actiontype_custom', array(
                    'label' => '',
                    'description' => "Please specify the custom action type if you have created <a href='https://developers.facebook.com/apps/" . Engine_Api::_()->getApi("settings", "core")->core_facebook_appid . "/open-graph/object-types?ref=nav' target='_blank'>here </a> and that you want to associate with this type of content.<br />", // DONE CHANGES IN THE URL TO VIEW OBJECT TYPES
                ));

                $this->actiontype_custom->getDecorator('Description')->setOptions(array('placement' => 'append', 'escape' => false));


                $this->addElement('Text', 'objecttype_custom', array(
                    'label' => 'Object Type',
                    'description' => "Please specify the object type associated with the above action type. The default object type for default action type will be \"object\".",
                    'value' => 'object',
                    'required' => true,
                    'allowEmpty' => false,
                ));

                $this->addElement('Text', 'fbbutton_liketext', array(
                    'label' => 'Like Action Text on Button',
                    'description' => "Please specify the action text which will be shown on the FB Button. The default action text will be \"Like\".",
                    'value' => 'Like',
                    'required' => true,
                    'allowEmpty' => false,
                ));

                $this->addElement('Text', 'fbbutton_unliketext', array(
                    'label' => 'Unlike Action Text on Button',
                    'description' => "Please specify the unlike action text which will be shown on the FB Button. The default action text will be \"Unlike\".",
                    'value' => 'Unlike',
                    'required' => true,
                    'allowEmpty' => false,
                ));

                $this->addElement('Radio', 'show_customicon', array(
                    'label' => 'Show FB Button Icon',
                    'description' => 'Do you want to show an icon on the custom Facebook Button.',
                    'multiOptions' => array(
                        1 => 'Yes',
                        0 => 'No'
                    ),
                    'value' => 1,
                    'onclick' => 'javascript:showLikeUnlikeIcons(this.value);'
                ));

                $image_likethumbsup = Engine_Api::_()->facebookse()->getDefaultLikeUnlikeIcon('like', false);
                $image_likethumbsdown = Engine_Api::_()->facebookse()->getDefaultLikeUnlikeIcon('unlike', false);
                $likeunlike_icons = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getFBIconCustom('', $pagelevel_id);
                // Get available files (Icon for activity Feed).
                $logoOptions = array($image_likethumbsup => 'Default Icon');
                $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                $files_like = $files_unlike = Engine_Api::_()->facebookse()->getUPloadedFiles();

                if (isset($files_like[$image_likethumbsup]))
                    unset($files_like[$image_likethumbsup]);

                $logoOptions = array_merge($logoOptions, $files_like);

                $URL = $view->baseUrl() . "/admin/files";
                $click = '<a href="' . $URL . '" target="_blank">over here</a>';
                $customBlocks = sprintf("Upload a small icon for your custom like button %s. (The dimensions of the image should be 13x13 px. The currently associated image is shown below this field.). Once you upload a new icon at the link mentioned, then refresh this page to see its preview below after selection.)", $click);

                if (!empty($logoOptions)) {
                    $this->addElement('Select', 'fbbutton_likeicon', array(
                        'label' => 'Like Thumbs-up Image',
                        'description' => $customBlocks,
                        'multiOptions' => $logoOptions,
                        'onchange' => "updateTextFields(this.value)",
                    ));
                    $this->getElement('fbbutton_likeicon')->getDecorator('Description')->setOptions(array('placement' =>
                        'PREPEND', 'escape' => false));
                }
                if (empty($_POST))
                    $logo_photo = !empty($likeunlike_icons->fbbutton_likeicon) ? $likeunlike_icons->fbbutton_likeicon : $image_likethumbsup;
                else
                    $logo_photo = !empty($_POST['fbbutton_likeicon']) ? $_POST['fbbutton_likeicon'] : $image_likethumbsup;
                if (!empty($logo_photo)) {

                    $photoName = $view->baseUrl() . '/' . $logo_photo;
                    $description = "<img src='$photoName' width='13' height='13'/>";
                }
                //VALUE FOR LOGO PREVIEW.
                $this->addElement('Dummy', 'fbbutton_likeicon_preview', array(
                    'label' => 'Like Icon Preview',
                    'description' => $description,
                ));
                $this->fbbutton_likeicon_preview
                        ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));


                // Get available files (Icon for activity Feed).

                $logoOptions = array($image_likethumbsdown => 'Default Icon');
                if (isset($files_unlike[$image_likethumbsdown]))
                    unset($files_unlike[$image_likethumbsdown]);
                $logoOptions = array_merge($logoOptions, $files_unlike);
                if (!empty($logoOptions)) {
                    $this->addElement('Select', 'fbbutton_unlikeicon', array(
                        'label' => 'Unlike Thumbs-down Image',
                        'description' => $customBlocks,
                        'multiOptions' => $logoOptions,
                        'onchange' => "updateTextFields1(this.value)",
                    ));
                    $this->getElement('fbbutton_unlikeicon')->getDecorator('Description')->setOptions(array('placement' =>
                        'PREPEND', 'escape' => false));
                }
                if (empty($_POST))
                    $logo_photo = !empty($likeunlike_icons->fbbutton_unlikeicon) ? $likeunlike_icons->fbbutton_unlikeicon : $image_likethumbsdown;
                else
                    $logo_photo = !empty($_POST['fbbutton_unlikeicon']) ? $_POST['fbbutton_unlikeicon'] : $image_likethumbsdown;
                if (!empty($logo_photo)) {

                    $photoName = $view->baseUrl() . '/' . $logo_photo;
                    $description = "<img src='$photoName' width='13' height='13'/>";
                }
                //VALUE FOR LOGO PREVIEW.
                $this->addElement('Dummy', 'fbbutton_unlikeicon_preview', array(
                    'label' => 'Unlike Icon Preview',
                    'description' => $description,
                ));
                $this->fbbutton_unlikeicon_preview
                        ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));


                $this->addElement('MultiCheckbox', 'like_commentbox', array(
                    'label' => 'Show Comment Box',
                    'description' => 'Show Comment Box for posting comment also when users will like website content.',
                    'multiOptions' => array(
                        1 => 'Show Comment Box'
                    ),
                    'value' => array(1)
                ));
            }

            $this->addElement('Button', 'submit', array(
                'label' => 'Save Settings',
                'type' => 'submit',
                'ignore' => true,
                'decorators' => array(
                    'ViewHelper'
                )
            ));
            $buttons[] = 'submit';
            $this->addElement('Button', 'preview', array(
                'label' => 'Preview',
                'type' => 'button',
                'onclick' => 'javascript:show_likepreview();',
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

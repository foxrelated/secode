<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$pages_Array = array();
$pageevents_Array = array();
$comment_array = array(1 => 'Yes', 2 => 'No');

//MAKE ELEMENTS OF FACEBOOK LIKE BUTTON BASED ON BUTTON TYPE EITHER IT'S DEFAULT OR CUSTOM

$fbLikeButton = Engine_Api::_()->getApi("settings", "core")->getSetting('fblike.type', 'default');
$hiddenelement = array(
    'Hidden',
    'hidden',
    array(
        'value' => 0,
    )
);

$layout_style = $hiddenelement;
$like_width = $hiddenelement;
$like_verb_display = $hiddenelement;
$like_font = $hiddenelement;
$like_colour = $hiddenelement;
$action_type = $hiddenelement;
$action_type_custom = $hiddenelement;
$fbbutton_liketext = $hiddenelement;
$fbbutton_unliketext = $hiddenelement;
$object_type_custom = $hiddenelement;
$fb_commentbox = $hiddenelement;
$show_customIcon = $hiddenelement;
$fbbutton_likeicon = $hiddenelement;
$fbbutton_unlikeicon = $hiddenelement;
$likeicon_preview = $hiddenelement;
$unlikeicon_preview = $hiddenelement;
$send_button = array(
    'MultiCheckbox',
    'send_button',
    array(
        'label' => '',
        'description' => '',
        'multiOptions' => array(
            1 => $this->view->translate('Show Share Button alongside the Like Button. Share Button allows users to add a personal message and customize who they share with.')
        ),
    )
);
if ($fbLikeButton == 'default') {
    $layout_style = array(
        'Select',
        'layout_style',
        array(
            'label' => '',
            'description' => $this->view->translate('Select the Layout Style. This determines the size and amount of social context next to the button.'),
            'multiOptions' => array('standard' => 'standard', 'button_count' => 'button_count', 'box_count' => 'box_count')
        )
    );

    $like_width = array(
        'Text', 'like_width', array(
            'label' => '',
            'description' => $this->view->translate('Specify the width of the Facebook Like Button plugin in pixels.'),
            'value' => 450
        )
    );


    $like_verb_display = array(
        'Select', 'like_verb_display', array(
            'label' => '',
            'description' => $this->view->translate("Select the verb to display in the button. You may select from 'like' or 'recommend'."),
            'multiOptions' => array('like' => 'like', 'recommend' => 'recommend')
        )
    );

    $like_font = array(
        'Select', 'like_font', array(
            'label' => '',
            'description' => $this->view->translate('Select the font of the Facebook Like Button.'),
            'multiOptions' => array('' => '', 'arial' => 'arial', 'lucida grande' => 'lucida grande', 'segoe ui' => 'segoe ui', 'tahoma' => 'tahoma', 'trebuchet ms' => 'trebuchet ms', 'verdana' => 'verdana')
        )
    );

    $like_colour = array(
        'Select', 'like_color_scheme', array(
            'label' => '',
            'description' => $this->view->translate('Select the color scheme of the Facebook Like Button.'),
            'multiOptions' => array('light' => 'light', 'dark' => 'dark')
        )
    );
} else {
    $action_type = array(
        'Select',
        'action_type',
        array(
            'label' => '',
            'description' => 'Select the Action Type which you want to associate with this module type.The default action type will be "og.likes".',
            'multiOptions' => array('og.likes' => 'default', 'custom' => 'custom'),
            'onchange' => 'javascript:setActionType(this);',
        )
    );

    $action_type_custom = array(
        'Text', 'actiontype_custom', array(
            'label' => '',
            'description' => 'Specify the custom action type which you have created in your app dashboard.',
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => 'application/modules/Facebookse/views/scripts/_fblikebutton.tpl',
                        'class' => 'form element')))
        ),
    );

    $object_type_custom = array(
        'Text', 'objecttype_custom', array(
            'label' => '',
            'description' => "Please specify the object type associated with the above action type. The default object type for default action type will be \"object\".",
            'value' => 'object',
            'required' => true,
            'allowEmpty' => false,
        ),
    );

    $fbbutton_liketext = array(
        'Text', 'fbbutton_liketext', array(
            'label' => '',
            'description' => "Please specify the action text which will be shown on the FB Button. The default action text will be \"Like\".",
            'value' => 'Like',
            'required' => true,
            'allowEmpty' => false,
        ),
    );

    $fbbutton_unliketext = array(
        'Text', 'fbbutton_unliketext', array(
            'label' => '',
            'description' => "Please specify the action text which will be shown on the FB Button. The default action text will be \"Unlike\".",
            'value' => 'Unlike',
            'required' => true,
            'allowEmpty' => false,
        ),
    );

    $fb_commentbox = array('Radio', 'fbbutton_commentbox', array(
            'label' => '',
            'description' => $this->view->translate("Do you want users to be able to add a comment when they Like a content using the Facebook Like Button?"),
            'multiOptions' => array('1' => 'Yes', '0' => 'No'),
            'value' => '1',
    ));


    //MAKE THE SETTINGS FOR SHOWING LIKE BUTTON ICON.
    $show_customIcon = array('Radio', 'show_customicon', array(
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
    // Get available files (Icon for activity Feed).
    $logoOptions = array($image_likethumbsup => 'Default Icon');
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $files = Engine_Api::_()->facebookse()->getUPloadedFiles();
    $logoOptions = array_merge($logoOptions, $files);

    $URL = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $view->baseUrl() . "/admin/files";

    $customBlocks = "Upload a small icon for your custom like button at: " . $URL . ". (The dimensions of the image should be 13x13 px. The currently associated image is shown below this field.). Once you upload a new icon at the link mentioned, then refresh this page to see its preview below after selection.)";

    $fbbutton_likeicon = array('Select', 'fbbutton_likeicon', array(
            'description' => $customBlocks,
            'multiOptions' => $logoOptions,
            'onchange' => "updateTextFields(this.value)",
    ));

    $likeicon_preview = array('Image', 'fbbutton_likeicon_preview', array(
            'src' => '',
            'width' => 13,
            'height' => 13
    ));

    $logoOptions = array($image_likethumbsdown => 'Default Icon');
    $logoOptions = array_merge($logoOptions, $files);
    $fbbutton_unlikeicon = array('Select', 'fbbutton_unlikeicon', array(
            'description' => $customBlocks,
            'multiOptions' => $logoOptions,
            'onchange' => "updateTextFields1(this.value)",
    ));
    $unlikeicon_preview = array('Image', 'fbbutton_unlikeicon_preview', array(
            'src' => '',
            'width' => 13,
            'height' => 13
    ));
}

$final_array = array(
//    array(
//        'title' => $this->view->translate('Recommendations Social Plugin'),
//        'description' => $this->view->translate('The Recommendations Social Plugin shows personalized recommendations to your users. The plugin can display personalized recommendations whether or not the user has logged into your site. To generate the recommendations, the plugin considers all the social interactions with URLs from your site. For a logged in Facebook user, the plugin will give preference to and highlight objects her Facebook friends have interacted with on your site.'),
//        'category' => $this->view->translate('Facebook Social Plugins'),
//        'type' => 'widget',
//        'name' => 'Facebookse.facebookse-recommendation',
//        'defaultParams' => array(
//            'title' => '',
//            'titleCount' => true,
//        ),
//    ),
//    array(
//        'title' => $this->view->translate('Activity Feed Social Plugin'),
//        'description' => $this->view->translate('The Activity Feed Social Plugin displays stories when users like content on your site using the Facebook Like Button. If a user is logged into Facebook, the plugin will be personalized to highlight content from their Facebook friends. If the user is logged out, the activity feed will show recommendations from your site, and give the user the option to log in to Facebook.'),
//        'category' => $this->view->translate('Facebook Social Plugins'),
//        'type' => 'widget',
//        'name' => 'Facebookse.facebookse-activity',
//        'defaultParams' => array(
//            'title' => '',
//            'titleCount' => true,
//        ),
//    ),
//    array(
//        'title' => $this->view->translate('Facepile Social Plugin'),
//        'description' => $this->view->translate("The Facepile Social Plugin shows the Facebook profile pictures of the user's Facebook friends who have already signed up for your site."),
//        'category' => $this->view->translate('Facebook Social Plugins'),
//        'type' => 'widget',
//        'name' => 'Facebookse.facebookse-facepile',
//        'defaultParams' => array(
//            'title' => '',
//            'titleCount' => true,
//        ),
//    ),
//    array(
//        'title' => $this->view->translate('Like Box Social Plugin'),
//        'description' => $this->view->translate('The Like Box is a Social Plugin that enables Facebook Page owners to attract and gain Likes from their own website.'),
//        'category' => $this->view->translate('Facebook Social Plugins'),
//        'type' => 'widget',
//        'name' => 'Facebookse.facebookse-likebox',
//        'defaultParams' => array(
//            'title' => '',
//            'titleCount' => true,
//        ),
//    ),
    array(
        'title' => $this->view->translate('Like us on Facebook'),
        'description' => $this->view->translate("This widget enables people to Like your website on Facebook. It contains the Facebook Like Button configured for your website. This widget should be placed on the Site Homepage or the Member Homepage."),
        'category' => $this->view->translate('Facebook Social Plugins'),
        'type' => 'widget',
        'name' => 'Facebookse.facebookse-websitelike',
        'defaultParams' => array(
            'title' => $this->view->translate('Like us on Facebook'),
            'titleCount' => true,
        ),
    ),
//    array(
//        'title' => $this->view->translate('Like Group'),
//        'description' => $this->view->translate("This widget enables people to Like Groups on your site. It contains the Facebook Like Button configured for your website. This widget should be placed on the Group Profile Page."),
//        'category' => $this->view->translate('Facebook Social Plugins'),
//        'type' => 'widget',
//        'name' => 'Facebookse.facebookse-groupprofilelike',
//        'defaultParams' => array(
//            'title' => '',
//            'titleCount' => true,
//        ),
//    ),
//    array(
//        'title' => $this->view->translate('Like Event'),
//        'description' => $this->view->translate("This widget enables people to Like Events on your site. It contains the Facebook Like Button configured for your website. This widget should be placed on the Event Profile Page."),
//        'category' => $this->view->translate('Facebook Social Plugins'),
//        'type' => 'widget',
//        'name' => 'Facebookse.facebookse-eventprofilelike',
//        'defaultParams' => array(
//            'title' => '',
//            'titleCount' => true,
//        ),
//    ),
    array(
        'title' => $this->view->translate('Like User Profile'),
        'description' => $this->view->translate("This widget enables people to Like User Profiles on your site. It contains the Facebook Like Button configured for your website. This widget should be placed on the Member Profile Page."),
        'category' => $this->view->translate('Facebook Social Plugins'),
        'type' => 'widget',
        'name' => 'Facebookse.facebookse-userprofilelike',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
//    array(
//        'title' => $this->view->translate('Like Listing / Catalog Item'),
//        'description' => $this->view->translate("This widget enables people to Like Listings / Catalog Items on your site. It contains the Facebook Like Button configured for your website. This widget should be placed on the Listing Profile Page."),
//        'category' => $this->view->translate('Facebook Social Plugins'),
//        'type' => 'widget',
//        'name' => 'Facebookse.facebookse-listprofilelike',
//        'defaultParams' => array(
//            'title' => '',
//            'titleCount' => true,
//        ),
//    ),
    array(
        'title' => $this->view->translate('Facebook Comments Box'),
        'description' => $this->view->translate("This widget contains the Facebook Comments Box. To know more about this, please visit the 'FB Comments Box Settings' section. It is recommended that this widget should not be placed on pages where SocialEngine's Core Comments widget is placed. To show this Facebook Comments Box on main content pages, please visit the 'FB Comments Box Settings' section in the Admins Panel of Advanced Facebook Integration plugin and enable this from there. Edit settings of this widget contains customization options."),
        'category' => $this->view->translate('Facebook Social Plugins'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'Facebookse.facebookse-comments',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array('Radio', 'enable', array(
                        'label' => "Do you want to replace the SocialEngine Comment Box? (If you choose 'No', then both the SE Comment Box and Facebook Comments Box will be shown on this content page.)",
                        'multiOptions' => $comment_array,
                        'value' => 1
                    )),
                array(
                    'hidden',
                    'nomobile',
                    array(
                        'label' => ''
                    )
                ),
                array('Radio', 'commentbox_privacy', array(
                        'label' => 'Inheriting Core Comments Privacy',
                        'description' => $this->view->translate("Do you want the Facebook Comments Box from this widget to inherit the privacy settings and checks from the SocialEngine core and settings of the page where this is placed, if it is a content page? (If you choose No, then you should also uncheck all the Comment privacy options from the setting of the content plugin if this widget is placed on a content plugin page so that users are not able to choose privacy for commenting on their content of this type. In that case, everyone will be able to comment with their Facebook accounts, irrespective of their login status on the site and privacy. If you choose Yes, then your site's privacy settings will apply for commenting, and users will not be able to see comments also if they are not logged-in on the site.)"),
                        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
                        'value' => '1',
                    )),
                array('Text', 'commentbox_width', array(
                        'label' => 'Width',
                        'description' => $this->view->translate('Specify the width of the Facebook Comments Box in pixels.'),
                        'value' => '450',
                    )),
                array('Select', 'commentbox_color', array(
                        'label' => 'Color Scheme ',
                        'description' => $this->view->translate('Select the color scheme of the Facebook Comments Box.'),
                        'multiOptions' => array('light' => 'light', 'dark' => 'dark')
                    )),
            )
        ),
    ),
    array(
        'title' => $this->view->translate('Facebook Like Button'),
        'description' => $this->view->translate("This widget contains the Facebook Like Button which enables users to Like content of your website on Facebook. The edit popup contains the configurator enabling you to customize the button."),
        'category' => $this->view->translate('Facebook Social Plugins'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'Facebookse.facebookse-commonlike',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $send_button,
                $layout_style,
                $like_width,
                $like_verb_display,
                $like_font,
                $like_colour,
                array(
                    'MultiCheckbox',
                    'show_faces',
                    array(
                        'label' => '',
                        'description' => '',
                        'multiOptions' => array(
                            1 => $this->view->translate('Show Facebook profile photos of people who have liked that content.')
                        ),
                    )
                ),
                $action_type,
                $action_type_custom,
                $object_type_custom,
                $fbbutton_liketext,
                $fbbutton_unliketext,
                $show_customIcon,
                $fbbutton_likeicon,
                $likeicon_preview,
                $fbbutton_unlikeicon,
                $unlikeicon_preview,
                $fb_commentbox
            )
        ),
    ),
);


//if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) {
//    $pages_Array = array(
//        array(
//            'title' => $this->view->translate('Like Directory / Page'),
//            'description' => $this->view->translate("This widget enables people to Like Directory / Pages on your site. It contains the Facebook Like Button configured for your website. This widget should be placed on the Page Profile page."),
//            'category' => $this->view->translate('Facebook Social Plugins'),
//            'type' => 'widget',
//            'name' => 'Facebookse.facebookse-sitepageprofilelike',
//            'defaultParams' => array(
//                'title' => '',
//                'titleCount' => true,
//            ),
//    ));
//}

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
    $pageevents_Array = array(
        array(
            'title' => $this->view->translate('Like Page Event'),
            'description' => $this->view->translate("This widget enables people to Like Page Events on your site. It contains the Facebook Like Button configured for your website. This widget should be placed on the Page Event profile page."),
            'category' => $this->view->translate('Facebook Social Plugins'),
            'type' => 'widget',
            'name' => 'Facebookse.facebookse-sitepageeventprofilelike',
            'defaultParams' => array(
                'title' => '',
                'titleCount' => true,
            ),
    ));
}

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('recipe')) {
    $recipes_Array = array(
        array(
            'title' => $this->view->translate('Like Recipes'),
            'description' => $this->view->translate("This widget enables people to Like Recipes on your site. It contains the Facebook Like Button configured for your website. This widget should be placed on the Recipe Profile Page."),
            'category' => $this->view->translate('Facebook Social Plugins'),
            'type' => 'widget',
            'name' => 'Facebookse.facebookse-recipeprofilelike',
            'defaultParams' => array(
                'title' => '',
                'titleCount' => true,
            ),
    ));
}



if (!empty($pages_Array)) {
    $final_array = array_merge($final_array, $pages_Array);
}
if (!empty($pageevents_Array)) {
    $final_array = array_merge($final_array, $pageevents_Array);
}

if (!empty($recipes_Array)) {
    $final_array = array_merge($final_array, $recipes_Array);
}


return $final_array;
?>

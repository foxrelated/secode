<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.isActivate', 0);
if (empty($isActive)) {
  return;
}
$showContent_cover = array("mainPhoto" => $view->translate("Group Profile Photo"), "title" => $view->translate("Group Title"), "followButton" => $view->translate("Follow Button"), "likeButton" => $view->translate("Like Button"), "likeCount" => $view->translate("Total Likes"),"followCount" => $view->translate("Total Followers"));
$showContent_option = array("mainPhoto", "title", "followButton", "likeButton", "followCount", "likeCount");
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
	$showContent_cover['memberCount'] = $view->translate('Total Members');
	$showContent_cover['addButton'] = $view->translate('Add People Button');
	$showContent_cover['joinButton'] = $view->translate('Join Group Button');
	$showContent_option[] = 'addButton';
	$showContent_option[] = 'joinButton';
	$showContent_option[] = 'memberCount';
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Group Profile Albums'),
        'description' => $view->translate('Forms the Albums tab of your Group. It displays the photos added by you and by the Group visitors. It should be placed in the Tabbed Blocks area of the Group Profile.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.photos-sitegroup',
        'defaultParams' => array(
            'title' => $view->translate('Photos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Random Albums'),
        'description' => $view->translate('Displays random albums and photos of the Group.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.albums-sitegroup',
        'defaultParams' => array(
            'title' => $view->translate('Albums'),
            'titleCount' => '',
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Most Commented Photos'),
        'description' => $view->translate('Displays the most commented photos of the Group.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.photocomment-sitegroup',
        'defaultParams' => array(
            'title' => $view->translate('Most Commented Photos'),
            'titleCount' => '',
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Most Liked Photos'),
        'description' => $view->translate('Displays the most liked photos of the Group.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.photolike-sitegroup',
        'defaultParams' => array(
            'title' => $view->translate('Most Liked Photos'),
            'titleCount' => '',
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Photos Strip'),
        'description' => $view->translate("Displays some photos out of all the albums of a Group in a strip. Group Admin (you) can choose which photos to be shown in the strip by hiding the ones that should not be displayed. Hidden photos are replaced by new photos and so on."),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.photorecent-sitegroup',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Cover Photo and Information'),
        'description' => $view->translate('Displays the group cover photo with group profile photo, title and various action links that can be performed on the group from their Profile group (Like, Follow, etc.). You can choose various options from the Edit Settings of this widget. This widget should be placed on the Group Profile group.'),
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.group-cover-information-sitegroup',
        'defaultParams' => array(
            'title' => $view->translate('Information'),
            'titleCount' => true,
            'showContent' => $showContent_option
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => $view->translate('Select the information options that you want to be available in this block.'),
                        'multiOptions' => $showContent_cover,
                    ),
                ), 
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Enter the cover photo height (in px). (Minimum 150 px required.)'),
                        'value' => '300',
                    )
                ),             
            ),
        ),
    ),
)
?>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: templateinfoPartial.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
if( empty($modObj) ){ return; }

if( strstr($modType, "sitereview") && !empty($tempReviewObj) ){
  $listingTypeId = $tempReviewObj->listingtype_id;
  $getModId = Engine_Api::_()->suggestion()->getReviewModInfo($listingTypeId);
  $tempModInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed("sitereview_" . $getModId);
  $tempModInfo = $tempModInfo["sitereview_" . $getModId];
}else {
  $tempModInfo = $this->getModInfo($modType);
}

switch ($modType) {
  case 'friend':
  case 'user':
    // Mutual Friend Work.
    $getItemMember = '<a class="pabsolute"></a>';
    $addFriendTitle = '';
    $getMutualFriend = $this->getMutualFriend($modObj->getIdentity());
    if ($this->viewer()->getIdentity() && !empty($getMutualFriend)) {
      $getMutualFriend = @COUNT($getMutualFriend);
      $getMutualFriendTitle = $this->translate(array('%s Mutual Friend', '%s Mutual Friends', $getMutualFriend), $this->locale()->toNumber($getMutualFriend));
      $link = $this->url(array('module' => 'suggestion', 'controller' => 'index', 'action' => 'mutualfriend', 'friend_id' => $modObj->getIdentity()), 'default', true);
      $getItemMember = '<a href="javascript:void(0);" onclick = "getPopup(\'' . $link . '\');" id="mutual_friend_' . $modInfoView . '_' . $modObj->getIdentity() . '">' . $getMutualFriendTitle . '</a>';
    }

    // Add Friend Link Work.
    if (empty($defaultFriendship) && $this->viewer()->getIdentity() && !$modObj->membership()->isMember($this->viewer(), null)):

      $getFriendTitle = $this->translate('Add Friend');
      $getUserFriendDirection = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
      if (empty($getUserFriendDirection)) {
        $getFriendTitle = $this->translate('Follow');
      }

      $addFriendTitle = '
<div id = "add_' . $modDivId . '"><a class ="buttonlink icon_friend_add" href="javascript:void(0);" onclick="suggestionAddFriend(\'' . $modInfoView . '\', \'' . $modObj->getIdentity() . '\', \'confirm_' . $modDivId . '\', \'add_' . $modDivId . '\', \'' . $modDivId . '\', 0, ' . $is_middle_layout_enabled . ')">' . $getFriendTitle . '</a></div>

<div id = "confirm_' . $modDivId . '" style="display:none;" class="confirm_tip"><a href="javascript:void(0);" onclick="suggestionAddFriend(\'' . $modInfoView . '\', \'' . $modObj->getIdentity() . '\', \'add_' . $modDivId . '\',\'confirm_' . $modDivId . '\', \'' . $modDivId . '\', 1, ' . $is_middle_layout_enabled . ')">' . $this->translate('Confirm') . '</a> ' . $this->translate('or') . ' <a href="javascript:void(0);" onclick="suggestionAddFriend(\'' . $modInfoView . '\', \'' . $modObj->getIdentity() . '\',\'add_' . $modDivId . '\', \'confirm_' . $modDivId . '\', \'' . $modDivId . '\', 2, ' . $is_middle_layout_enabled . ')"> ' . $this->translate('Undo') . ' </a></div>
';
    endif;

    if (!empty($defaultFriendship)) :
      $addFriendTitle = $this->userFriendship($modObj);
    endif;



    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'), array('class' => 'item_photo'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'), array('class' => 'item_photo'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $getItemMember;
    $modInfo['view_title_link'] = $modInfo['view_title'] = $addFriendTitle;
    $modInfo['view_class_name'] = '';
    break;

  case 'messagefriend':
    $site_name = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');
    $getlink = Zend_Controller_Front::getInstance()->getBaseUrl() . 'messages/compose/to/' . $modObj->user_id;
    $link = "<a  style='background-image: url(" . $this->layout()->staticBaseUrl . "application/modules/Messages/externals/images/send.png);' class='buttonlink' href='$getlink' >" . $this->translate('Send Message') . "</a>";
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'), array('class' => 'item_photo'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'), array('class' => 'item_photo'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate("You haven't talked on %s lately", $site_name);
    $modInfo['view_title_link'] = $modInfo['view_title'] = $link;
    $modInfo['view_class_name'] = '';
    break;

  case 'friendfewfriend':
    $link = $this->url(array('module' => 'suggestion', 'controller' => 'index', 'action' => 'switch-popup', 'modName' => 'friendfewfriend', 'modContentId' => $modObj->user_id, 'modError' => 1), 'default', true);
    $getLink = '<a href="javascript:void(0);" onclick = "getPopup(\'' . $link . '\');" class ="buttonlink notification_type_friend_suggestion">' . $this->translate('Suggest Friends') . '</a>';
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'), array('class' => 'item_photo'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'), array('class' => 'item_photo'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate("Help %s find more friends.", $modObj->displayname);
    $modInfo['view_title_link'] = $modInfo['view_title'] = $getLink;
    $modInfo['view_class_name'] = '';
    break;

  case 'photo':
    if (!empty($suggestionTableObj)) {
      $getLink = $this->htmlLink(array('route' => 'user_extended', 'module' => 'user', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => 'suggestion_photo_' . $suggestionTableObj->entity_id, 'format' => 'smoothbox'), $this->translate('View Photo Suggestion'), array('class' => 'smoothbox button'));
    }
    $modInfo['mod_image_normal'] = $modInfo['mod_image'] = $this->itemPhoto($suggestionTableObj, 'thumb.profile');
    $modInfo['mod_title_full'] = $modInfo['mod_title'] = '';
    $modInfo['view_title_link'] = $modInfo['view_title'] = $getLink;
    break;

  case 'friendphoto':
    $link = $this->url(array('module' => 'suggestion', 'controller' => 'index', 'action' => 'profile-picture', 'id' => $modObj->user_id), 'default', true);
    $getLink = '<a href="javascript:void(0);" onclick = "getPopup(\'' . $link . '\');" class ="buttonlink notification_type_picture_suggestion">' . $this->translate('Suggest Picture') . '</a>';

    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'), array('class' => 'item_photo'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'), array('class' => 'item_photo'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate('%s needs a profile picture.', $modObj->getTitle());
    $modInfo['view_title_link'] = $modInfo['view_title'] = $getLink; // $link;
    $modInfo['view_class_name'] = '';
    break;

  case 'event':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate('%s | ', $this->locale()->toDateTime($modObj->starttime)) .
            $this->translate(array('%s guest', '%s guests', $modObj->membership()->getMemberCount()), $this->locale()->toNumber($modObj->membership()->getMemberCount())) .
            $this->translate(' led by %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle()));
    $getTitlte = '';
    if ($this->viewer()->getIdentity() && !$modObj->membership()->isMember($this->viewer(), null)):
      $link = $this->url(array('controller' => 'member', 'action' => 'join', 'event_id' => $modObj->getIdentity()), 'event_extended', true);
      $getTitlte = '<a href="javascript:void(0);" onclick = "getPopup(\'' . $link . '\');" class ="buttonlink icon_event_join" style="clear:both;">' . $this->translate('Join Event') . '</a>';
      $isTitle = $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'join', 'event_id' => $modObj->getIdentity()), $this->translate('Join Event'), array('class' => 'buttonlink smoothbox icon_event_join'));
    endif;
    $modInfo['view_title_link'] = $modInfo['view_title'] = $getTitlte;
    $modInfo['view_class_name'] = '';
    break;

  case 'group':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate('%s led by %s', $this->translate(array('%s member', '%s members', $modObj->membership()->getMemberCount()), $this->locale()->toNumber($modObj->membership()->getMemberCount())), $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle()));
    $getTitlte = '';
    if ($this->viewer()->getIdentity() && !$modObj->membership()->isMember($this->viewer(), null)):
      $link = $this->url(array('controller' => 'member', 'action' => 'join', 'group_id' => $modObj->getIdentity()), 'group_extended', true);
      $getTitlte = '<a href="javascript:void(0);" onclick = "getPopup(\'' . $link . '\');" class ="buttonlink icon_group_join" style="clear:both;">' . $this->translate('Join Group') . '</a>';
    endif;
    $modInfo['view_title_link'] = $modInfo['view_title'] = $getTitlte;
    $modInfo['view_class_name'] = '';
    break;

  case 'poll':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate('Posted by %s ', $this->htmlLink($modObj->getOwner(), $modObj->getOwner()->getTitle())) .
            '<div>' .
            $this->timestamp($modObj->creation_date) .
            $this->translate(array(' - %s vote', ' - %s votes', $modObj->vote_count), $this->locale()->toNumber($modObj->vote_count)) .
            $this->translate(array(' - %s view', ' - %s views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count)) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_poll_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_poll_suggestion';
    break;

  case 'album':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate('By %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '<div>' .
            $this->translate(array(' %s photo ', ' %s photos ', $modObj->count()), $this->locale()->toNumber($modObj->count())) .
            '</div><div>' .
            $this->timestamp($modObj->modified_date) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_album_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_album_suggestion';
    break;

  case 'blog':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = '<div>' .
            $this->translate(' Posted %s', $this->timestamp(strtotime($modObj->creation_date))) .
            $this->translate(' by %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_blog_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_blog_suggestion';
    break;

  case 'classified':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = '<div>' .
            $this->translate('Posted %s', $this->timestamp(strtotime($modObj->creation_date))) .
            $this->translate(' by %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_classified_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_classified_suggestion';
    break;

  case 'forum':
    $forum_name = Engine_Api::_()->getItem('forum_forum', $modObj->forum_id);
    $photo_obj = Engine_Api::_()->getItem('user', $modObj->user_id);
    $categoryTitle = Engine_Api::_()->getItem('forum_category', $forum_name->category_id)->title;
    ;
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($photo_obj->getOwner(), 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($photo_obj->getOwner(), 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            $this->translate(' in %s', $this->htmlLink($forum_name->getHref(), Engine_Api::_()->suggestion()->truncateTitle($forum_name->getTitle()), array('title' => $forum_name->getTitle()))) .
            '<div>' .
            $this->translate('%s | %s', $this->translate(array('%s Reply', '%s Replies', $modObj->post_count), $this->locale()->toNumber($modObj->post_count)), $this->translate(array('%s View', '%s Views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count))) .
            '<br/>' .
            $this->translate('Category : %s', $categoryTitle) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_forum_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_forum_suggestion';
    break;

  case 'music':
    $image = '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Music/externals/images/nophoto_playlist_thumb_icon.png" />';
    $image_normal = '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Music/externals/images/nophoto_playlist_main.png" />';
    if ($modObj->photo_id)
      $image = $image_normal = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image'] = $image;
    $modInfo['mod_image_normal'] = $image_normal;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate('Created %s by %s', $this->timestamp($modObj->creation_date), $this->htmlLink($modObj->getOwner(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_music_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_music_suggestion';
    break;

  case 'video':
    $image = '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Video/externals/images/video.png">';
    if ($modObj->photo_id)
      $image = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image'] = $image;
    $modInfo['mod_image_normal'] = $image;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            $this->translate('by %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '<div>' .
            $this->translate(array(' %s view', ' %s views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count)) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_video_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_video_suggestion';
    break;

  case 'document':
    $image = $this->htmlLink($modObj->getHref(), '<img src="' . $this->layout()->staticBaseUrl . 'application/modules/Document/externals/images/document_thumb.png" class="browse_document_thumb" />', array('title' => $modObj->document_title));
    if (!empty($modObj->thumbnail)) {
      $image = $this->htmlLink($modObj->getHref(), '<img src="' . $modObj->thumbnail . '" class="browse_document_thumb" />', array('title' => $modObj->document_title));
    }
    $modInfo['mod_image'] = $image;
    $modInfo['mod_image_normal'] = $image;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate(' Created %s by %s', $this->timestamp(strtotime($modObj->creation_date)), $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_document_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_document_suggestion';
    break;

  case 'list':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate(' Posted %s by %s', $this->timestamp(strtotime($modObj->creation_date)), $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_listing_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_listing_suggestion';
    break;

  case 'sitepage':
  case 'page':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'), array('title' => $modObj->title));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'), array('title' => $modObj->title));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate(' Posted %s by %s', $this->timestamp(strtotime($modObj->creation_date)), $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            $this->translate(array(' - %s like', ' - %s likes', $modObj->like_count), $this->locale()->toNumber($modObj->like_count)) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_page_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_page_suggestion';
    break;

  case 'sitebusiness':
  case 'business':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'), array('title' => $modObj->title));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'), array('title' => $modObj->title));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate(' Posted %s by %s', $this->timestamp(strtotime($modObj->creation_date)), $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            $this->translate(array(' - %s like', ' - %s likes', $modObj->like_count), $this->locale()->toNumber($modObj->like_count)) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_business_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_business_suggestion';
    break;
  
  case 'sitegroup':
  case 'group':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'), array('title' => $modObj->title));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'), array('title' => $modObj->title));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate(' Posted %s by %s', $this->timestamp(strtotime($modObj->creation_date)), $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            $this->translate(array(' - %s like', ' - %s likes', $modObj->like_count), $this->locale()->toNumber($modObj->like_count)) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_group_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_group_suggestion';
    break;

  case 'recipe':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate(' Posted %s by %s', $this->timestamp(strtotime($modObj->creation_date)), $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_recipe_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_recipe_suggestion';
    break;

  case 'sitepagemusic':
  case 'page_music':
    $image = '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitepagemusic/externals/images/nophoto_playlist_thumb_icon.png" />';
    $image_normal = '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitepagemusic/externals/images/nophoto_music_playlist.png" />';
    if ($modObj->photo_id)
      $image = $image_normal = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image'] = $image;
    $modInfo['mod_image_normal'] = $image_normal;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate('Created %s by %s', $this->timestamp($modObj->creation_date), $this->htmlLink($modObj->getOwner(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_page_music_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_page_music_suggestion';
    break;

  case 'sitepageoffer':
  case 'page_offer':
    $image = '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitepageoffer/externals/images/offer_thumb.png" />';
    if ($modObj->photo_id)
      $image = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image_normal'] = $modInfo['mod_image'] = $image;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate('Created %s by %s', $this->timestamp($modObj->creation_date), $this->htmlLink($modObj->getOwner(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_page_offer_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_page_offer_suggestion';
    break;

  case 'sitepagereport':
  case 'page_report':
  case 'sitepagealbum':
  case 'page_album':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate('By %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '<div>' .
            $this->translate(array(' %s photo ', ' %s photos ', $modObj->count()), $this->locale()->toNumber($modObj->count())) .
            '</div><div>' .
            $this->timestamp($modObj->modified_date) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_page_album_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_page_album_suggestion';
    break;

  case 'sitepagereview':
  case 'page_review':
    $page_obj = Engine_Api::_()->getItem('sitepage_page', $modObj->page_id);
    $modInfo['mod_image'] = $this->htmlLink($modObj->getOwner()->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getOwner()->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            $this->translate(' of %s', $this->htmlLink($page_obj->getHref(), $page_obj->getTitle())) .
            '<br />' .
            $this->translate('by ', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '<br />' .
            $this->translate(array(' %s view', ' %s views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count)) .
            '<br />';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_page_review_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_page_review_suggestion';
    break;

  case 'sitepagepoll':
  case 'page_poll':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate('Posted by %s ', $this->htmlLink($modObj->getOwner(), $modObj->getOwner()->getTitle())) .
            '<div>' .
            $this->timestamp($modObj->creation_date) .
            $this->translate(array(' - %s vote', ' - %s votes', $modObj->vote_count), $this->locale()->toNumber($modObj->vote_count)) .
            $this->translate(array(' - %s view', ' - %s views', $modObj->views), $this->locale()->toNumber($modObj->views)) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_page_poll_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_page_poll_suggestion';
    break;

  case 'sitepageevent':
  case 'page_event':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate('%s | ', $this->locale()->toDateTime($modObj->starttime)) .
            $this->translate(array('%s guest', '%s guests', $modObj->membership()->getMemberCount()), $this->locale()->toNumber($modObj->membership()->getMemberCount())) .
            $this->translate(' led by %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle()));
    $getTitlte = '';
    if ($this->viewer()->getIdentity() && !$modObj->membership()->isMember($this->viewer(), null)):
      $getTitlte = $this->htmlLink(array('route' => 'sitepageevent_specific', 'controller' => 'index', 'action' => 'join', 'event_id' => $modObj->getIdentity(), 'page_id' => $modObj->page_id), $this->translate('Join Page Event'), array('class' => 'buttonlink smoothbox icon_page event_join'));
    endif;
    $modInfo['view_title_link'] = $modInfo['view_title'] = $getTitlte;
    $modInfo['view_class_name'] = '';
    break;

  case 'sitepagedocument':
  case 'page_document':
    $image = $this->htmlLink($modObj->getHref(), '<img src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/pagedocument_thumb.png" class="browse_pagedocument_thumb" />', array('title' => $modObj->sitepagedocument_title));
    if (!empty($modObj->thumbnail)) {
      $image = $this->htmlLink($modObj->getHref(), '<img src="' . $modObj->thumbnail . '" class="browse_pagedocument_thumb" />', array('title' => $modObj->sitepagedocument_title));
    }
    $modInfo['mod_image'] = $image;
    $modInfo['mod_image_normal'] = $image;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate(' Created %s by %s', $this->timestamp(strtotime($modObj->creation_date)), $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_page_document_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_page_document_suggestion';
    break;

  case 'sitepagenote':
  case 'page_note':
    $page_obj = Engine_Api::_()->getItem('sitepage_page', $modObj->page_id);
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'));
    ;
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    ;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            $this->translate(' of %s', $this->htmlLink($page_obj->getHref(), $page_obj->getTitle())) .
            '<br />' .
            $this->translate('by ', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '<br />' .
            $this->translate(array(' %s view', ' %s views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count)) .
            '<br />';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_page_note_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_page_note_suggestion';
    break;

  case 'sitepagevideo':
  case 'page_video':
    $image = '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitepagevideo/externals/images/page video.png">';
    if ($modObj->photo_id)
      $image = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image'] = $image;
    $modInfo['mod_image_normal'] = $image;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            $this->translate('by %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '<div>' .
            $this->translate(array(' %s view', ' %s views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count)) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_page_video_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_page_video_suggestion';
    break;


  case 'sitebusinessmusic':
  case 'business_music':
    $image = '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitebusinessmusic/externals/images/nophoto_playlist_thumb_icon.png" />';
    $image_normal = '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitebusinessmusic/externals/images/nophoto_music_playlist.png" />';
    if ($modObj->photo_id)
      $image = $image_normal = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image'] = $image;
    $modInfo['mod_image_normal'] = $image_normal;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate('Created %s by %s', $this->timestamp($modObj->creation_date), $this->htmlLink($modObj->getOwner(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_business_music_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_business_music_suggestion';
    break;

  case 'sitebusinessoffer':
  case 'business_offer':
    $image = '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitebusinessoffer/externals/images/offer_thumb.png" />';
    if ($modObj->photo_id)
      $image = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image_normal'] = $modInfo['mod_image'] = $image;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate('Created %s by %s', $this->timestamp($modObj->creation_date), $this->htmlLink($modObj->getOwner(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_business_offer_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_business_offer_suggestion';
    break;

  case 'sitebusinessalbum':
  case 'business_album':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate('By %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '<div>' .
            $this->translate(array(' %s photo ', ' %s photos ', $modObj->count()), $this->locale()->toNumber($modObj->count())) .
            '</div><div>' .
            $this->timestamp($modObj->modified_date) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_business_album_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_business_album_suggestion';
    break;

  case 'sitebusinessreview':
  case 'business_review':
    $page_obj = Engine_Api::_()->getItem('sitebusiness_business', $modObj->business_id);
    $modInfo['mod_image'] = $this->htmlLink($modObj->getOwner()->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getOwner()->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            $this->translate(' of %s', $this->htmlLink($page_obj->getHref(), $page_obj->getTitle())) .
            '<br />' .
            $this->translate('by ', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '<br />' .
            $this->translate(array(' %s view', ' %s views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count)) .
            '<br />';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_business_review_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_business_review_suggestion';
    break;

  case 'sitebusinesspoll':
  case 'business_poll':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate('Posted by %s ', $this->htmlLink($modObj->getOwner(), $modObj->getOwner()->getTitle())) .
            '<div>' .
            $this->timestamp($modObj->creation_date) .
            $this->translate(array(' - %s vote', ' - %s votes', $modObj->vote_count), $this->locale()->toNumber($modObj->vote_count)) .
            $this->translate(array(' - %s view', ' - %s views', $modObj->views), $this->locale()->toNumber($modObj->views)) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_business_poll_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_business_poll_suggestion';
    break;

  case 'sitebusinessevent':
  case 'business_event':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate('%s | ', $this->locale()->toDateTime($modObj->starttime)) .
            $this->translate(array('%s guest', '%s guests', $modObj->membership()->getMemberCount()), $this->locale()->toNumber($modObj->membership()->getMemberCount())) .
            $this->translate(' led by %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle()));
    $getTitlte = '';
    if ($this->viewer()->getIdentity() && !$modObj->membership()->isMember($this->viewer(), null)):
      $getTitlte = $this->htmlLink(array('route' => 'sitebusinessevent_specific', 'controller' => 'index', 'action' => 'join', 'event_id' => $modObj->getIdentity(), 'business_id' => $modObj->business_id), $this->translate('Join Page Event'), array('class' => 'buttonlink smoothbox icon_page event_join'));
    endif;
    $modInfo['view_title_link'] = $modInfo['view_title'] = $getTitlte;
    $modInfo['view_class_name'] = '';
    break;

  case 'sitebusinessdocument':
  case 'business_document':
    $image = $this->htmlLink($modObj->getHref(), '<img src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitebusinessdocument/externals/images/sitebusinessdocument_thumb.png" class="browse_pagedocument_thumb" />', array('title' => $modObj->sitebusinessdocument_title));
    if (!empty($modObj->thumbnail)) {
      $image = $this->htmlLink($modObj->getHref(), '<img src="' . $modObj->thumbnail . '" class="browse_pagedocument_thumb" />', array('title' => $modObj->sitebusinessdocument_title));
    }
    $modInfo['mod_image'] = $image;
    $modInfo['mod_image_normal'] = $image;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate(' Created %s by %s', $this->timestamp(strtotime($modObj->creation_date)), $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_business_document_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_business_document_suggestion';
    break;

  case 'sitebusinessnote':
  case 'business_note':
    $page_obj = Engine_Api::_()->getItem('sitebusiness_business', $modObj->business_id);
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'));
    ;
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    ;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            $this->translate(' of %s', $this->htmlLink($page_obj->getHref(), $page_obj->getTitle())) .
            '<br />' .
            $this->translate('by ', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '<br />' .
            $this->translate(array(' %s view', ' %s views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count)) .
            '<br />';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_business_note_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_business_note_suggestion';
    break;

  case 'sitebusinessvideo':
  case 'business_video':
    $image = '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitebusinessvideo/externals/images/video.png">';
    if ($modObj->photo_id)
      $image = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image'] = $image;
    $modInfo['mod_image_normal'] = $image;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            $this->translate('by %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '<div>' .
            $this->translate(array(' %s view', ' %s views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count)) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_business_video_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_business_video_suggestion';
    break;
    
  case 'siteestore':
    if( is_array($modObj) ) {
    $getPhotoURL = Engine_Api::_()->suggestion()->displayPhoto($modObj['photo_id']); // $modObj->getPhotoUrl('thumb.icon');
    $magentoHref = $modObj['uri'];
    if( !empty($getPhotoURL) ) {
      $modInfo['mod_image'] = $this->htmlLink($magentoHref, '<img alt="" src="' . $getPhotoURL . '" />');
      $modInfo['mod_image_normal'] = $this->htmlLink($magentoHref, '<img alt="" src="' . $getPhotoURL . '" />');
    }else {
      $modInfo['mod_image'] = $this->htmlLink($magentoHref, '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Siteestore/externals/images/np_thumb.gif" />');
      $modInfo['mod_image_normal'] = $this->htmlLink($magentoHref, '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Siteestore/externals/images/np_thumb.gif" />');
    }

    $modInfo['mod_title'] = $this->htmlLink($magentoHref, Engine_Api::_()->suggestion()->truncateTitle($modObj['title']), array('title' => $modObj['title']));
    $modInfo['mod_title_full'] = $this->htmlLink($magentoHref, $modObj['title']);
    $modInfo['mod_item_members'] =
            '<div><b>' .
				$modObj['price'] .
				// $this->translate('Price %s', $modObj->price) . 
            '</b></div>';
    }else {
    $getPhotoURL = $modObj->getPhotoUrl('thumb.icon');
    $magentoHref = $modObj->uri;
    if( !empty($getPhotoURL) ) {
      $modInfo['mod_image'] = $this->htmlLink($magentoHref, $this->itemPhoto($modObj, 'thumb.icon'));
      $modInfo['mod_image_normal'] = $this->htmlLink($magentoHref, $this->itemPhoto($modObj, 'thumb.profile'));
    }else {
      $modInfo['mod_image'] = $this->htmlLink($magentoHref, '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Siteestore/externals/images/np_thumb.gif" />');
      $modInfo['mod_image_normal'] = $this->htmlLink($magentoHref, '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Siteestore/externals/images/np_thumb.gif" />');
    }

    $modInfo['mod_title'] = $this->htmlLink($magentoHref, Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($magentoHref, $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div><b>' .
				$modObj->price .
				// $this->translate('Price %s', $modObj->price) . 
            '</b></div>';
    }
    $modInfo['view_title'] = $this->htmlLink($magentoHref, $tempModInfo['buttonLabel'], array('class' => 'buttonlink item_icon_siteestore'));
    $modInfo['view_title_link'] = $this->htmlLink($magentoHref, $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'item_icon_siteestore';
    break;
    
  case 'sitestore':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'), array('title' => $modObj->title));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'), array('title' => $modObj->title));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate(' Created %s by %s', $this->timestamp(strtotime($modObj->creation_date)), $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            $this->translate(array(' - %s like', ' - %s likes', $modObj->like_count), $this->locale()->toNumber($modObj->like_count)) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_sitestore_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_sitestore_suggestion';
    break;
    
  case 'sitestoreproduct':
    $getPhotoURL = $modObj->getPhotoUrl('thumb.icon');
    $showAddToCart = !empty($view_type)? 0: 1;
    if( !empty($getPhotoURL) ) {
      $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'));
      $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    }else {
      $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.icon'));
      $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.profile'));
    }

    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div> ' .
              $this->getProductInfo($modObj, 1, 'list_view', $showAddToCart, 1, false) . 
            //$this->translate('Created by %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_sitestoreproduct_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_sitestoreproduct_suggestion';
    break;
    
  case 'siteevent':
    $showAddToCart = !empty($view_type)? 0: 1;
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));

    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle()); 
//    $getStartEndDate = $modObj->getStartEndDate();
    $getStartEndDate = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getEventDate($modObj->event_id, null);
    $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');
    
    $getEventHostName = $modObj->getHostName(true);
    $eventLocations = $modObj->location;
    $tempModItemMember = '';
    
    if( !empty($getEventHostName) ) {
      $tempModItemMember .= '<div class="siteevent_listings_stats"><i class="siteevent_icon_strip siteevent_icon siteevent_icon_host" title="Host"></i>
    <div class="o_hidden">
      <b>'. $this->translate('%s', $getEventHostName) .'</b>
    </div>
  </div>';
    }
    
    if( !empty($eventLocations) ) {
      $tempLocationAddress = $this->url(array("id" => $modObj->event_id, 'resouce_type' => 'siteevent_event'), 'seaocore_viewmap', true);
      $tempModItemMember .= '<div class="siteevent_listings_stats"><i class="siteevent_icon_strip siteevent_icon siteevent_icon_location" title="Location"></i>
    <div class="o_hidden f_small">
        '.
              $this->htmlLink('javascript:void(0);', $eventLocations, array('onclick' =>  'Smoothbox.open("'.$tempLocationAddress.'")'))
              .'      
    </div>
  </div>';
    }
    
    if( !empty($getStartEndDate['starttime']) ) {
      $tempModItemMember .= '<div class="siteevent_listings_stats"><i class="siteevent_icon_strip siteevent_icon siteevent_icon_time" title="Start Date"></i>
    <div class="o_hidden f_small">
      ' . $this->locale()->toEventDateTime($getStartEndDate['starttime'], array('size' => $datetimeFormat)) . '
    </div>
  </div>';
    }
    
    
    $modInfo['mod_item_members'] = $tempModItemMember;         
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_siteevent_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_siteevent_suggestion';
    break;
    
  default:
    $getPhotoURL = $modObj->getPhotoUrl('thumb.icon');
    if( !empty($getPhotoURL) ) {
      $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'));
      $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    }else {
      $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.icon'));
      $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.profile'));
    }

    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div> ' .
            $this->translate('Created by %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink icon_int_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'icon_int_suggestion';
    break;

  case 'sitegroupmusic':
  case 'group_music':
    $image = '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitegroupmusic/externals/images/nophoto_playlist_thumb_icon.png" />';
    $image_normal = '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitegroupmusic/externals/images/nophoto_music_playlist.png" />';
    if ($modObj->photo_id)
      $image = $image_normal = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image'] = $image;
    $modInfo['mod_image_normal'] = $image_normal;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate('Created %s by %s', $this->timestamp($modObj->creation_date), $this->htmlLink($modObj->getOwner(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_group_music_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_group_music_suggestion';
    break;

  case 'sitegroupoffer':
  case 'group_offer':
    $image = '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitegroupoffer/externals/images/offer_thumb.png" />';
    if ($modObj->photo_id)
      $image = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image_normal'] = $modInfo['mod_image'] = $image;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate('Created %s by %s', $this->timestamp($modObj->creation_date), $this->htmlLink($modObj->getOwner(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_group_offer_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_group_offer_suggestion';
    break;

  case 'sitegroupalbum':
  case 'group_album':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate('By %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '<div>' .
            $this->translate(array(' %s photo ', ' %s photos ', $modObj->count()), $this->locale()->toNumber($modObj->count())) .
            '</div><div>' .
            $this->timestamp($modObj->modified_date) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_group_album_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_group_album_suggestion';
    break;

  case 'sitegroupreview':
  case 'group_review':
    $page_obj = Engine_Api::_()->getItem('sitegroup_group', $modObj->group_id);
    $modInfo['mod_image'] = $this->htmlLink($modObj->getOwner()->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getOwner()->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            $this->translate(' of %s', $this->htmlLink($page_obj->getHref(), $page_obj->getTitle())) .
            '<br />' .
            $this->translate('by ', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '<br />' .
            $this->translate(array(' %s view', ' %s views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count)) .
            '<br />';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_group_review_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_group_review_suggestion';
    break;

  case 'sitegrouppoll':
  case 'group_poll':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.icon'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj->getOwner(), 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate('Posted by %s ', $this->htmlLink($modObj->getOwner(), $modObj->getOwner()->getTitle())) .
            '<div>' .
            $this->timestamp($modObj->creation_date) .
            $this->translate(array(' - %s vote', ' - %s votes', $modObj->vote_count), $this->locale()->toNumber($modObj->vote_count)) .
            $this->translate(array(' - %s view', ' - %s views', $modObj->views), $this->locale()->toNumber($modObj->views)) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_group_poll_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_group_poll_suggestion';
    break;

  case 'sitegroupevent':
  case 'group_event':
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] = $this->translate('%s | ', $this->locale()->toDateTime($modObj->starttime)) .
            $this->translate(array('%s guest', '%s guests', $modObj->membership()->getMemberCount()), $this->locale()->toNumber($modObj->membership()->getMemberCount())) .
            $this->translate(' led by %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle()));
    $getTitlte = '';
    if ($this->viewer()->getIdentity() && !$modObj->membership()->isMember($this->viewer(), null)):
      $getTitlte = $this->htmlLink(array('route' => 'sitegroupevent_specific', 'controller' => 'index', 'action' => 'join', 'event_id' => $modObj->getIdentity(), 'group_id' => $modObj->group_id), $this->translate('Join Page Event'), array('class' => 'buttonlink smoothbox icon_page event_join'));
    endif;
    $modInfo['view_title_link'] = $modInfo['view_title'] = $getTitlte;
    $modInfo['view_class_name'] = '';
    break;

  case 'sitegroupdocument':
  case 'group_document':
    $image = $this->htmlLink($modObj->getHref(), '<img src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitegroupdocument/externals/images/sitegroupdocument_thumb.png" class="browse_pagedocument_thumb" />', array('title' => $modObj->sitegroupdocument_title));
    if (!empty($modObj->thumbnail)) {
      $image = $this->htmlLink($modObj->getHref(), '<img src="' . $modObj->thumbnail . '" class="browse_pagedocument_thumb" />', array('title' => $modObj->sitegroupdocument_title));
    }
    $modInfo['mod_image'] = $image;
    $modInfo['mod_image_normal'] = $image;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            '<div>' .
            $this->translate(' Created %s by %s', $this->timestamp(strtotime($modObj->creation_date)), $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_group_document_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_group_document_suggestion';
    break;

  case 'sitegroupnote':
  case 'group_note':
    $page_obj = Engine_Api::_()->getItem('sitegroup_group', $modObj->group_id);
    $modInfo['mod_image'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.icon'));
    ;
    $modInfo['mod_image_normal'] = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.profile'));
    ;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            $this->translate(' of %s', $this->htmlLink($page_obj->getHref(), $page_obj->getTitle())) .
            '<br />' .
            $this->translate('by ', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '<br />' .
            $this->translate(array(' %s view', ' %s views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count)) .
            '<br />';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_group_note_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_group_note_suggestion';
    break;

  case 'sitegroupvideo':
  case 'group_video':
    $image = '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitegroupvideo/externals/images/video.png">';
    if ($modObj->photo_id)
      $image = $this->htmlLink($modObj->getHref(), $this->itemPhoto($modObj, 'thumb.normal'));
    $modInfo['mod_image'] = $image;
    $modInfo['mod_image_normal'] = $image;
    $modInfo['mod_title'] = $this->htmlLink($modObj->getHref(), Engine_Api::_()->suggestion()->truncateTitle($modObj->getTitle()), array('title' => $modObj->getTitle()));
    $modInfo['mod_title_full'] = $this->htmlLink($modObj->getHref(), $modObj->getTitle());
    $modInfo['mod_item_members'] =
            $this->translate('by %s', $this->htmlLink($modObj->getOwner()->getHref(), $modObj->getOwner()->getTitle())) .
            '<div>' .
            $this->translate(array(' %s view', ' %s views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count)) .
            '</div>';
    $modInfo['view_title'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink notification_type_group_video_suggestion'));
    $modInfo['view_title_link'] = $this->htmlLink($modObj->getHref(), $tempModInfo['buttonLabel'], array('class' => 'buttonlink'));
    $modInfo['view_class_name'] = 'notification_type_group_video_suggestion';
    break;
}
?>

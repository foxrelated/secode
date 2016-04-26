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
if (empty($modObj)) {
    return;
}

if (strstr($modType, "sitereview") && !empty($tempReviewObj)) { 
    $listingTypeId = $tempReviewObj->listingtype_id;
    $getModId = Engine_Api::_()->suggestion()->getReviewModInfo($listingTypeId);
    $tempModInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed("sitereview_" . $getModId);
    $tempModInfo = $tempModInfo["sitereview_" . $getModId];
} else {
    $tempModInfo = $this->getModInfo($modType);
}

switch ($modType) {
    case 'event':
        $modInfo['mod_item_members'] = $this->translate(array('%s guest', '%s guests', $modObj->membership()->getMemberCount()), $this->locale()->toNumber($modObj->membership()->getMemberCount())) .
                $this->translate(' led by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b>
                <p>' . $this->translate($this->locale()->toDateTime($modObj->starttime)) . '</p>';

        $modInfo['view_title'] = 'suggestion_icon notification_type_event_suggestion';
        break;

    case 'group':
        $modInfo['mod_item_members'] = $this->translate(array('%s member', '%s members', $modObj->membership()->getMemberCount()), $this->locale()->toNumber($modObj->membership()->getMemberCount())) . $this->translate(' led by ') .
                '<b>' . $modObj->getOwner()->getTitle() . '</b>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_group_suggestion';
        break;

    case 'poll':
        $modInfo['mod_item_members'] = $this->translate('Posted by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b>
                <p>' .
                $this->timestamp($modObj->creation_date) .
                $this->translate(array(' - %s vote', ' - %s votes', $modObj->vote_count), $this->locale()->toNumber($modObj->vote_count)) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_poll_suggestion';
        break;

    case 'album':
        $modInfo['mod_item_members'] = $this->translate('By ') . '<b>' . $modObj->getOwner()->getTitle() . '</b>
                 <p>' .
                $this->timestamp($modObj->modified_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_album_suggestion';
        break;

    case 'blog':
        $modInfo['mod_item_members'] = $this->translate(' Posted by ') . '<b>' . $modObj->getOwner()->getTitle() .
                '</b><p>' . $this->timestamp(strtotime($modObj->creation_date)) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_blog_suggestion';
        break;
//
//    case 'classified':
//        $modInfo['mod_item_members'] = $this->translate(' Posted by ') . '<b>' . $modObj->getOwner()->getTitle() .
//                '</b><p>' . $this->timestamp(strtotime($modObj->creation_date)) .
//                '</p>';
//        $modInfo['view_title'] = 'suggestion_icon notification_type_classified_suggestion';
//        break;

    case 'forum':
        $forum_name = Engine_Api::_()->getItem('forum_forum', $modObj->forum_id);
        $modInfo['mod_item_members'] = $this->translate('in ') . '<b>' . Engine_Api::_()->suggestion()->truncateTitle($forum_name->getTitle()) . '</b>' .
                '<p>' .
                $this->translate(array('%s Reply', '%s Replies', $modObj->post_count), $this->locale()->toNumber($modObj->post_count)) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_forum_suggestion';
        break;

    case 'music':
        $modInfo['mod_item_members'] = $this->translate('Created by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_music_suggestion';
        break;

    case 'video':
        $modInfo['mod_item_members'] = $this->translate('by ') . '<b>' . $modObj->getOwner()->getTitle() .
                '</b><p>' .
                $this->translate(array(' %s view', ' %s views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count)) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_video_suggestion';
        break;

    case 'document':
        $modInfo['mod_item_members'] = $this->translate('Created by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_document_suggestion';

    case 'list':
        $modInfo['mod_item_members'] = $this->translate('Posted by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_listing_suggestion';
        break;

    case 'sitepage':
    case 'page':
        $modInfo['mod_item_members'] = $this->translate('Posted by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_page_suggestion';
        break;

    case 'sitebusiness':
    case 'business':
        $modInfo['mod_item_members'] = $this->translate('Posted by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_business_suggestion';
        break;

    case 'sitegroup':
    case 'group':
        $modInfo['mod_item_members'] = $this->translate('Posted by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_group_suggestion';
        break;

    case 'recipe':
        $modInfo['mod_item_members'] = $this->translate('Posted by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_recipe_suggestion';
        break;

    case 'sitepagemusic':
    case 'page_music':
        $modInfo['mod_item_members'] = $this->translate('Created by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_page_music_suggestion';
        break;

    case 'sitepageoffer':
    case 'page_offer':
        $modInfo['mod_item_members'] = $this->translate('Created by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_page_offer_suggestion';
        break;

    case 'sitepagereport':
    case 'page_report':
    case 'sitepagealbum':
    case 'page_album':
        $modInfo['mod_item_members'] = $this->translate('By ') . '<b>' . $modObj->getOwner()->getTitle() . '</b>
                 <p>' .
                $this->timestamp($modObj->modified_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_page_album_suggestion';
        break;

    case 'sitepagereview':
    case 'page_review':
        $page_obj = Engine_Api::_()->getItem('sitepage_page', $modObj->page_id);
        $modInfo['mod_item_members'] = $this->translate(' of ') . '<b>' . $page_obj->getTitle() .
                '</b><p>' .
                $this->translate('by ') . '<b>' . $modObj->getOwner()->getTitle() .
                '</b></p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_page_review_suggestion';
        break;

    case 'sitepagepoll':
    case 'page_poll':
        $modInfo['mod_item_members'] = $this->translate('Posted by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b>
                <p>' .
                $this->timestamp($modObj->creation_date) .
                $this->translate(array(' - %s vote', ' - %s votes', $modObj->vote_count), $this->locale()->toNumber($modObj->vote_count)) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_page_poll_suggestion';
        break;

    case 'sitepageevent':
    case 'page_event':
        $modInfo['mod_item_members'] = $this->translate(array('%s guest', '%s guests', $modObj->membership()->getMemberCount()), $this->locale()->toNumber($modObj->membership()->getMemberCount())) .
                $this->translate(' led by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b>
                <p>' . $this->translate($this->locale()->toDateTime($modObj->starttime)) . '</p>';
        $getTitlte = '';
        if ($this->viewer()->getIdentity() && !$modObj->membership()->isMember($this->viewer(), null)):
            $getTitlte = $this->htmlLink(array('route' => 'sitepageevent_specific', 'controller' => 'index', 'action' => 'join', 'event_id' => $modObj->getIdentity(), 'page_id' => $modObj->page_id), $this->translate('Join Page Event'), array('class' => 'suggestion_icon smoothbox icon_page event_join'));
        endif;
        $modInfo['view_title'] = 'suggestion_icon notification_type_event_suggestion';
        break;

    case 'sitepagedocument':
    case 'page_document':
        $modInfo['mod_item_members'] = $this->translate('Created by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_page_document_suggestion';
        break;

    case 'sitepagenote':
    case 'page_note':
        $page_obj = Engine_Api::_()->getItem('sitepage_page', $modObj->page_id);
        $modInfo['mod_item_members'] = $this->translate(' of ') . '<b>' . $page_obj->getTitle() .
                '</b><p>' .
                $this->translate('by ') . '<b>' . $modObj->getOwner()->getTitle() .
                '</b></p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_page_note_suggestion';
        break;

    case 'sitepagevideo':
    case 'page_video':
        $modInfo['mod_item_members'] = $this->translate('by ') . '<b>' . $modObj->getOwner()->getTitle() .
                '</b><p>' .
                $this->translate(array(' %s view', ' %s views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count)) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_page_video_suggestion';
        break;


    case 'sitebusinessmusic':
    case 'business_music':
        $modInfo['mod_item_members'] = $this->translate('Created by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_business_music_suggestion';
        break;

    case 'sitebusinessoffer':
    case 'business_offer':
        $modInfo['mod_item_members'] = $this->translate('Created by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_business_offer_suggestion';
        break;

    case 'sitebusinessalbum':
    case 'business_album':
        $modInfo['mod_item_members'] = $this->translate('By ') . '<b>' . $modObj->getOwner()->getTitle() . '</b>
                 <p>' .
                $this->timestamp($modObj->modified_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_business_album_suggestion';
        break;

    case 'sitebusinessreview':
    case 'business_review':
        $page_obj = Engine_Api::_()->getItem('sitebusiness_business', $modObj->business_id);
        $modInfo['mod_item_members'] = $this->translate('of ') . '<b>' . $page_obj->getTitle() .
                '</b><p>' .
                $this->translate('by ') . '<b>' . $modObj->getOwner()->getTitle() .
                '</b></p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_business_review_suggestion';
        break;

    case 'sitebusinesspoll':
    case 'business_poll':
        $modInfo['mod_item_members'] = $this->translate('Posted by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b>
                <p>' .
                $this->timestamp($modObj->creation_date) .
                $this->translate(array(' - %s vote', ' - %s votes', $modObj->vote_count), $this->locale()->toNumber($modObj->vote_count)) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_business_poll_suggestion';
        break;

    case 'sitebusinessevent':
    case 'business_event':
        $modInfo['mod_item_members'] = $this->translate(array('%s guest', '%s guests', $modObj->membership()->getMemberCount()), $this->locale()->toNumber($modObj->membership()->getMemberCount())) .
                $this->translate(' led by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b>
                <p>' . $this->translate($this->locale()->toDateTime($modObj->starttime)) . '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_event_suggestion';
        break;

    case 'sitebusinessdocument':
    case 'business_document':
        $modInfo['mod_item_members'] = $this->translate('Created by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_business_document_suggestion';
        break;

    case 'sitebusinessnote':
    case 'business_note':
        $page_obj = Engine_Api::_()->getItem('sitebusiness_business', $modObj->business_id);
        $modInfo['mod_item_members'] = $this->translate(' of ') . '<b>' . $page_obj->getTitle() .
                '</b><p>' .
                $this->translate('by ') . '<b>' . $modObj->getOwner()->getTitle() .
                '</b></p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_business_note_suggestion';
        break;

    case 'sitebusinessvideo':
    case 'business_video':
        $modInfo['mod_item_members'] = $this->translate('by ') . '<b>' . $modObj->getOwner()->getTitle() .
                '</b><p>' .
                $this->translate(array(' %s view', ' %s views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count)) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_business_video_suggestion';
        break;
    default: 
        $modInfo['mod_item_members'] =$this->translate('Created by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b>';
        $modInfo['view_title'] = 'suggestion_icon icon_int_suggestion';
        break;

    case 'sitegroupmusic':
    case 'group_music':
        $modInfo['mod_item_members'] = $this->translate('Created by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_group_music_suggestion';
        break;

    case 'sitegroupoffer':
    case 'group_offer':
        $modInfo['mod_item_members'] = $this->translate('Created by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_group_offer_suggestion';
        break;

    case 'sitegroupalbum':
    case 'group_album':
        $modInfo['mod_item_members'] = $this->translate('By ') . '<b>' . $modObj->getOwner()->getTitle() . '</b>
                 <p>' .
                $this->timestamp($modObj->modified_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_group_album_suggestion';
        break;

    case 'sitegroupreview':
    case 'group_review':
        $page_obj = Engine_Api::_()->getItem('sitegroup_group', $modObj->group_id);
        $modInfo['mod_item_members'] = $this->translate(' of ') . '<b>' . $page_obj->getTitle() .
                '</b><p>' .
                $this->translate('by ') . '<b>' . $modObj->getOwner()->getTitle() .
                '</b></p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_group_review_suggestion';
        break;

    case 'sitegrouppoll':
    case 'group_poll':
        $modInfo['mod_item_members'] = $this->translate('Posted by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b>
                <p>' .
                $this->timestamp($modObj->creation_date) .
                $this->translate(array(' - %s vote', ' - %s votes', $modObj->vote_count), $this->locale()->toNumber($modObj->vote_count)) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_group_poll_suggestion';
        break;

    case 'sitegroupevent':
    case 'group_event':
        $modInfo['mod_item_members'] = $this->translate(array('%s guest', '%s guests', $modObj->membership()->getMemberCount()), $this->locale()->toNumber($modObj->membership()->getMemberCount())) .
                $this->translate(' led by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b>
                <p>' . $this->translate($this->locale()->toDateTime($modObj->starttime)) . '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_event_suggestion';
        break;

    case 'sitegroupdocument':
    case 'group_document':
        $modInfo['mod_item_members'] = $this->translate('Created by ') . '<b>' . $modObj->getOwner()->getTitle() . '</b><p>' .
                $this->timestamp($modObj->creation_date) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_group_document_suggestion';
        break;

    case 'sitegroupnote':
    case 'group_note':
        $page_obj = Engine_Api::_()->getItem('sitegroup_group', $modObj->group_id);
        $modInfo['mod_item_members'] = $this->translate(' of ') . '<b>' . $page_obj->getTitle() .
                '</b><p>' .
                $this->translate('by ') . '<b>' . $modObj->getOwner()->getTitle() .
                '</b></p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_group_note_suggestion';
        break;

    case 'sitegroupvideo':
    case 'group_video':
        $modInfo['mod_item_members'] = $this->translate('by ') . '<b>' . $modObj->getOwner()->getTitle() .
                '</b><p>' .
                $this->translate(array(' %s view', ' %s views', $modObj->view_count), $this->locale()->toNumber($modObj->view_count)) .
                '</p>';
        $modInfo['view_title'] = 'suggestion_icon notification_type_group_video_suggestion';
        break;
      
  case 'siteevent':    
    $getStartEndDate = $modObj->getStartEndDate();
    $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');
    
    $getEventHostName = $modObj->getHostName(true);
    $eventLocations = $modObj->location;
    $tempModItemMember = '';
    
    if( !empty($getEventHostName) ) {
      $tempModItemMember .= '<p><b>'. $this->translate('%s', $getEventHostName) .'</b></p>';
    }
    
    if( !empty($eventLocations) ) {
      $tempModItemMember .= '<p>' . $eventLocations. '</p>';
    }
    
    if( !empty($getStartEndDate['starttime']) ) {
      $tempModItemMember .= '<p>' . $this->locale()->toDateTime($getStartEndDate['starttime'], array('size' => $datetimeFormat)) . '</p>';
    }
    
    
    $modInfo['mod_item_members'] = $tempModItemMember;         
    $modInfo['view_title'] = 'suggestion_icon notification_type_siteevent_suggestion'; 
    break;
}
?>

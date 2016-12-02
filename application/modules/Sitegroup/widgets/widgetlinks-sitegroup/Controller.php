<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_WidgetlinksSitegroupController extends Engine_Content_Widget_Abstract {

    //ACTION FOR GETTING THE LINKS OF THE WIDGETS ON GROUP PROFILE GROUP (MEANS WITHOUT TAB LINKS)
    public function indexAction() {

        //DON'T RENDER IF SUNJECT IS NOT THERE
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        //GET SITEGROUP SUBJECT
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('sitegroup_group');

        //GET GROUP ID 
        $group_id = $subject->group_id;

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'view');
        if (empty($isManageAdmin)) {
            return $this->setNoRender();
        }
        //END MANAGE-ADMIN CHECK
        //GET CORE CONTENT TABLE
        $contentTable = Engine_Api::_()->getDbtable('content', 'core');

        //GET CORE GROUPES TABLE
        $groupTable = Engine_Api::_()->getDbtable('pages', 'core');

        //SELECT GROUP
        $selectGroup = $groupTable->select()->from($groupTable->info('name'), array('page_id'))->where('name =?', 'sitegroup_index_view')->limit(1);

        //GET GROUP INFO
        $groupInfo = $groupTable->fetchRow($selectGroup);

        //HOW MAY LINK SHOULD BE SHOW IN THE WIDGET LINK BEFORE MORE LINK
        $this->view->linklimit = $linklimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.showmore', 8);

        if (!empty($groupInfo)) {
            $selectContent = $contentTable->select()->from($contentTable->info('name'), array('page_id'))->where('name =?', 'core.container-tabs')->where('page_id =?', $groupInfo->page_id)->limit(1);
            $contentinfo = $contentTable->fetchRow($selectContent);
            if (!empty($contentinfo) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0)) {
                return $this->setNoRender();
            }
        }
        $resource_type = "";
        //GET TAB ID
        $this->view->tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0)) {
            $groupAdminContentTable = Engine_Api::_()->getDbtable('content', 'core');
            $selectGroupAdmin = $groupAdminContentTable->select()
                    ->from($groupAdminContentTable->info('name'), array('content_id', 'params', 'name'))
                    ->where('page_id =?', $groupInfo->page_id)
                    ->where("name IN ('sitegroup.overview-sitegroup', 'sitegroup.photos-sitegroup', 'sitegroup.discussion-sitegroup', 'sitegroupnote.profile-sitegroupnotes', 'sitegrouppoll.profile-sitegrouppolls', 'sitegroupevent.profile-sitegroupevents', 'sitegroupvideo.profile-sitegroupvideos', 'sitegroupoffer.profile-sitegroupoffers', 'sitegroupreview.profile-sitegroupreviews', 'sitegroupdocument.profile-sitegroupdocuments', 'sitegroupform.sitegroup-viewform','sitegroup.info-sitegroup', 'seaocore.feed', 'activity.feed','sitegroup.location-sitegroup', 'core.profile-links', 'sitegroupmusic.profile-sitegroupmusic', 'sitegroupintegration.profile-items','sitegrouptwitter.feeds-sitegrouptwitter', 'sitegroupmember.profile-sitegroupmembers', 'advancedactivity.home-feeds', 'siteevent-contenttype-events', 'sitevideo-contenttype-videos')");
            $groupAdminresult = $groupAdminContentTable->fetchAll($selectGroupAdmin);
            $contentWigentLinks = array();
            if (!empty($groupAdminresult)) {
                foreach ($groupAdminresult as $key => $value) {
                    if (isset($value->params['resource_type'])) {
                        $resource_type = $value->params['resource_type'];

                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration') && strstr($resource_type, 'sitereview_listing') && !empty($resource_type)) {
                            $pieces = explode("_", $resource_type);
                            $listingTypeId = $pieces[2];
                            $count = Engine_Api::_()->getDbtable('contents', 'sitegroupintegration')->getCountResults('sitereview_listing', $listingTypeId);
                            if ($count == 0)
                                continue;
                        }
                    }
                    $content = $this->getContentName($value->name, $value->params['title'], $resource_type);
                    if (!empty($content)) {
                        $contentWigentLinks[$key]['content_id'] = $value->content_id;
                        $contentWigentLinks[$key]['content_name'] = $content[0];
                        $contentWigentLinks[$key]['content_class'] = $content[1];
                        if (isset($content[2]))
                            $contentWigentLinks[$key]['content_resource'] = $content[2];
                    }
                }

                $this->view->contentWigentLinks = $contentWigentLinks;
            }
        } else {
            $row = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup')->getContentGroupId($group_id);
            if (!empty($row)) {
                $contentgroup_id = $row->contentgroup_id;
                $groupAdminresult = Engine_Api::_()->getDbtable('content', 'sitegroup')->getContents($contentgroup_id);
            } else {
                $contentgroup_id = Engine_Api::_()->sitegroup()->getWidgetizedGroup()->page_id;
                $groupAdminresult = Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->getContents($contentgroup_id);
            }


            $contentWigentLinks = array();
            if (!empty($groupAdminresult)) {
                foreach ($groupAdminresult as $key => $value) {
                    if (isset($value->params['resource_type'])) {
                        $resource_type = $value->params['resource_type'];
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration') && strstr($resource_type, 'sitereview_listing') && !empty($resource_type)) {
                            $pieces = explode("_", $resource_type);
                            $listingTypeId = $pieces[2];
                            $count = Engine_Api::_()->getDbtable('contents', 'sitegroupintegration')->getCountResults('sitereview_listing', $listingTypeId);
                            if ($count == 0)
                                continue;
                        }
                    }
                    $content = $this->getContentName($value->name, $value->params['title'], $resource_type);
                    if (!empty($content)) {
                        if (isset($value->content_id)) {
                            $contentWigentLinks[$key]['content_id'] = $value->content_id;
                        } else {
                            $contentWigentLinks[$key]['content_id'] = $value->admincontent_id;
                        }

                        $contentWigentLinks[$key]['content_name'] = $content[0];
                        $contentWigentLinks[$key]['content_class'] = $content[1];
                        if (isset($content[2]))
                            $contentWigentLinks[$key]['content_resource'] = $content[2];
                    }
                }
                $this->view->contentWigentLinks = $contentWigentLinks;
            }
        }
    }

    public function getContentName($name, $widgettitle, $resource_type) {
        $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
        $group_id = $sitegroup->group_id;
        $can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        $content_array = array();

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = 0;
        }
        switch ($name) {
            case 'advancedactivity.home-feeds':
                $content_array = array($widgettitle, 'icon_sitegroup_update');
                break;
            case 'seaocore.feed':
                $content_array = array($widgettitle, 'icon_sitegroup_update');
                break;
            case 'activity.feed':
                $content_array = array($widgettitle, 'icon_sitegroup_update');
                break;
            case 'sitegroup.info-sitegroup':
                $content_array = array($widgettitle, 'icon_sitegroup_info');
                break;
            case 'core.profile-links':
                $linkTable = Engine_Api::_()->getDbtable('links', 'core');
                $linkTableresult = $linkTable->fetchAll($linkTable->select()->where('parent_id =?', $group_id))->toarray();
                if (!empty($linkTableresult)) {
                    $content_array = array($widgettitle, 'icon_sitegroup_group_link');
                }
                break;
            case 'sitegroup.photos-sitegroup':
                $enable_albums = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
                if ($enable_albums) {

                    //TOTAL ALBUMS
                    $albumCount = Engine_Api::_()->sitegroup()->getTotalCount($group_id, 'sitegroup', 'albums');
                    $photoCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');
                    if (empty($photoCreate) && empty($albumCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupalbum")) {
                            break;
                        }
                    } else {
                        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'spcreate');
                        if (empty($isGroupOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_sitegroup_photo_view');
                }
                break;
            case 'sitegroup.discussion-sitegroup':
                $enable_discussions = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion');
                if ($enable_discussions) {

                    //TOTAL TOPICS
                    $discussionsCount = Engine_Api::_()->sitegroup()->getTotalCount($group_id, 'sitegroup', 'topics');
                    $topicComment = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sdicreate');
                    if (empty($topicComment) && empty($discussionsCount)) {
                        break;
                    }

                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupdiscussion")) {
                            break;
                        }
                    } else {
                        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sdicreate');
                        if (empty($isGroupOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_sitegroups_discussion');
                }
                break;
            case 'sitegroupnote.profile-sitegroupnotes':
                $enable_notes = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote');
                if ($enable_notes) {
                    //TOTAL NOTES
                    $notesCount = Engine_Api::_()->sitegroup()->getTotalCount($group_id, 'sitegroupnote', 'notes');
                    $noteCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sncreate');
                    if (empty($noteCreate) && empty($notesCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupnote")) {
                            break;
                        }
                    } else {
                        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sncreate');
                        if (empty($isGroupOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_sitegroupnote_note');
                }
                break;
            case 'sitegroupevent.profile-sitegroupevents':
                $enable_events = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');
                if ($enable_events) {
                    //TOTAL EVENTS
                    $eventCount = Engine_Api::_()->sitegroup()->getTotalCount($group_id, 'sitegroupevent', 'events');
                    $eventCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'secreate');
                    if (empty($eventCreate) && empty($eventCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupevent")) {
                            break;
                        }
                    } else {
                        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'secreate');
                        if (empty($isGroupOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_sitegroupevent');
                }
                break;
            case 'siteevent.contenttype-events':
                $enable_events = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');
                if ($enable_events) {
                    $eventCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'siteevent', 'events');
                    $eventCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'secreate');
                    if (empty($eventCreate) && empty($eventCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupevent")) {
                            break;
                        }
                    } else {
                        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'secreate');
                        if (empty($isGroupOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_sitegroupevent');
                }
                break;
            case 'sitegrouppoll.profile-sitegrouppolls':
                $enable_polls = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll');
                if ($enable_polls) {
                    //TOTAL POLLS
                    $pollCount = Engine_Api::_()->sitegroup()->getTotalCount($group_id, 'sitegrouppoll', 'polls');
                    $pollCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'splcreate');
                    if (empty($pollCreate) && empty($pollCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegrouppoll")) {
                            break;
                        }
                    } else {
                        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'splcreate');
                        if (empty($isGroupOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'item_icon_sitegrouppoll');
                }
                break;
            case 'sitegroupvideo.profile-sitegroupvideos':
                $enable_videos = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo');
                if ($enable_videos) {
                    //TOTAL VIDEOS
                    $videoCount = Engine_Api::_()->sitegroup()->getTotalCount($group_id, 'sitegroupvideo', 'videos');
                    $videoCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'svcreate');
                    if (empty($videoCreate) && empty($videoCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupvideo")) {
                            break;
                        }
                    } else {
                        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'svcreate');
                        if (empty($isGroupOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_type_sitegroupvideo');
                }
                break;
            case 'sitegroupdocument.profile-sitegroupdocuments':
                $enable_documents = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument');
                if ($enable_documents) {
                    //TOTAL DOCUMENTS
                    $documentCount = Engine_Api::_()->sitegroup()->getTotalCount($group_id, 'sitegroupdocument', 'documents');
                    $documentCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sdcreate');
                    if (empty($documentCreate) && empty($documentCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupdocument")) {
                            break;
                        }
                    } else {
                        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sdcreate');
                        if (empty($isGroupOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'item_icon_sitegroupdocument_detail');
                }
                break;
            case 'document.contenttype-documents':
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupdocument") == 1) {
                            $flag = true;
                        }
                    } else {
                        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sdcreate');
                        if (!empty($isGroupOwnerAllow)) {
                            $flag = true;
                        }
                    }
                    //TOTAL DOCUMENTS
                    $documentCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'document', 'documents');
                    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sdcreate');
                    if (!empty($isManageAdmin) || !empty($documentCount)) {
                        $flag = true;
                    } else {
                        $flag = false;
                    }
                }
                break;
            case 'sitevideo.contenttype-videos':
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupvideo") == 1) {
                            $flag = true;
                        }
                    } else {
                        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'svcreate');
                        if (!empty($isGroupOwnerAllow)) {
                            $flag = true;
                        }
                    }
                    //TOTAL VIDEOS
                    $videoCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitevideo', 'videos');
                    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'svcreate');
                    if (!empty($isManageAdmin) || !empty($videoCount)) {
                        $flag = true;
                    } else {
                        $flag = false;
                    }
                }
                break;
            case 'sitegroupreview.profile-sitegroupreviews':
                $enable_reviews = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');
                if ($enable_reviews) {
                    //TOTAL REVIEW
                    $reviewCount = Engine_Api::_()->sitegroup()->getTotalCount($group_id, 'sitegroupreview', 'reviews');
                    $level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitegroupreview_review', 'create');
                    if (empty($level_allow) && empty($reviewCount)) {
                        break;
                    }
                    $content_array = array($widgettitle, 'icon_sitegroups_review');
                }
                break;
            case 'sitegroup.location-sitegroup':
                $check_location = Engine_Api::_()->sitegroup()->enableLocation();
                $value['id'] = $group_id;
                $location = Engine_Api::_()->getDbtable('locations', 'sitegroup')->getLocation($value);
                $isManageAdmin = 0;
                $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'map');
                if ($check_location && $location && !empty($isManageAdmin)) {
                    $content_array = array($widgettitle, 'icon_sitegroups_map');
                }
                break;
            case 'sitegroup.overview-sitegroup':
                $isManageAdmin = 0;
                $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'overview');
                if (empty($isManageAdmin) || empty($can_edit) && empty($sitegroup->overview))
                    break;
                $content_array = array($widgettitle, 'icon_sitegroups_overview');
                break;
            case 'sitegroupform.sitegroup-viewform':
                $enable_forms = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform');
                if ($enable_forms) {
                    $quetion = Engine_Api::_()->getDbtable('groupquetions', 'sitegroupform');
                    $result_quetion = $quetion->fetchRow($quetion->select()->where('group_id = ?', $group_id));
                    $option_id = $result_quetion->option_id;
                    $itegroupforms_table = Engine_Api::_()->getDbtable('sitegroupforms', 'sitegroupform');
                    $select_sitegroupform_result = $itegroupforms_table->fetchRow($itegroupforms_table->select()->where('group_id = ?', $group_id));
                    if (!empty($option_id)) {
                        if ($select_sitegroupform_result->status == 0 || $select_sitegroupform_result->groupformactive == 0) {
                            break;
                        }
                        //PACKAGE BASE PRIYACY START
                        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupform")) {
                                break;
                            }
                        } else {
                            $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'form');
                            if (empty($isGroupOwnerAllow)) {
                                break;
                            }
                        }
                        //PACKAGE BASE PRIYACY END
                        $content_array = array($widgettitle, 'icon_sitegroup_form');
                    }
                }
                break;
            case 'sitegroupoffer.profile-sitegroupoffers':
                $enable_offers = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer');
                if ($enable_offers) {

                    $can_edit = 1;
                    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
                    if (empty($isManageAdmin)) {
                        $can_edit = 0;
                    }

                    $can_offer = 1;
                    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'offer');

                    if (empty($isManageAdmin)) {
                        $can_offer = 0;
                    }
                    $can_create_offer = '';

                    //OFFER CREATION AUTHENTICATION CHECK
                    if ($can_edit == 1 && $can_offer == 1) {
                        $this->view->can_create_offer = $can_create_offer = 1;
                    }
                    //TOTAL OFFER
                    $offerCount = Engine_Api::_()->sitegroup()->getTotalCount($group_id, 'sitegroupoffer', 'offers');
                    if (empty($can_create_offer) && empty($offerCount)) {
                        break;
                    }

                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupoffer")) {
                            break;
                        }
                    } else {
                        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'offer');
                        if (empty($isGroupOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'sitegroupoffer_type_offer');
                }
                break;
            case 'sitegroupmusic.profile-sitegroupmusic':
                $enable_musics = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic');
                if ($enable_musics) {
                    //TOTAL MUSIC
                    $musicCount = Engine_Api::_()->sitegroup()->getTotalCount($group_id, 'sitegroupmusic', 'playlists');
                    $musicCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'smcreate');
                    if (empty($musicCreate) && empty($musicCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmusic")) {
                            break;
                        }
                    } else {
                        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smcreate');
                        if (empty($isGroupOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_sitegroupmusic_music');
                }
                break;
            case 'sitegroupmember.profile-sitegroupmembers':
                $enable_member = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
                if ($enable_member) {
                    //TOTAL MEMBER
                    $memberCount = Engine_Api::_()->sitegroup()->getTotalCount($group_id, 'sitegroup', 'membership');
                    $memberCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'smecreate');
                    if (empty($memberCreate) && empty($memberCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmember")) {
                            break;
                        }
                    } else {
                        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smecreate');
                        if (empty($isGroupOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_sitegroup_member');
                }
                break;
            case 'sitegroupintegration.profile-items':
                $pieces = explode("_", $resource_type);
                $resourceType = $pieces[0] . '_' . $pieces[1];
                $listingTypeId = $pieces[2];
                $enable_sitegroupintegration = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration');
                if ($enable_sitegroupintegration) {
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", $resourceType . '_' . $listingTypeId)) {
                            break;
                        }
                    } else {
                        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, $resourceType . '_' . $listingTypeId);
                        if (empty($isGroupOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END          
                    //PACKAGE BASE PRIYACY END
                    if ($resourceType == 'list_listing') {
                        $content_array = array($widgettitle, "item_icon_list", $resourceType);
                    } elseif ($resourceType == 'sitepage_page') {
                        $content_array = array($widgettitle, "item_icon_sitepage", $resourceType);
                    } else {
                        $content_array = array($widgettitle, "item_icon_sitereview_listtype_$listingTypeId", $resourceType);
                    }
                }
                break;

//         case 'sitegrouptwitter.feeds-sitegrouptwitter':
//         
// 					$enable_sitegrouptwitter = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter');
// 					if ($enable_sitegrouptwitter) {
// 						$isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'twitter');
// 					if (empty($isManageAdmin)) {
// 							break;
// 						}
// 						$content_array = array($widgettitle, 'icon_sitegroupmusic_music');
// 					}
        }
        return $content_array;
    }

}

?>
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Content.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Model_DbTable_MobileContent extends Engine_Db_Table {

    protected $_serializedColumns = array('params');

    /**
     * Set profile group default widget in user content table without tab
     *
     * @param string $name
     * @param int $group_id
     * @param string $title
     * @param int $titleCount
     * @param int $order
     */
    public function setDefaultInfo($name = null, $contentgroup_id, $title = null, $titleCount = null, $order = null, $params = null) {
        $db = Engine_Db_Table::getDefaultAdapter();
        if (!empty($name)) {
            $select = $this->select();
            $select_content = $select
                    ->from($this->info('name'))
                    ->where('mobilecontentgroup_id = ?', $contentgroup_id)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', $name)
                    ->limit(1);
            $content = $select_content->query()->fetchAll();
            if (empty($content)) {
                $select = $this->select();
                $select_container = $select
                        ->from($this->info('name'), array('mobilecontent_id'))
                        ->where('mobilecontentgroup_id = ?', $contentgroup_id)
                        ->where('type = ?', 'container')
                        ->limit(1);
                $container = $select_container->query()->fetchAll();
                if (!empty($container)) {
                    $select = $this->select();
                    $container_id = $container[0]['mobilecontent_id'];
                    $select_middle = $select
                            ->from($this->info('name'))
                            ->where('parent_content_id = ?', $container_id)
                            ->where('type = ?', 'container')
                            ->where('name = ?', 'middle')
                            ->limit(1);
                    $middle = $select_middle->query()->fetchAll();
                    if (!empty($middle)) {
                        $select = $this->select();
                        $middle_id = $middle[0]['mobilecontent_id'];
                        $select_tab = $select
                                ->from($this->info('name'))
                                ->where('type = ?', 'widget')
                                ->where('name = ?', 'sitemobile.container-tabs-columns')
                                ->where('mobilecontentgroup_id = ?', $contentgroup_id)
                                ->limit(1);
                        $tab = $select_tab->query()->fetchAll();
                        $tab_id = '';
                        if (!empty($tab)) {
                            $tab_id = $tab[0]['content_id'];
                        } else {
                            $contentWidget = $this->createRow();
                            $contentWidget->mobilecontentgroup_id = $contentgroup_id;
                            $contentWidget->type = 'widget';
                            $contentWidget->name = 'sitemobile.container-tabs-columns';
                            $contentWidget->parent_content_id = ($tab_id ? $tab_id : $middle_id);
                            $contentWidget->order = $order;
                            $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.showmore', 8);
                            $contentWidget->params = "{\"max\":\"$showmaxtab\"}";
                            $contentWidget->save();
                        }
                        if ($name != 'sitegroupintegration.profile-items') {
                            $contentWidget = $this->createRow();
                            $contentWidget->mobilecontentgroup_id = $contentgroup_id;
                            $contentWidget->type = 'widget';
                            $contentWidget->name = $name;
                            $contentWidget->parent_content_id = ($tab_id ? $tab_id : $middle_id);
                            $contentWidget->order = $order;
                            if ($params) {
                                $contentWidget->params = $params;
                            } else {
                                $contentWidget->params = '{"title":"' . $title . '","titleCount":' . $titleCount . '}';
                            }
                            $contentWidget->save();
                        } else {
                            $select = new Zend_Db_Select($db);
                            $select
                                    ->from('engine4_core_modules')
                                    ->where('name = ?', 'sitereview');
                            $check_list = $select->query()->fetchObject();
                            if (!empty($check_list)) {
                                $results = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getIntegrationItems();

                                foreach ($results as $value) {
                                    $item_title = $value['item_title'];
                                    $resource_type = $value['resource_type'] . '_' . $value['listingtype_id'];

                                    // Check if it's already been placed
                                    $select = new Zend_Db_Select($db);
                                    $select
                                            ->from('engine4_sitegroup_mobilecontent')
                                            ->where('parent_content_id = ?', $tab_id)
                                            ->where('type = ?', 'widget')
                                            ->where('name = ?', 'sitegroupintegration.profile-items')
                                            ->where('params = ?', '{"title":"' . $item_title . '","resource_type":"' . $resource_type . '","nomobile":"0","name":"sitegroupintegration.profile-items"}');
                                    $info = $select->query()->fetch();
                                    if (empty($info)) {

                                        // tab on profile
                                        $db->insert('engine4_sitegroup_mobilecontent', array(
                                            'mobilecontentgroup_id' => $contentgroup_id,
                                            'type' => 'widget',
                                            'name' => 'sitegroupintegration.profile-items',
                                            'parent_content_id' => $tab_id,
                                            'order' => 999,
                                            'params' => '{"title":"' . $item_title . '","resource_type":"' . $resource_type . '","nomobile":"0","name":"sitegroupintegration.profile-items"}',
                                        ));
                                    }
                                    //}
                                }
                            }


                            $this->tabgroupintwidgetlayout('document', '{"title":"Documents","resource_type":"document_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $tab_id, $contentgroup_id);

                            $this->tabgroupintwidgetlayout('sitegroup', '{"title":"Groups","resource_type":"sitegroup_group_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $tab_id, $contentgroup_id);

                            $this->tabgroupintwidgetlayout('sitepage', '{"title":"Pages","resource_type":"sitepage_page_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $tab_id, $contentgroup_id);

                            $this->tabgroupintwidgetlayout('list', '{"title":"Listings","resource_type":"list_listing_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $tab_id, $contentgroup_id);

                            $this->tabgroupintwidgetlayout('folder', '{"title":"Folders","resource_type":"folder_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $tab_id, $contentgroup_id);

                            $this->tabgroupintwidgetlayout('quiz', '{"title":"Quiz","resource_type":"quiz_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $tab_id, $contentgroup_id);

                            $this->tabgroupintwidgetlayout('sitestoreproduct', '{"title":"Products","resource_type":"sitestoreproduct_product_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $tab_id, $contentgroup_id);

                            $this->tabgroupintwidgetlayout('sitefaq', '{"title":"FAQs","resource_type":"sitefaq_faq_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $tab_id, $contentgroup_id);

                            $this->tabgroupintwidgetlayout('sitetutorial', '{"title":"Tutorials","resource_type":"sitetutorial_tutorial_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $tab_id, $contentgroup_id);
                        }
                    }
                }
            }
        }
    }

    /**
     * Gets content id,parama,name
     *
     * @param int $contentgroup_id
     * @return content id,parama,name
     */
    public function getContents($mobilecontentgroup_id) {

        $selectGroupAdmin = $this->select()
                ->from($this->info('name'), array('mobilecontent_id', 'params', 'name'))
                ->where('mobilecontentgroup_id =?', $mobilecontentgroup_id)
                ->where("name IN ('sitegroup.overview-sitegroup', 'sitegroup.photos-sitegroup', 'sitegroup.discussion-sitegroup', 'sitegroupnote.profile-sitegroupnotes', 'sitegrouppoll.profile-sitegrouppolls', 'sitegroupevent.profile-sitegroupevents', 'sitegroupvideo.profile-sitegroupvideos', 'sitegroupoffer.profile-sitegroupoffers', 'sitegroupreview.profile-sitegroupreviews', 'sitegroupdocument.profile-sitegroupdocuments', 'sitegroupform.sitegroup-viewform','sitegroup.info-sitegroup', 'seaocore.feed','advancedactivity.home-feeds', 'activity.feed', 'sitegroup.location-sitegroup', 'core.profile-links', 'sitegroupmusic.profile-sitegroupmusic', 'sitegroupmember.profile-sitegroupmembers', 'sitegroupintegration.profile-items','sitegrouptwitter.feeds-sitegrouptwitter', 'siteevent.contenttype-events', 'sitevideo.contenttype-videos')");
        return $this->fetchAll($selectGroupAdmin);
    }

    /**
     * Gets content_id
     *
     * @param int $contentgroup_id
     * @return $params
     */
    public function getContentId($mobilecontentgroup_id, $sitegroup) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = 0;
        }
        $itemAlbumCount = 10;
        $itemPhotoCount = 100;
        $select = $this->select();
        $select_content = $select
                ->from($this->info('name'))
                ->where('mobilecontentgroup_id = ?', $mobilecontentgroup_id)
                ->where('type = ?', 'container')
                ->where('name = ?', 'main')
                ->limit(1);
        $content = $select_content->query()->fetchAll();
        if (!empty($content)) {
            $select = $this->select();
            $select_container = $select
                    ->from($this->info('name'), array('mobilecontent_id'))
                    ->where('mobilecontentgroup_id = ?', $mobilecontentgroup_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'middle')
                    ->where("name NOT IN ('	sitegroup.title-sitegroup', 'seaocore.like-button', 'sitegroup.photorecent-sitegroup')")
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
                $select = $this->select();
                $container_id = $container[0]['mobilecontent_id'];
                $select_middle = $select
                        ->from($this->info('name'))
                        ->where('parent_content_id = ?', $container_id)
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'core.container-tabs')
                        ->where('mobilecontentgroup_id = ?', $mobilecontentgroup_id)
                        ->limit(1);
                $middle = $select_middle->query()->fetchAll();
                if (!empty($middle)) {
                    $mobilecontent_id = $middle[0]['mobilecontent_id'];
                } else {
                    $mobilecontent_id = $container_id;
                }
            }
        }

        if (!empty($mobilecontent_id)) {
            $select = $this->select();
            $select_middle = $select
                    ->from($this->info('name'), array('mobilecontent_id', 'name', 'params'))
                    ->where('parent_content_id = ?', $mobilecontent_id)
                    ->where('type = ?', 'widget')
                    ->where("name NOT IN ('sitegroup.title-sitegroup', 'seaocore.like-button', 'sitegroup.photorecent-sitegroup', 'Facebookse.facebookse-sitegroupprofilelike', 'sitegroup.thumbphoto-sitegroup')")
                    ->where('mobilecontentgroup_id = ?', $mobilecontentgroup_id)
                    ->order('order')
            ;

            $select = $this->select();
            $select_photo = $select
                    ->from($this->info('name'), array('params'))
                    ->where('parent_content_id = ?', $mobilecontent_id)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'sitegroup.photos-sitegroup')->where('contentgroup_id = ?', $contentgroup_id)
                    ->order('mobilecontent_id ASC');

            $middlePhoto = $select_photo->query()->fetchColumn();
            if (!empty($middlePhoto)) {
                $photoParamsDecodedArray = Zend_Json_Decoder::decode($middlePhoto);
                if (isset($photoParamsDecodedArray['itemCount']) && !empty($photoParamsDecodedArray)) {
                    $itemAlbumCount = $photoParamsDecodedArray['itemCount'];
                }
                if (isset($photoParamsDecodedArray['itemCount_photo']) && !empty($photoParamsDecodedArray)) {
                    $itemPhotoCount = $photoParamsDecodedArray['itemCount_photo'];
                }
            }
            $middle = $select_middle->query()->fetchAll();
            $editpermission = '';
            $isManageAdmin = '';
            $mobilecontent_ids = '';
            $content_names = '';
            $resource_type_integration = 0;
            $ads_display_integration = 0;
            $flag = false;
            foreach ($middle as $value) {
                $content_name = $value['name'];
                switch ($content_name) {
                    case 'sitegroup.overview-sitegroup':
                        if (!empty($sitegroup)) {
                            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'overview');
                            if (!empty($isManageAdmin)) {
                                $editpermission = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
                                if (!empty($editpermission) && empty($sitegroup->overview)) {
                                    $flag = true;
                                } elseif (empty($editpermission) && empty($sitegroup->overview)) {
                                    $flag = false;
                                } elseif (!empty($editpermission) && !empty($sitegroup->overview)) {
                                    $flag = true;
                                } elseif (empty($editpermission) && !empty($sitegroup->overview)) {
                                    $flag = true;
                                }
                            }
                        }
                        break;
                    case 'sitegroup.location-sitegroup':
                        if (!empty($sitegroup)) {
                            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'map');
                            if (!empty($isManageAdmin)) {
                                $value['id'] = $sitegroup->getIdentity();
                                $location = Engine_Api::_()->getDbtable('locations', 'sitegroup')->getLocation($value);
                                if (!empty($location)) {
                                    $flag = true;
                                }
                            }
                        }
                        break;
                    case 'core.html-block':
                        $flag = true;
                        break;
                    case 'activity.feed':
                        $flag = true;
                        break;
                    case 'seaocore.feed':
                        $flag = true;
                        break;
                    case 'advancedactivity.home-feeds':
                        $flag = true;
                        break;
                    case 'sitegroup.info-sitegroup':
                        $flag = true;
                        break;
                    case 'core.profile-links':
                        $flag = true;
                        break;
                    case 'sitegroupnote.profile-sitegroupnotes':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) {
                            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupnote") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sncreate');
                                if (!empty($isGroupOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL NOTES
                            $noteCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupnote', 'notes');
                            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sncreate');
                            if (!empty($isManageAdmin) || !empty($noteCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitegroupevent.profile-sitegroupevents':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
                            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupevent") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'secreate');
                                if (!empty($isGroupOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL EVENTS
                            $eventCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupevent', 'events');
                            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'secreate');
                            if (!empty($isManageAdmin) || !empty($eventCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitegroup.discussion-sitegroup':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
                            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupdiscussion") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sdicreate');
                                if (!empty($isGroupOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL TOPICS
                            $topicCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroup', 'topics');
                            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sdicreate');
                            if (!empty($isManageAdmin) || !empty($topicCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitegroup.photos-sitegroup':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
                            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupalbum") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'spcreate');
                                if (!empty($isGroupOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL ALBUMS
                            $albumCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroup', 'albums');
                            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');
                            if (!empty($isManageAdmin) || !empty($albumCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitegroupmusic.profile-sitegroupmusic':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
                            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmusic") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smcreate');
                                if (!empty($isGroupOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL PLAYLISTS
                            $musicCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupmusic', 'playlists');
                            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'smcreate');
                            if (!empty($isManageAdmin) || !empty($musicCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;

                    case 'sitegroupmember.profile-sitegroupmembers':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
                            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmember") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smecreate');
                                if (!empty($isGroupOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL PLAYLISTS
                            $memberCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupm', 'membership');
                            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'smecreate');
                            if (!empty($isManageAdmin) || !empty($memberCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;

                    case 'sitegroupdocument.profile-sitegroupdocuments':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
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
                            $documentCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupdocument', 'documents');
                            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sdcreate');
                            if (!empty($isManageAdmin) || !empty($documentCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitegroupreview.profile-sitegroupreviews':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
                            //TOTAL REVIEW
                            $reviewCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupreview', 'reviews');
                            $level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitegroupreview_review', 'create');
                            if (!empty($level_allow) || !empty($reviewCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitegroupvideo.profile-sitegroupvideos':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
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
                            //TOTAL VIDEO
                            $videoCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupvideo', 'videos');
                            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'svcreate');
                            if (!empty($isManageAdmin) || !empty($videoCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitegrouppoll.profile-sitegrouppolls':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
                            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegrouppoll") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'splcreate');
                                if (!empty($isGroupOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL POLL
                            $pollCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegrouppoll', 'polls');
                            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'splcreate');
                            if (!empty($isManageAdmin) || !empty($pollCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitegroupoffer.profile-sitegroupoffers':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer')) {
                            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupoffer") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'offer');
                                if (!empty($isGroupOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL OFFERS
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
                                $can_create_offer = 1;
                            }

                            //TOTAL OFFER
                            $offerCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupoffer', 'offers');
                            if (!empty($can_create_offer) || !empty($offerCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitegroupform.sitegroup-viewform':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform')) {
                            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupform") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'form');
                                if (!empty($isGroupOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                        }
                        break;
                    case 'sitegroupintegration.profile-items':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration')) {
                            $content_params = $value['params'];
                            $paramsDecodedArray = Zend_Json_Decoder::decode($content_params);
                            $resource_type_integration = $paramsDecodedArray['resource_type'];
                            $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitegroup.ad.$resource_type_integration", 3);

                            //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY START
                            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", $resource_type_integration)) {
                                    $flag = true;
                                }
                            } else {
                                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, $resource_type_integration);
                                if (!empty($isGroupOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY END
                        }
                        break;
                    case 'sitegrouptwitter.feeds-sitegrouptwitter':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter')) {
                            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'twitter');
                            if (!empty($isManageAdmin)) {
                                $flag = true;
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
                            //TOTAL EVENTS
                            $videoCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitevideo', 'videos');
                            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'svcreate');
                            if (!empty($isManageAdmin) || !empty($videoCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'siteevent.contenttype-events':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
                            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupevent") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'secreate');
                                if (!empty($isGroupOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL EVENTS
                            $eventCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'siteevent', 'events');
                            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'secreate');
                            if (!empty($isManageAdmin) || !empty($eventCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                }

                if (!empty($flag)) {
                    $mobilecontent_ids = $value['mobilecontent_id'];
                    $content_names = $value['name'];
                    break;
                }
            }
        }

        return array('content_id' => $mobilecontent_ids, 'content_name' => $content_names, 'itemAlbumCount' => $itemAlbumCount, 'itemPhotoCount' => $itemPhotoCount, 'resource_type_integration' => $resource_type_integration, 'ads_display_integration' => $ads_display_integration);
    }

    /**
     * Gets content_id, name
     *
     * @param int $contentgroup_id
     * @return content_id, name
     */
    public function getContentInformation($mobilecontentgroup_id) {
        $select = $this->select()->from($this->info('name'), array('mobilecontent_id', 'name'))
                        ->where("name IN ('sitegroup.info-sitegroup', 'seaocore.feed', 'advancedactivity.home-feeds','activity.feed', 'sitegroup.location-sitegroup', 'core.profile-links', 'core.html-block')")
                        ->where('mobilecontentgroup_id = ?', $mobilecontentgroup_id)->order('mobilecontent_id ASC');

        return $this->fetchAll($select);
    }

    /**
     * Gets content_id, name
     *
     * @param int $contentgroup_id
     * @param int $name 
     * @return content_id, name
     */
    public function getContentByWidgetName($name, $mobilecontentgroup_id) {
        $select = $this->select()->from($this->info('name'), array('mobilecontent_id', 'name'))
                ->where('name =?', $name)
                ->where('mobilecontentgroup_id = ?', $mobilecontentgroup_id)
                ->limit(1)
        ;
        return $this->fetchAll($select)->toarray();
    }

    /**
     * Gets name
     *
     * @param int $tab_main
     * @return name
     */
    public function getCurrentTabName($tab_main = null) {
        if (empty($tab_main)) {
            return;
        }
        $current_tab_name = $this->select()
                ->from($this->info('name'), array('name'))
                ->where('mobilecontent_id = ?', $tab_main)
                ->query()
                ->fetchColumn();
        return $current_tab_name;
    }

    public function tabgroupintwidgetlayout($module_name, $params, $tab_id, $group_id) {

        $db = Engine_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', $module_name);
        $module_enable = $select->query()->fetchObject();

        if (!empty($module_enable)) {

            $results = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getIntegrationItems();

            foreach ($results as $value) {

                // Check if it's already been placed
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_sitegroup_mobilecontent')
                        ->where('parent_content_id = ?', $tab_id)
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'sitegroupintegration.profile-items')
                        ->where('params = ?', $params);
                $info = $select->query()->fetch();
                if (empty($info)) {

                    // tab on profile
                    $db->insert('engine4_sitegroup_mobilecontent', array(
                        'mobilecontentgroup_id' => $group_id,
                        'type' => 'widget',
                        'name' => 'sitegroupintegration.profile-items',
                        'parent_content_id' => $tab_id,
                        'order' => 999,
                        'params' => $params,
                    ));
                }
            }
        }
    }

}

?>
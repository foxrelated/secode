<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminDefaultlayoutController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_AdminDefaultlayoutController extends Core_Controller_Action_Admin {

  //ACTION FOR SETTING THE DEFAULT LAYOUT
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_layoutdefault');

    //FORM   
    $this->view->form = $form = new Sitegroup_Form_Admin_Layoutdefault();

    //FORM   
    if(!Engine_Api::_()->getApi("settings", "core")->getSetting('sitegroup.layout.coverphotoenabled', 0)) {
			$this->view->coverForm = $coverForm = new Sitegroup_Form_Admin_CoverPhotoLayout();
    }

    //GET GROUP PROFILE GROUP INFO
    $selectGroup = Engine_Api::_()->sitegroup()->getWidgetizedGroup();
    if (!empty($selectGroup)) {
      $this->view->group_id = $selectGroup->page_id;
    }
 
    //GET GROUP PROFILE GROUP INFO
    if (Engine_Api::_()->sitegroup()->getMobileWidgetizedGroup()) {
      $this->view->mobile_group_id = Engine_Api::_()->sitegroup()->getMobileWidgetizedGroup()->page_id;
    }

    //FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) || isset($_GET['group_reload'])) {
      //GET FORM VALUES
      $values = $form->getValues();

      //GET ADMIN CONTENT TABLE
      $contentTable = Engine_Api::_()->getDbtable('admincontent', 'sitegroup');

      //GET ADMIN CONTENT TABLE NAME
      $contentTableName = $contentTable->info('name');

      //GET SITEGROUP CONTENT TABLE
      $sitegroupcontentTable = Engine_Api::_()->getDbtable('content', 'sitegroup');

      //CORE CONTENT TABLE
      $corecontentTable = Engine_Api::_()->getDbtable('content', 'core');

      //GET SITEGROUP CONTENT GROUPS TABLE
      $sitegroupgroupTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');

      //GET HIDE PROFILE WODGET TABLE   
      $hideprofilewidgetsTable = Engine_Api::_()->getDbtable('hideprofilewidgets', 'sitegroup');

      //DELETING THE OLD ENTRIES
      $hideprofilewidgets = $hideprofilewidgetsTable->select()->query()->fetchAll();
      if (!empty($hideprofilewidgets)) {
        foreach ($hideprofilewidgets as $data) {
          $hideprofilewidgetsTable->delete(array('hideprofilewidgets_id =?' => $data['hideprofilewidgets_id']));
        }
      }
      $totalGroups = $sitegroupgroupTable->select()
                      ->from($sitegroupgroupTable->info('name'), array('count(*) as count'))
                      ->where('name =?', 'sitegroup_index_view')->query()->fetchColumn();
      if (isset($_POST['sitegroup_sitegroup_layout_setting'])) {
        $layout_option = $_POST['sitegroup_sitegroup_layout_setting'];
      } else {
        $layout_option = $_GET['sitegroup_sitegroup_layout_setting'];
      }
      $limit = 300;
      $sitegroup_layout_cover_photo = 1;
      if(isset($_POST['sitegroup_layout_cover_photo'])) {
        $sitegroup_layout_cover_photo = $_POST['sitegroup_layout_cover_photo'];
      }
      
      $reload_count = round($totalGroups / $limit);
      $group_reload = $this->_getParam('group_reload', 1);
      $offset = ($group_reload - 1) * $limit;

      $selectsitegroupGroup = $sitegroupgroupTable->select()
              ->from($sitegroupgroupTable->info('name'), array('contentgroup_id'))
              ->where('name =?', 'sitegroup_index_view')
              ->limit($limit, $offset);
      $contentgroups_id = $selectsitegroupGroup->query()->fetchAll();
      foreach ($contentgroups_id as $key => $value) {
				if($value['contentgroup_id']) {	
					$sitegroupcontentTable->delete(array('contentgroup_id =?' => $value['contentgroup_id']));
					if (empty($layout_option)) {
						Engine_Api::_()->getDbtable('content', 'sitegroup')->setWithoutTabLayout($value['contentgroup_id'], $sitegroup_layout_cover_photo);
					} else {
						Engine_Api::_()->getDbtable('content', 'sitegroup')->setTabbedLayout($value['contentgroup_id'], $sitegroup_layout_cover_photo);
					}
				}
      }

      if ($group_reload == 1) {
        if (!empty($layout_option)) {
          Engine_Api::_()->getApi("settings", "core")->setSetting('sitegroup.layout.setting', $layout_option);
          include_once APPLICATION_PATH . '/application/modules/Sitegroup/controllers/AdminviewgroupwidgetController.php';
          if (!empty($selectGroup)) {
            $group_id = $selectGroup->page_id;
            $contentTable->delete(array('group_id =?' => $group_id));
            Engine_Api::_()->getApi('layoutcore', 'sitegroup')->setTabbedLayoutContent($group_id, $sitegroup_layout_cover_photo);
          }

          if (!empty($group_id)) {
            //INSERT MAIN CONTAINER
            $mainContainer = $contentTable->createRow();
            $mainContainer->group_id = $group_id;
            $mainContainer->type = 'container';
            $mainContainer->name = 'main';
            $mainContainer->order = 2;
            $mainContainer->save();
            $container_id = $mainContainer->admincontent_id;

            //INSERT MAIN-MIDDLE CONTAINER
            $mainMiddleContainer = $contentTable->createRow();
            $mainMiddleContainer->group_id = $group_id;
            $mainMiddleContainer->type = 'container';
            $mainMiddleContainer->name = 'middle';
            $mainMiddleContainer->parent_content_id = $container_id;
            $mainMiddleContainer->order = 6;
            $mainMiddleContainer->save();
            $middle_id = $mainMiddleContainer->admincontent_id;

            //INSERT MAIN-LEFT CONTAINER
            $mainLeftContainer = $contentTable->createRow();
            $mainLeftContainer->group_id = $group_id;
            $mainLeftContainer->type = 'container';
            $mainLeftContainer->name = 'right';
            $mainLeftContainer->parent_content_id = $container_id;
            $mainLeftContainer->order = 4;
            $mainLeftContainer->save();
            $left_id = $mainLeftContainer->admincontent_id;
            $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.showmore', 8);

            //INSERT MAIN-MIDDLE TAB CONTAINER
            $middleTabContainer = $contentTable->createRow();
            $middleTabContainer->group_id = $group_id;
            $middleTabContainer->type = 'widget';
            $middleTabContainer->name = 'core.container-tabs';
            $middleTabContainer->parent_content_id = $middle_id;
            $middleTabContainer->order = 10;
            $middleTabContainer->params = "{\"max\":\"$showmaxtab\"}";
            $middleTabContainer->save();
            $middle_tab = $middleTabContainer->admincontent_id;
						
            if(empty($sitegroup_layout_cover_photo)) {
              Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-profile-breadcrumb', $middle_id, 1, '', 'true');
              
							//INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
              if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')){
								Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupmember.groupcover-photo-sitegroupmembers', $middle_id, 2, '', 'true');
              }

							//INSERTING TITLE WIDGET
							Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.title-sitegroup', $middle_id, 4, '', 'true');
													
							//INSERTING LIKE WIDGET
							Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'seaocore.like-button', $middle_id, 5, '', 'true');
            
							//INSERTING FOLLOW WIDGET
							Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'seaocore.seaocore-follow', $middle_id, 6,'','true');

							//INSERTING FACEBOOK LIKE WIDGET
							if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
								Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'Facebookse.facebookse-sitegroupprofilelike', $middle_id, 7, '', 'true');
							}

							//INSERTING MAIN PHOTO WIDGET 
							Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.mainphoto-sitegroup', $left_id, 10, '', 'true');

            } else {
							//INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
							Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-profile-breadcrumb', $middle_id, 1, '', 'true');
							
							Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-cover-information-sitegroup', $middle_id, 2, '', 'true');
            }

            //INSERTING CONTACT DETAIL WIDGET
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.contactdetails-sitegroup', $middle_id, 8, '', 'true');

            //INSERTING OPTIONS WIDGET
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.options-sitegroup', $left_id, 11, '', 'true');

            //INSERTING INFORMATION WIDGET 
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.information-sitegroup', $left_id, 10, 'Information', 'true');

            //INSERTING WRITE SOMETHING ABOUT WIDGET 
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

            //INSERTING RATING WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
              Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupreview.ratings-sitegroupreviews', $left_id, 16, 'Ratings', 'true');
            }
						
            //INSERTING BADGE WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge')) {
              Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupbadge.badge-sitegroupbadge', $left_id, 17, 'Badge', 'true');
            }

            //INSERTING YOU MAY ALSO LIKE WIDGET 
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.suggestedgroup-sitegroup', $left_id, 18, 'You May Also Like', 'true');

            $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitegroup.socialshare-sitegroup"}';

            //INSERTING SOCIAL SHARE WIDGET 
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.socialshare-sitegroup', $left_id, 19, 'Social Share', 'true', $social_share_default_code);

//            //INSERTING FOUR SQUARE WIDGET 
//            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.foursquare-sitegroup', $left_id, 20, '', 'true');

            //INSERTING INSIGHTS WIDGET 
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.insights-sitegroup', $left_id, 21, 'Insights', 'true');

            //INSERTING FEATURED OWNER WIDGET 
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.featuredowner-sitegroup', $left_id, 22, 'Owners', 'true');

            //INSERTING ALBUM WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
              Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.albums-sitegroup', $left_id, 23, 'Albums', 'true');
            }

            //INSERTING GROUP PROFILE PLAYER WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
              Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupmusic.profile-player', $left_id, 24, '', 'true');
            }

            //INSERTING LINKED GROUPS WIDGET
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.favourite-group', $left_id, 25, 'Linked Groups', 'true');

            //INSERTING ACTIVITY FEED WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
              $advanced_activity_params =
                      '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
              Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 2, 'Updates', 'true', $advanced_activity_params);
            } else {
              Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'activity.feed', $middle_tab, 2, 'Updates', 'true');
            }

            //INSERTING INFORAMTION WIDGET
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.info-sitegroup', $middle_tab, 3, 'Info', 'true');

            //INSERTING OVERVIEW WIDGET
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.overview-sitegroup', $middle_tab, 4, 'Overview', 'true');

            //INSERTING LOCATION WIDGET
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.location-sitegroup', $middle_tab, 5, 'Map', 'true');

            //INSERTING LINKS WIDGET
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'core.profile-links', $middle_tab, 125, 'Links', 'true');

            //INSERTING ALBUM WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroup.photos-sitegroup', $group_id, 'Photos', 'true', '110');
            }

            //INSERTING VIDEO WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroupvideo.profile-sitegroupvideos', $group_id, 'Videos', 'true', '111');
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
                Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitevideo.contenttype-videos', $group_id, 'Videos', 'true', '117');
            }

                        
            //INSERTING NOTE WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroupnote.profile-sitegroupnotes', $group_id, 'Notes', 'true', '112');
            }

            //INSERTING REVIEW WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroupreview.profile-sitegroupreviews', $group_id, 'Reviews', 'true', '113');
            }

            //INSERTING FORM WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroupform.sitegroup-viewform', $group_id, 'Form', 'false', '114');
            }

            //INSERTING DOCUMENT WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroupdocument.profile-sitegroupdocuments', $group_id, 'Documents', 'true', '115');
            }
            
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
                Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('document.contenttype-documents', $group_id, 'Documents', 'true', '115');
            }

            //INSERTING OFFER WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroupoffer.profile-sitegroupoffers', $group_id, 'Offers', 'true', '116');
            }

            //INSERTING EVENT WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroupevent.profile-sitegroupevents', $group_id, 'Events', 'true', '117');
            }

						//INSERTING EVENT WIDGET 
						if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
							Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('siteevent.contenttype-events', $group_id, 'Events', 'true', '117');
						}

            //INSERTING POLL WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegrouppoll.profile-sitegrouppolls', $group_id, 'Polls', 'true', '118');
            }

            //INSERTING DISCUSSION WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroup.discussion-sitegroup', $group_id, 'Discussions', 'true', '119');
            }

            //INSERTING MUSIC WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroupmusic.profile-sitegroupmusic', $group_id, 'Music', 'true', '120');
            }

            //INSERTING TWITTER WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegrouptwitter.feeds-sitegrouptwitter', $group_id, 'Twitter', 'true', '121');
            }

						if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
							Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroupmember.profile-sitegroupmembers', $group_id, 'Members', 'true', '122');
							Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroupmember.profile-sitegroupmembers-announcements', $group_id, 'Announcements', 'true', '123');
						}

            //INSERTING INTEGRATION WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroupintegration.profile-items', $group_id, '', '', '999');
            }

          }
        } else {
          //IF EMPTY THEN MAKE GROUP PROFILE GROUP
          if (empty($selectGroup)) {
            $groupCreate = $groupTable->createRow();
            $groupCreate->name = 'sitegroup_index_view';
            $groupCreate->displayname = 'Group Profile';
            $groupCreate->title = 'Group Profile';
            $groupCreate->description = 'This is the group view  group.';
            $groupCreate->custom = 1;
            $current_group_id = $groupCreate->save();
          } else {
            $current_group_id = $selectGroup->page_id;
          }

          if (!empty($current_group_id)) {
            $corecontentTable->delete(array('page_id =?' => $current_group_id));
            Engine_Api::_()->getApi('layoutcore', 'sitegroup')->setWithoutTabContent($current_group_id, $sitegroup_layout_cover_photo);
          }

          if (!empty($selectGroup)) {
            $group_id = $selectGroup->page_id;
            $contentTable->delete(array('group_id =?' => $group_id));
          }
          //INSERT MAIN CONTAINER
          $mainContainer = $contentTable->createRow();
          $mainContainer->group_id = $group_id;
          $mainContainer->type = 'container';
          $mainContainer->name = 'main';
          $mainContainer->order = 2;
          $mainContainer->save();
          $container_id = $mainContainer->admincontent_id;

          //INSERT MAIN-MIDDLE CONTAINER.
          $mainMiddleContainer = $contentTable->createRow();
          $mainMiddleContainer->group_id = $group_id;
          $mainMiddleContainer->type = 'container';
          $mainMiddleContainer->name = 'middle';
          $mainMiddleContainer->parent_content_id = $container_id;
          $mainMiddleContainer->order = 6;
          $mainMiddleContainer->save();
          $middle_id = $mainMiddleContainer->admincontent_id;

          //INSERT MAIN-LEFT CONTAINER.
          $mainLeftContainer = $contentTable->createRow();
          $mainLeftContainer->group_id = $group_id;
          $mainLeftContainer->type = 'container';
          $mainLeftContainer->name = 'right';
          $mainLeftContainer->parent_content_id = $container_id;
          $mainLeftContainer->order = 4;
          $mainLeftContainer->save();
          $left_id = $mainLeftContainer->admincontent_id;

          //INSERTING TITLE WIDGET
          if(empty($sitegroup_layout_cover_photo)) {
          
							Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-profile-breadcrumb', $middle_id, 1, '', 'true');
             
						//INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
							Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupmember.groupcover-photo-sitegroupmembers', $middle_id, 2, '', 'true');
            }
						Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.title-sitegroup', $middle_id, 3, '', 'true');

						//INSERTING LIKE WIDGET 
						Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'seaocore.like-button', $middle_id, 4, '', 'true');

						//INSERTING FACEBOOK LIKE WIDGET
						if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
							Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'Facebookse.facebookse-sitegroupprofilelike', $middle_id, 5, '', 'true');
						} 

						//INSERTING MAIN PHOTO WIDGET 
						Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.mainphoto-sitegroup', $left_id, 10, '', 'true');

          } else {
          
						Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-profile-breadcrumb', $middle_id, 1, '', 'true');
          
						//INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
						Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-cover-information-sitegroup', $middle_id, 2, '', 'true');
          }

          //INSERTING CONTACT DETAIL WIDGET
          Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.contactdetails-sitegroup', $middle_id, 5, '', 'true');

          //INSERTING ACTIVITY FEED WIDGET
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
            $advanced_activity_params =
                    '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'advancedactivity.home-feeds', $middle_id, 6, 'Updates', 'true', $advanced_activity_params);
          } else {
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'activity.feed', $middle_id, 6, 'Updates', 'true');
          }

          //INSERTING INFORAMTION WIDGET
          Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.info-sitegroup', $middle_id, 7, 'Info', 'true');

          //INSERTING OVERVIEW WIDGET
          Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.overview-sitegroup', $middle_id, 8, 'Overview', 'true');

          //INSERTING LOCATION WIDGET
          Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.location-sitegroup', $middle_id, 9, 'Map', 'true');

          //INSERTING LINKS WIDGET  
          Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'core.profile-links', $middle_id, 125, 'Links', 'true');

          //INSERTING WIDGET LINK WIDGET 
          Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.widgetlinks-sitegroup', $left_id, 11, '', 'true');

          //INSERTING OPTIONS WIDGET 
          Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.options-sitegroup', $left_id, 12, '', 'true');

          //INSERTING WRITE SOMETHING ABOUT WIDGET 
          Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.write-group', $left_id, 13, '', 'true');

          //INSERTING INFORMATION WIDGET 
          Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.information-sitegroup', $left_id, 10, 'Information', 'true');

          //INSERTING LIKE WIDGET 
          Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

          //INSERTING RATING WIDGET 	
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupreview.ratings-sitegroupreviews', $left_id, 16, 'Ratings', 'true');
          }

          //INSERTING BADGE WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge')) {
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupbadge.badge-sitegroupbadge', $left_id, 17, 'Badge', 'true');
          }

          $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitegroup.socialshare-sitegroup"}';

          //INSERTING SOCIAL SHARE WIDGET 
          Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.socialshare-sitegroup', $left_id, 19, 'Social Share', 'true', $social_share_default_code);

//          //INSERTING FOUR SQUARE WIDGET 
//          Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.foursquare-sitegroup', $left_id, 20, '', 'true');

          //INSERTING INSIGHTS WIDGET 
          Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.insights-sitegroup', $left_id, 22, 'Insights', 'true');

          //INSERTING FEATURED OWNER WIDGET 
          Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.featuredowner-sitegroup', $left_id, 23, 'Owners', 'true');

          //INSERTING ALBUM WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.albums-sitegroup', $left_id, 24, 'Albums');
            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitegroup.photos-sitegroup', $group_id, 'Photos', 'true', '110');
          }

          //INSERTING GROUP PROFILE PLAYER WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
            Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupmusic.profile-player', $left_id, 25, '', 'true');
          }

          //INSERTING LINKED GROUPS WIDGET   
          Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.favourite-group', $left_id, 26, 'Linked Groups', 'true');

          //INSERTING VIDEO WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitegroupvideo.profile-sitegroupvideos', $group_id, 'Videos', 'true', '111');
          }

          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
                Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitevideo.contenttype-videos', $group_id, 'Videos', 'true', '117');
            }
            
          //INSERTING NOTE WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitegroupnote.profile-sitegroupnotes', $group_id, 'Notes', 'true', '112');
          }

          //INSERTING REVIEW WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitegroupreview.profile-sitegroupreviews', $group_id, 'Reviews', 'true', '113');
          }

          //INSERTING FORM WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitegroupform.sitegroup-viewform', $group_id, 'Form', 'false', '114');
          }

          //INSERTING DOCUMENT WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitegroupdocument.profile-sitegroupdocuments', $group_id, 'Documents', 'true', '115');
          }
          
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
                Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('document.contenttype-documents', $group_id, 'Documents', 'true', '115');
            }

          //INSERTING OFFER WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitegroupoffer.profile-sitegroupoffers', $group_id, 'Offers', 'true', '116');
          }

          //INSERTING EVENT WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitegroupevent.profile-sitegroupevents', $group_id, 'Events', 'true', '117');
          }

					//INSERTING EVENT WIDGET 
					if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
						Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('siteevent.contenttype-events', $group_id, 'Events', 'true', '117');
					}

          //INSERTING POLL WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitegrouppoll.profile-sitegrouppolls', $group_id, 'Polls', 'true', '118');
          }

          //INSERTING DISCUSSION WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitegroup.discussion-sitegroup', $group_id, 'Discussions', 'true', '119');
          }

          //INSERTING MUSIC WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitegroupmusic.profile-sitegroupmusic', $group_id, 'Music', 'true', '120');
          }
          //INSERTING TWITTER WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitegrouptwitter.feeds-sitegrouptwitter', $group_id, 'Twitter', 'true', '121');
          }

					if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
						Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitegroupmember.profile-sitegroupmembers', $group_id, 'Members', 'true', '122');
						Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitegroupmember.profile-sitegroupmembers-announcements', $group_id, 'Announcements', 'true', '123');
					}

					//INSERTING INTEGRATION WIDGET 
					if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration')) {
						Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminContentDefaultInfoWithoutTab('sitegroupintegration.profile-items', $group_id, '', '', '999');
					}
        }
      }
      Engine_Api::_()->getApi("settings", "core")->setSetting('sitegroup.layout.setting', $layout_option);
      Engine_Api::_()->getApi("settings", "core")->setSetting('sitegroup.layout.coverphotoenabled', 1);

      Engine_Api::_()->getApi("settings", "core")->setSetting('sitegroup.layout.cover.photo', $sitegroup_layout_cover_photo);
      if ($group_reload < $reload_count) {
        $group_reload++;
        $this->_redirect("admin/sitegroup/defaultlayout/index?group_reload=$group_reload&sitegroup_sitegroup_layout_setting=$layout_option");
      }
      
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => false,
          'redirect' => $this->view->url(array('module' => 'sitegroup', 'controller' => 'defaultlayout'), 'admin_default', true),
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('<div class="tip" style="margin:10px auto;width:750px;"><span>Please do not close this group or navigate to another group till you see a default layout changes completion or error message.</span></div><div>
					<center><img src="application/modules/Sitegroup/externals/images/layout/uploading.gif" alt="" /></center>
				</div>'))
      ));
    }
  }

  //ACTION FOR SAVING THE LAYOUT
  public function savelayoutAction() {

    $this->_helper->layout->setLayout('admin-simple');
  }

  public function saveCoverGroupLayoutAction() {
   

    if(Engine_Api::_()->getApi("settings", "core")->getSetting('sitegroup.layout.coverphotoenabled', 0) && $this->getRequest()->isPost())
      return;
      $db = Engine_Db_Table::getDefaultAdapter();
   	$coreContentTable = Engine_Api::_()->getDbtable('content', 'core');
		$coreContentTableName = $coreContentTable->info('name');
   	$corePagesTable = Engine_Api::_()->getDbtable('pages', 'core');
		$corePagesTableName = $corePagesTable->info('name');
		$adminContentTable = Engine_Api::_()->getDbtable('admincontent', 'sitegroup');
		$adminContentTableName = $adminContentTable->info('name');
		$groupContentTable = Engine_Api::_()->getDbtable('content', 'sitegroup');
		$groupContentTableName = $groupContentTable->info('name');
		$coreContentGroupID = $corePagesTable->select()->from($corePagesTableName, array('page_id'))->where('name =?', 'sitegroup_index_view')->query()->fetchColumn();
		$adminContentId = $adminContentTable->select()->from($adminContentTableName, array('name'))->where('group_id =?', $coreContentGroupID)->where('name =?', 'left')->where('name =?', 'right')->query()->fetchColumn();
    $sitegroupCoverPhoto = $_POST['sitegroup_sitegroup_layout_coverphoto'];
		if($_POST['sitegroup_sitegroup_layout_coverphoto']  == 1) {
			$current='right';
			$prev='left';
		} else {
			$current='left';
			$prev='right';
		}
		if(empty($adminContentId) ) {
		$adminContentCurrentColumn = $adminContentTable->select()->from($adminContentTableName, array('name'))->where('name =?', $prev)->where('group_id =?', $coreContentGroupID)->query()->fetchColumn();
			if($adminContentCurrentColumn) {
				$adminContentTable->update(array('name' =>  $current), array('name=?' =>  $prev, 'group_id =?'=> $coreContentGroupID));
			}

			$adminContentTable->delete(array('name =?' => 'sitegroup.mainphoto-sitegroup'));
			$adminContentTable->delete(array('name =?' => 'sitegroupmember.profile-sitegroupmembers-announcements'));
			$adminContentTable->delete(array('name =?' => 'seaocore.like-button'));
			$adminContentTable->delete(array('name =?' => 'seaocore.seaocore-follow'));		
			$adminContentTable->delete(array('name =?' => 'facebookse.facebookse-commonlike'));	
			$adminContentTable->delete(array('name =?' => 'sitegroup.title-sitegroup'));	
      $adminContentTable->delete(array('name =?' => 'sitegroup.photorecent-sitegroup'));
		}

		$groupContentId = $groupContentTable->select()->from($groupContentTableName, array('name'))->where('name =?', 'left')->where('name =?', 'right')->query()->fetchColumn();
		
		if(empty($groupContentId) ) {
			$groupContentCurrentColumn = $groupContentTable->select()->from($groupContentTableName, array('name'))->where('name =?', $prev)->query()->fetchColumn();
			if($groupContentCurrentColumn) {
				$groupContentTable->update(array('name' =>  $current), array('name=?' =>  $prev));
			}

			$groupContentTable->delete(array('name =?' => 'sitegroup.mainphoto-sitegroup'));
      $groupContentTable->delete(array('name =?' => 'sitegroup.photorecent-sitegroup'));
			$groupContentTable->delete(array('name =?' => 'sitegroupmember.profile-sitegroupmembers-announcements'));
			$groupContentTable->delete(array('name =?' => 'seaocore.like-button'));
			$groupContentTable->delete(array('name =?' => 'seaocore.seaocore-follow'));		
			$groupContentTable->delete(array('name =?' => 'facebookse.facebookse-commonlike'));	
			$groupContentTable->delete(array('name =?' => 'sitegroup.title-sitegroup'));	
		}
		
    $coreContentId = $coreContentTable->select()->from($coreContentTableName, array('name'))->where('page_id =?', $coreContentGroupID)->where('name =?', 'left')->where('name =?', 'right')->query()->fetchColumn();
		
		if(empty($coreContentId) ) {
		$coreContentCurrentColumn = $coreContentTable->select()->from($coreContentTableName, array('name'))->where('page_id =?', $coreContentGroupID)->where('name =?', $prev)->where('name =?', $prev)->where('page_id =?', $coreContentGroupID)->query()->fetchColumn();
			if($coreContentCurrentColumn) {
				$coreContentTable->update(array('name' =>  $current), array('name=?' =>  $prev, 'page_id =?'=> $coreContentGroupID));
			}

			$coreContentTable->delete(array('name =?' => 'sitegroup.mainphoto-sitegroup'));
      $coreContentTable->delete(array('name =?' => 'sitegroup.photorecent-sitegroup'));
			$coreContentTable->delete(array('name =?' => 'sitegroupmember.profile-sitegroupmembers-announcements'));
			$coreContentTable->delete(array('name =?' => 'seaocore.like-button'));
			$coreContentTable->delete(array('name =?' => 'seaocore.seaocore-follow'));		
			$coreContentTable->delete(array('name =?' => 'facebookse.facebookse-commonlike'));	
			$coreContentTable->delete(array('name =?' => 'sitegroup.title-sitegroup'));	
		}

    Engine_Api::_()->getApi("settings", "core")->setSetting('sitegroup.layout.coverphotoenabled', 1);
    Engine_Api::_()->getApi("settings", "core")->setSetting('sitegroup.layout.coverphoto', $sitegroupCoverPhoto);
    
    		$select = new Zend_Db_Select($db);
		$select_group = $select
								->from('engine4_core_pages', 'page_id')
								->where('name = ?', 'sitegroup_index_view')
								->limit(1);
		$group = $select_group->query()->fetchAll();
		
		if(!empty($group)) {
			$group_id = $group[0]['page_id'];

			//INSERTING THE MEMBER WIDGET IN SITEGROUP_CONTENT TABLE ALSO.
			$select = new Zend_Db_Select($db);
			$contentgroup_ids = $select->from('engine4_sitegroup_contentgroups', 'contentgroup_id')->query()->fetchAll();
			foreach ($contentgroup_ids as $contentgroup_id) {
				if(!empty($contentgroup_id)) {

						$select = new Zend_Db_Select($db);
						$select_content = $select
												->from('engine4_sitegroup_content')
												->where('contentgroup_id = ?', $contentgroup_id['contentgroup_id'])
												->where('type = ?', 'widget')
												->where('name = ?', 'sitegroup.group-cover-information-sitegroup')
												->limit(1);
						$content = $select_content->query()->fetchAll();
						if(empty($content)) {
							$select = new Zend_Db_Select($db);
							$select_container = $select
													->from('engine4_sitegroup_content', 'content_id')
													->where('contentgroup_id = ?', $contentgroup_id['contentgroup_id'])
													->where('type = ?', 'container')
													->limit(1);
							$container = $select_container->query()->fetchAll();
							if(!empty($container)) {
								$container_id = $container[0]['content_id'];
								$select = new Zend_Db_Select($db);
								$select_left = $select
													->from('engine4_sitegroup_content')
													->where('parent_content_id = ?', $container_id)
													->where('type = ?', 'container')
													->where('name = ?', 'middle')
													->limit(1);
								$middle = $select_left->query()->fetchAll();
								if(!empty($middle)) {
									$middle_id = $middle[0]['content_id'];
									$db->insert('engine4_sitegroup_content', array(
									'contentgroup_id' => $contentgroup_id['contentgroup_id'],
									'type' => 'widget',
									'name' => 'sitegroup.group-cover-information-sitegroup',
									'parent_content_id' => $middle_id,
									'order' => 1,
									'params' => '{"title":""}',
									));
								}
							}
						}
				}
			}

			$select = new Zend_Db_Select($db);
			$select_content = $select
									->from('engine4_sitegroup_admincontent')
									->where('group_id = ?', $group_id)
									->where('type = ?', 'widget')
									->where('name = ?', 'sitegroup.group-cover-information-sitegroup')
									->limit(1);
			$content = $select_content->query()->fetchAll();
			if(empty($content)) {
				$select = new Zend_Db_Select($db);
				$select_container = $select
										->from('engine4_sitegroup_admincontent', 'admincontent_id')
										->where('group_id = ?', $group_id)
										->where('type = ?', 'container')
										->limit(1);
				$container = $select_container->query()->fetchAll();
				if(!empty($container)) {
					$container_id = $container[0]['admincontent_id'];
					$select = new Zend_Db_Select($db);
					$select_left = $select
										->from('engine4_sitegroup_admincontent')
										->where('parent_content_id = ?', $container_id)
										->where('type = ?', 'container')
										->where('name = ?', 'middle')
										->limit(1);
					$middle = $select_left->query()->fetchAll();
					if(!empty($middle)) {
						$middle_id = $middle[0]['admincontent_id'];
						$db->insert('engine4_sitegroup_admincontent', array(
						'group_id' => $group_id,
						'type' => 'widget',
						'name' => 'sitegroup.group-cover-information-sitegroup',
						'parent_content_id' => $middle_id,
						'order' => 1,
						'params' => '{"title":""}',
						));
					}
				}
			} 
			
				$select = new Zend_Db_Select($db);
				$select_content = $select
										->from('engine4_core_content')
										->where('page_id = ?', $group_id)
										->where('type = ?', 'widget')
										->where('name = ?', 'sitegroup.group-cover-information-sitegroup')
										->limit(1);
				$content = $select_content->query()->fetchAll();
				if(empty($content)) {
					$select = new Zend_Db_Select($db);
					$select_container = $select
											->from('engine4_core_content', 'content_id')
											->where('page_id = ?', $group_id)
											->where('type = ?', 'container')
											->limit(1);
					$container = $select_container->query()->fetchAll();
					if(!empty($container)) {
						$container_id = $container[0]['content_id'];
						$select = new Zend_Db_Select($db);
						$select_left = $select
											->from('engine4_core_content')
											->where('parent_content_id = ?', $container_id)
											->where('type = ?', 'container')
											->where('name = ?', 'middle')
											->limit(1);
						$middle = $select_left->query()->fetchAll();
						if(!empty($middle)) {
							$middle_id = $middle[0]['content_id'];
							$db->insert('engine4_core_content', array(
							'page_id' => $group_id,
							'type' => 'widget',
							'name' => 'sitegroup.group-cover-information-sitegroup',
							'parent_content_id' => $middle_id,
							'order' => 1,
							'params' => '{"title":""}',
							));
						}
					}
				}
		}

    $this->_redirect("admin/sitegroup/defaultlayout/index");

	}

  //ACTION FOR SAVING THE LAYOUT
  public function setCoverGroupLayoutAction() {

    $this->_helper->layout->setLayout('admin-simple');
  }

}

?>
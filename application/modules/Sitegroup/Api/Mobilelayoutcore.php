<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Layoutcore.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Api_MobilelayoutCore extends Core_Api_Abstract {

  /**
   * Sets the without tab widgets information in core content table
   *
   * @param int $group_id
   */
  public function setWithoutTabContent($group_id, $sitegroup_layout_cover_photo) {

    //GET CONTENT TABLE
    $contentTable = Engine_Api::_()->getDbtable('content', 'core');

    //GET CONTENT TABLE NAME
    $contentTableName = $contentTable->info('name');

    //INSERTING MAIN CONTAINER
    $mainContainer = $contentTable->createRow();
    $mainContainer->group_id = $group_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->content_id;

    //INSERTING MAIN-MIDDLE CONTAINER
    $mainMiddleContainer = $contentTable->createRow();
    $mainMiddleContainer->group_id = $group_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->content_id;

    //INSERTING MAIN-LEFT CONTAINER
    $mainLeftContainer = $contentTable->createRow();
    $mainLeftContainer->group_id = $group_id;
    $mainLeftContainer->type = 'container';
    $mainLeftContainer->name = 'right';
    $mainLeftContainer->parent_content_id = $container_id;
    $mainLeftContainer->order = 4;
    $mainLeftContainer->save();
    $left_id = $mainLeftContainer->content_id;

    //INSERTING TITLE WIDGET
		if(empty($sitegroup_layout_cover_photo)) {

			//INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
			Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-profile-breadcrumb', $middle_id, 1, '', 'true');

			//INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
				Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupmember.groupcover-photo-sitegroupmembers', $middle_id, 2, '', 'true');
      }

			Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.title-sitegroup', $middle_id, 3,'','true');

			//INSERTING LIKE WIDGET 
			Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'seaocore.like-button', $middle_id, 4,'','true');

			//INSERTING FACEBOOK LIKE WIDGET
			if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
				Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'Facebookse.facebookse-sitegroupprofilelike', $middle_id, 5,'','true');
			}

			//INSERTING MAIN PHOTO WIDGET 
			Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.mainphoto-sitegroup', $left_id, 10,'','true');

    } else {
			//INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
			Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-profile-breadcrumb', $middle_id, 1, '', 'true');

			//INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
			Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-cover-information-sitegroup', $middle_id, 2, '', 'true');
    }

    //INSERTING CONTACT DETAIL WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.contactdetails-sitegroup', $middle_id, 5,'','true');
    
    //INSERTING PHOTO STRIP WIDGET   
//     if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
//       Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.photorecent-sitegroup', $middle_id, 6,'','true');
//     }

    //INSERTING ACTIVITY FEED WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
      $advanced_activity_params =
  '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
      Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'advancedactivity.home-feeds', $middle_id, 6, 'Updates', 'true',$advanced_activity_params);
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
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.widgetlinks-sitegroup', $left_id, 11,'','true');

    //INSERTING OPTIONS WIDGET 
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.options-sitegroup', $left_id, 12,'','true');

    //INSERTING WRITE SOMETHING ABOUT WIDGET 
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.write-group', $left_id, 13,'','true');

    //INSERTING INFORMATION WIDGET 
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.information-sitegroup', $left_id, 10, 'Information', 'true');

    //INSERTING LIKE WIDGET 
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'seaocore.people-like', $left_id, 15,'','true');

    //INSERTING RATING WIDGET 	
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
      Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupreview.ratings-sitegroupreviews', $left_id, 16, 'Ratings','true');
    }
    
    //INSERTING BADGE WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge')) {
      Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupbadge.badge-sitegroupbadge', $left_id, 17, 'Badge','true');
    }

		$social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitegroup.socialshare-sitegroup"}';

    //INSERTING SOCIAL SHARE WIDGET 
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.socialshare-sitegroup', $left_id, 19, 'Social Share','true', $social_share_default_code);

//    //INSERTING FOUR SQUARE WIDGET 
//    Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.foursquare-sitegroup', $left_id, 20,'','true');

    //INSERTING INSIGHTS WIDGET 
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.insights-sitegroup', $left_id, 22, 'Insights','true');

    //INSERTING FEATURED OWNER WIDGET 
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.featuredowner-sitegroup', $left_id, 23, 'Owners','true');

    //INSERTING ALBUM WIDGET 
    $sitegroupAlbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
    if ($sitegroupAlbumEnabled) {
      Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.albums-sitegroup', $left_id, 24, 'Albums','true');
      $this->setDefaultInfoWithoutTab('sitegroup.photos-sitegroup', $group_id, 'Photos', 'true', '110');
    }

    //INSERTING LINKED GROUPS WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.favourite-group', $left_id, 26, 'Linked Groups','true');
    
    //INSERTING VIDEO WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
      Engine_Api::_()->sitegroup()->setDefaultDataWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupmusic.profile-player', $left_id, 25,'','true');
    }
    
    //INSERTING VIDEO WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
      $this->setDefaultInfoWithoutTab('sitegroupvideo.profile-sitegroupvideos', $group_id, 'Videos', 'true', '111');
    }
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
     $this->setDefaultInfoWithoutTab('sitevideo.contenttype-videos', $group_id, 'Videos', 'true', '117');
    }    

    //INSERTING NOTE WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) {
      $this->setDefaultInfoWithoutTab('sitegroupnote.profile-sitegroupnotes', $group_id, 'Notes', 'true', '112');
    }

    //INSERTING REVIEW WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
      $this->setDefaultInfoWithoutTab('sitegroupreview.profile-sitegroupreviews', $group_id, 'Reviews', 'true', '113');
    }

    //INSERTING FORM WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform')) {
      $this->setDefaultInfoWithoutTab('sitegroupform.sitegroup-viewform', $group_id, 'Form', 'false', '114');
    }

    //INSERTING DOCUMENT WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
      $this->setDefaultInfoWithoutTab('sitegroupdocument.profile-sitegroupdocuments', $group_id, 'Documents', 'true', '115');
    }
    
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
     $this->setDefaultInfoWithoutTab('document.contenttype-documents', $group_id, 'Documents', 'true', '115');
    }

    //INSERTING OFFER WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer')) {
      $this->setDefaultInfoWithoutTab('sitegroupoffer.profile-sitegroupoffers', $group_id, 'Offers', 'true', '116');
    }

    //INSERTING EVENT WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
      $this->setDefaultInfoWithoutTab('sitegroupevent.profile-sitegroupevents', $group_id, 'Events', 'true', '117');
    }

    //INSERTING EVENT WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
     $this->setDefaultInfoWithoutTab('siteevent.contenttype-events', $group_id, 'Events', 'true', '117');
    }

    //INSERTING POLL WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
      $this->setDefaultInfoWithoutTab('sitegrouppoll.profile-sitegrouppolls', $group_id, 'Polls', 'true', '118');
    }

    //INSERTING DISCUSSION WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
      $this->setDefaultInfoWithoutTab('sitegroup.discussion-sitegroup', $group_id, 'Discussions', 'true', '119');
    }

    //INSERTING MUSIC WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
      $this->setDefaultInfoWithoutTab('sitegroupmusic.profile-sitegroupmusic', $group_id, 'Music', 'true', '120');
    }
    
    //INSERTING TWITTER WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter')) {
      $this->setDefaultInfoWithoutTab('sitegrouptwitter.feeds-sitegrouptwitter', $group_id, 'Twitter', 'true', '121');
    }

    //INSERTING MEMBER WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
      $this->setDefaultInfoWithoutTab('sitegroupmember.profile-sitegroupmembers', $group_id, 'Member', 'true', '122');
      $this->setDefaultInfoWithoutTab('sitegroupmember.profile-sitegroupmembers-announcements', $group_id, 'Announcements', 'true', '123');
    }

		//INSERTING MEMBER WIDGET
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration')) {
			$this->setDefaultInfoWithoutTab('sitegroupintegration.profile-items', $group_id, '', '', 999);
		}

  }

  /**
   * Sets the tab widgets information in core content table
   *
   * @param int $group_id
   */
  public function setTabbedLayoutContent($group_id, $sitegroup_layout_cover_photo) {

    //NOW INSERTING DEFUALT INFO OF OTHER SUB PLUGINS WHICH ARE DEPENDENTS ON THIS SITEGROUP PLUGIN ARE ENABLED.
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
      $this->setContentDefaultInfo('sitegroup.photos-sitegroup', $group_id, 'Photos', 'true', '110');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
      $this->setContentDefaultInfo('sitegroupvideo.profile-sitegroupvideos', $group_id, 'Videos', 'true', '111');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
     $this->setContentDefaultInfo('sitevideo.contenttype-videos', $group_id, 'Videos', 'true', '117');
    } 
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) {
      $this->setContentDefaultInfo('sitegroupnote.profile-sitegroupnotes', $group_id, 'Notes', 'true', '112');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
      $this->setContentDefaultInfo('sitegroupreview.profile-sitegroupreviews', $group_id, 'Reviews', 'true', '113');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform')) {
      $this->setContentDefaultInfo('sitegroupform.sitegroup-viewform', $group_id, 'Form', 'false', '114');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
      $this->setContentDefaultInfo('sitegroupdocument.profile-sitegroupdocuments', $group_id, 'Documents', 'true', '115');
    }
    
     if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
     $this->setContentDefaultInfo('document.contenttype-documents', $group_id, 'Documents', 'true', '115');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer')) {
      $this->setContentDefaultInfo('sitegroupoffer.profile-sitegroupoffers', $group_id, 'Offers', 'true', '116');
    }

    //INSERTING EVENT WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
      $this->setContentDefaultInfo('sitegroupevent.profile-sitegroupevents', $group_id, 'Events', 'true', '117');
    }

    //INSERTING EVENT WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
     $this->setContentDefaultInfo('siteevent.contenttype-events', $group_id, 'Events', 'true', '117');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
      $this->setContentDefaultInfo('sitegrouppoll.profile-sitegrouppolls', $group_id, 'Polls', 'true', '118');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
      $this->setContentDefaultInfo('sitegroup.discussion-sitegroup', $group_id, 'Discussions', 'true', '119');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
      $this->setContentDefaultInfo('sitegroupmusic.profile-sitegroupmusic', $group_id, 'Music', 'true', '120');
    }
    //INSERTING TWITTER WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter')) {
      $this->setContentDefaultInfo('sitegrouptwitter.feeds-sitegrouptwitter', $group_id, 'Twitter', 'true', '121');
    }
		//INSERTING MEMBER WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
      $this->setContentDefaultInfo('sitegroupmember.profile-sitegroupmembers', $group_id, 'Member', 'true', '122');
      $this->setContentDefaultInfo('sitegroupmember.profile-sitegroupmembers-announcements', $group_id, 'Announcements', 'true', '123');
    }

		//INSERTING MEMBER WIDGET
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration')) {
			$this->setContentDefaultInfo('sitegroupintegration.profile-items', $group_id, '', '', 999);
		}
  }

  /**
   * Sets the tab widgets information in admin content and also content table for user layout
   *
   * @param int $group_id
   */
  public function setContentDefaultLayout($group_id) {

    //GET ADMIN CONTENT TABLE
    $admincontentTable = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitegroup');

    //GET CONTENT TABLE
    $contentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitegroup');

    //FETCH
    $coregroupinfo = Engine_Api::_()->sitegroup()->getMobileWidgetizedGroup();

    //SELECT ADMIN CONTENT
    $admincontentselected = $admincontentTable->select()->where('group_id =?', $coregroupinfo->page_id)->where('type =?', 'container');

    //FETCH
    $admintableinfo = $admincontentTable->fetchAll($admincontentselected);

    //DEFINE OLD CONTAINER   
    $oldContener = array();

    //CREATING A ROW
    foreach ($admintableinfo as $value) {
      $mainContainer = $contentTable->createRow();
      $mainContainer->mobilecontentgroup_id = $group_id;
      $mainContainer->type = 'container';
      $mainContainer->name = $value->name;
      $mainContainer->order = $value->order;
      $mainContainer->params = $value->params;
      if (isset($oldContener[$value->parent_content_id]))
        $mainContainer->parent_content_id = $oldContener[$value->parent_content_id];
      $mainContainer->save();
      $container_id = $mainContainer->mobilecontent_id;
      $oldContener[$value->mobileadmincontent_id] = $container_id;
    }

    //SELECT ADMIN CONTENT
    $admincontentselected = $admincontentTable->select()->where('group_id =?', $coregroupinfo->page_id)->where('type =?', 'widget')->where('name =?', 'sitemobile.container-tabs-columns');

    //FETCH
    $admintableinfo = $admincontentTable->fetchAll($admincontentselected);

    //CREATING A ROW
    foreach ($admintableinfo as $values) {
      $mainWidgets = $contentTable->createRow();
      $mainWidgets->mobilecontentgroup_id = $group_id;
      $mainWidgets->type = 'widget';
      $mainWidgets->name = $values->name;
      $mainWidgets->order = $values->order;
      $mainWidgets->params = $values->params;
      if (isset($oldContener[$values->parent_content_id]))
        $mainWidgets->parent_content_id = $oldContener[$values->parent_content_id];
      $mainWidgets->save();
      $container_id = $mainWidgets->mobilecontent_id;
      $oldContener[$values->mobileadmincontent_id] = $container_id;
    }

    //SELECT ADMIN CONTENT
    $admincontentselected = $admincontentTable->select()->where('group_id =?', $coregroupinfo->page_id)->where('type =?', 'widget')->where('name <>?', 'sitemobile.container-tabs-columns');

    //FETCH
    $admintableinfo = $admincontentTable->fetchAll($admincontentselected);

    //CREATING A ROW
    foreach ($admintableinfo as $values) {
      $mainWidgets = $contentTable->createRow();
      $mainWidgets->mobilecontentgroup_id = $group_id;
      $mainWidgets->type = 'widget';
      $mainWidgets->name = $values->name;
      $mainWidgets->order = $values->order;
      $mainWidgets->params = $values->params;
      if (isset($oldContener[$values->parent_content_id]))
        $mainWidgets->parent_content_id = $oldContener[$values->parent_content_id];
      $mainWidgets->save();
    }
  }

  /**
   * Set profile group default widget in core content table with tab
   *
   * @param string $name
   * @param int $group_id
   * @param string $title
   * @param int $titleCount
   * @param int $order
   */
  public function setContentDefaultInfo($name = null, $group_id, $title = null, $titleCount = null, $order = null, $params=null) {
    $db = Engine_Db_Table::getDefaultAdapter();
    if (!empty($name)) {
      $contentTable = Engine_Api::_()->getDbtable('content', 'sitemobile');
      $contentTableName = $contentTable->info('name');
      $select = $contentTable->select();
      $select_content = $select
              ->from($contentTableName)
              ->where('page_id = ?', $group_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', $name)
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = $contentTable->select();
        $select_container = $select
                ->from($contentTableName, array('content_id'))
                ->where('page_id = ?', $group_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['content_id'];
          $select = $contentTable->select();
          $select_middle = $select
                  ->from($contentTableName)
                  ->where('parent_content_id = ?', $container_id)
                  ->where('type = ?', 'container')
                  ->where('name = ?', 'middle')
                  ->limit(1);
          $middle = $select_middle->query()->fetchAll();
          if (!empty($middle)) {
            $middle_id = $middle[0]['content_id'];
            $select = $contentTable->select();
            $select_tab = $select
                    ->from($contentTableName)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'sitemobile.container-tabs-columns')
                    ->where('page_id = ?', $group_id)
                    ->limit(1);
            $tab = $select_tab->query()->fetchAll();
            $tab_id='';
            if (!empty($tab)) {
              $tab_id = $tab[0]['content_id'];
            } else {
							$contentWidget = $contentTable->createRow();
							$contentWidget->page_id = $group_id;
							$contentWidget->type = 'widget';
							$contentWidget->name = 'sitemobile.container-tabs-columns';
							$contentWidget->parent_content_id = $middle_id;
							$contentWidget->order = $order;
              $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.showmore', 8);
							$contentWidget->params = "{\"max\":\"$showmaxtab\"}";
							$tab_id = $contentWidget->save();
            }

            if($name != 'sitegroupintegration.profile-items') {
							$contentWidget = $contentTable->createRow();
							$contentWidget->page_id = $group_id;
							$contentWidget->type = 'widget';
							$contentWidget->name = $name;
							$contentWidget->parent_content_id = ($tab_id ? $tab_id : $middle_id);
							$contentWidget->order = $order;
							$contentWidget->params = '{"title":"' . $title . '" ,"titleCount":' . $titleCount . '}';

							if($params) {
								$contentWidget->params = $params;
							} else {
								$contentWidget->params = '{"title":"' . $title . '" , "titleCount":' . $titleCount . '}';
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
                   $resource_type = $value['resource_type']. '_'. $value['listingtype_id'];

                  // Check if it's already been placed
                  $select = new Zend_Db_Select($db);
                  $select
                          ->from('engine4_core_content')
                          ->where('parent_content_id = ?', $tab_id)
                          ->where('type = ?', 'widget')
                          ->where('name = ?', 'sitegroupintegration.profile-items')
                          ->where('params = ?', '{"title":"' . $item_title . '","resource_type":"'.$resource_type.'","nomobile":"0","name":"sitegroupintegration.profile-items"}');
                  $info = $select->query()->fetch();
                  if (empty($info)) {

                    // tab on profile
                    $db->insert('engine4_core_content', array(
                        'page_id' => $group_id,
                        'type' => 'widget',
                        'name' => 'sitegroupintegration.profile-items',
                        'parent_content_id' => $tab_id,
                        'order' => 999,
                        'params' => '{"title":"' . $item_title . '","resource_type":"'.$resource_type.'","nomobile":"0","name":"sitegroupintegration.profile-items"}',
                    ));
                  }
                  //}
                }
              }

              
              $this->setgroupsintwidgetTab('document', '{"title":"Documents","resource_type":"document_0","nomobile":"0","name":"sitegroupsintegration.profile-items"}', $tab_id, $group_id);

              $this->setgroupsintwidgetTab('sitebusiness', '{"title":"Businesses","resource_type":"sitebusiness_business_0","nomobile":"0","name":"sitegroupsintegration.profile-items"}', $tab_id, $group_id);

              $this->setgroupsintwidgetTab('sitepage', '{"title":"Pages","resource_type":"sitepage_page_0","nomobile":"0","name":"sitegroupsintegration.profile-items"}', $tab_id, $group_id);
              
              $this->setgroupsintwidgetTab('sitestoreproduct', '{"title":"Products","resource_type":"sitestoreproduct_product_0","nomobile":"0","name":"sitegroupsintegration.profile-items"}', $tab_id, $group_id);
              
              $this->setgroupsintwidgetTab('sitefaq', '{"title":"FAQs","resource_type":"sitefaq_faq_0","nomobile":"0","name":"sitegroupsintegration.profile-items"}', $tab_id, $group_id);
              
              $this->setgroupsintwidgetTab('sitetutorial', '{"title":"Tutorials","resource_type":"sitetutorial_tutorial_0","nomobile":"0","name":"sitegroupsintegration.profile-items"}', $tab_id, $group_id);

              $this->setgroupsintwidgetTab('list', '{"title":"Listings","resource_type":"list_listing_0","nomobile":"0","name":"sitegroupsintegration.profile-items"}', $tab_id, $group_id);
              
              $this->setgroupsintwidgetTab('quiz', '{"title":"Quiz","resource_type":"quiz_0","nomobile":"0","name":"sitegroupsintegration.profile-items"}', $tab_id, $group_id);
              
              $this->setgroupsintwidgetTab('folder', '{"title":"Folder","resource_type":"folder_0","nomobile":"0","name":"sitegroupsintegration.profile-items"}', $tab_id, $group_id);
            }
          }
        }
      }
    }
  }

  /**
   * Set profile group default widget in core content table without tab
   *
   * @param string $name
   * @param int $group_id
   * @param string $title
   * @param int $titleCount
   * @param int $order
   */
  public function setDefaultInfoWithoutTab($name = null, $group_id, $title = null, $titleCount = null, $order = null) {
    $db = Engine_Db_Table::getDefaultAdapter();
    if (!empty($name)) {
      $contentTable = Engine_Api::_()->getDbtable('content', 'core');
      $contentTableName = $contentTable->info('name');
      $select = $contentTable->select();
      $select_content = $select
              ->from($contentTableName)
              ->where('group_id = ?', $group_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', $name)
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = $contentTable->select();
        $select_container = $select
                ->from($contentTableName, array('content_id'))
                ->where('group_id = ?', $group_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['content_id'];
          $select = $contentTable->select();
          $select_middle = $select
                  ->from($contentTableName)
                  ->where('parent_content_id = ?', $container_id)
                  ->where('type = ?', 'container')
                  ->where('name = ?', 'middle')
                  ->limit(1);
          $middle = $select_middle->query()->fetchAll();
          if (!empty($middle)) {
            $middle_id = $middle[0]['content_id'];

            if($name != 'sitegroupintegration.profile-items') {
							$contentWidget = $contentTable->createRow();
							$contentWidget->group_id = $group_id;
							$contentWidget->type = 'widget';
							$contentWidget->name = $name;
							$contentWidget->parent_content_id = ($middle_id);
							$contentWidget->order = $order;
							$contentWidget->params = '{"title":"' . $title . '" , "titleCount":' . $titleCount . '}';
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
                   $resource_type = $value['resource_type']. '_'. $value['listingtype_id'];

                  // Check if it's already been placed
                  $select = new Zend_Db_Select($db);
                  $select
                          ->from('engine4_core_content')
                          ->where('parent_content_id = ?', $middle_id)
                          ->where('type = ?', 'widget')
                          ->where('name = ?', 'sitegroupintegration.profile-items')
                          ->where('params = ?', '{"title":"' . $item_title . '","resource_type":"'.$resource_type.'","nomobile":"0","name":"sitegroupintegration.profile-items"}');
                  $info = $select->query()->fetch();
                  if (empty($info)) {

                    // tab on profile
                    $db->insert('engine4_core_content', array(
                        'group_id' => $group_id,
                        'type' => 'widget',
                        'name' => 'sitegroupintegration.profile-items',
                        'parent_content_id' => $middle_id,
                        'order' => 999,
                        'params' => '{"title":"' . $item_title . '","resource_type":"'.$resource_type.'","nomobile":"0","name":"sitegroupintegration.profile-items"}',
                    ));
                  }
                  //}
                }
              }

              //START GROUP PLUIGN WORK  
              $select = new Zend_Db_Select($db);
              $select
                      ->from('engine4_core_modules')
                      ->where('name = ?', 'document');
              $check_sitedocument = $select->query()->fetchObject();
              if (!empty($check_sitedocument)) {
                $results = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getIntegrationItems();

                foreach ($results as $value) {

                  // Check if it's already been placed
                  $select = new Zend_Db_Select($db);
                  $select
                          ->from('engine4_core_content')
                          ->where('parent_content_id = ?', $middle_id)
                          ->where('type = ?', 'widget')
                          ->where('name = ?', 'sitegroupintegration.profile-items')
													->where('params = ?', '{"title":"Documents","resource_type":"document_0","nomobile":"0","name":"sitegroupintegration.profile-items"}');
                  $info = $select->query()->fetch();
                  if (empty($info)) {

                    // tab on profile
                    $db->insert('engine4_core_content', array(
                        'group_id' => $group_id,
                        'type' => 'widget',
                        'name' => 'sitegroupintegration.profile-items',
                        'parent_content_id' => $middle_id,
                        'order' => 999,
                        'params' => '{"title":"c","resource_type":"document_0","nomobile":"0","name":"sitegroupintegration.profile-items"}',
                    ));
                  }
                }
              }
              //END GROUP PLUIGN WORK 
              
              //START GROUP PLUIGN WORK  
              $select = new Zend_Db_Select($db);
              $select
                      ->from('engine4_core_modules')
                      ->where('name = ?', 'sitegroup');
              $check_sitegroup = $select->query()->fetchObject();
              if (!empty($check_sitegroup)) {
                $results = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getIntegrationItems();

                foreach ($results as $value) {

                  // Check if it's already been placed
                  $select = new Zend_Db_Select($db);
                  $select
                          ->from('engine4_core_content')
                          ->where('parent_content_id = ?', $middle_id)
                          ->where('type = ?', 'widget')
                          ->where('name = ?', 'sitegroupintegration.profile-items')
													->where('params = ?', '{"title":"Groups","resource_type":"sitegroup_group_0","nomobile":"0","name":"sitegroupintegration.profile-items"}');
                  $info = $select->query()->fetch();
                  if (empty($info)) {

                    // tab on profile
                    $db->insert('engine4_core_content', array(
                        'group_id' => $group_id,
                        'type' => 'widget',
                        'name' => 'sitegroupintegration.profile-items',
                        'parent_content_id' => $middle_id,
                        'order' => 999,
                        'params' => '{"title":"Groups","resource_type":"sitegroup_group_0","nomobile":"0","name":"sitegroupintegration.profile-items"}',
                    ));
                  }
                }
              }
              //END GROUP PLUIGN WORK 
              
              $select = new Zend_Db_Select($db);
              $select
                      ->from('engine4_core_modules')
                      ->where('name = ?', 'sitegroup');
              $check_sitegroup = $select->query()->fetchObject();
              if (!empty($check_sitegroup)) {
                $results = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getIntegrationItems();

                foreach ($results as $value) {

                  // Check if it's already been placed
                  $select = new Zend_Db_Select($db);
                  $select
                          ->from('engine4_core_content')
                          ->where('parent_content_id = ?', $middle_id)
                          ->where('type = ?', 'widget')
                          ->where('name = ?', 'sitegroupintegration.profile-items')
													->where('params = ?', '{"title":"Groups","resource_type":"sitegroup_group_0","nomobile":"0","name":"sitegroupintegration.profile-items"}');
                  $info = $select->query()->fetch();
                  if (empty($info)) {

                    // tab on profile
                    $db->insert('engine4_core_content', array(
                        'group_id' => $group_id,
                        'type' => 'widget',
                        'name' => 'sitegroupintegration.profile-items',
                        'parent_content_id' => $middle_id,
                        'order' => 999,
                        'params' => '{"title":"Groups","resource_type":"sitegroup_group_0","nomobile":"0","name":"sitegroupintegration.profile-items"}',
                    ));
                  }
                }
              }
              
              $select = new Zend_Db_Select($db);
              $select
                      ->from('engine4_core_modules')
                      ->where('name = ?', 'list');
              $check_list = $select->query()->fetchObject();
              if (!empty($check_list)) {
                $results = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getIntegrationItems();

                foreach ($results as $value) {

                  // Check if it's already been placed
                  $select = new Zend_Db_Select($db);
                  $select
                          ->from('engine4_core_content')
                          ->where('parent_content_id = ?', $middle_id)
                          ->where('type = ?', 'widget')
                          ->where('name = ?', 'sitegroupintegration.profile-items')
													->where('params = ?', '{"title":"Listings","resource_type":"list_listing_0","nomobile":"0","name":"sitegroupintegration.profile-items"}');
                  $info = $select->query()->fetch();
                  if (empty($info)) {

                    // tab on profile
                    $db->insert('engine4_core_content', array(
                        'group_id' => $group_id,
                        'type' => 'widget',
                        'name' => 'sitegroupintegration.profile-items',
                        'parent_content_id' => $middle_id,
                        'order' => 999,
                        'params' => '{"title":"Listings","resource_type":"list_listing_0","nomobile":"0","name":"sitegroupintegration.profile-items"}',
                    ));
                  }
                  //}
                }
              }
            }
          }
        }
      }
    }
  }

  public function setgroupsintwidgetTab($module_name, $params, $tab_id, $group_id) {
 
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
						->from('engine4_sitemobile_content')
						->where('parent_content_id = ?', $tab_id)
						->where('type = ?', 'widget')
						->where('name = ?', 'sitegroupintegration.profile-items')
						->where('params = ?', $params);
				$info = $select->query()->fetch();
				if (empty($info)) {
					// tab on profile
					$db->insert('engine4_sitemobile_content', array(
							'page_id' => $group_id,
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

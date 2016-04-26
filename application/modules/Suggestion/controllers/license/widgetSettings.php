<?php
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $isSitereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitereview");
    if( !empty($isSitereviewEnabled) ) {
      $getListingType = $db->query("SELECT * FROM `engine4_sitereview_listingtypes` LIMIT 0 , 30")->fetchAll();
      if( !empty($getListingType) ) {
        foreach($getListingType as $listingType) {
          // Make Table exist conditions.
          $notificationType = "sitereview_" . $listingType['listingtype_id'] . "_suggestion";
          $isExist = $db->query("SELECT * FROM `engine4_suggestion_module_settings` WHERE `notification_type` LIKE '" . $notificationType . "' LIMIT 1")->fetch();

          $isSuggModTable = $db->query("SHOW TABLES LIKE 'engine4_suggestion_module_settings'")->fetch();
          if( empty($isExist) && !empty($isSuggModTable) ) {
            $tempReviewTitle = $listingType["title_singular"];
            $tempReviewTitle = strtolower($tempReviewTitle);
            $getReviewTitle = @ucfirst($tempReviewTitle);
            $suggSettingId = array("default" => 1, "listing_id" => $listingType['listingtype_id']);
            $suggNotificationType = $notificationType;
            $suggNotificationBody = '{item:$subject} has suggested to you a {item:$object:' . $tempReviewTitle . '}.';
            $suggestionModuleTable = Engine_Api::_()->getItemTable('suggestion_modinfo');
            $suggestionModuleTableName = $suggestionModuleTable->info('name');

            // Insert Notification Type in notification table.
            $db->query("INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type` , `module` , `body` , `is_request` ,`handler`) VALUES ('$suggNotificationType', 'suggestion', '$suggNotificationBody', 1, 'suggestion.widget.get-notify')");

            // Insert in Mail Template Table.
            $emailtemType = 'notify_' . $suggNotificationType;
            $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('$emailtemType', 'suggestion', '[suggestion_sender], [suggestion_entity], [email], [link]'
          );");

            // Show "Suggest to Friend" link on "Listing Profile Page".
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name` , `module` , `label` , `plugin` ,`params`, `menu`, `enabled`, `custom`, `order`) VALUES ("sitereview_gutter_suggesttofriend_'.$listingType['listingtype_id'].'", "suggestion", "Suggest to Friends", \'Suggestion_Plugin_Menus::showSitereview\', \'{"route":"suggest_to_friend_link","class":"buttonlink icon_review_friend_suggestion smoothbox", "listing_id": "'.$listingType['listingtype_id'].'", "type":"popup"}\', "sitereview_gutter_listtype_'.$listingType['listingtype_id'].'", 1, 0, 999 )');

            // Insert in Language Files.
            $language1 = array('You have a ' . $getReviewTitle . ' suggestion');
            $language2 = array('View all ' . $getReviewTitle . ' suggestions');
            $language3 = array('This ' . $tempReviewTitle . ' was suggested by');

            $temprequestWidgetLan = "sitereview " . $listingType['listingtype_id'] . " suggestion";
            $requestTab = array(
                "%s " . $temprequestWidgetLan => array("%s " . strtolower($getReviewTitle) . " suggestion", "%s " . strtolower($getReviewTitle) . " suggestions")
            );


            $languageModTitle = "SITEREVIEW_" . $listingType['listingtype_id'];
            $makeEmailArray = array(
              "_EMAIL_NOTIFY_" . $languageModTitle . "_SUGGESTION_TITLE" => $getReviewTitle . " Suggestion",
              "_EMAIL_NOTIFY_" . $languageModTitle . "_SUGGESTION_DESCRIPTION" => "This email is sent to the member when someone suggest a " . $getReviewTitle . '.',
              "_EMAIL_NOTIFY_" . $languageModTitle . "_SUGGESTION_SUBJECT" => $getReviewTitle . " Suggestion",
              "_EMAIL_NOTIFY_" . $languageModTitle . "_SUGGESTION_BODY" => "[header]

          [sender_title] has suggested to you a " . $getReviewTitle . ". To view this suggestion please click on: <a href='http://[host][object_link]'>http://[host][object_link]</a>.

          [footer]"
            );
            $userSettingsNotfication = array("ACTIVITY_TYPE_" . $languageModTitle . "_SUGGESTION" => "When I receive a " . strtolower($getReviewTitle) . " suggestion.");
            $userNotification = array($notificationLanguage => $notificationLanguage);

            $this->addPhraseAction($makeEmailArray);
            $this->addPhraseAction($userSettingsNotfication);
            $this->addPhraseAction($userNotification);

            $this->addPhraseAction($language1);
            $this->addPhraseAction($language2);
            $this->addPhraseAction($language3);
            $this->addPhraseAction($requestTab);

            // Insert in Suggestion modules tables.
            $row = $suggestionModuleTable->createRow();
            $row->module = "sitereview";
            $row->item_type = "sitereview_listing";
            $row->field_name = "listing_id";
            $row->owner_field = "owner_id";
            $row->item_title = $getReviewTitle;
            $row->button_title = "View this " . @ucfirst($tempReviewTitle);
            $row->enabled = "1";
            $row->notification_type = $suggNotificationType;
            $row->quality = "1";
            $row->link = "1";
            $row->popup = "1";
            $row->recommendation = "1";
            $row->default = "1";
            $row->settings = @serialize($suggSettingId);
            $row->save();
          }
        }
      }
    }


		$db = Engine_Api::_()->getDbtable('menuitems', 'core');
		$db_name = $db->info('name');


		$db_select = $db->select()
						->where('name =?', 'sitepage_suggest_friend');
		$setting_obj = $db->fetchAll($db_select)->toArray();
		if (empty($setting_obj)) {
		  $db->insert(array(
			  'name' => 'sitepage_suggest_friend',
			  'module' => 'suggestion',
			  'label' => 'Suggest to Friends',
			  'plugin' => 'Suggestion_Plugin_Menus',
			  'params' => '{"route":"default", "class":"buttonlink icon_page_friend_suggestion smoothbox"}',
			  'menu' => 'sitepage_gutter',
			  'submenu' => '',
			  'custom' => '0',
			  'order' => '999',
		  ));
		}


		$db_select = $db->select()
						->where('name =?', 'sitepage_event_suggest_friend');
		$setting_obj = $db->fetchAll($db_select)->toArray();
		if (empty($setting_obj)) {
		  $db->insert(array(
			  'name' => 'sitepage_event_suggest_friend',
			  'module' => 'suggestion',
			  'label' => 'Suggest to Friends',
			  'plugin' => 'Suggestion_Plugin_Menus',
			  'params' => '{"route":"default", "icon":"application/modules/Suggestion/externals/images/sugg_blub.png"}',
			  'menu' => 'sitepageevent_gutter',
			  'submenu' => '',
			  'custom' => '0',
			  'order' => '999',
		  ));
		}


		$db_select = $db->select()
						->where('name =?', 'recipe_suggest_friend');
		$setting_obj = $db->fetchAll($db_select)->toArray();
		if (empty($setting_obj)) {
		  $db->insert(array(
			  'name' => 'recipe_suggest_friend',
			  'module' => 'suggestion',
			  'label' => 'Suggest to Friends',
			  'plugin' => 'Suggestion_Plugin_Menus',
			  'params' => '{"route":"suggest_recipe","class":"buttonlink icon_recipe_friend_suggestion smoothbox"}',
			  'menu' => 'recipe_gutter',
			  'submenu' => '',
			  'custom' => '0',
			  'order' => '999',
		  ));
		}

		$db_select = $db->select()
						->where('name =?', 'suggestion_explore_suggestion');
		$setting_obj = $db->fetchAll($db_select)->toArray();
		if (empty($setting_obj)) {
		  $db->insert(array(
			  'name' => 'suggestion_explore_suggestion',
			  'module' => 'suggestion',
			  'label' => 'Explore Suggestions',
			  'plugin' => 'Suggestion_Plugin_Menus',
			  'params' => '{"route":"default", "icon":"application/modules/Suggestion/externals/images/sugg_explore.png"}',
			  'menu' => 'user_home',
			  'submenu' => '',
			  'custom' => '0',
			  'order' => '999',
		  ));
		}

		$db_select = $db->select()
						->where('name =?', 'suggestion_find_friend');
		$setting_obj = $db->fetchAll($db_select)->toArray();
		if (empty($setting_obj)) {
		  $db->insert(array(
			  'name' => 'suggestion_find_friend',
			  'module' => 'suggestion',
			  'label' => 'Find Friends',
			  'plugin' => 'Suggestion_Plugin_Menus',
			  'params' => '{"route":"default", "icon":"application/modules/Suggestion/externals/images/user-ex.png"}',
			  'menu' => 'user_home',
			  'submenu' => '',
			  'custom' => '0',
			  'order' => '999',
		  ));
		}

		$db_select = $db->select()
						->where('name =?', 'suggestion_friend_profile');
		$setting_obj = $db->fetchAll($db_select)->toArray();
		if (empty($setting_obj)) {
		  $db->insert(array(
			  'name' => 'suggestion_friend_profile',
			  'module' => 'suggestion',
			  'label' => 'Find Friends',
			  'plugin' => 'Suggestion_Plugin_Menus',
			  'params' => '{"route":"default", "icon":"application/modules/Suggestion/externals/images/user-ex.png"}',
			  'menu' => 'user_profile',
			  'submenu' => '',
			  'custom' => '0',
			  'order' => '999',
		  ));
		}

		$db_select = $db->select()
						->where('name =?', 'list_suggest_friend');
		$setting_obj = $db->fetchAll($db_select)->toArray();
		if (empty($setting_obj)) {
		  $db->insert(array(
			  'name' => 'list_suggest_friend',
			  'module' => 'suggestion',
			  'label' => 'Suggest to Friends',
			  'plugin' => 'Suggestion_Plugin_Menus',
			  'params' => '{"route":"suggest_list","class":"buttonlink icon_list_friend_suggestion smoothbox"}',
			  'menu' => 'list_gutter',
			  'submenu' => '',
			  'custom' => '0',
			  'order' => '999',
		  ));
		}

		$db_select = $db->select()
						->where('name =?', 'suggestion_admin_main_mix');
		$mix_suggestion_obj = $db->fetchAll($db_select)->toArray();
		if (empty($mix_suggestion_obj)) {
		  $db->insert(array(
			  'name' => 'suggestion_admin_main_mix',
			  'module' => 'suggestion',
			  'label' => 'Mixed Suggestions',
			  'plugin' => NULL,
			  'params' => '{"route":"admin_default","module":"suggestion","controller":"mix"}',
			  'menu' => 'sugg_admin_main',
			  'submenu' => '',
			  'custom' => '0',
			  'order' => '3',
		  ));
		}

		$db_select = $db->select()
						->where('name =?', 'suggestion_introduction');
		$intro_obj = $db->fetchAll($db_select)->toArray();
		if (empty($intro_obj)) {
		  $db->insert(array(
			  'name' => 'suggestion_introduction',
			  'module' => 'suggestion',
			  'label' => 'Site Introduction',
			  'plugin' => NULL,
			  'params' => '{"route":"admin_default","module":"suggestion","controller":"introduction"}',
			  'menu' => 'sugg_admin_main',
			  'submenu' => '',
			  'custom' => '0',
			  'order' => '4',
		  ));
		}

		$db_select = $db->select()
						->where('name =?', 'suggestion_modInfo');
		$intro_obj = $db->fetchAll($db_select)->toArray();
		if (empty($intro_obj)) {
		  $db->insert(array(
			  'name' => 'suggestion_modInfo',
			  'module' => 'suggestion',
			  'label' => 'Manage Modules',
			  'plugin' => NULL,
			  'params' => '{"route":"admin_default","module":"suggestion","controller":"settings", "action":"manage-module"}',
			  'menu' => 'sugg_admin_main',
			  'submenu' => '',
			  'custom' => '0',
			  'order' => '3',
		  ));
		}


		$contentTable = Engine_Api::_()->getDbtable('content', 'core');
		$contentTableName = $contentTable->info('name');
		$pageTable = Engine_Api::_()->getDbtable('pages', 'core');
		$pageTableName = $pageTable->info('name');


		// Page: Document Profile page.
		$selectPage = $pageTable->select()
						->from($pageTableName, array('page_id'))
						->where('name =?', 'document_index_browse')
						->limit(1);
		$fetchPageId = $selectPage->query()->fetchAll();
		if (!empty($fetchPageId)) {
		  $pageId = $fetchPageId[0]['page_id'];

		  $selectWidgets = $contentTable->select()
						  ->from($contentTableName, array('content_id'))
						  ->where('page_id =?', $pageId)
						  ->where('type = ?', 'widget')
						  ->where('name = ?', 'suggestion.common-suggestion')
						  ->limit(1);
		  $fetchContainerId = $selectWidgets->query()->fetchAll();

		  if (empty($fetchContainerId)) {
			$selectContainerId = $contentTable->select()
							->from($contentTableName, array('content_id'))
							->where('page_id =?', $pageId)
							->where('name = ?', 'main')
							->where('type = ?', 'container')
							->limit(1);
			$fetchContainerId = $selectContainerId->query()->fetchAll();

			if (!empty($fetchContainerId)) {
			  $mainContainerId = $fetchContainerId[0]['content_id'];

			  $selectChildContainerId = $contentTable->select()
							  ->from($contentTableName, array('content_id'))
							  ->where('page_id =?', $pageId)
							  ->where('name = ?', 'right')
							  ->where('type = ?', 'container')
							  ->where('parent_content_id = ?', $mainContainerId)
							  ->limit(1);
			  $fetchChildContainerId = $selectChildContainerId->query()->fetchAll();
			  if (!empty($fetchChildContainerId)) {
				$childContainerId = $fetchChildContainerId[0]['content_id'];

				$contentWidget = $contentTable->createRow();
				$contentWidget->page_id = $pageId;
				$contentWidget->type = 'widget';
				$contentWidget->name = 'suggestion.common-suggestion';
				$contentWidget->parent_content_id = $childContainerId;
				$contentWidget->order = 999;
				$contentWidget->params = '{"title":"Recommended Documents","resource_type":"document","getWidAjaxEnabled":"1","getWidLimit":"5","nomobile":"0","name":"suggestion.common-suggestion"}';
				$contentWidget->save();
			  }
			}
		  }
		}



		// Page: List Plugin Page.
		$selectPage = $pageTable->select()
						->from($pageTableName, array('page_id'))
						->where('name =?', 'list_index_home')
						->limit(1);
		$fetchPageId = $selectPage->query()->fetchAll();
		if (!empty($fetchPageId)) {
		  $pageId = $fetchPageId[0]['page_id'];

		  $selectWidgets = $contentTable->select()
						  ->from($contentTableName, array('content_id'))
						  ->where('page_id =?', $pageId)
						  ->where('type = ?', 'widget')
						  ->where('name = ?', 'suggestion.common-suggestion')
						  ->limit(1);
		  $fetchContainerId = $selectWidgets->query()->fetchAll();

		  if (empty($fetchContainerId)) {
			$selectContainerId = $contentTable->select()
							->from($contentTableName, array('content_id'))
							->where('page_id =?', $pageId)
							->where('name = ?', 'main')
							->where('type = ?', 'container')
							->limit(1);
			$fetchContainerId = $selectContainerId->query()->fetchAll();

			if (!empty($fetchContainerId)) {
			  $mainContainerId = $fetchContainerId[0]['content_id'];

			  $selectChildContainerId = $contentTable->select()
							  ->from($contentTableName, array('content_id'))
							  ->where('page_id =?', $pageId)
							  ->where('name = ?', 'left')
							  ->where('type = ?', 'container')
							  ->where('parent_content_id = ?', $mainContainerId)
							  ->limit(1);
			  $fetchChildContainerId = $selectChildContainerId->query()->fetchAll();
			  if (!empty($fetchChildContainerId)) {
				$childContainerId = $fetchChildContainerId[0]['content_id'];

				$contentWidget = $contentTable->createRow();
				$contentWidget->page_id = $pageId;
				$contentWidget->type = 'widget';
				$contentWidget->name = 'suggestion.common-suggestion';
				$contentWidget->parent_content_id = $childContainerId;
				$contentWidget->order = 999;
				$contentWidget->params = '{"title":"Recommended Listings","resource_type":"list","getWidAjaxEnabled":"1","getWidLimit":"5","nomobile":"0","name":"suggestion.common-suggestion"}';
				$contentWidget->save();
			  }
			}
		  }
		}




		// Page: Group Plugin Page.
		$selectPage = $pageTable->select()
						->from($pageTableName, array('page_id'))
						->where('name =?', 'group_profile_index')
						->limit(1);
		$fetchPageId = $selectPage->query()->fetchAll();
		if (!empty($fetchPageId)) {
		  $pageId = $fetchPageId[0]['page_id'];

		  $selectWidgets = $contentTable->select()
						  ->from($contentTableName, array('content_id'))
						  ->where('page_id =?', $pageId)
						  ->where('type = ?', 'widget')
						  ->where('name = ?', 'suggestion.common-suggestion')
						  ->limit(1);
		  $fetchContainerId = $selectWidgets->query()->fetchAll();

		  if (empty($fetchContainerId)) {
			$selectContainerId = $contentTable->select()
							->from($contentTableName, array('content_id'))
							->where('page_id =?', $pageId)
							->where('name = ?', 'main')
							->where('type = ?', 'container')
							->limit(1);
			$fetchContainerId = $selectContainerId->query()->fetchAll();

			if (!empty($fetchContainerId)) {
			  $mainContainerId = $fetchContainerId[0]['content_id'];

			  $selectChildContainerId = $contentTable->select()
							  ->from($contentTableName, array('content_id'))
							  ->where('page_id =?', $pageId)
							  ->where('name = ?', 'left')
							  ->where('type = ?', 'container')
							  ->where('parent_content_id = ?', $mainContainerId)
							  ->limit(1);
			  $fetchChildContainerId = $selectChildContainerId->query()->fetchAll();
			  if (!empty($fetchChildContainerId)) {
				$childContainerId = $fetchChildContainerId[0]['content_id'];

				$contentWidget = $contentTable->createRow();
				$contentWidget->page_id = $pageId;
				$contentWidget->type = 'widget';
				$contentWidget->name = 'suggestion.common-suggestion';
				$contentWidget->parent_content_id = $childContainerId;
				$contentWidget->order = 3;
				$contentWidget->params = '{"title":"Recommended Groups","resource_type":"group","getWidAjaxEnabled":"1","getWidLimit":"5","nomobile":"0","name":"suggestion.common-suggestion"}';
				$contentWidget->save();
			  }
			}
		  }
		}




		// Page: Event Plugin Page.
		$selectPage = $pageTable->select()
						->from($pageTableName, array('page_id'))
						->where('name =?', 'event_profile_index')
						->limit(1);
		$fetchPageId = $selectPage->query()->fetchAll();
		if (!empty($fetchPageId)) {
		  $pageId = $fetchPageId[0]['page_id'];

		  $selectWidgets = $contentTable->select()
						  ->from($contentTableName, array('content_id'))
						  ->where('page_id =?', $pageId)
						  ->where('type = ?', 'widget')
						  ->where('name = ?', 'suggestion.common-suggestion')
						  ->limit(1);
		  $fetchContainerId = $selectWidgets->query()->fetchAll();

		  if (empty($fetchContainerId)) {
			$selectContainerId = $contentTable->select()
							->from($contentTableName, array('content_id'))
							->where('page_id =?', $pageId)
							->where('name = ?', 'main')
							->where('type = ?', 'container')
							->limit(1);
			$fetchContainerId = $selectContainerId->query()->fetchAll();

			if (!empty($fetchContainerId)) {
			  $mainContainerId = $fetchContainerId[0]['content_id'];

			  $selectChildContainerId = $contentTable->select()
							  ->from($contentTableName, array('content_id'))
							  ->where('page_id =?', $pageId)
							  ->where('name = ?', 'left')
							  ->where('type = ?', 'container')
							  ->where('parent_content_id = ?', $mainContainerId)
							  ->limit(1);
			  $fetchChildContainerId = $selectChildContainerId->query()->fetchAll();
			  if (!empty($fetchChildContainerId)) {
				$childContainerId = $fetchChildContainerId[0]['content_id'];

				$contentWidget = $contentTable->createRow();
				$contentWidget->page_id = $pageId;
				$contentWidget->type = 'widget';
				$contentWidget->name = 'suggestion.common-suggestion';
				$contentWidget->parent_content_id = $childContainerId;
				$contentWidget->order = 3;
				$contentWidget->params = '{"title":"Recommended Events","resource_type":"event","getWidAjaxEnabled":"1","getWidLimit":"5","nomobile":"0","name":"suggestion.common-suggestion"}';
				$contentWidget->save();
			  }
			}
		  }
		}

		// Make a Widgitized Page & widgets for 'Explore Suggestion'.
		$selectPage = $pageTable->select()
						->from($pageTableName, array('page_id'))
						->where('name =?', 'suggestion_index_explore')
						->limit(1);
		$fetchPageId = $selectPage->query()->fetchAll();
		if (empty($fetchPageId)) {
		  $pageCreate = $pageTable->createRow();
		  $pageCreate->name = 'suggestion_index_explore';
		  $pageCreate->displayname = 'Explore Suggestions Page';
		  $pageCreate->title = 'Explore Suggestion';
		  $pageCreate->description = 'This is the explore suggestion page which show mix suggestion.';
		  $pageCreate->custom = 1;
		  $pageCreate->save();

		  $page_id = $pageCreate->page_id;

		  // Insert Main Container.
		  $mainContainer = $contentTable->createRow();
		  $mainContainer->page_id = $page_id;
		  $mainContainer->type = 'container';
		  $mainContainer->name = 'main';
		  $mainContainer->order = 1;
		  $mainContainer->save();

		  // Insert Main-Middle Container.
		  $mainMiddleContainer = $contentTable->createRow();
		  $mainMiddleContainer->page_id = $page_id;
		  $mainMiddleContainer->type = 'container';
		  $mainMiddleContainer->name = 'middle';
		  $mainMiddleContainer->parent_content_id = $mainContainer->content_id;
		  $mainMiddleContainer->order = 6;
		  $mainMiddleContainer->save();

		  // Insert Main-Right Container.
		  $mainRightContainer = $contentTable->createRow();
		  $mainRightContainer->page_id = $page_id;
		  $mainRightContainer->type = 'container';
		  $mainRightContainer->name = 'right';
		  $mainRightContainer->parent_content_id = $mainContainer->content_id;
		  $mainRightContainer->order = 5;
		  $mainRightContainer->save();

		  $widgets_1 = $contentTable->createRow();
		  $widgets_1->page_id = $page_id;
		  $widgets_1->type = 'widget';
		  $widgets_1->name = 'Suggestion.explore-friend';
		  $widgets_1->parent_content_id = $mainMiddleContainer->content_id;
		  $widgets_1->order = 3;
		  $widgets_1->params = '{"title":"Explore Suggestions"}';
		  $widgets_1->save();

		  $widgets_2 = $contentTable->createRow();
		  $widgets_2->page_id = $page_id;
		  $widgets_2->type = 'widget';
		  $widgets_2->name = 'Suggestion.suggestion-friend';
		  $widgets_2->parent_content_id = $mainRightContainer->content_id;
		  $widgets_2->order = 7;
		  $widgets_2->params = '{"title":"People you may know"}';
		  $widgets_2->save();
		}

		// Page: Member home Page.
		$selectPage = $pageTable->select()
						->from($pageTableName, array('page_id'))
						->where('name =?', 'user_index_home')
						->limit(1);
		$fetchPageId = $selectPage->query()->fetchAll();
		if (!empty($fetchPageId)) {
		  $pageId = $fetchPageId[0]['page_id'];
		  $selectContainerId = $contentTable->select()
						  ->from($contentTableName, array('content_id'))
						  ->where('page_id = ?', $pageId)
						  ->where('name = ?', 'main')
						  ->where('type = ?', 'container')
						  ->limit(1);
		  $fetchContainerId = $selectContainerId->query()->fetchAll();

		  if (!empty($fetchContainerId)) {
			$mainContainerId = $fetchContainerId[0]['content_id'];

			$selectChildContainerId = $contentTable->select()
							->from($contentTableName, array('content_id'))
							->where('page_id =?', $pageId)
							->where('name = ?', 'right')
							->where('type = ?', 'container')
							->where('parent_content_id = ?', $mainContainerId)
							->limit(1);
			$fetchChildContainerId = $selectChildContainerId->query()->fetchAll();
			if (!empty($fetchChildContainerId)) {
			  $childContainerId = $fetchChildContainerId[0]['content_id'];

			  $selectWidgets = $contentTable->select()
							  ->from($contentTableName, array('content_id'))
							  ->where('page_id =?', $pageId)
							  ->where('type = ?', 'widget')
							  ->where('name = ?', 'Suggestion.suggestion-friend')
							  ->limit(1);
			  $fetchContainerId = $selectWidgets->query()->fetchAll();
			  if (empty($fetchContainerId)) {
				$contentWidget = $contentTable->createRow();
				$contentWidget->page_id = $pageId;
				$contentWidget->type = 'widget';
				$contentWidget->name = 'Suggestion.suggestion-friend';
				$contentWidget->parent_content_id = $childContainerId;
				$contentWidget->order = 1;
				$contentWidget->params = '{"title":"People you may know"}';
				$contentWidget->save();
			  }

			  $selectWidgets = $contentTable->select()
							  ->from($contentTableName, array('content_id'))
							  ->where('page_id =?', $pageId)
							  ->where('type = ?', 'widget')
							  ->where('name = ?', 'Suggestion.suggestion-mix')
							  ->limit(1);
			  $fetchContainerId = $selectWidgets->query()->fetchAll();
			  if (empty($fetchContainerId)) {
				$contentWidget = $contentTable->createRow();
				$contentWidget->page_id = $pageId;
				$contentWidget->type = 'widget';
				$contentWidget->name = 'Suggestion.suggestion-mix';
				$contentWidget->parent_content_id = $childContainerId;
				$contentWidget->order = 2;
				$contentWidget->params = '{"title":"Recommendations"}';
				$contentWidget->save();
			  }
			}
		  }
		}

		// Page: Home Page.
		$selectPage = $pageTable->select()
						->from($pageTableName, array('page_id'))
						->where('name =?', 'core_index_index')
						->limit(1);
		$fetchPageId = $selectPage->query()->fetchAll();
		if (!empty($fetchPageId)) {
		  $pageId = $fetchPageId[0]['page_id'];
		  $selectContainerId = $contentTable->select()
						  ->from($contentTableName, array('content_id'))
						  ->where('page_id = ?', $pageId)
						  ->where('name = ?', 'main')
						  ->where('type = ?', 'container')
						  ->limit(1);
		  $fetchContainerId = $selectContainerId->query()->fetchAll();

		  if (!empty($fetchContainerId)) {
			$mainContainerId = $fetchContainerId[0]['content_id'];

			$selectChildContainerId = $contentTable->select()
							->from($contentTableName, array('content_id'))
							->where('page_id =?', $pageId)
							->where('name = ?', 'right')
							->where('type = ?', 'container')
							->where('parent_content_id = ?', $mainContainerId)
							->limit(1);
			$fetchChildContainerId = $selectChildContainerId->query()->fetchAll();
			if (!empty($fetchChildContainerId)) {
			  $childContainerId = $fetchChildContainerId[0]['content_id'];

			  $selectWidgets = $contentTable->select()
							  ->from($contentTableName, array('content_id'))
							  ->where('page_id =?', $pageId)
							  ->where('type = ?', 'widget')
							  ->where('name = ?', 'Suggestion.suggestion-mix')
							  ->limit(1);
			  $fetchContainerId = $selectWidgets->query()->fetchAll();
			  if (empty($fetchContainerId)) {
				$contentWidget = $contentTable->createRow();
				$contentWidget->page_id = $pageId;
				$contentWidget->type = 'widget';
				$contentWidget->name = 'Suggestion.suggestion-mix';
				$contentWidget->parent_content_id = $childContainerId;
				$contentWidget->order = 2;
				$contentWidget->params = '{"title":"Recommendations"}';
				$contentWidget->save();
			  }
			}
		  }
		}

<?php

$db = Engine_Db_Table::getDefaultAdapter();
$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sitemenu_admin_main_editor", "Sitemenu", "Menu Editor", "", \'{"route":"admin_default","module":"sitemenu","controller":"menu-settings","action":"editor"}\', "sitemenu_admin_main", "", 2),
("sitemenu_admin_main_manage", "Sitemenu", "Manage Modules", "", \'{"route":"admin_default","module":"sitemenu","controller":"modules-settings"}\', "sitemenu_admin_main", "", 3);');
$db->query('INSERT IGNORE INTO `engine4_sitemenu_modules` (`module_name`, `module_title`, `item_type`, `title_field`, `body_field`, `owner_field`, `like_field`, `comment_field`, `date_field`, `featured_field`, `sponsored_field`, `status`, `image_option`, `category_name`, `category_title_field`, `is_delete`) VALUES
("classified", "Classified", "classified", "title", "body", "owner_id","","comment_count","modified_date","","","1","1","","",1),
("blog", "Blog", "blog", "title", "body", "owner_id","","","","","","1","1","blog_category", "category_name",1),
("album", "Album", "album", "title", "description", "owner_id","","comment_count","modified_date","","","1","1","album_category", "category_name",1),
("event", "Event", "event", "title", "description", "user_id","","","modified_date","","","1","1","event_category", "title",1),
("forum", "Forum Topic", "forum_topic", "title", "description", "user_id","","","modified_date","","","1","1","forum_category", "title",1),
("group", "Group", "group", "title", "description", "user_id","","","modified_date","","","1","1","group_category", "title",1),
("music", "Music", "music_playlist", "title", "description", "owner_id","","","","","","1","1","", "",1),
("poll", "Poll", "poll", "title", "description", "user_id", "","comment_count","creation_date","","","1","1","", "",1),
("video", "Video", "video", "title", "description", "owner_id","","comment_count","modified_date","","","1","1","", "",1),
("list", "Listing", "list_listing", "title", "body", "owner_id","","","","","","1","1","list_category", "category_name",1),
("sitestore", "Stores / Marketplace - Ecommerce Plugin", "sitestore_store", "title", "body", "owner_id","like_count","comment_count","modified_date","featured","sponsored","1","1","sitestore_category", "category_name",1),
("sitestoreproduct", "Stores / Marketplace - Products Extension", "sitestoreproduct_product", "title", "body", "owner_id","like_count","comment_count","modified_date","featured","sponsored","1","1","sitestoreproduct_category", "category_name",1),
("siteevent", "Advanced Events Plugin", "siteevent_event", "title", "body", "owner_id","like_count","comment_count","modified_date","featured","sponsored","1","1","siteevent_category", "category_name",1),
("sitepage", "Directory / Pages", "sitepage_page", "title", "body", "owner_id","like_count","comment_count","modified_date","featured","sponsored","1","1","sitepage_category", "category_name",1),
("sitepagedocument", "Directory / Pages - Documents Extension", "sitepagedocument_document", "sitepagedocument_title", "sitepagedocument_description", "owner_id","like_count","comment_count","modified_date","featured","","1","1","", "",1),
("sitepageevent", "Directory / Pages - Events Extension", "sitepageevent_event", "title", "description", "user_id","","","modified_date","featured","","1","1","", "",1),
("sitepagenote", "Directory / Pages - Notes Extension", "sitepagenote_note", "title", "body", "owner_id","like_count","comment_count","modified_date","featured","","1","1","", "",1),
("sitepageoffer", "Directory / Pages - Offers Extension", "sitepageoffer_offer", "title", "description", "owner_id","like_count","comment_count","creation_date","","","1","1","", "",1),
("sitepagevideo", "Directory / Pages - Videos Extension", "sitepagevideo_video", "title", "description", "owner_id","like_count","comment_count","modified_date","featured","","1","1","", "",1),
("sitepagemusic", "Directory / Pages - Music Extension", "sitepagemusic_playlist", "title", "description", "owner_id","like_count","comment_count","modified_date","featured","","1","1","", "",1),
("sitepagepoll", "Directory / Pages - Polls Extension", "sitepagepoll_poll", "title", "description", "owner_id","like_count","comment_count","creation_date","","","1","1","", "",1),
("sitepagereview", "Directory / Pages - Reviews and Ratings Extension", "sitepagereview_review", "title", "body", "owner_id","like_count","comment_count","modified_date","featured","","1","1","sitepagereview_reviewcat", "reviewcat_name",1),
("sitegroup", "Groups / Communities", "sitegroup_group", "title", "body", "owner_id","like_count","comment_count","modified_date","featured","sponsored","1","1","sitegroup_category", "category_name",1),
("sitebusiness", "Directory / Businesses", "sitebusiness_business", "title", "body", "owner_id","like_count","comment_count","modified_date","featured","sponsored","1","1","sitebusiness_category", "category_name",1);');


//WORK FOR SETTING UP NAVIGATION MENUS
$enabledModulesArray = $db->select()->from('engine4_core_modules', 'name')->where('enabled = ?', 1)->query()->fetchAll();

$menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
$menuItemsSelect = $menuItemsTable->select()->where('menu = ?', 'core_main');
if (!empty($enabledModulesArray)) {
    $menuItemsSelect = $menuItemsSelect->where('module IN(?)', $enabledModulesArray);
}
$menuItems = $menuItemsTable->fetchAll($menuItemsSelect);

foreach ($menuItems as $menuItem) {

    if (strstr($menuItem['module'], "sitereview")) {
        $sitereviewMenuNameArray = explode("core_main_sitereview_listtype_", $menuItem['name']);
        $listingtypeId = $sitereviewMenuNameArray[1];
        $menuTypeName = 'sitereview_main_listtype_' . $listingtypeId;

        $tempNavMenuArray = $menuItemsTable->select()
                ->from($menuItemsTable->info('name'), 'id')
                ->where('menu = ?', $menuTypeName)
                ->where('menu != ?', 'core_main')
                ->where("module != 'core'")
                ->where("custom = 0")
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
    } else {
        $menuTypeName = $menuItem['module'] . '_main';
        $tempNavMenuArray = $menuItemsTable->select()
                ->from($menuItemsTable->info('name'), 'id')
                ->where('menu = ?', $menuTypeName)
                ->where('menu != ?', 'core_main')
                ->where("module != 'core'")
                ->where("custom = 0")
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
    }

    if (!empty($tempNavMenuArray)) {
        $tempNavMenuArray = serialize($tempNavMenuArray);
        if (!empty($tempNavMenuArray)) {
            $values = $menuItem->params;
            $values['sub_navigation'] = $tempNavMenuArray;
            $values['nav_menu'] = $menuTypeName;

            $menuItem->params = $values;
            $menuItem->save();
        }
    }
}


// Mini Menu and Main Menu insert in core content
$isMiniMenuWidgetChange = false;
$headerPageId = $db->select()
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'header')
                ->limit(1)->query()->fetchColumn();

if (!empty($headerPageId)) {
    $parentContentId = $db->select()
                    ->from('engine4_core_content', 'content_id')
                    ->where('page_id = ?', $headerPageId)
                    ->where('name = ?', 'main')
                    ->where('type =?', 'container')
                    ->limit(1)->query()->fetchColumn();

    if (!empty($parentContentId)) {
        // CORE MINI MENU WIDGET EXIST
        $isMiniMenuWidgetExist = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $headerPageId)
                        ->where('parent_content_id =?', $parentContentId)
                        ->where('name = ?', 'core.menu-mini')
                        ->where('type =?', 'widget')
                        ->limit(1)->query()->fetchColumn();

        // SITEMENU MINI MENU WIDGET EXIST
        $isSitemenuMiniMenuExist = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $headerPageId)
                        ->where('parent_content_id =?', $parentContentId)
                        ->where('name = ?', 'sitemenu.menu-mini')
                        ->where('type =?', 'widget')
                        ->limit(1)->query()->fetchColumn();
        if (!empty($isMiniMenuWidgetExist) && empty($isSitemenuMiniMenuExist)) {
            $isMiniMenuWidgetChange = true;
            $db->query("UPDATE `engine4_core_content` SET `name` = 'sitemenu.menu-mini' WHERE `name` = 'core.menu-mini' LIMIT 1");
        }

        // SITETHEME MINI MENU WIDGET EXIST
        if (empty($isMiniMenuWidgetExist)) {
            $isSitethemeMiniMenuExist = $db->select()
                            ->from('engine4_core_content', 'content_id')
                            ->where('page_id = ?', $headerPageId)
                            ->where('parent_content_id =?', $parentContentId)
                            ->where('name = ?', 'sitetheme.menu-mini')
                            ->where('type =?', 'widget')
                            ->limit(1)->query()->fetchColumn();

            if (!empty($isSitethemeMiniMenuExist) && empty($isSitemenuMiniMenuExist)) {
                $isMiniMenuWidgetChange = true;
                $db->query("UPDATE `engine4_core_content` SET `name` = 'sitemenu.menu-mini' WHERE `name` = 'sitetheme.menu-mini' LIMIT 1");
                // DELETE SITETHEME SEARCHBOX
                $sitethemeSearchWidgetId = $db->select()
                                ->from('engine4_core_content', 'content_id')
                                ->where('page_id = ?', $headerPageId)
                                ->where('parent_content_id =?', $parentContentId)
                                ->where('name = ?', 'sitetheme.searchbox-sitestoreproduct')
                                ->where('type =?', 'widget')
                                ->limit(1)->query()->fetchColumn();

                if (!empty($sitethemeSearchWidgetId)) {
                    $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`content_id` = $sitethemeSearchWidgetId LIMIT 1");
                }
            }
        }

        if (!empty($isMiniMenuWidgetChange)) {
            // CHANGE ORDER OF MINI MENUS
            $miniMenusLink = $db->select()
                    ->from('engine4_core_menuitems', array('name'))
                    ->where('menu = ?', 'core_mini')
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);

            foreach ($miniMenusLink as $linkName) {
                if ($linkName == 'sitemenu_mini_cart') {
                    $db->query("UPDATE `engine4_core_menuitems` SET `order` = '1' WHERE `name` = 'sitemenu_mini_cart' LIMIT 1 ;");
                } else if ($linkName == 'sitemenu_mini_friend_request') {
                    $db->query("UPDATE `engine4_core_menuitems` SET `order` = '2' WHERE `name` = 'sitemenu_mini_friend_request' LIMIT 1 ;");
                } else if ($linkName == 'core_mini_messages') {
                    $db->query("UPDATE `engine4_core_menuitems` SET `order` = '3' WHERE `name` = 'core_mini_messages' LIMIT 1 ;");
                } else if ($linkName == 'sitemenu_mini_notification') {
                    $db->query("UPDATE `engine4_core_menuitems` SET `order` = '4' WHERE `name` = 'sitemenu_mini_notification' LIMIT 1 ;");
                } else if ($linkName == 'core_mini_settings') {
                    $db->query("UPDATE `engine4_core_menuitems` SET `order` = '5' WHERE `name` = 'core_mini_settings' LIMIT 1 ;");
                } else if ($linkName == 'core_mini_profile') {
                    $db->query("UPDATE `engine4_core_menuitems` SET `order` = '6' WHERE `name` = 'core_mini_profile' LIMIT 1 ;");
                } else if ($linkName == 'core_mini_admin') {
                    $db->query("UPDATE `engine4_core_menuitems` SET `order` = '7' WHERE `name` = 'core_mini_admin' LIMIT 1 ;");
                } else if ($linkName == 'core_mini_auth') {
                    $db->query("UPDATE `engine4_core_menuitems` SET `order` = '98' WHERE `name` = 'core_mini_auth' LIMIT 1 ;");
                } else if ($linkName == 'core_mini_signup') {
                    $db->query("UPDATE `engine4_core_menuitems` SET `order` = '99' WHERE `name` = 'core_mini_signup' LIMIT 1 ;");
                }
            }
        }

        // CORE MAIN MENU WIDGET EXIST
        $isMainMenuWidgetExist = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $headerPageId)
                        ->where('parent_content_id =?', $parentContentId)
                        ->where('name = ?', 'core.menu-main')
                        ->where('type =?', 'widget')
                        ->limit(1)->query()->fetchColumn();

        // SITEMENU MAIN MENU WIDGET EXIST
        $isSitemenuMainMenuExist = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $headerPageId)
                        ->where('parent_content_id =?', $parentContentId)
                        ->where('name = ?', 'sitemenu.menu-main')
                        ->where('type =?', 'widget')
                        ->limit(1)->query()->fetchColumn();

        if (!empty($isMainMenuWidgetExist) && empty($isSitemenuMainMenuExist)) {
            $db->query("UPDATE `engine4_core_content` SET `name` = 'sitemenu.menu-main' WHERE `name` = 'core.menu-main' LIMIT 1");
        }

        // SITETHEME MAIN MENU WIDGET EXIST
        if (empty($isMainMenuWidgetExist)) {
            $isSitethemeMainMenuExist = $db->select()
                            ->from('engine4_core_content', 'content_id')
                            ->where('page_id = ?', $headerPageId)
                            ->where('parent_content_id =?', $parentContentId)
                            ->where('name = ?', 'sitetheme.menu-main')
                            ->where('type =?', 'widget')
                            ->limit(1)->query()->fetchColumn();

            if (!empty($isSitethemeMainMenuExist) && empty($isSitemenuMainMenuExist)) {
                $db->query("UPDATE `engine4_core_content` SET `name` = 'sitemenu.menu-main' WHERE `name` = 'sitetheme.menu-main' LIMIT 1");
                $db->query('UPDATE `engine4_core_content` SET `params` = \'{"sitemenu_is_fixed":"1"}\' WHERE `name` = "sitemenu.menu-main" LIMIT 1');
            }
        }

//WORK FOR COMPATIBILITY WITH SITETHEME STARTS
        $themesTable = Engine_Api::_()->getDbtable('themes', 'core');
        $themeSelect = $themesTable->select()->where('name = ?', 'shoppinghub')->where('active = ?', 1)->limit(1);
        $isShoppingHubActive = $themesTable->fetchRow($themeSelect);
        if (!empty($isShoppingHubActive)) {
            $contentTable = Engine_Api::_()->getDbtable('content', 'core');
            $mainMenuSelect = $contentTable->select()
                    ->where('page_id = ?', $headerPageId)
                    ->where('parent_content_id =?', $parentContentId)
                    ->where('name = ?', 'sitemenu.menu-main')
                    ->where('type =?', 'widget')
                    ->limit(1);
            $mainMenuRow = $contentTable->fetchRow($mainMenuSelect);
            if (!empty($mainMenuRow)) {
                $values = $mainMenuRow->params;
                if (!empty($values) && !is_array($values))
                    $values = array();

                $values['sitemenu_fixed_height'] = "50";
                $mainMenuRow->params = $values;
                $mainMenuRow->save();
            }
        }
//WORK FOR COMPATIBILITY WITH SITETHEME ENDS
    }
}





// Insret Footer Widget
$footerPageId = $db->select()
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'footer')
                ->limit(1)->query()->fetchColumn();

if (!empty($footerPageId)) {
    $parentContentId = $db->select()
                    ->from('engine4_core_content', 'content_id')
                    ->where('page_id = ?', $footerPageId)
                    ->where('name = ?', 'main')
                    ->where('type = ?', 'container')
                    ->limit(1)->query()->fetchColumn();

    if (!empty($parentContentId)) {
        // CORE FOOTER MENU WIDGET EXIST
        $isFooterMenuWidgetExist = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $footerPageId)
                        ->where('parent_content_id =?', $parentContentId)
                        ->where('name = ?', 'core.menu-footer')
                        ->where('type =?', 'widget')
                        ->limit(1)->query()->fetchColumn();

        // SITEMENU FOOTER MENU WIDGET EXIST
        $isSitemenuFooterMenuExist = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $footerPageId)
                        ->where('parent_content_id =?', $parentContentId)
                        ->where('name = ?', 'sitemenu.menu-footer')
                        ->where('type =?', 'widget')
                        ->limit(1)->query()->fetchColumn();

        if (!empty($isFooterMenuWidgetExist) && empty($isSitemenuFooterMenuExist)) {
            $db->query("UPDATE `engine4_core_content` SET `name` = 'sitemenu.menu-footer' WHERE `name` = 'core.menu-footer' LIMIT 1");
        }
    }
}




// WORK FOR SITEREVIEW INTEGRATION
$select = new Zend_Db_Select($db);
$select->from('engine4_core_modules')->where('name = ?', 'sitereview')->where('enabled = ?', 1);
$isSitereviewEnabled = $select->query()->fetchObject();
if (!empty($isSitereviewEnabled)) {
    $getListingType = $db->query("SELECT `listingtype_id`, `title_singular` FROM `engine4_sitereview_listingtypes`")->fetchAll();
    if (!empty($getListingType)) {
        $sitemenuModuleTable = Engine_Api::_()->getDbTable('modules', 'sitemenu');
        foreach ($getListingType as $listingType) {
            $tempTableName = "sitereview_listing_" . $listingType["listingtype_id"];

            $isContentModuleExist = $db->query("SELECT * FROM `engine4_sitemenu_modules` WHERE `item_type` LIKE '" . $tempTableName . "' LIMIT 1")->fetch();
            if (empty($isContentModuleExist)) {
                $row = $sitemenuModuleTable->createRow();
                $row->module_name = "sitereview";
                $row->module_title = $listingType["title_singular"];
                $row->item_type = $tempTableName;
                $row->title_field = "title";
                $row->body_field = "body";
                $row->owner_field = "owner_id";
                $row->like_field = "like_count";
                $row->comment_field = "comment_count";
                $row->date_field = "creation_date";
                $row->featured_field = "featured";
                $row->sponsored_field = "sponsored";
                $row->status = 1;
                $row->image_option = 1;
                $row->category_name = "sitereview_category";
                $row->category_title_field = "category_name";
                $row->is_delete = 1;
                $row->save();
            }
        }
    }
}

if (Engine_Api::_()->hasModuleBootstrap('captivate')) {
    $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
    $header_page_id = Engine_Api::_()->sitemenu()->getWidgetizedPageId(array('name' => 'header'));

    $main_content_id = $tableNameContent->select()
            ->from($tableNameContent->info('name'), 'content_id')
            ->where('name =?', 'main')
            ->where('page_id =?', $header_page_id)
            ->query()
            ->fetchColumn();
    $db = Engine_Db_Table::getDefaultAdapter();
    if (!empty($main_content_id)) {
        $params = $tableNameContent->select()
                ->from($tableNameContent->info('name'), 'params')
                ->where('name =?', 'sitemenu.menu-mini')
                ->where('page_id =?', $header_page_id)->query()
                ->fetchColumn();
        if ($params) {
            $encodedParams = json_decode($params);
            if (isset($encodedParams->sitemenu_show_in_mini_options)) {
                $encodedParams->sitemenu_show_in_mini_options = 0;
                $decodedParams = json_encode($encodedParams);
                $db->query("UPDATE `engine4_core_content` SET `params` = '$decodedParams' WHERE `engine4_core_content`.`page_id` = '$header_page_id' AND `engine4_core_content`.`name` = 'sitemenu.menu-mini' ;");
            }
        }
    }
}
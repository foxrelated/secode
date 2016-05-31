<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: widgetSettings.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
Engine_Api::_()->getDbTable('contents', 'siteadvsearch')->iconUpload();

$db = Engine_Db_Table::getDefaultAdapter();

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("siteadvsearch_admin_main_manage", "siteadvsearch", "Manage Modules", "", \'{"route":"admin_default","module":"siteadvsearch","controller":"manage"}\', "siteadvsearch_admin_main", "", 2),
("siteadvsearch_admin_main_icon", "siteadvsearch", "Manage Content Icons", "", \'{"route":"admin_default","module":"siteadvsearch","controller":"manage","action":"manage-icon"}\', "siteadvsearch_admin_main", "", 3);');

$select = new Zend_Db_Select($db);
$select->from('engine4_core_content')
        ->where('name = ?', "core.menu-mini");
$isExist = $select->query()->fetchObject();
if (!empty($isExist)) {
    $db->query("UPDATE `engine4_core_content` SET `name` = 'siteadvsearch.menu-mini' WHERE `engine4_core_content`.`name` ='core.menu-mini' LIMIT 1 ;");
}

$select = new Zend_Db_Select($db);
$select->from('engine4_core_content')
        ->where('name = ?', "sitemenu.menu-mini");
$isSitemenuExist = $select->query()->fetchObject();
if (!empty($isSitemenuExist)) {
    $params = $isSitemenuExist->params;
    if (!empty($params) && $params != '[""]') {
        $decodeparams = Zend_Json::decode($params);

        if (isset($decodeparams['sitemenu_show_in_mini_options'])) {
            $decodeparams['sitemenu_show_in_mini_options'] = 3;
            $params = json_encode($decodeparams);
            $db->query('UPDATE `engine4_core_content` SET `params` = \' ' . $params . ' \' WHERE `engine4_core_content`.`name` = "sitemenu.menu-mini" LIMIT 1 ;');
        }
    }
}
$headerPageId = $db->select()
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'header')
                ->limit(1)->query()->fetchColumn();

$select = new Zend_Db_Select($db);
$select->from('engine4_core_content')
        ->where('name = ?', "sitetheme.searchbox-sitestoreproduct")
        ->where('page_id =?', $headerPageId);
$isSitethemeExist = $select->query()->fetchObject();
if (!empty($isSitethemeExist)) {
    $db->query("UPDATE `engine4_core_content` SET `name` = 'siteadvsearch.search-box', `params` = '{\"title\":\"\",\"titleCount\":true,\"advsearch_search_box_width\":\"275\",\"nomobile\":\"0\",\"name\":\"siteadvsearch.search-box\"}' WHERE `engine4_core_content`.`name` ='sitetheme.searchbox-sitestoreproduct' and `engine4_core_content`.`page_id` =$headerPageId LIMIT 1 ;");
}

$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "siteadvsearch_index_index")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {

    $db->insert('engine4_core_pages', array(
        'name' => "siteadvsearch_index_index",
        'displayname' => 'Advanced Search - All Results Page',
        'title' => '',
        'description' => '',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();

    //TOP CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
    ));
    $top_container_id = $db->lastInsertId();

    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
    ));
    $main_container_id = $db->lastInsertId();

    //INSERT TOP-MIDDLE
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => 1,
    ));
    $top_middle_id = $db->lastInsertId();

    //RIGHT CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => 5,
    ));
    $right_container_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => 6,
    ));
    $main_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'siteadvsearch.search-contents',
        'parent_content_id' => $main_middle_id,
        'order' => 1,
        'params' => '{"show_statstics":["contenttype","postedby","viewcount","likecount","commentcount","photocount","rating","reviewcount","followercount","description","category","location","sponsored","featured"]}',
    ));
}


//START DEFAULT WIDGETIZE PAGE WORK
$values = array();
$select = new Zend_Db_Select($db);
$select->from('engine4_core_modules')
        ->where('name = ?', "sitereview")
        ->where('enabled = ?', 1);
$isExist = $select->query()->fetchObject();

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_settings')
        ->where('name = ?', 'sitereview.isActivate')
        ->limit(1);
$sitereview_settings = $select->query()->fetchObject();
if (!empty($isExist) && !empty($sitereview_settings)) {

    $moduleName = 'sitereview';
    $select = new Zend_Db_Select($db);
    $select->from('engine4_sitereview_listingtypes', array('title_plural', 'listingtype_id', 'slug_plural'));
    $listingTypes = $select->query()->fetchAll();

    foreach ($listingTypes as $listingType) {
        $defaultProductTitle = $listingType['title_plural'];
        $listingtype_id = $listingType['listingtype_id'];
        $resource_type = 'sitereview_listingtype_' . $listingtype_id;
        $db->query("INSERT IGNORE INTO `engine4_siteadvsearch_contents` (`module_name`, `resource_type`, `resource_title`, `listingtype_id`, `widgetize`, `content_tab`,`order`,`file_id`, `default`) VALUES('$moduleName', '$resource_type', '$defaultProductTitle', '$listingtype_id', 0, 0, 999, 0, 1);");

        if ($listingType['slug_plural'] != 'blogs' && $listingType['slug_plural'] != 'classifieds') {
            $values['module_name'] = $moduleName;
            $values['slug_url'] = '';
            $values['resource_type'] = '';
            $values['core_module_name'] = '';
            $values['resource_type_key'] = 'sitereview_listingtype_' . $listingtype_id;
            $content_id = Engine_Api::_()->siteadvsearch()->getContentId($values);
            if (!empty($content_id))
                Engine_Api::_()->siteadvsearch()->makeWidgetizePage('', $content_id);
        }
    }

    $values['module_name'] = 'blog';
    $values['slug_url'] = 'blogs';
    $values['resource_type'] = 'blog';
    $values['core_module_name'] = '';
    $values['resource_type_key'] = '';

    $content_id = Engine_Api::_()->siteadvsearch()->getContentId($values);
    if (!empty($content_id))
        Engine_Api::_()->siteadvsearch()->makeWidgetizePage('', $content_id);

    $values['module_name'] = 'classified';
    $values['slug_url'] = 'classifieds';
    $values['resource_type'] = 'classified';
    $values['core_module_name'] = '';
    $values['resource_type_key'] = '';

    $content_id = Engine_Api::_()->siteadvsearch()->getContentId($values);
    if (!empty($content_id))
        Engine_Api::_()->siteadvsearch()->makeWidgetizePage('', $content_id);
}
else {
    $values['module_name'] = 'blog';
    $values['slug_url'] = '';
    $values['resource_type'] = '';
    $values['core_module_name'] = '';
    $values['resource_type_key'] = 'blog';

    $content_id = Engine_Api::_()->siteadvsearch()->getContentId($values);
    if (!empty($content_id))
        Engine_Api::_()->siteadvsearch()->makeWidgetizePage('', $content_id);

    $values['module_name'] = 'classified';
    $values['slug_url'] = '';
    $values['resource_type'] = '';
    $values['core_module_name'] = '';
    $values['resource_type_key'] = 'classified';

    $content_id = Engine_Api::_()->siteadvsearch()->getContentId($values);
    if (!empty($content_id))
        Engine_Api::_()->siteadvsearch()->makeWidgetizePage('', $content_id);
}

$values['module_name'] = 'sitegroup';
$values['slug_url'] = '';
$values['resource_type'] = '';
$values['core_module_name'] = 'group';
$values['resource_type_key'] = '';

$content_id = Engine_Api::_()->siteadvsearch()->getContentId($values);
if (!empty($content_id))
    Engine_Api::_()->siteadvsearch()->makeWidgetizePage('', $content_id);

$values['module_name'] = 'siteevent';
$values['slug_url'] = '';
$values['resource_type'] = '';
$values['core_module_name'] = 'event';
$values['resource_type_key'] = '';

$content_id = Engine_Api::_()->siteadvsearch()->getContentId($values);
if (!empty($content_id))
    Engine_Api::_()->siteadvsearch()->makeWidgetizePage('', $content_id);

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemember')) {
    $modules = array('user' => 'user', 'album' => 'album', 'video' => 'video', 'poll' => 'poll', 'music_playlist' => 'music', 'document' => 'document', 'recipe' => 'recipe', 'list_listing' => 'list', 'sitepage_page' => 'sitepage', 'sitebusiness_business' => 'sitebusiness', 'sitestore_store' => 'sitestore', 'sitestoreproduct_product' => 'sitestoreproduct', 'sitefaq_faq' => 'sitefaq', 'sitetutorial_tutorial' => 'sitetutorial', 'feedback' => 'feedback', 'sitevideo_video' => 'sitevideo');
} else {
    $modules = array('album' => 'album', 'video' => 'video', 'poll' => 'poll', 'music_playlist' => 'music', 'document' => 'document', 'recipe' => 'recipe', 'list_listing' => 'list', 'sitepage_page' => 'sitepage', 'sitebusiness_business' => 'sitebusiness', 'sitestore_store' => 'sitestore', 'sitestoreproduct_product' => 'sitestoreproduct', 'sitefaq_faq' => 'sitefaq', 'sitetutorial_tutorial' => 'sitetutorial', 'feedback' => 'feedback', 'sitevideo_video' => 'sitevideo');
}

foreach ($modules as $key => $module) {

    $values['module_name'] = $module;
    $values['slug_url'] = '';
    $values['resource_type'] = '';
    $values['core_module_name'] = '';
    $values['resource_type_key'] = $key;

    $content_id = Engine_Api::_()->siteadvsearch()->getContentId($values);
    if (!empty($content_id))
        Engine_Api::_()->siteadvsearch()->makeWidgetizePage('', $content_id);
}
//END DEFAULT WIDGETIZE PAGE WORK

$isColumnExist = $db->query("SHOW COLUMNS FROM engine4_core_search LIKE 'item_type'")->fetch();
if (!empty($isColumnExist))
    $db->query("UPDATE `engine4_core_search` SET  item_type = type");

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview')) {
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_search', array('id', 'item_type'))->where('type =?', 'sitereview_listing')->limit('5000');
    $coreSearchResults = $select->query()->fetchAll();
    foreach ($coreSearchResults as $result) {
        $itemId = $result['id'];
        $listingtype_id = Engine_Api::_()->getDbtable('listings', 'sitereview')->getListingTypeId($itemId);
        $type = 'sitereview_listingtype_' . $listingtype_id;
        $db->query("UPDATE `engine4_core_search` SET `item_type` = '$type' WHERE `id` = $itemId LIMIT 1;");
    }
}

//Advanced Search plugin work
$select = new Zend_Db_Select($db);
$select->from('engine4_core_modules')
        ->where('name = ?', 'sitevideo');
$is_enabled = $select->query()->fetchObject();
if (!empty($is_enabled)) {

    $containerCount = 0;
    $widgetCount = 0;
    $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'siteadvsearch_index_browse-page_sitevideo_channel')
            ->limit(1)
            ->query()
            ->fetchColumn();
    if (!$page_id) {
        $db->insert('engine4_core_pages', array(
            'name' => 'siteadvsearch_index_browse-page_sitevideo_channel',
            'displayname' => 'Advanced Search - SEAO - Channels',
            'title' => '',
            'description' => '',
            'custom' => 0,
        ));
        $page_id = $db->lastInsertId();

        //TOP CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'top',
            'page_id' => $page_id,
            'order' => $containerCount++,
        ));
        $top_container_id = $db->lastInsertId();

        //MAIN CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'main',
            'page_id' => $page_id,
            'order' => $containerCount++,
        ));
        $main_container_id = $db->lastInsertId();

        //INSERT TOP-MIDDLE
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $top_container_id,
            'order' => $containerCount++,
        ));
        $top_middle_id = $db->lastInsertId();

        //RIGHT CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'right',
            'page_id' => $page_id,
            'parent_content_id' => $main_container_id,
            'order' => $containerCount++,
        ));
        $right_container_id = $db->lastInsertId();

        //MAIN-MIDDLE CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $main_container_id,
            'order' => $containerCount++,
        ));
        $main_middle_id = $db->lastInsertId();

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitevideo.search-sitevideo',
            'parent_content_id' => $right_container_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"viewType":"vertical","showAllCategories":"1","nomobile":"0","name":"sitevideo.search-sitevideo"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitevideo.create-new-channel',
            'parent_content_id' => $right_container_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true}',
        ));


        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitevideo.browse-channels-sitevideo',
            'parent_content_id' => $main_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"viewType":["videoView","gridView","listView"],"defaultViewType":"videoView","channelOption":["title","owner","like","comment","favourite","numberOfVideos","subscribe","facebook","twitter","linkedin","googleplus"],"category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","videoViewWidth":"433","videoViewHeight":"330","gridViewWidth":"283","gridViewHeight":"250","show_content":"2","orderby":"creationDate","titleTruncation":"60","titleTruncationGridNVideoView":"30","descriptionTruncation":"350","itemCountPerPage":"12","nomobile":"0","name":"sitevideo.browse-channels-sitevideo"}',
        ));
    }


    $containerCount = 0;
    $widgetCount = 0;
    $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'siteadvsearch_index_browse-page_sitevideo_video')
            ->limit(1)
            ->query()
            ->fetchColumn();
    if (!$page_id) {
        $db->insert('engine4_core_pages', array(
            'name' => 'siteadvsearch_index_browse-page_sitevideo_video',
            'displayname' => 'Advanced Search - SEAO - Videos',
            'title' => '',
            'description' => '',
            'custom' => 0,
        ));
        $page_id = $db->lastInsertId();

        //TOP CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'top',
            'page_id' => $page_id,
            'order' => $containerCount++,
        ));
        $top_container_id = $db->lastInsertId();

        //MAIN CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'main',
            'page_id' => $page_id,
            'order' => $containerCount++,
        ));
        $main_container_id = $db->lastInsertId();

        //INSERT TOP-MIDDLE
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $top_container_id,
            'order' => $containerCount++,
        ));
        $top_middle_id = $db->lastInsertId();

        //RIGHT CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'right',
            'page_id' => $page_id,
            'parent_content_id' => $main_container_id,
            'order' => $containerCount++,
        ));
        $right_container_id = $db->lastInsertId();

        //MAIN-MIDDLE CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $main_container_id,
            'order' => $containerCount++,
        ));
        $main_middle_id = $db->lastInsertId();

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitevideo.search-video-sitevideo',
            'parent_content_id' => $right_container_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"viewType":"vertical","showAllCategories":"1","locationDetection":"1","nomobile":"0","name":"sitevideo.search-video-sitevideo"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitevideo.post-new-video',
            'parent_content_id' => $right_container_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"upload_button":"1","upload_button_title":"Post New Video","nomobile":"0","name":"sitevideo.post-new-video"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitevideo.browse-videos-sitevideo',
            'parent_content_id' => $main_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"viewType":["videoView","gridView","listView"],"defaultViewType":"videoView","videoOption":["title","owner","creationDate","view","like","comment","duration","rating","watchlater","favourite","facebook","twitter","linkedin","googleplus"],"videoType":"","category_id":"0","subcategory_id":null,"hidden_video_category_id":"","hidden_video_subcategory_id":"","hidden_video_subsubcategory_id":"","videoViewWidth":"350","videoViewHeight":"340","gridViewWidth":"283","gridViewHeight":"250","show_content":"2","orderby":"creationDate","detactLocation":"0","defaultLocationDistance":"0","titleTruncation":"75","titleTruncationGridNVideoView":"30","descriptionTruncation":"370","itemCountPerPage":"12","nomobile":"0","name":"sitevideo.browse-videos-sitevideo"}',
        ));
    }

    $db->query("UPDATE `engine4_siteadvsearch_contents` SET `module_name` = 'sitevideo', `resource_type` = 'sitevideo_video',`main_search` = '1' WHERE `engine4_siteadvsearch_contents`.`resource_type` ='video' LIMIT 1 ;");

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_siteadvsearch_contents')
            ->where('resource_type = ?', 'sitevideo_channel');
    $sitevideo_channel = $select->query()->fetchObject();
    if (!$sitevideo_channel) {
        $db->query("INSERT IGNORE INTO `engine4_siteadvsearch_contents` ( `module_name`, `resource_type`, `resource_title`, `listingtype_id`, `widgetize`, `content_tab`, `main_search`, `order`, `file_id`, `default`, `enabled`) VALUES ( 'sitevideo', 'sitevideo_channel', 'Channels', '1', '1', '1', '1', '999', '', '1', '1');");
    }

    //MAKE DIRECTORY IN PUBLIC FOLDER
    @mkdir(APPLICATION_PATH . "/temporary/siteadvsearch_search_icons", 0777, true);

    //COPY THE ICONS IN NEWLY CREATED FOLDER
    $dir = APPLICATION_PATH . "/application/modules/Siteadvsearch/externals/images/icons";
    $public_dir = APPLICATION_PATH . "/temporary/siteadvsearch_search_icons";

    if (is_dir($dir) && is_dir($public_dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if (strstr($file, '.png')) {
                @copy(APPLICATION_PATH . "/application/modules/Siteadvsearch/externals/images/icons/$file", APPLICATION_PATH . "/temporary/siteadvsearch_search_icons/$file");
            }
        }
        @chmod(APPLICATION_PATH . '/temporary/siteadvsearch_search_icons', 0777);
    }
    $contentTable = Engine_Api::_()->getDbtable('contents', 'siteadvsearch');
    //MAKE QUERY
    $select = $contentTable->select()->from($contentTable->info('name'), array('content_id', 'resource_title', 'file_id'))->where('resource_type in(?)', array('sitevideo_video', 'sitevideo_channel'));
    $contentTypes = $contentTable->fetchAll($select);
    //UPLOAD DEFAULT ICONS
    foreach ($contentTypes as $contentType) {
        $contentTypeName = $contentType->resource_title;
        $iconName = $contentTypeName . '.png';

        @chmod(APPLICATION_PATH . '/temporary/siteadvsearch_search_icons', 0777);

        $file = array();
        $file['tmp_name'] = APPLICATION_PATH . "/temporary/siteadvsearch_search_icons/$iconName";
        $file['name'] = $iconName;

        if (file_exists($file['tmp_name'])) {
            $name = basename($file['tmp_name']);
            $path = dirname($file['tmp_name']);
            $mainName = $path . '/' . $file['name'];

            @chmod($mainName, 0777);

            $photo_params = array(
                'parent_id' => $contentType->content_id,
                'parent_type' => "siteadvsearch_content",
            );

            //RESIZE IMAGE WORK
            $image = Engine_Image::factory();
            $image->open($file['tmp_name']);
            $image->open($file['tmp_name'])
                    ->resample(0, 0, $image->width, $image->height, $image->width, $image->height)
                    ->write($mainName)
                    ->destroy();

            $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);

            //UPDATE FILE ID IN CATEGORY TABLE
            if (!empty($photoFile->file_id)) {
                $contentType = Engine_Api::_()->getItem('siteadvsearch_content', $contentType->content_id);
                $contentType->file_id = $photoFile->file_id;
                $contentType->save();
            }
        }
    }

    //REMOVE THE CREATED PUBLIC DIRECTORY
    if (is_dir(APPLICATION_PATH . '/temporary/siteadvsearch_search_icons')) {
        $files = scandir(APPLICATION_PATH . '/temporary/siteadvsearch_search_icons');
        foreach ($files as $file) {
            $is_exist = file_exists(APPLICATION_PATH . "/temporary/siteadvsearch_search_icons/$file");
            if ($is_exist) {
                @unlink(APPLICATION_PATH . "/temporary/siteadvsearch_search_icons/$file");
            }
        }
        @rmdir(APPLICATION_PATH . '/temporary/siteadvsearch_search_icons');
    }
}


if (Engine_Api::_()->hasModuleBootstrap('captivate')) {
    $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
    $header_page_id = Engine_Api::_()->siteadvsearch()->getWidgetizedPageId(array('name' => 'header'));

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
                ->where('name =?', 'siteadvsearch.menu-mini')
                ->where('page_id =?', $header_page_id)->query()
                ->fetchColumn();
        if ($params) {
            $encodedParams = json_decode($params);
            if (isset($encodedParams->advsearch_search_width)) {
                $encodedParams->advsearch_search_width = 200;
                $decodedParams = json_encode($encodedParams);
                $db->query("UPDATE `engine4_core_content` SET `params` = '$decodedParams' WHERE `engine4_core_content`.`page_id` = '$header_page_id' AND `engine4_core_content`.`name` = 'siteadvsearch.menu-mini' ;");
            }
        }
    }
}
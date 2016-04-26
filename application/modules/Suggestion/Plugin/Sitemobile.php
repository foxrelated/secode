<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Plugin_Sitemobile {

    protected $_pagesTable;
    protected $_contentTable;

    public function onIntegrated() {

        $this->_pagesTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
        $this->_contentTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
        //Suggestion page
        $this->addSuggestionPymkHomeContent();
        $this->addSuggestionSeeAllPage();
        $this->addSuggestionMainPage();
        $this->addSuggestionRequestPage();
        $this->addSuggestionNavBrowseMemberPage();
    }

    public function addSuggestionPymkHomeContent() {
        // install content areas

        $db = Engine_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);

        // profile page
        $select
                ->from($this->_pagesTable)
                ->where('name = ?', 'user_index_home')
                ->limit(1);
        $page_id = $select->query()->fetchObject()->page_id;

        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
                ->from($this->_contentTable)
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'suggestion.suggestion-friend')
        ;
        $info = $select->query()->fetch();

        if (empty($info)) {

            // container_id (will always be there)
            $select = new Zend_Db_Select($db);
            $select
                    ->from($this->_contentTable)
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container_id = $select->query()->fetchObject()->content_id;

            // middle_id (will always be there)
            $select = new Zend_Db_Select($db);
            $select
                    ->from($this->_contentTable)
                    ->where('parent_content_id = ?', $container_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'middle')
                    ->limit(1);
            $middle_id = $select->query()->fetchObject()->content_id;

            $db->insert($this->_contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'suggestion.suggestion-friend',
                'parent_content_id' => $middle_id,
                'order' => 4,
                'params' => '{"title":"People you may know","getWidLimit":"3","friendMaxLimit":"100","suggestionView":"grid","carouselView":"1"}',
                'module' => 'suggestion'
            ));
        }
    }

    public function addSuggestionSeeAllPage() {
        $db = Engine_Db_Table::getDefaultAdapter();

        $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('suggestion_index_viewfriendsuggestion');
        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert($this->_pagesTable, array(
                'name' => 'suggestion_index_viewfriendsuggestion',
                'displayname' => 'Suggestion - View Page',
                'title' => 'Suggestion View page',
                'description' => 'This page displays the list of all suggestions.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert main
            $db->insert($this->_contentTable, array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $main_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert($this->_contentTable, array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitemobile.sitemobile-navigation',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));

            
            if($this->_pagesTable != 'engine4_sitemobileapp_pages' && $this->_pagesTable != 'engine4_sitemobileapp_tablet_pages') {
              // Insert Advance search
              $db->insert($this->_contentTable, array(
                  'type' => 'widget',
                  'name' => 'sitemobile.sitemobile-advancedsearch',
                  'page_id' => $page_id,
                  'parent_content_id' => $main_middle_id,
                  'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
                  'order' => 2,
                  'module' => 'sitemobile'
              ));
            }
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
            ));
        }
    }

    public function addSuggestionMainPage() {
        $db = Engine_Db_Table::getDefaultAdapter();

        $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('suggestion_index-sitemobile_suggestions');
        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert($this->_pagesTable, array(
                'name' => 'suggestion_index-sitemobile_suggestions',
                'displayname' => 'Suggestion - Explore Page',
                'title' => 'Suggestion Explore Page',
                'description' => 'This page displays all suggestions and recommendations.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert main
            $db->insert($this->_contentTable, array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $main_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert($this->_contentTable, array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert($this->_contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitemobile.container-tabs-columns',
                'parent_content_id' => $main_middle_id,
                'order' => 5,
                'params' => '{"max":6}',
                'module' => 'sitemobile'
            ));
            $tab_id = $db->lastInsertId($this->_contentTable);

            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'suggestion.suggestion-friend',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 1,
                'module' => 'suggestion',
                'params' => '{"title":"People you may know","suggestionView":"list","getWidLimit":"10"}',
            ));

            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'suggestion.suggestion-mix',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 2,
                'module' => 'suggestion',
                'params' => '{"title":"Recommendations","recommendationView":"list","getWidLimit":"10"}',
            ));

//            // Insert content
//            $db->insert($this->_contentTable, array(
//                'type' => 'widget',
//                'name' => 'suggestion.common-suggestion',
//                'page_id' => $page_id,
//                'parent_content_id' => $tab_id,
//                'order' => 3,
//                'module' => 'suggestion',
//                'params' => '{"title":"Recommendations (selected content)","recommendationView":"list","getWidLimit":"10"}',
//            ));
        }
    }

    public function addSuggestionRequestPage() {
        $db = Engine_Db_Table::getDefaultAdapter();

        $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('suggestion_index-sitemobile_request');
        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert($this->_pagesTable, array(
                'name' => 'suggestion_index-sitemobile_request',
                'displayname' => 'Suggestion - Friend Request Page',
                'title' => 'Suggestion Friend Request Page',
                'description' => 'This page displays the list of all friend requests.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert main
            $db->insert($this->_contentTable, array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $main_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert($this->_contentTable, array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitemobile.sitemobile-navigation',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));

            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'suggestion.sitemobile-suggestion-request',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
                'module' => 'suggestion'
            ));
        }
    }

    public function addSuggestionNavBrowseMemberPage() {
        // install content areas

        $db = Engine_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);

        // profile page
        $select
                ->from($this->_pagesTable)
                ->where('name = ?', 'user_index_browse')
                ->limit(1);
        $page_id = $select->query()->fetchObject()->page_id;

        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
                ->from($this->_contentTable)
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'sitemobile.sitemobile-navigation')
        ;
        $info = $select->query()->fetch();

        if (empty($info)) {

            // container_id (will always be there)
            $select = new Zend_Db_Select($db);
            $select
                    ->from($this->_contentTable)
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container_id = $select->query()->fetchObject()->content_id;

            // middle_id (will always be there)
            $select = new Zend_Db_Select($db);
            $select
                    ->from($this->_contentTable)
                    ->where('parent_content_id = ?', $container_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'middle')
                    ->limit(1);
            $middle_id = $select->query()->fetchObject()->content_id;

            $db->insert($this->_contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitemobile.sitemobile-navigation',
                'parent_content_id' => $middle_id,
                'order' => 0,
                'params' => '',
                'module' => 'suggestion'
            ));
        }
    }

}

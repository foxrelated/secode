<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagediscussion
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagediscussion_Installer extends Engine_Package_Installer_Module {

  function onInstall() {

    //GET DB
    $db = $this->getDb();
    //CHECK THAT SITEPAGE PLUGIN IS ACTIVATED OR NOT
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitepage.is.active')
            ->limit(1);
    $sitepage_settings = $select->query()->fetchAll();
    if (!empty($sitepage_settings)) {
      $sitepage_is_active = $sitepage_settings[0]['value'];
    } else {
      $sitepage_is_active = 0;
    }

    //CHECK THAT SITEPAGE PLUGIN IS INSTALLED OR NOT
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('enabled = ?', 1);
    $check_sitepage = $select->query()->fetchObject();
    if (!empty($check_sitepage) && !empty($sitepage_is_active)) {
      $select = new Zend_Db_Select($db);
      $check_sitepagediscussion = $select
                      ->from('engine4_core_modules')
                      ->where('name = ?', 'sitepagediscussion')->query()->fetchObject();
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_modules')
              ->where('name = ?', 'sitepagediscussion')
              ->where('version <= ?', '4.2.1');
      $is_enabled = $select->query()->fetchObject();
      if (!empty($is_enabled)) {
        $select = new Zend_Db_Select($db);
        $select_page = $select
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'sitepage_index_view')
                ->limit(1);
        $page = $select_page->query()->fetchAll();
        if (!empty($page)) {
          $page_id = $page[0]['page_id'];
          //PUT SITEPAGE DISCUSSION WIDGET IN ADMIN CONTENT TABLE
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitepage_admincontent')
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitepage.discussion-sitepage')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitepage_admincontent', 'admincontent_id')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['admincontent_id'];
              $select = new Zend_Db_Select($db);
              $select_middle = $select
                      ->from('engine4_sitepage_admincontent')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('type = ?', 'container')
                      ->where('name = ?', 'middle')
                      ->limit(1);
              $middle = $select_middle->query()->fetchAll();
              if (!empty($middle)) {
                $middle_id = $middle[0]['admincontent_id'];
                $select = new Zend_Db_Select($db);
                $select_tab = $select
                        ->from('engine4_sitepage_admincontent')
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'core.container-tabs')
                        ->where('page_id = ?', $page_id)
                        ->limit(1);
                $tab = $select_tab->query()->fetchAll();
                $tab_id = 0;
                if (!empty($tab)) {
                  $tab_id = $tab[0]['admincontent_id'];
                } else {
                  $tab_id = $middle_id;
                }
                $db->insert('engine4_sitepage_admincontent', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepage.discussion-sitepage',
                    'parent_content_id' => $tab_id,
                    'order' => 119,
                    'params' => '{"title":"Discussions","titleCount":"true"}',
                ));
              }
            }
          }
        }
      }

      //PUT SITEPAGE MOST DISCUSSION WIDGET IN SITEPAGE HOME PAGE
      if (empty($check_sitepagediscussion)) {
        $select = new Zend_Db_Select($db);
        $fetchPageId = $select
                        ->from('engine4_core_pages', 'page_id')
                        ->where('name =?', 'sitepage_index_home')
                        ->limit(1)->query()->fetchAll();
        $select = new Zend_Db_Select($db);
        $selectWidgetId = $select
                ->from('engine4_core_content', 'content_id')
                ->where('page_id =?', $fetchPageId[0]['page_id'])
                ->where('type = ?', 'container')
                ->where('name = ?', 'main')
                ->limit(1);
        $fetchWidgetContenerId = $selectWidgetId->query()->fetchAll();
        $select = new Zend_Db_Select($db);
        $selectWidgetId = $select
                ->from('engine4_core_content', 'content_id')
                ->where('page_id =?', $fetchPageId[0]['page_id'])
                ->where('type = ?', 'container')
                ->where('name = ?', 'right')
                ->where('parent_content_id = ?', $fetchWidgetContenerId[0]['content_id'])
                ->limit(1);
        $rightid = $selectWidgetId->query()->fetchAll();
        if (!empty($rightid)) {
          $select = new Zend_Db_Select($db);
          $selectWidgetId = $select
                  ->from('engine4_core_content', 'content_id')
                  ->where('page_id =?', $fetchPageId[0]['page_id'])
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitepage.mostdiscussion-sitepage')
                  ->where('parent_content_id = ?', $rightid[0]['content_id'])
                  ->limit(1);
          $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
          if (empty($fetchWidgetContentId)) {
            $db = $this->getDb();
            $db->insert('engine4_core_content', array(
                'page_id' => $fetchPageId[0]['page_id'],
                'type' => 'widget',
                'name' => 'sitepage.mostdiscussion-sitepage',
                'parent_content_id' => $rightid[0]['content_id'],
                'order' => 999,
                'params' => '{"title":"Most Discussed Pages","titleCount":"true"}',
            ));
          }
        }
        //PUT SITEPAGE DISCUSSION WIDGET IN ADMIN CONTENT TABLE
        $select = new Zend_Db_Select($db);
        $select_page = $select
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'sitepage_index_view')
                ->limit(1);
        $page = $select_page->query()->fetchAll();
        if (!empty($page)) {
          $page_id = $page[0]['page_id'];
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitepage_admincontent')
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitepage.discussion-sitepage')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitepage_admincontent', 'admincontent_id')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['admincontent_id'];
              $select = new Zend_Db_Select($db);
              $select_middle = $select
                      ->from('engine4_sitepage_admincontent')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('type = ?', 'container')
                      ->where('name = ?', 'middle')
                      ->limit(1);
              $middle = $select_middle->query()->fetchAll();
              if (!empty($middle)) {
                $middle_id = $middle[0]['admincontent_id'];
                $select = new Zend_Db_Select($db);
                $select_tab = $select
                        ->from('engine4_sitepage_admincontent')
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'core.container-tabs')
                        ->where('page_id = ?', $page_id)
                        ->limit(1);
                $tab = $select_tab->query()->fetchAll();
                $tab_id = 0;
                if (!empty($tab)) {
                  $tab_id = $tab[0]['admincontent_id'];
                } else {
                  $tab_id = $middle_id;
                }

                $db->insert('engine4_sitepage_admincontent', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepage.discussion-sitepage',
                    'parent_content_id' => $tab_id,
                    'order' => 119,
                    'params' => '{"title":"Discussions","titleCount":"true"}',
                ));
              }
            }
          }

          //PUT SITEPAGE DISCUSSION WIDGET IN CORE CONTENT TABLE
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_core_content')
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitepage.discussion-sitepage')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_core_content', 'content_id')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['content_id'];

              $select = new Zend_Db_Select($db);
              $select_middle = $select
                      ->from('engine4_core_content')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('type = ?', 'container')
                      ->where('name = ?', 'middle')
                      ->limit(1);
              $middle = $select_middle->query()->fetchAll();
              if (!empty($middle)) {
                $middle_id = $middle[0]['content_id'];

                $select = new Zend_Db_Select($db);
                $select_tab = $select
                        ->from('engine4_core_content')
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'core.container-tabs')
                        ->where('page_id = ?', $page_id)
                        ->limit(1);
                $tab = $select_tab->query()->fetchAll();
                if (!empty($tab)) {
                  $tab_id = $tab[0]['content_id'];
                }

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepage.discussion-sitepage',
                    'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
                    'order' => 119,
                    'params' => '{"title":"Discussions","titleCount":"true"}',
                ));

                //PUT SITEPAGE DISCUSSION WIDGET IN USER CONTENT TABLE
                $select = new Zend_Db_Select($db);
                $select = $select
                        ->from('engine4_sitepage_contentpages', 'contentpage_id');

                $contentpage_ids = $select->query()->fetchAll();
                foreach ($contentpage_ids as $contentpage_id) {
                  if (!empty($contentpage_id)) {
                    $page_id = $contentpage_id['contentpage_id'];
                    $select = new Zend_Db_Select($db);
                    $select_content = $select
                            ->from('engine4_sitepage_content')
                            ->where('contentpage_id = ?', $page_id)
                            ->where('type = ?', 'widget')
                            ->where('name = ?', 'sitepage.discussion-sitepage')
                            ->limit(1);
                    $content = $select_content->query()->fetchAll();
                    if (empty($content)) {
                      $select = new Zend_Db_Select($db);
                      $select_container = $select
                              ->from('engine4_sitepage_content', 'content_id')
                              ->where('contentpage_id = ?', $page_id)
                              ->where('type = ?', 'container')
                              ->limit(1);
                      $container = $select_container->query()->fetchAll();
                      if (!empty($container)) {
                        $container_id = $container[0]['content_id'];
                        $select = new Zend_Db_Select($db);
                        $select_middle = $select
                                ->from('engine4_sitepage_content')
                                ->where('parent_content_id = ?', $container_id)
                                ->where('type = ?', 'container')
                                ->where('name = ?', 'middle')
                                ->limit(1);
                        $middle = $select_middle->query()->fetchAll();
                        if (!empty($middle)) {
                          $middle_id = $middle[0]['content_id'];
                          $select = new Zend_Db_Select($db);
                          $select_tab = $select
                                  ->from('engine4_sitepage_content')
                                  ->where('type = ?', 'widget')
                                  ->where('name = ?', 'core.container-tabs')
                                  ->where('contentpage_id = ?', $page_id)
                                  ->limit(1);
                          $tab = $select_tab->query()->fetchAll();
                          if (!empty($tab)) {
                            $tab_id = $tab[0]['content_id'];
                          }
                          $db->insert('engine4_sitepage_content', array(
                              'contentpage_id' => $page_id,
                              'type' => 'widget',
                              'name' => 'sitepage.discussion-sitepage',
                              'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
                              'order' => 119,
                              'params' => '{"title":"Discussions","titleCount":"true"}',
                          ));
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
        $this->oninstallPackageEnableSubMOdules();
      }
      //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
      $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'type'")->fetch();
      if (!empty($type_array)) {
        $varchar = $type_array['Type'];
        $length_varchar = explode("(", $varchar);
        $length = explode(")", $length_varchar[1]);
        $length_type = $length[0];
        if ($length_type < 32) {
          $run_query = $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
        }
      }

      //CODE FOR INCREASE THE SIZE OF engine4_authorization_allow's FIELD type
      $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_allow LIKE 'resource_type'")->fetch();
      if (!empty($type_array)) {
        $varchar = $type_array['Type'];
        $length_varchar = explode("(", $varchar);
        $length = explode(")", $length_varchar[1]);
        $length_type = $length[0];
        if ($length_type < 32) {
          $run_query = $db->query("ALTER TABLE `engine4_authorization_allow` CHANGE `resource_type` `resource_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
        }
      }

      //CODE FOR INCREASE THE SIZE OF engine4_activity_attachments's FIELD type
      $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_attachments LIKE 'type'")->fetch();
      if (!empty($type_array)) {
        $varchar = $type_array['Type'];
        $length_varchar = explode("(", $varchar);
        $length = explode(")", $length_varchar[1]);
        $length_type = $length[0];
        if ($length_type < 32) {
          $run_query = $db->query("ALTER TABLE `engine4_activity_attachments` CHANGE `type` `type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
        }
      }

      //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
      $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'subject_type'")->fetch();
      if (!empty($type_array)) {
        $varchar = $type_array['Type'];
        $length_varchar = explode("(", $varchar);
        $length = explode(")", $length_varchar[1]);
        $length_type = $length[0];
        if ($length_type < 32) {
          $run_query = $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `subject_type` `subject_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
        }
      }

      //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
      $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'object_type'")->fetch();
      if (!empty($type_array)) {
        $varchar = $type_array['Type'];
        $length_varchar = explode("(", $varchar);
        $length = explode(")", $length_varchar[1]);
        $length_type = $length[0];
        if ($length_type < 32) {
          $run_query = $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `object_type` `object_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
        }
      }

      $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_topicwatches'")->fetch();
      if (!empty($table_exist)) {
        //ADD THE INDEX FROM THE "engine4_sitepage_topicwatches" TABLE
        $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_topicwatches` WHERE Key_name = 'page_id'")->fetch();

        if (empty($pageIdColumnIndex)) {
          $db->query("ALTER TABLE `engine4_sitepage_topicwatches` ADD INDEX ( `page_id` )");
        }
      }
      parent::onInstall();
    } elseif (!empty($check_sitepage) && empty($sitepage_is_active)) {
      $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      if (strstr($url_string, "manage/install")) {
        $calling_from = 'install';
      } else if (strstr($url_string, "manage/query")) {
        $calling_from = 'queary';
      }
      $explode_base_url = explode("/", $baseUrl);
      foreach ($explode_base_url as $url_key) {
        if ($url_key != 'install') {
          $core_final_url .= $url_key . '/';
        }
      }

      return $this->_error("<span style='color:red'>Note: You have installed the Page Plugin but not activated it on your site yet. Please activate it first before installing the Page Discussion Extension.</span><br/> <a href='" . 'http://' . $core_final_url . "admin/sitepage/settings/readme'>Click here</a> to activate the Page Plugin.");
    } else {
      $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      return $this->_error("<span style='color:red'>Note: You have not installed the Page Plugin on your site yet. Please install it first before installing the Page Discussion Extension.</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.");
    }
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitepage.feed.type');
    $info = $select->query()->fetch();
    $enable = 1;
    if (!empty($info)) 
      $enable = $info['value'];
      $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES("sitepage_admin_topic_create", "sitepagediscussion", "{item:$object} posted a new discussion topic:", ' . $enable . ', 6, 2, 1, 1, 1, 1),("sitepage_admin_topic_reply", "sitepagediscussion", "{item:$object} replied to a discussion in the page:", ' . $enable . ', "6", "2", "1", "1", "1", "1")');
    
  }

  function oninstallPackageEnableSubMOdules() {

    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepagediscussion');
    $check_sitepagediscussion = $select->query()->fetchObject();
    if (empty($check_sitepagediscussion)) {
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_sitepage_packages')
              ->where('defaultpackage = ?', '1')
              ->limit(1);
      $sitepage_defaultPackage = $select->query()->fetchAll();
      if (!empty($sitepage_defaultPackage)) {
        $values = array();
        $values = unserialize($sitepage_defaultPackage[0]['modules']);
        $values[] = 'sitepagediscussion';
        $modules = serialize($values);
        $db->update('engine4_sitepage_packages', array(
            'modules' => $modules,
                ), array(
            'defaultpackage = ?' => "1"
        ));
      }
    }
  }

}

?>
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
class Sitegroup_Model_DbTable_Content extends Engine_Db_Table {

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
                    ->where('contentgroup_id = ?', $contentgroup_id)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', $name)
                    ->limit(1);
            $content = $select_content->query()->fetchAll();
            if (empty($content)) {
                $select = $this->select();
                $select_container = $select
                        ->from($this->info('name'), array('content_id'))
                        ->where('contentgroup_id = ?', $contentgroup_id)
                        ->where('type = ?', 'container')
                        ->limit(1);
                $container = $select_container->query()->fetchAll();
                if (!empty($container)) {
                    $select = $this->select();
                    $container_id = $container[0]['content_id'];
                    $select_middle = $select
                            ->from($this->info('name'))
                            ->where('parent_content_id = ?', $container_id)
                            ->where('type = ?', 'container')
                            ->where('name = ?', 'middle')
                            ->limit(1);
                    $middle = $select_middle->query()->fetchAll();
                    if (!empty($middle)) {
                        $select = $this->select();
                        $middle_id = $middle[0]['content_id'];
                        $select_tab = $select
                                ->from($this->info('name'))
                                ->where('type = ?', 'widget')
                                ->where('name = ?', 'core.container-tabs')
                                ->where('contentgroup_id = ?', $contentgroup_id)
                                ->limit(1);
                        $tab = $select_tab->query()->fetchAll();
                        $tab_id = '';
                        if (!empty($tab)) {
                            $tab_id = $tab[0]['content_id'];
                        } else {
                            $contentWidget = $this->createRow();
                            $contentWidget->contentgroup_id = $contentgroup_id;
                            $contentWidget->type = 'widget';
                            $contentWidget->name = 'core.container-tabs';
                            $contentWidget->parent_content_id = ($tab_id ? $tab_id : $middle_id);
                            $contentWidget->order = $order;
                            $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.showmore', 8);
                            $contentWidget->params = "{\"max\":\"$showmaxtab\"}";
                            $contentWidget->save();
                        }
                        if ($name != 'sitegroupintegration.profile-items') {
                            $contentWidget = $this->createRow();
                            $contentWidget->contentgroup_id = $contentgroup_id;
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
                                            ->from('engine4_sitegroup_content')
                                            ->where('parent_content_id = ?', $tab_id)
                                            ->where('type = ?', 'widget')
                                            ->where('name = ?', 'sitegroupintegration.profile-items')
                                            ->where('params = ?', '{"title":"' . $item_title . '","resource_type":"' . $resource_type . '","nomobile":"0","name":"sitegroupintegration.profile-items"}');
                                    $info = $select->query()->fetch();
                                    if (empty($info)) {

                                        // tab on profile
                                        $db->insert('engine4_sitegroup_content', array(
                                            'contentgroup_id' => $contentgroup_id,
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
     * Set profile group default widget in user content table with tab
     *
     * @param string $name
     * @param int $group_id
     * @param string $title
     * @param int $titleCount
     * @param int $order
     */
    public function setContentDefaultInfoWithoutTab($name = null, $contentgroup_id, $title = null, $titleCount = null, $order = null) {
        $db = Engine_Db_Table::getDefaultAdapter();
        if (!empty($name)) {
            $select = $this->select();
            $select_content = $select
                    ->from($this->info('name'))
                    ->where('contentgroup_id = ?', $contentgroup_id)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', $name)
                    ->limit(1);
            $content = $select_content->query()->fetchAll();
            if (empty($content)) {
                $select = $this->select();
                $select_container = $select
                        ->from($this->info('name'), array('content_id'))
                        ->where('contentgroup_id = ?', $contentgroup_id)
                        ->where('type = ?', 'container')
                        ->limit(1);
                $container = $select_container->query()->fetchAll();
                if (!empty($container)) {
                    $select = $this->select();
                    $container_id = $container[0]['content_id'];
                    $select_middle = $select
                            ->from($this->info('name'))
                            ->where('parent_content_id = ?', $container_id)
                            ->where('type = ?', 'container')
                            ->where('name = ?', 'middle')
                            ->limit(1);
                    $middle = $select_middle->query()->fetchAll();
                    if (!empty($middle)) {
                        $middle_id = $middle[0]['content_id'];

                        if ($name != 'sitegroupintegration.profile-items') {
                            $contentWidget = $this->createRow();
                            $contentWidget->contentgroup_id = $contentgroup_id;
                            $contentWidget->type = 'widget';
                            $contentWidget->name = $name;
                            $contentWidget->parent_content_id = ($middle_id);
                            $contentWidget->order = $order;
                            $contentWidget->params = '{"title":"' . $title . '" , "titleCount":"' . $titleCount . '"}';
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
                                            ->from('engine4_sitegroup_content')
                                            ->where('parent_content_id = ?', $middle_id)
                                            ->where('type = ?', 'widget')
                                            ->where('name = ?', 'sitegroupintegration.profile-items')
                                            ->where('params = ?', '{"title":"' . $item_title . '","resource_type":"' . $resource_type . '","nomobile":"0","name":"sitegroupintegration.profile-items"}');
                                    $info = $select->query()->fetch();
                                    if (empty($info)) {

                                        // tab on profile
                                        $db->insert('engine4_sitegroup_content', array(
                                            'contentgroup_id' => $contentgroup_id,
                                            'type' => 'widget',
                                            'name' => 'sitegroupintegration.profile-items',
                                            'parent_content_id' => $middle_id,
                                            'order' => 999,
                                            'params' => '{"title":"' . $item_title . '","resource_type":"' . $resource_type . '","nomobile":"0","name":"sitegroupintegration.profile-items"}',
                                        ));
                                    }
                                    // }
                                }
                            }

                            $this->tabgroupintwidgetlayout('document', '{"title":"Docuemts","resource_type":"document_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $middle_id, $group_id);

                            $this->tabgroupintwidgetlayout('sitegroup', '{"title":"Groups","resource_type":"sitegroup_group_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $middle_id, $group_id);

                            $this->tabgroupintwidgetlayout('sitepage', '{"title":"Pages","resource_type":"sitepage_page_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $middle_id, $group_id);

                            $this->tabgroupintwidgetlayout('list', '{"title":"Listings","resource_type":"list_listing_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $middle_id, $group_id);
                            $this->tabgroupintwidgetlayout('folder', '{"title":"Folders","resource_type":"folder_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $middle_id, $group_id);
                            $this->tabgroupintwidgetlayout('quiz', '{"title":"Quiz","resource_type":"quiz_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $middle_id, $group_id);

                            $this->tabgroupintwidgetlayout('sitefaq', '{"title":"FAQs","resource_type":"sitefaq_faq_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $middle_id, $group_id);

                            $this->tabgroupintwidgetlayout('sitetutorial', '{"title":"Tutorials","resource_type":"sitetutorial_tutorial_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $middle_id, $group_ids);

                            $this->tabgroupintwidgetlayout('sitestoreproduct', '{"title":"Products","resource_type":"sitestoreproduct_product_0","nomobile":"0","name":"sitegroupintegration.profile-items"}', $middle_id, $group_id);
                        }
                    }
                }
            }
        }
    }

    /**
     * Set default widget in content groups table
     *
     * @param object $table
     * @param string $tablename
     * @param int $group_id
     * @param string $type
     * @param string $widgetname
     * @param int $middle_id 
     * @param int $order 
     * @param string $title 
     * @param int $titlecount    
     */
    public function setDefaultDataUserWidget($table, $tablename, $contentgroup_id, $type, $widgetname, $middle_id, $order, $title = null, $titlecount = null, $advanced_activity_params = null) {
        $selectWidgetId = $this->select()
                ->where('contentgroup_id =?', $contentgroup_id)
                ->where('type = ?', $type)
                ->where('name = ?', $widgetname)
                ->where('parent_content_id = ?', $middle_id)
                ->limit(1);
        $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
        if (empty($fetchWidgetContentId)) {
            $contentWidget = $this->createRow();
            $contentWidget->contentgroup_id = $contentgroup_id;
            $contentWidget->type = $type;
            $contentWidget->name = $widgetname;
            $contentWidget->parent_content_id = $middle_id;
            $contentWidget->order = $order;
            if (empty($advanced_activity_params)) {
                $contentWidget->params = "{\"title\":\"$title\",\"titleCount\":$titlecount}";
            } else {
                $contentWidget->params = "$advanced_activity_params";
            }
            $contentWidget->save();
        }
    }

    /**
     * Set default widget in content groups table without tab
     *
     * @param int $group_id
     */
    public function setWithoutTabLayout($group_id, $sitegroup_layout_cover_photo = 1) {

        // GET CONTENT TABLE
        $contentTable = Engine_Api::_()->getDbtable('content', 'sitegroup');

        // GET CONTENT TABLE NAME
        $contentTableName = $this->info('name');

        //INSERTING MAIN CONTAINER
        $mainContainer = $this->createRow();
        $mainContainer->contentgroup_id = $group_id;
        $mainContainer->type = 'container';
        $mainContainer->name = 'main';
        $mainContainer->order = 2;
        $mainContainer->save();
        $container_id = $mainContainer->content_id;

        //INSERTING MAIN-MIDDLE CONTAINER
        $mainMiddleContainer = $this->createRow();
        $mainMiddleContainer->contentgroup_id = $group_id;
        $mainMiddleContainer->type = 'container';
        $mainMiddleContainer->name = 'middle';
        $mainMiddleContainer->parent_content_id = $container_id;
        $mainMiddleContainer->order = 6;
        $mainMiddleContainer->save();
        $middle_id = $mainMiddleContainer->content_id;

        //INSERTING MAIN-LEFT CONTAINER
        $mainLeftContainer = $this->createRow();
        $mainLeftContainer->contentgroup_id = $group_id;
        $mainLeftContainer->type = 'container';
        $mainLeftContainer->name = 'right';
        $mainLeftContainer->parent_content_id = $container_id;
        $mainLeftContainer->order = 4;
        $mainLeftContainer->save();
        $left_id = $mainLeftContainer->content_id;

        if (empty($sitegroup_layout_cover_photo)) {

            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-profile-breadcrumb', $middle_id, 1, '', 'true');

            //INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
                $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupmember.groupcover-photo-sitegroupmembers', $middle_id, 2, '', 'true');
            }

            //INSERTING TITLE WIDGET
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.title-sitegroup', $middle_id, 3, '', 'true');

            //INSERTING LIKE WIDGET
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'seaocore.like-button', $middle_id, 4, '', 'true');

            //INSERTING FACEBOOK LIKE WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
                $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'Facebookse.facebookse-sitegroupprofilelike', $middle_id, 5, '', 'true');
            }

            //INSERTING MAIN PHOTO WIDGET
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.mainphoto-sitegroup', $left_id, 10, '', 'true');
        } else {
            //INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-profile-breadcrumb', $middle_id, 1, '', 'true');

            //INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-cover-information-sitegroup', $middle_id, 2, '', 'true');
        }

        //INSERTING CONTACT DETAIL WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.contactdetails-sitegroup', $middle_id, 5, '', 'true');

//     //INSERTING PHOTO STRIP WIDGET
//     if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
//       $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.photorecent-sitegroup', $middle_id, 6, '', 'true');
//     }
        //INSERTING ACTIVITY FEED WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
            $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'advancedactivity.home-feeds', $middle_id, 6, 'Updates', 'true', $advanced_activity_params);
        } else {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'activity.feed', $middle_id, 6, 'Updates', 'true');
        }

        //INSERTING INFORAMTION WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.info-sitegroup', $middle_id, 7, 'Info', 'true');

        //INSERTING OVERVIEW WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.overview-sitegroup', $middle_id, 8, 'Overview', 'true');

        //INSERTING LOCATION WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.location-sitegroup', $middle_id, 9, 'Map', 'true');

        //INSERTING LINKS WIDGET  
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'core.profile-links', $middle_id, 125, 'Links', 'true');

        //INSERTING WIDGET LINK WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.widgetlinks-sitegroup', $left_id, 11, '', 'true');

        //INSERTING OPTIONS WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.options-sitegroup', $left_id, 12, '', 'true');

        //INSERTING WRITE SOMETHING ABOUT WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.write-group', $left_id, 13, '', 'true');

        //INSERTING WRITE SOMETHING ABOUT WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.information-sitegroup', $left_id, 10, 'Information', 'true');

        //INSERTING LIKE WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

        //INSERTING RATING WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupreview.ratings-sitegroupreviews', $left_id, 16, 'Ratings', 'true');
        }

        //INSERTING BADGE WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge')) {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupbadge.badge-sitegroupbadge', $left_id, 17, 'Badge', 'true');
        }

        $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitegroup.socialshare-sitegroup"}';

        //INSERTING SOCIAL SHARE WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.socialshare-sitegroup', $left_id, 19, 'Social Share', 'true', $social_share_default_code);

        //INSERTING ACTIVITY FEED WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
            $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'advancedactivity.home-feeds', $middle_id, 6, 'Updates', 'true', $advanced_activity_params);
        } else {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'activity.feed', $middle_id, 6, 'Updates', 'true');
        }

        //INSERTING INFORAMTION WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.info-sitegroup', $middle_id, 7, 'Info', 'true');

        //INSERTING OVERVIEW WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.overview-sitegroup', $middle_id, 8, 'Overview', 'true');

        //INSERTING LOCATION WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.location-sitegroup', $middle_id, 9, 'Map', 'true');

        //INSERTING LINKS WIDGET  
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'core.profile-links', $middle_id, 125, 'Links', 'true');

        //INSERTING WIDGET LINK WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.widgetlinks-sitegroup', $left_id, 11, '', 'true');

        //INSERTING OPTIONS WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.options-sitegroup', $left_id, 12, '', 'true');

        //INSERTING WRITE SOMETHING ABOUT WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.write-group', $left_id, 13, '', 'true');

        //INSERTING WRITE SOMETHING ABOUT WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.information-sitegroup', $left_id, 10, 'Information', 'true');

        //INSERTING LIKE WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

        //INSERTING RATING WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupreview.ratings-sitegroupreviews', $left_id, 16, 'Ratings', 'true');
        }

        //INSERTING BADGE WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge')) {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupbadge.badge-sitegroupbadge', $left_id, 17, 'Badge', 'true');
        }

        $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitegroup.socialshare-sitegroup"}';

        //INSERTING SOCIAL SHARE WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.socialshare-sitegroup', $left_id, 19, 'Social Share', 'true', $social_share_default_code);

        //INSERTING INSIGHTS WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.insights-sitegroup', $left_id, 22, 'Insights', 'true');

        //INSERTING FEATURED OWNER WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.featuredowner-sitegroup', $left_id, 23, 'Owners', 'true');

        //INSERTING ALBUM WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.albums-sitegroup', $left_id, 24, 'Albums', 'true');
            $this->setContentDefaultInfoWithoutTab('sitegroup.photos-sitegroup', $group_id, 'Photos', 'true', '110');
        }

        //INSERTING GROUP PROFILE PLAYER WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupmusic.profile-player', $left_id, 25, '', 'true');
        }

        //INSERTING LINKED GROUP WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.favourite-group', $left_id, 26, 'Linked Groups', 'true');

        //INSERTING VIDEO WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
            $this->setContentDefaultInfoWithoutTab('sitegroupvideo.profile-sitegroupvideos', $group_id, 'Videos', 'true', '111');
        }

        //INSERTING EVENT WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
            $this->setContentDefaultInfoWithoutTab('sitevideo.contenttype-videos', $group_id, 'Videos', 'true', '117');
        }

        //INSERTING NOTE WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) {
            $this->setContentDefaultInfoWithoutTab('sitegroupnote.profile-sitegroupnotes', $group_id, 'Notes', 'true', '112');
        }

        //INSERTING REVIEW WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
            $this->setContentDefaultInfoWithoutTab('sitegroupreview.profile-sitegroupreviews', $group_id, 'Reviews', 'true', '113');
        }

        //INSERTING FORM WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform')) {
            $this->setContentDefaultInfoWithoutTab('sitegroupform.sitegroup-viewform', $group_id, 'Form', 'false', '114');
        }

        //INSERTING DOCUMENT WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
            $this->setContentDefaultInfoWithoutTab('sitegroupdocument.profile-sitegroupdocuments', $group_id, 'Documents', 'true', '115');
        }

        //INSERTING OFFER WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer')) {
            $this->setContentDefaultInfoWithoutTab('sitegroupoffer.profile-sitegroupoffers', $group_id, 'Offers', 'true', '116');
        }

        //INSERTING EVENT WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
            $this->setContentDefaultInfoWithoutTab('siteevent.contenttype-events', $group_id, 'Events', 'true', '117');
        }

        //INSERTING EVENT WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
            $this->setContentDefaultInfoWithoutTab('sitegroupevent.profile-sitegroupevents', $group_id, 'Events', 'true', '117');
        }

        //INSERTING POLL WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
            $this->setContentDefaultInfoWithoutTab('sitegrouppoll.profile-sitegrouppolls', $group_id, 'Polls', 'true', '118');
        }

        //INSERTING DISCUSSION WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
            $this->setContentDefaultInfoWithoutTab('sitegroup.discussion-sitegroup', $group_id, 'Discussions', 'true', '119');
        }

        //INSERTING NOTE WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
            $this->setContentDefaultInfoWithoutTab('sitegroupmusic.profile-sitegroupmusic', $group_id, 'Music', 'true', '120');
        }
        //INSERTING TWITTER WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter')) {
            $this->setContentDefaultInfoWithoutTab('sitegrouptwitter.feeds-sitegrouptwitter', $group_id, 'Twitter', 'true', '121');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
            $this->setContentDefaultInfoWithoutTab('sitegroupmember.profile-sitegroupmembers', $group_id, 'Members', 'true', '122');
            $this->setContentDefaultInfoWithoutTab('sitegroupmember.profile-sitegroupmembers-announcements', $group_id, 'Announcements', 'true', '123');
        }

        //INSERTING GROUP INTEGRATION WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration')) {
            $this->setContentDefaultInfoWithoutTab('sitegroupintegration.profile-items', $group_id, '', '', 999);
        }
    }

    public function setTabbedLayout($group_id, $sitegroup_layout_cover_photo = 1) {

        //SHOW HOW MANY TAB SHOULD BE SHOW IN THE GROUP PROFILE GROUP BEFORE MORE LINK
        $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.showmore', 8);

        // GET CONTENT TABLE
        $contentTable = Engine_Api::_()->getDbtable('content', 'sitegroup');

        // GET CONTENT TABLE NAME    
        $contentTableName = $this->info('name');

        //INSERTING MAIN CONTAINER
        $mainContainer = $this->createRow();
        $mainContainer->contentgroup_id = $group_id;
        $mainContainer->type = 'container';
        $mainContainer->name = 'main';
        $mainContainer->order = 2;
        $mainContainer->save();
        $container_id = $mainContainer->content_id;

        //INSERTING MAIN-MIDDLE CONTAINER
        $mainMiddleContainer = $this->createRow();
        $mainMiddleContainer->contentgroup_id = $group_id;
        $mainMiddleContainer->type = 'container';
        $mainMiddleContainer->name = 'middle';
        $mainMiddleContainer->parent_content_id = $container_id;
        $mainMiddleContainer->order = 6;
        $mainMiddleContainer->save();
        $middle_id = $mainMiddleContainer->content_id;

        //INSERTING MAIN-LEFT CONTAINER
        $mainLeftContainer = $this->createRow();
        $mainLeftContainer->contentgroup_id = $group_id;
        $mainLeftContainer->type = 'container';
        $mainLeftContainer->name = 'right';
        $mainLeftContainer->parent_content_id = $container_id;
        $mainLeftContainer->order = 4;
        $mainLeftContainer->save();
        $left_id = $mainLeftContainer->content_id;

        //INSERTING MAIN-MIDDLE-TAB CONTAINER
        $middleTabContainer = $this->createRow();
        $middleTabContainer->contentgroup_id = $group_id;
        $middleTabContainer->type = 'widget';
        $middleTabContainer->name = 'core.container-tabs';
        $middleTabContainer->parent_content_id = $middle_id;
        $middleTabContainer->order = 10;
        $middleTabContainer->params = "{\"max\":\"$showmaxtab\"}";
        $middleTabContainer->save();
        $middle_tab = $middleTabContainer->content_id;

        //INSERTING THUMB PHOTO WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.thumbphoto-sitegroup', $middle_id, 3, '', 'true');

        if (empty($sitegroup_layout_cover_photo)) {

            //INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-profile-breadcrumb', $middle_id, 1, '', 'true');

            //INSERTING THUMB PHOTO WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
                $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupmember.groupcover-photo-sitegroupmembers', $middle_id, 2, '', 'true');
            }

            //INSERTING TITLE WIDGET
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.title-sitegroup', $middle_id, 4, '', 'true');

            //INSERTING LIKE WIDGET
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'seaocore.like-button', $middle_id, 5, '', 'true');

            //INSERTING FACEBOOK LIKE WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
                $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'Facebookse.facebookse-sitegroupprofilelike', $middle_id, 6, '', 'true');
            }

            //INSERTING MAIN PHOTO WIDGET
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.mainphoto-sitegroup', $left_id, 10, '', 'true');
        } else {

            //INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-profile-breadcrumb', $middle_id, 1, '', 'true');

            //INSERTING THUMB PHOTO WIDGET
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-cover-information-sitegroup', $middle_id, 2, '', 'true');
        }

        //INSERTING CONTACT DETAIL WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.contactdetails-sitegroup', $middle_id, 7, '', 'true');

//     //INSERTING PHOTO STRIP WIDGET
// 	  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
// 	    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.photorecent-sitegroup', $middle_id, 8, '', 'true');
// 	  }
        //INSERTING MAIN PHOTO WIDGET 
        //$this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.mainphoto-sitegroup', $left_id, 10, '', 'true');
        //INSERTING OPTIONS WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.options-sitegroup', $left_id, 11, '', 'true');

        //INSERTING INFORMATION WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.information-sitegroup', $left_id, 10, 'Information', 'true');

        //INSERTING WRITE SOMETHING ABOUT WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

        //INSERTING RATING WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupreview.ratings-sitegroupreviews', $left_id, 16, 'Ratings', 'true');
        }

        //INSERTING BADGE WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge')) {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupbadge.badge-sitegroupbadge', $left_id, 17, 'Badge', 'true');
        }

        //INSERTING YOU MAY ALSO LIKE WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.suggestedgroup-sitegroup', $left_id, 18, 'You May Also Like', 'true');

        $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitegroup.socialshare-sitegroup"}';

        //INSERTING SOCIAL SHARE WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.socialshare-sitegroup', $left_id, 19, 'Social Share', 'true', $social_share_default_code);


        //INSERTING INSIGHTS WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.insights-sitegroup', $left_id, 21, 'Insights', 'true');

        //INSERTING INSIGHTS WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.insights-sitegroup', $left_id, 21, 'Insights', 'true');

        //INSERTING FEATURED OWNER WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.featuredowner-sitegroup', $left_id, 22, 'Owners', 'true');

        //INSERTING ALBUM WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.albums-sitegroup', $left_id, 23, 'Albums', 'true');
        }

        //INSERTING GROUP PROFILE PLAYER WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupmusic.profile-player', $left_id, 24, '', 'true');
        }

        //INSERTING LINKED GROUP WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.favourite-group', $left_id, 25, 'Linked Groups', 'true');

        //INSERTING ACTIVITY FEED WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
            $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 2, 'Updates', 'true', $advanced_activity_params);
        } else {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'activity.feed', $middle_tab, 2, 'Updates', 'true');
        }

        //INSERTING INFORAMTION WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.info-sitegroup', $middle_tab, 3, 'Info', 'true');

        //INSERTING OVERVIEW WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.overview-sitegroup', $middle_tab, 4, 'Overview', 'true');

        //INSERTING LOCATION WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.location-sitegroup', $middle_tab, 5, 'Map', 'true');

        //INSERTING LINKS WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'core.profile-links', $middle_tab, 125, 'Links', 'true');

        //INSERTING ALBUM WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
            $this->setDefaultInfo('sitegroup.photos-sitegroup', $group_id, 'Photos', 'true', '110');
        }

        //INSERTING FEATURED OWNER WIDGET 
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.featuredowner-sitegroup', $left_id, 22, 'Owners', 'true');

        //INSERTING ALBUM WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.albums-sitegroup', $left_id, 23, 'Albums', 'true');
        }

        //INSERTING GROUP PROFILE PLAYER WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupmusic.profile-player', $left_id, 24, '', 'true');
        }

        //INSERTING LINKED GROUP WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.favourite-group', $left_id, 25, 'Linked Groups', 'true');

        //INSERTING ACTIVITY FEED WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
            $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 2, 'Updates', 'true', $advanced_activity_params);
        } else {
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'activity.feed', $middle_tab, 2, 'Updates', 'true');
        }

        //INSERTING INFORAMTION WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.info-sitegroup', $middle_tab, 3, 'Info', 'true');

        //INSERTING OVERVIEW WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.overview-sitegroup', $middle_tab, 4, 'Overview', 'true');

        //INSERTING LOCATION WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.location-sitegroup', $middle_tab, 5, 'Map', 'true');

        //INSERTING LINKS WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'core.profile-links', $middle_tab, 125, 'Links', 'true');

        //INSERTING ALBUM WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
            $this->setDefaultInfo('sitegroup.photos-sitegroup', $group_id, 'Photos', 'true', '110');
        }

        //INSERTING VIDEO WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
            $this->setDefaultInfo('sitegroupvideo.profile-sitegroupvideos', $group_id, 'Videos', 'true', '111');
        }
        //INSERTING EVENT WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
            $this->setDefaultInfo('sitevideo.contenttype-videos', $group_id, 'Videos', 'true', '117');
        }

        //INSERTING NOTE WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) {
            $this->setDefaultInfo('sitegroupnote.profile-sitegroupnotes', $group_id, 'Notes', 'true', '112');
        }

        //INSERTING REVIEW WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
            $this->setDefaultInfo('sitegroupreview.profile-sitegroupreviews', $group_id, 'Reviews', 'true', '113');
        }

        //INSERTING FORM WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform')) {
            $this->setDefaultInfo('sitegroupform.sitegroup-viewform', $group_id, 'Form', 'false', '114');
        }

        //INSERTING DOCUMENT WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
            $this->setDefaultInfo('sitegroupdocument.profile-sitegroupdocuments', $group_id, 'Documents', 'true', '115');
        }
        
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
            $this->setDefaultInfo('document.contenttype-documents', $group_id, 'Documents', 'true', '115');
        }

        //INSERTING OFFER WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer')) {
            $this->setDefaultInfo('sitegroupoffer.profile-sitegroupoffers', $group_id, 'Offers', 'true', '116');
        }

        //INSERTING EVENT WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
            $this->setDefaultInfo('siteevent.contenttype-events', $group_id, 'Events', 'true', '117');
        }

        //INSERTING EVENT WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
            $this->setDefaultInfo('sitegroupevent.profile-sitegroupevents', $group_id, 'Events', 'true', '117');
        }

        //INSERTING POLL WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
            $this->setDefaultInfo('sitegrouppoll.profile-sitegrouppolls', $group_id, 'Polls', 'true', '118');
        }

        //INSERTING DISCUSSION WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
            $this->setDefaultInfo('sitegroup.discussion-sitegroup', $group_id, 'Discussions', 'true', '119');
        }

        //INSERTING MUSIC WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
            $this->setDefaultInfo('sitegroupmusic.profile-sitegroupmusic', $group_id, 'Music', 'true', '120');
        }
        //INSERTING TWITTER WIDGET 
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter')) {
            $this->setDefaultInfo('sitegrouptwitter.feeds-sitegrouptwitter', $group_id, 'Twitter', 'true', '121');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
            $this->setDefaultInfo('sitegroupmember.profile-sitegroupmembers', $group_id, 'Members', 'true', '122');
            $this->setDefaultInfo('sitegroupmember.profile-sitegroupmembers-announcements', $group_id, 'Announcements', 'true', '123');
        }

        //INSERTING MEMBER WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration')) {
            $this->setDefaultInfo('sitegroupintegration.profile-items', $group_id, '', '', 999);
        }
    }

    public function setContentDefault($group_id, $sitegroup_layout_cover_photo = 1) {

        $grouplayout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layout.setting', 1);
        $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.showmore', 8);

        if ($grouplayout) {
            //GET CONTENT TABLE
            $contentTable = Engine_Api::_()->getDbtable('content', 'sitegroup');

            //GET CONTENT TABLE NAME
            $contentTableName = $this->info('name');

            //INSERTING MAIN CONTAINER
            $mainContainer = $this->createRow();
            $mainContainer->contentgroup_id = $group_id;
            $mainContainer->type = 'container';
            $mainContainer->name = 'main';
            $mainContainer->order = 2;
            $mainContainer->save();
            $container_id = $mainContainer->content_id;

            //INSERTING MAIN-MIDDLE CONTAINER
            $mainMiddleContainer = $this->createRow();
            $mainMiddleContainer->contentgroup_id = $group_id;
            $mainMiddleContainer->type = 'container';
            $mainMiddleContainer->name = 'middle';
            $mainMiddleContainer->parent_content_id = $container_id;
            $mainMiddleContainer->order = 6;
            $mainMiddleContainer->save();
            $middle_id = $mainMiddleContainer->content_id;

            //INSERTING MAIN-LEFT CONTAINER
            $mainLeftContainer = $this->createRow();
            $mainLeftContainer->contentgroup_id = $group_id;
            $mainLeftContainer->type = 'container';
            $mainLeftContainer->name = 'right';
            $mainLeftContainer->parent_content_id = $container_id;
            $mainLeftContainer->order = 4;
            $mainLeftContainer->save();
            $left_id = $mainLeftContainer->content_id;

            //INSERTING MAIN-MIDDLE TAB CONTAINER
            $middleTabContainer = $this->createRow();
            $middleTabContainer->contentgroup_id = $group_id;
            $middleTabContainer->type = 'widget';
            $middleTabContainer->name = 'core.container-tabs';
            $middleTabContainer->parent_content_id = $middle_id;
            $middleTabContainer->order = 10;
            $middleTabContainer->params = "{\"max\":\"$showmaxtab\"}";
            $middleTabContainer->save();
            $middle_tab = $middleTabContainer->content_id;

            //INSERTING THUMB PHOTO WIDGET
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-cover-information-sitegroup', $middle_id, 1, '', 'true');

            if (empty($sitegroup_layout_cover_photo)) {

                //INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
                $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-profile-breadcrumb', $middle_id, 1, '', 'true');

                //INSERTING THUMB PHOTO WIDGET
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
                    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupmember.groupcover-info-sitegroupmembers', $middle_id, 2, '', 'true');
                }

                //INSERTING TITLE WIDGET
                $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.title-sitegroup', $middle_id, 4, '', 'true');

                //INSERTING LIKE WIDGET
                $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'seaocore.like-button', $middle_id, 5, '', 'true');

                //INSERTING FACEBOOK LIKE WIDGET
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
                    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'Facebookse.facebookse-sitegroupprofilelike', $middle_id, 6, '', 'true');
                }

                //INSERTING MAIN PHOTO WIDGET
                $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.mainphoto-sitegroup', $left_id, 10, '', 'true');
            } else {
                //INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
                $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-profile-breadcrumb', $middle_id, 1, '', 'true');

                //INSERTING THUMB PHOTO WIDGET
                $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-cover-information-sitegroup', $middle_id, 2, '', 'true');
            }

            //INSERTING CONTACT DETAIL WIDGET
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.contactdetails-sitegroup', $middle_id, 7, '', 'true');

// 	    //INSERTING PHOTO STRIP WIDGET
// 		  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
// 		    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.photorecent-sitegroup', $middle_id, 8, '', 'true');
// 		  }
            //INSERTING OPTIONS WIDGET
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.options-sitegroup', $left_id, 11, '', 'true');

            //INSERTING WRITE SOMETHING ABOUT WIDGET 
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

            //INSERTING RATING WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
                $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupreview.ratings-sitegroupreviews', $left_id, 16, 'Ratings', 'true');
            }

            //INSERTING BADGE WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge')) {
                $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupbadge.badge-sitegroupbadge', $left_id, 17, 'Badge', 'true');
            }

            $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitegroup.socialshare-sitegroup"}';

            //INSERTING YOU MAY ALSO LIKE WIDGET 
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.suggestedgroup-sitegroup', $left_id, 18, 'You May Also Like', 'true', $social_share_default_code);


            //INSERTING SOCIAL SHARE WIDGET 
            $this->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.socialshare-sitegroup', $left_id, 19, 'Social Share', 'true');

//			//INSERTING FOUR SQUARE WIDGET 
//			Engine_Api::_()->getDbTable('content', 'sitegroup')->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.foursquare-sitegroup', $left_id, 20, '', 'true');
            //INSERTING INSIGHTS WIDGET 
            Engine_Api::_()->getDbTable('content', 'sitegroup')->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.insights-sitegroup', $left_id, 21, 'Insights', 'true');

            //INSERTING FEATURED OWNER WIDGET 
            Engine_Api::_()->getDbTable('content', 'sitegroup')->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.featuredowner-sitegroup', $left_id, 22, 'Owners', 'true');

            //INSERTING ALBUM WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
                Engine_Api::_()->getDbTable('content', 'sitegroup')->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.albums-sitegroup', $left_id, 23, 'Albums', 'true');
            }

            //INSERTING GROUP PROFILE PLAYER WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
                Engine_Api::_()->getDbTable('content', 'sitegroup')->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupmusic.profile-player', $left_id, 24, '', 'true');
            }

            //INSERTING LINKED GROUP WIDGET
            Engine_Api::_()->getDbTable('content', 'sitegroup')->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.favourite-group', $left_id, 25, 'Linked Groups', 'true');

            //INSERTING ACTIVITY FEED WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
                Engine_Api::_()->getDbTable('content', 'sitegroup')->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 1, 'Updates', 'true', $advanced_activity_params);
            } else {
                Engine_Api::_()->getDbTable('content', 'sitegroup')->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'activity.feed', $middle_tab, 1, 'Updates', 'true');
            }

            //INSERTING INFORAMTION WIDGET
            Engine_Api::_()->getDbTable('content', 'sitegroup')->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.info-sitegroup', $middle_tab, 2, 'Info', 'true');

            //INSERTING OVERVIEW WIDGET
            Engine_Api::_()->getDbTable('content', 'sitegroup')->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.overview-sitegroup', $middle_tab, 3, 'Overview', 'true');

            //INSERTING LOCATION WIDGET
            Engine_Api::_()->getDbTable('content', 'sitegroup')->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.location-sitegroup', $middle_tab, 4, 'Map', 'true');

            //INSERTING LINKS WIDGET
            Engine_Api::_()->getDbTable('content', 'sitegroup')->setDefaultDataUserWidget($contentTable, $contentTableName, $group_id, 'widget', 'core.profile-links', $middle_tab, 125, 'Links', 'true');

            //INSERTING ALBUM WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroup.photos-sitegroup', $group_id, 'Photos', 'true', '110');
            }

            //INSERTING VIDEO WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroupvideo.profile-sitegroupvideos', $group_id, 'Videos', 'true', '111');
            }

            //INSERTING NOTE WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) {
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroupnote.profile-sitegroupnotes', $group_id, 'Notes', 'true', '112');
            }

            //INSERTING REVIEW WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroupreview.profile-sitegroupreviews', $group_id, 'Reviews', 'true', '113');
            }

            //INSERTING FORM WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform')) {
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroupform.sitegroup-viewform', $group_id, 'Form', 'false', '114');
            }

            //INSERTING DOCUMENT WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroupdocument.profile-sitegroupdocuments', $group_id, 'Documents', 'true', '115');
            }
            
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
            Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('document.contenttype-documents', $group_id, 'Documents', 'true', '115');
        }

            //INSERTING OFFER WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer')) {
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroupoffer.profile-sitegroupoffers', $group_id, 'Offer', 'true', '116');
            }

            //INSERTING EVENT WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroupevent.profile-sitegroupevents', $group_id, 'Events', 'true', '117');
            }

            //INSERTING POLL WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegrouppoll.profile-sitegrouppolls', $group_id, 'Polls', 'true', '118');
            }

            //INSERTING DISCUSSION WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroup.discussion-sitegroup', $group_id, 'Discussions', 'true', '119');
            }

            //INSERTING NOTE WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroupmusic.profile-sitegroupmusic', $group_id, 'Music', 'true', '120');
            }
            //INSERTING TWITTER WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter')) {
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegrouptwitter.feeds-sitegrouptwitter', $group_id, 'Twitter', 'true', '121');
            }

            //INSERTING MEMBER WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroupmember.profile-sitegroupmembers', $group_id, 'Members', 'true', '122');
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroupmember.profile-sitegroupmembers-announcements', $group_id, 'Announcements', 'true', '123');
            }

            //INSERTING MEMBER WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration')) {
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroupintegration.profile-items', $group_id, '', '', 999);
            }
        } else {
            Engine_Api::_()->getDbtable('content', 'sitegroup')->setWithoutTabLayout($group_id, $sitegroup_layout_cover_photo);
        }
    }

    /**
     * Gets content id,parama,name
     *
     * @param int $contentgroup_id
     * @return content id,parama,name
     */
    public function getContents($contentgroup_id) {

        $selectGroupAdmin = $this->select()
                ->from($this->info('name'), array('content_id', 'params', 'name'))
                ->where('contentgroup_id =?', $contentgroup_id)
                ->where("name IN ('sitegroup.overview-sitegroup', 'sitegroup.photos-sitegroup', 'sitegroup.discussion-sitegroup', 'sitegroupnote.profile-sitegroupnotes', 'sitegrouppoll.profile-sitegrouppolls', 'sitegroupevent.profile-sitegroupevents', 'sitegroupvideo.profile-sitegroupvideos', 'sitegroupoffer.profile-sitegroupoffers', 'sitegroupreview.profile-sitegroupreviews', 'sitegroupdocument.profile-sitegroupdocuments', 'sitegroupform.sitegroup-viewform','sitegroup.info-sitegroup', 'seaocore.feed', 'activity.feed', 'sitegroup.location-sitegroup', 'core.profile-links', 'sitegroupmusic.profile-sitegroupmusic', 'sitegroupintegration.profile-items','sitegrouptwitter.feeds-sitegrouptwitter', 'sitegroupmember.profile-sitegroupmembers', 'siteevent.contenttype-events', 'sitevideo.contenttype-videos', 'document.contenttype-documents')");
        return $this->fetchAll($selectGroupAdmin);
    }

    /**
     * Gets content_id
     *
     * @param int $contentgroup_id
     * @return $params
     */
    public function getContentId($contentgroup_id, $sitegroup) {
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
                ->where('contentgroup_id = ?', $contentgroup_id)
                ->where('type = ?', 'container')
                ->where('name = ?', 'main')
                ->limit(1);
        $content = $select_content->query()->fetchAll();
        if (!empty($content)) {
            $select = $this->select();
            $select_container = $select
                    ->from($this->info('name'), array('content_id'))
                    ->where('contentgroup_id = ?', $contentgroup_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'middle')
                    ->where("name NOT IN ('	sitegroup.title-sitegroup', 'seaocore.like-button', 'sitegroup.photorecent-sitegroup')")
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
                $select = $this->select();
                $container_id = $container[0]['content_id'];
                $select_middle = $select
                        ->from($this->info('name'))
                        ->where('parent_content_id = ?', $container_id)
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'core.container-tabs')
                        ->where('contentgroup_id = ?', $contentgroup_id)
                        ->limit(1);
                $middle = $select_middle->query()->fetchAll();
                if (!empty($middle)) {
                    $content_id = $middle[0]['content_id'];
                } else {
                    $content_id = $container_id;
                }
            }
        }

        if (!empty($content_id)) {
            $select = $this->select();
            $select_middle = $select
                    ->from($this->info('name'), array('content_id', 'name', 'params'))
                    ->where('parent_content_id = ?', $content_id)
                    ->where('type = ?', 'widget')
                    ->where("name NOT IN ('sitegroup.title-sitegroup', 'seaocore.like-button', 'sitegroup.photorecent-sitegroup', 'Facebookse.facebookse-commonlike', 'sitegroup.thumbphoto-sitegroup')")
                    ->where('contentgroup_id = ?', $contentgroup_id)
                    ->order('order')
            ;

            $select = $this->select();
            $select_photo = $select
                    ->from($this->info('name'), array('params'))
                    ->where('parent_content_id = ?', $content_id)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'sitegroup.photos-sitegroup')->where('contentgroup_id = ?', $contentgroup_id)
                    ->order('content_id ASC');

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
            $content_ids = '';
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
                    case 'advancedactivity.home-feeds':
                        $flag = true;
                        break;
                    case 'activity.feed':
                        $flag = true;
                        break;
                    case 'seaocore.feed':
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
                            $memberCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupmember', 'membership');
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
                        $resource_type = $resource_type_integration;
                        $pieces = explode("_", $resource_type);
                        if ($resource_type == 'document_0' || $resource_type == 'folder_0' || $resource_type == 'quiz_0') {
                            $paramsIntegration['listingtype_id'] = $listingTypeId = $pieces[1];
                            $paramsIntegration['resource_type'] = $resource_type = $pieces[0];
                        } else {
                            $paramsIntegration['listingtype_id'] = $listingTypeId = $pieces[2];
                            $paramsIntegration['resource_type'] = $resource_type = $pieces[0] . '_' . $pieces[1];
                        }

                        $paramsIntegration['group_id'] = $sitegroup->group_id;
                        $paginator = Engine_Api::_()->getDbtable('contents', 'sitegroupintegration')->getResults($paramsIntegration);
                        if ($paginator->getTotalItemCount() <= 0) {
                            $flag = false;
                        } else {
                            $flag = true;
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
                }
                if (!empty($flag)) {
                    $content_ids = $value['content_id'];
                    $content_names = $value['name'];
                    break;
                }
            }
        }

        return array('content_id' => $content_ids, 'content_name' => $content_names, 'itemAlbumCount' => $itemAlbumCount, 'itemPhotoCount' => $itemPhotoCount, 'resource_type_integration' => $resource_type_integration, 'ads_display_integration' => $ads_display_integration);
    }

    /**
     * Gets content_id, name
     *
     * @param int $contentgroup_id
     * @return content_id, name
     */
    public function getContentInformation($contentgroup_id) {
        $select = $this->select()->from($this->info('name'), array('content_id', 'name'))
                        ->where("name IN ('sitegroup.info-sitegroup', 'seaocore.feed', 'advancedactivity.home-feeds','activity.feed', 'sitegroup.location-sitegroup', 'core.profile-links', 'core.html-block')")
                        ->where('contentgroup_id = ?', $contentgroup_id)->order('content_id ASC');

        return $this->fetchAll($select);
    }

    /**
     * Gets content_id, name
     *
     * @param int $contentgroup_id
     * @param int $name 
     * @return content_id, name
     */
    public function getContentByWidgetName($name, $contentgroup_id) {
        $select = $this->select()->from($this->info('name'), array('content_id', 'name'))
                ->where('name =?', $name)
                ->where('contentgroup_id = ?', $contentgroup_id)
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
                ->where('content_id = ?', $tab_main)
                ->query()
                ->fetchColumn();
        return $current_tab_name;
    }

    public function checkWidgetExist($group_id = 0, $widgetName) {

        $params = $this->select()
                        ->from($this->info('name'), 'params')
                        ->where('contentgroup_id = ?', $group_id)
                        ->where('name = ?', $widgetName)
                        ->where('type = ?', 'widget')
                        ->query()->fetchColumn();
        return $params;
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
                        ->from('engine4_sitegroup_content')
                        ->where('parent_content_id = ?', $tab_id)
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'sitegroupintegration.profile-items')
                        ->where('params = ?', $params);
                $info = $select->query()->fetch();
                if (empty($info)) {

                    // tab on profile
                    $db->insert('engine4_sitegroup_content', array(
                        'contentgroup_id' => $group_id,
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
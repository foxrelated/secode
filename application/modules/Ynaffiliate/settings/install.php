<?php
class Ynaffiliate_Installer extends Engine_Package_Installer_Module {

    protected $currentVersion = 0;

    public function onInstall() {
        $this -> _addCommissionRulePage();
        $this -> _addMyAffiliatePage();
        $this -> _addMyAccountEditPage();
        $this -> _addSuggestLinksPage();
        $this -> _addHelpPage();
        $this -> _addFAQsPage();
        $this -> _addCommissionTrackingPage();
        $this -> _addLinksTrackingPage();
        $this -> _addMyRequestPage();
        $this -> _addStatisticPage();
        $this -> _addDynamicLinksPage();
        $this -> getPreviousVersion();
        parent::onInstall();
        $this -> _migrateData();
    }

    protected function getPreviousVersion() {
        $this->currentVersion = $this->getYnaffiliateVersion();
    }

    protected function _addDynamicLinksPage() {
        $db = $this->getDb();

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynaffiliate_sources_dynamic')
            ->limit(1)
            ->query()
            ->fetchColumn();

        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynaffiliate_sources_dynamic',
                'displayname' => 'YN - Affiliate Dynamic Links Page',
                'title' => 'Affiliate Dynamic Links Page',
                'description' => 'Affiliate Dynamic Links Page',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            //Insert main menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynaffiliate.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
        else
        {
            if(!$db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynaffiliate.main-menu') -> where('type = ?', 'widget') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn())
            {
                $top_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'top') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
                $top_middle_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'middle') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> where('parent_content_id = ?', $top_id) -> limit(1) -> query() -> fetchColumn();
                if($top_middle_id)
                {
                    //Insert main menu
                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'ynaffiliate.main-menu',
                        'page_id' => $page_id,
                        'parent_content_id' => $top_middle_id,
                        'order' => 1,
                    ));
                }
            }
        }
    }

    protected function _addStatisticPage() {
        $db = $this->getDb();

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynaffiliate_statistic_index')
            ->limit(1)
            ->query()
            ->fetchColumn();

        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynaffiliate_statistic_index',
                'displayname' => 'YN - Affiliate Statistic Page',
                'title' => 'Affiliate Statistic Page',
                'description' => 'Affiliate Statistic Page',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            //Insert main menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynaffiliate.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
        else
        {
            if(!$db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynaffiliate.main-menu') -> where('type = ?', 'widget') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn())
            {
                $top_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'top') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
                $top_middle_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'middle') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> where('parent_content_id = ?', $top_id) -> limit(1) -> query() -> fetchColumn();
                if($top_middle_id)
                {
                    //Insert main menu
                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'ynaffiliate.main-menu',
                        'page_id' => $page_id,
                        'parent_content_id' => $top_middle_id,
                        'order' => 1,
                    ));
                }
            }
        }
    }

    protected function _addMyRequestPage() {
        $db = $this->getDb();

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynaffiliate_my-request_index')
            ->limit(1)
            ->query()
            ->fetchColumn();

        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynaffiliate_my-request_index',
                'displayname' => 'YN - Affiliate Manage Requests Page',
                'title' => 'Affiliate Manage Requests Page',
                'description' => 'Affiliate Manage Requests Page',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            //Insert main menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynaffiliate.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
        else
        {
            if(!$db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynaffiliate.main-menu') -> where('type = ?', 'widget') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn())
            {
                $top_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'top') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
                $top_middle_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'middle') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> where('parent_content_id = ?', $top_id) -> limit(1) -> query() -> fetchColumn();
                if($top_middle_id)
                {
                    //Insert main menu
                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'ynaffiliate.main-menu',
                        'page_id' => $page_id,
                        'parent_content_id' => $top_middle_id,
                        'order' => 1,
                    ));
                }
            }
        }
    }

    protected function _addLinksTrackingPage() {
        $db = $this->getDb();

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynaffiliate_tracking_click')
            ->limit(1)
            ->query()
            ->fetchColumn();

        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynaffiliate_tracking_click',
                'displayname' => 'YN - Affiliate Links Tracking Page',
                'title' => 'Affiliate Links Tracking Page',
                'description' => 'Affiliate Links Tracking Page',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            //Insert main menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynaffiliate.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
        else
        {
            if(!$db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynaffiliate.main-menu') -> where('type = ?', 'widget') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn())
            {
                $top_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'top') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
                $top_middle_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'middle') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> where('parent_content_id = ?', $top_id) -> limit(1) -> query() -> fetchColumn();
                if($top_middle_id)
                {
                    //Insert main menu
                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'ynaffiliate.main-menu',
                        'page_id' => $page_id,
                        'parent_content_id' => $top_middle_id,
                        'order' => 1,
                    ));
                }
            }
        }
    }

    protected function _addCommissionTrackingPage() {
        $db = $this->getDb();

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynaffiliate_tracking_purchase')
            ->limit(1)
            ->query()
            ->fetchColumn();

        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynaffiliate_tracking_purchase',
                'displayname' => 'YN - Affiliate Commission Tracking Page',
                'title' => 'Affiliate Commission Tracking Page',
                'description' => 'Affiliate Commission Tracking Page',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            //Insert main menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynaffiliate.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
        else
        {
            if(!$db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynaffiliate.main-menu') -> where('type = ?', 'widget') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn())
            {
                $top_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'top') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
                $top_middle_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'middle') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> where('parent_content_id = ?', $top_id) -> limit(1) -> query() -> fetchColumn();
                if($top_middle_id)
                {
                    //Insert main menu
                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'ynaffiliate.main-menu',
                        'page_id' => $page_id,
                        'parent_content_id' => $top_middle_id,
                        'order' => 1,
                    ));
                }
            }
        }
    }

    protected function _addFAQsPage() {
        $db = $this->getDb();

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynaffiliate_faqs_index')
            ->limit(1)
            ->query()
            ->fetchColumn();

        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynaffiliate_faqs_index',
                'displayname' => 'YN - Affiliate FAQs Page',
                'title' => 'Affiliate FAQs Page',
                'description' => 'Affiliate FAQs Page',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            //Insert main menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynaffiliate.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
        else
        {
            if(!$db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynaffiliate.main-menu') -> where('type = ?', 'widget') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn())
            {
                $top_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'top') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
                $top_middle_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'middle') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> where('parent_content_id = ?', $top_id) -> limit(1) -> query() -> fetchColumn();
                if($top_middle_id)
                {
                    //Insert main menu
                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'ynaffiliate.main-menu',
                        'page_id' => $page_id,
                        'parent_content_id' => $top_middle_id,
                        'order' => 1,
                    ));
                }
            }
        }
    }

    protected function _addHelpPage() {
        $db = $this->getDb();

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynaffiliate_help_detail')
            ->limit(1)
            ->query()
            ->fetchColumn();

        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynaffiliate_help_detail',
                'displayname' => 'YN - Affiliate Help Page',
                'title' => 'Affiliate Help Page',
                'description' => 'Affiliate Help Page',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            //Insert main menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynaffiliate.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
        else
        {
            if(!$db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynaffiliate.main-menu') -> where('type = ?', 'widget') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn())
            {
                $top_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'top') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
                $top_middle_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'middle') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> where('parent_content_id = ?', $top_id) -> limit(1) -> query() -> fetchColumn();
                if($top_middle_id)
                {
                    //Insert main menu
                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'ynaffiliate.main-menu',
                        'page_id' => $page_id,
                        'parent_content_id' => $top_middle_id,
                        'order' => 1,
                    ));
                }
            }
        }
    }

    protected function _addSuggestLinksPage() {
        $db = $this->getDb();

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynaffiliate_sources_index')
            ->limit(1)
            ->query()
            ->fetchColumn();

        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynaffiliate_sources_index',
                'displayname' => 'YN - Affiliate Suggest Links Page',
                'title' => 'Affiliate Suggest Links Page',
                'description' => 'Affiliate Suggest Links Page',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            //Insert main menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynaffiliate.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
        else
        {
            if(!$db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynaffiliate.main-menu') -> where('type = ?', 'widget') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn())
            {
                $top_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'top') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
                $top_middle_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'middle') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> where('parent_content_id = ?', $top_id) -> limit(1) -> query() -> fetchColumn();
                if($top_middle_id)
                {
                    //Insert main menu
                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'ynaffiliate.main-menu',
                        'page_id' => $page_id,
                        'parent_content_id' => $top_middle_id,
                        'order' => 1,
                    ));
                }
            }
        }
    }

    protected function _addMyAccountEditPage() {
        $db = $this->getDb();

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynaffiliate_my-account_edit')
            ->limit(1)
            ->query()
            ->fetchColumn();

        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynaffiliate_my-account_edit',
                'displayname' => 'YN - Affiliate Account Edit Page',
                'title' => 'Affiliate Account Edit Page',
                'description' => 'Affiliate Account Edit Page',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            //Insert main menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynaffiliate.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
        else
        {
            if(!$db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynaffiliate.main-menu') -> where('type = ?', 'widget') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn())
            {
                $top_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'top') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
                $top_middle_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'middle') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> where('parent_content_id = ?', $top_id) -> limit(1) -> query() -> fetchColumn();
                if($top_middle_id)
                {
                    //Insert main menu
                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'ynaffiliate.main-menu',
                        'page_id' => $page_id,
                        'parent_content_id' => $top_middle_id,
                        'order' => 1,
                    ));
                }
            }
        }
    }

    protected function _addMyAffiliatePage() {
        $db = $this->getDb();

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynaffiliate_my-affiliate_index')
            ->limit(1)
            ->query()
            ->fetchColumn();

        if(!$page_id)
        {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynaffiliate_my-affiliate_index',
                'displayname' => 'YN - Affiliate Network Clients Page',
                'title' => 'Affiliate Network Clients Page',
                'description' => 'Affiliate Network Clients Page',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            //Insert main menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynaffiliate.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
        else
        {
            if(!$db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynaffiliate.main-menu') -> where('type = ?', 'widget') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn())
            {
                $top_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'top') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
                $top_middle_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'middle') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> where('parent_content_id = ?', $top_id) -> limit(1) -> query() -> fetchColumn();
                if($top_middle_id)
                {
                    //Insert main menu
                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'ynaffiliate.main-menu',
                        'page_id' => $page_id,
                        'parent_content_id' => $top_middle_id,
                        'order' => 1,
                    ));
                }
            }
        }
    }

    protected function _addCommissionRulePage() {
        $db = $this->getDb();

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynaffiliate_commission-rule_index')
            ->limit(1)
            ->query()
            ->fetchColumn();

        if(!$page_id)
        {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynaffiliate_commission-rule_index',
                'displayname' => 'YN - Affiliate Commission Rule Page',
                'title' => 'Affiliate CommissionRule Page',
                'description' => 'Affiliate Commission Rule Page',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            //Insert main menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynaffiliate.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
        else
        {
            if(!$db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynaffiliate.main-menu') -> where('type = ?', 'widget') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn())
            {
                $top_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'top') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
                $top_middle_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'middle') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> where('parent_content_id = ?', $top_id) -> limit(1) -> query() -> fetchColumn();
                if($top_middle_id)
                {
                    //Insert main menu
                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'ynaffiliate.main-menu',
                        'page_id' => $page_id,
                        'parent_content_id' => $top_middle_id,
                        'order' => 1,
                    ));
                }
            }
        }
    }

    protected function _migrateData() {

        // do nothing if refresh or install new or upgrade from 403 or above
        if (!$this->currentVersion || $this->currentVersion >= 403) {
            return;
        }

        // check for columns existence
        $db = $this -> getDb();
        $keptProfiletypeId = 0;
        $deleteRuleMapIds = array();
        // process rulemaps table
        try {
            $info = $db -> describeTable('engine4_ynaffiliate_rulemaps');
            if ($info && !isset($info['level_id'])) {
                $sql = "ALTER TABLE `engine4_ynaffiliate_rulemaps` ADD COLUMN `level_id` int(11) unsigned NOT NULL";
                try
                {
                    $db -> query($sql);
                }
                catch( Exception $e ) {
                }

                $sql = "ALTER TABLE `engine4_ynaffiliate_rulemaps` DROP INDEX `rule_id_profiletype_id`, ADD UNIQUE KEY `rule_id_level_id` (`rule_id`,`level_id`)";
                try
                {
                    $db -> query($sql);
                }
                catch( Exception $e ) {
                }
            }

            if ($info && isset($info['profiletype_id'])) {
                // get first profiletype_id that it's rulemaps will be kept
                $select = new Zend_Db_Select($db);
                $select -> from('engine4_ynaffiliate_rulemaps') -> order('profiletype_id ASC') -> limit(1);
                $keptProfiletypeId = $select -> query() -> fetchObject() -> profiletype_id;
            }
        }
        catch( Exception $e ) {
        }

        if ($keptProfiletypeId) {
            // get rule map ids that are not kept
            $select = new Zend_Db_Select($db);
            $select -> from('engine4_ynaffiliate_rulemaps') -> where('profiletype_id != ?', $keptProfiletypeId);
            $deleteRuleMapIds = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

            // delete rule maps
            $db -> delete('engine4_ynaffiliate_rulemaps', array("profiletype_id != $keptProfiletypeId"));
        }


        // drop column
        $info = $db -> describeTable('engine4_ynaffiliate_rulemaps');
        if ($info && !isset($info['profiletype_id'])) {
            $sql = "ALTER TABLE `engine4_ynaffiliate_rulemaps` DROP COLUMN `profiletype_id`";
            try {
                $db->query($sql);
            } catch (Exception $e) {
            }
        }

        // get level_id
        $select = new Zend_Db_Select($db);
        $select -> from('engine4_authorization_levels') -> where('type = ?', 'user') -> order('level_id ASC') -> limit(1);
        $info = $select -> query() -> fetch();
        if (empty($info)) {
            $select -> from('engine4_authorization_levels') -> where('type != ?', 'public') -> order('level_id ASC') -> limit(1);
        }
        $firstLevelId = $select -> query() -> fetchObject() -> level_id;

        if ($firstLevelId) {
            // assign level_id
            $sql = "UPDATE `engine4_ynaffiliate_rulemaps` SET `level_id` = $firstLevelId";
            try
            {
                $db -> query($sql);
            }
            catch( Exception $e )
            {
            }
        }

        // process rule map details
        // delete first purchase and unused rule
        try {
            $info = $db -> describeTable('engine4_ynaffiliate_rulemapdetails');
            if ($info && isset($info['option_id'])) {
                $db -> delete('engine4_ynaffiliate_rulemapdetails', array('option_id = ?' => 0));

                $sql = "ALTER TABLE `engine4_ynaffiliate_rulemapdetails` DROP COLUMN `option_id`";
                $db = $this -> getDb();
                try {
                    $db -> query($sql);
                }
                catch (Exception $e)
                {
                }
            }
        }
        catch( Exception $e ) {
        }

        foreach ($deleteRuleMapIds as $deleteRuleMapId) {
            $db -> delete('engine4_ynaffiliate_rulemapdetails', array('rule_map = ?' => $deleteRuleMapId));
        }
        // process rulemapdetails table
        try {
            $info = $db -> describeTable('engine4_ynaffiliate_rulemapdetails');
            if ($info && !isset($info['level'])) {
                $sql = "ALTER TABLE `engine4_ynaffiliate_rulemapdetails` ADD COLUMN `level` int(11) unsigned NOT NULL";
                try
                {
                    $db -> query($sql);
                }
                catch( Exception $e )
                {
                }

                $sql = "ALTER TABLE `engine4_ynaffiliate_rulemapdetailss` ADD UNIQUE KEY `rule_map_level` (`rule_map`,`level`)";
                try
                {
                    $db -> query($sql);
                }
                catch( Exception $e )
                {
                }
            }
        }
        catch( Exception $e )
        {
        }

        $sql = "UPDATE `engine4_ynaffiliate_rulemapdetails` SET `level` = 1";
        try
        {
            $db -> query($sql);
        }
        catch( Exception $e )
        {
        }
    }

    public function getYnaffiliateVersion()
    {
        $db = $this -> getDb();
        $select = new Zend_Db_Select($db);
        $select -> from('engine4_core_modules') -> where('name = ?', 'ynaffiliate') -> limit(1);
        $check = $select -> query() -> fetch();
        if (empty($check))
        {
            return 0;
        }
        else
        {
            $version = str_replace('.', '', $check['version']);
            $version = (int) substr($version, 0, 3);
            return $version;
        }
    }
}
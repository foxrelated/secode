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
class Mcard_Plugin_Sitemobile {

    protected $_pagesTable;
    protected $_contentTable;

    public function onIntegrated() {

        $this->_pagesTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
        $this->_contentTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
        //Mcard profile content widget
        $this->addMcardProfileContent();
    }

    public function addMcardProfileContent() {
        // install content areas

        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('user_profile_index');

        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
                ->from($this->_contentTable)
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'user_profile_index')
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

            // tab_id (tab container) may not always be there
            $select
                    ->reset('where')
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'sitemobile.container-tabs-columns')
                    ->where('page_id = ?', $page_id)
                    ->limit(1);
            $tab_id = $select->query()->fetchObject();
            if ($tab_id && @$tab_id->content_id) {
                $tab_id = $tab_id->content_id;
            } else {
                $tab_id = null;
            }

            // tab on profile
            $db->insert($this->_contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'mcard.print-card',
                'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
                'order' => 1950,
                'params' => '{"title":"Membership Card","titleCount":true}',
            ));
        }
    }

}
<?php

/* * 
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FriendsController.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Nestedcomment_FriendsController extends Core_Controller_Action_User {

    public function init() {
        // Try to set subject
        $user_id = $this->_getParam('user_id', null);
        if ($user_id && !Engine_Api::_()->core()->hasSubject()) {
            $user = Engine_Api::_()->getItem('user', $user_id);
            if ($user) {
                Engine_Api::_()->core()->setSubject($user);
            }
        }
    }

    public function suggestTagAction() {
        $subject_guid = $this->_getParam('subject', null);
        $enableContent = explode(",", $this->_getParam('taggingContent', null));
        $limit = (int) $this->_getParam('limit', 10);
        $viewer = Engine_Api::_()->user()->getViewer();

        $nestedcomment_friendssuggesttag = Zend_Registry::isRegistered('nestedcomment_friendssuggesttag') ? Zend_Registry::get('nestedcomment_friendssuggesttag') : null;
        if (empty($nestedcomment_friendssuggesttag))
            return;

        if ($subject_guid && (stripos($subject_guid, 'event') !== false || stripos($subject_guid, 'group') !== false)) {
            $subject = Engine_Api::_()->getItemByGuid($subject_guid);
        } else {
            $subject = $viewer;
        }
        if (!$viewer->getIdentity()) {
            $data = null;
        } else {
            $data = array();
//$enableContent = array('friends');
            if (in_array('friends', $enableContent)) {
                $table = Engine_Api::_()->getItemTable('user');
                $select = $subject->membership()->getMembersObjectSelect();

                if ($this->_getParam('includeSelf', false) && stripos($viewer->getTitle(), $this->_getParam('search', $this->_getParam('value'))) !== false) {
                    $data[] = array(
                        'type' => 'user',
                        'id' => $viewer->getIdentity(),
                        'guid' => $viewer->getGuid(),
                        'label' => $viewer->getTitle() . ' ' . $this->view->translate('(you)'),
                        'photo' => $this->view->itemPhoto($viewer, 'thumb.icon'),
                        'url' => $viewer->getHref(),
                    );
                }

                if (0 < ($limit = (int) $this->_getParam('limit', 10))) {
                    $select->limit($limit);
                }

                if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) {
                    $select->where('`' . $table->info('name') . '`.`displayname` LIKE ?', '%' . $text . '%');
                }
                $select->where('`' . $table->info('name') . '`.`user_id` <> ?', $viewer->getIdentity());
                $select->order("{$table->info('name')}.displayname ASC");
                $ids = array();
                foreach ($select->getTable()->fetchAll($select) as $friend) {
                    $data[] = array(
                        'type' => 'user',
                        'id' => $friend->getIdentity(),
                        'guid' => $friend->getGuid(),
                        'label' => $friend->getTitle(),
                        'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
                        'url' => $friend->getHref(),
                    );
                    $ids[] = $friend->getIdentity();
                    $friend_data[$friend->getIdentity()] = $friend->getTitle();
                }
            }

            if (in_array('sitepage', $enableContent) && Engine_Api::_()->hasItemType('sitepage_page')) {
                $remaningLimit = $limit - @count($data);
                if ($remaningLimit > 0) {
                    $table = Engine_Api::_()->getItemTable('sitepage_page');
                    $tableName = $table->info('name');
                    $select = $table->getPagesSelectSql(array('limit' => $remaningLimit));
                    // $select = $table->getPagesSelectSql();
                    if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) {
                        $select->where('`' . $table->info('name') . '`.`title` LIKE ?', '%' . $text . '%');
                    }
                    $select->order("{$tableName}.title ASC");
                    foreach ($select->getTable()->fetchAll($select) as $page) {
                        $data[] = array(
                            'type' => $page->getShortType(true),
                            'id' => $page->getIdentity(),
                            'guid' => $page->getGuid(),
                            'label' => $page->getTitle(),
                            'photo' => $this->view->itemPhoto($page, 'thumb.icon'),
                            'url' => $page->getHref(),
                        );
                        $ids[] = $page->getIdentity();
                    }
                }
            }

            if (in_array('sitebusiness', $enableContent) && Engine_Api::_()->hasItemType('sitebusiness_business')) {
                $remaningLimit = $limit - @count($data);
                if ($remaningLimit > 0) {
                    $table = Engine_Api::_()->getItemTable('sitebusiness_business');
                    $tableName = $table->info('name');
                    $select = $table->getBusinessesSelectSql(array('limit' => $remaningLimit));
                    if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) {
                        $select->where('`' . $table->info('name') . '`.`title` LIKE ?', '%' . $text . '%');
                    }
                    $select->order("{$tableName}.title ASC");
                    foreach ($select->getTable()->fetchAll($select) as $business) {
                        $data[] = array(
                            'type' => $business->getShortType(true),
                            'id' => $business->getIdentity(),
                            'guid' => $business->getGuid(),
                            'label' => $business->getTitle(),
                            'photo' => $this->view->itemPhoto($business, 'thumb.icon'),
                            'url' => $business->getHref(),
                        );
                        $ids[] = $business->getIdentity();
                    }
                }
            }

            if (in_array('sitegroup', $enableContent) && Engine_Api::_()->hasItemType('sitegroup_group')) {
                $remaningLimit = $limit - @count($data);
                if ($remaningLimit > 0) {
                    $table = Engine_Api::_()->getItemTable('sitegroup_group');
                    $tableName = $table->info('name');
                    $select = $table->getGroupsSelectSql(array('limit' => $remaningLimit));
                    if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) {
                        $select->where('`' . $table->info('name') . '`.`title` LIKE ?', '%' . $text . '%');
                    }
                    $select->order("{$tableName}.title ASC");
                    foreach ($select->getTable()->fetchAll($select) as $group) {
                        $data[] = array(
                            'type' => $group->getShortType(true),
                            'id' => $group->getIdentity(),
                            'guid' => $group->getGuid(),
                            'label' => $group->getTitle(),
                            'photo' => $this->view->itemPhoto($group, 'thumb.icon'),
                            'url' => $group->getHref(),
                        );
                        $ids[] = $group->getIdentity();
                    }
                }
            }

            if (in_array('sitestore', $enableContent) && Engine_Api::_()->hasItemType('sitestore_store')) {
                $remaningLimit = $limit - @count($data);
                if ($remaningLimit > 0) {
                    $table = Engine_Api::_()->getItemTable('sitestore_store');
                    $tableName = $table->info('name');
                    $select = $table->getStoresSelectSql(array('limit' => $remaningLimit));
                    if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) {
                        $select->where('`' . $table->info('name') . '`.`title` LIKE ?', '%' . $text . '%');
                    }
                    $select->order("{$tableName}.title ASC");
                    foreach ($select->getTable()->fetchAll($select) as $store) {
                        $data[] = array(
                            'type' => $store->getShortType(true),
                            'id' => $store->getIdentity(),
                            'guid' => $store->getGuid(),
                            'label' => $store->getTitle(),
                            'photo' => $this->view->itemPhoto($store, 'thumb.icon'),
                            'url' => $store->getHref(),
                        );
                        $ids[] = $store->getIdentity();
                    }
                }
            }

            if (in_array('list', $enableContent) && Engine_Api::_()->hasItemType('list_listing')) {
                $remaningLimit = $limit - @count($data);
                if ($remaningLimit > 0) {
                    $table = Engine_Api::_()->getItemTable('list_listing');
                    $tableName = $table->info('name');
                    $select = $table->getListingSelectSql(array('limit' => $remaningLimit));
                    if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) {
                        $select->where('`' . $table->info('name') . '`.`title` LIKE ?', '%' . $text . '%');
                    }
                    $select->order("{$tableName}.title ASC");
                    foreach ($select->getTable()->fetchAll($select) as $list) {
                        $data[] = array(
                            'type' => $list->getShortType(true),
                            'id' => $list->getIdentity(),
                            'guid' => $list->getGuid(),
                            'label' => $list->getTitle(),
                            'photo' => $this->view->itemPhoto($list, 'thumb.icon'),
                            'url' => $list->getHref(),
                        );
                        $ids[] = $list->getIdentity();
                    }
                }
            }

            if (in_array('recipe', $enableContent) && Engine_Api::_()->hasItemType('recipe')) {
                $remaningLimit = $limit - @count($data);
                if ($remaningLimit > 0) {
                    $table = Engine_Api::_()->getItemTable('recipe');
                    $tableName = $table->info('name');
                    $select = $table->getRecipeSelectSql(array('limit' => $remaningLimit));
                    if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) {
                        $select->where('`' . $table->info('name') . '`.`title` LIKE ?', '%' . $text . '%');
                    }
                    $select->order("{$tableName}.title ASC");
                    foreach ($select->getTable()->fetchAll($select) as $recipe) {
                        $data[] = array(
                            'type' => $recipe->getShortType(true),
                            'id' => $recipe->getIdentity(),
                            'guid' => $recipe->getGuid(),
                            'label' => $recipe->getTitle(),
                            'photo' => $this->view->itemPhoto($recipe, 'thumb.icon'),
                            'url' => $recipe->getHref(),
                        );
                        $ids[] = $recipe->getIdentity();
                    }
                }
            }

            if (in_array('group', $enableContent) && Engine_Api::_()->hasItemType('group')) {
                $remaningLimit = $limit - @count($data);
                if ($remaningLimit > 0) {
                    $table = Engine_Api::_()->getItemTable('group');
                    $tableName = $table->info('name');
                    $select = $table->select();
                    $select->where('search = ?', (bool) 1);
                    if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) {
                        $select->where('`' . $table->info('name') . '`.`title` LIKE ?', '%' . $text . '%');
                    }
                    $select->order("{$tableName}.title ASC");
                    foreach ($select->getTable()->fetchAll($select) as $group) {
                        $data[] = array(
                            'type' => $group->getShortType(true),
                            'id' => $group->getIdentity(),
                            'guid' => $group->getGuid(),
                            'label' => $group->getTitle(),
                            'photo' => $this->view->itemPhoto($group, 'thumb.icon'),
                            'url' => $group->getHref(),
                        );
                        $ids[] = $group->getIdentity();
                    }
                }
            }
            if (in_array('event', $enableContent) && Engine_Api::_()->hasItemType('event')) {
                $remaningLimit = $limit - @count($data);
                if ($remaningLimit > 0) {
                    $table = Engine_Api::_()->getItemTable('event');
                    $tableName = $table->info('name');
                    $select = $table->select();
                    $select->where('search = ?', (bool) 1);
                    $select->where("endtime > FROM_UNIXTIME(?)", time());
                    if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) {
                        $select->where('`' . $table->info('name') . '`.`title` LIKE ?', '%' . $text . '%');
                    }
                    $select->order("{$tableName}.title ASC");
                    foreach ($select->getTable()->fetchAll($select) as $event) {
                        $data[] = array(
                            'type' => $event->getShortType(true),
                            'id' => $event->getIdentity(),
                            'guid' => $event->getGuid(),
                            'label' => $event->getTitle(),
                            'photo' => $this->view->itemPhoto($event, 'thumb.icon'),
                            'url' => $event->getHref(),
                        );
                        $ids[] = $event->getIdentity();
                    }
                }
            }

            if (in_array('siteevent', $enableContent) && Engine_Api::_()->hasItemType('siteevent_event')) {
                $remaningLimit = $limit - @count($data);
                if ($remaningLimit > 0) {
                    $table = Engine_Api::_()->getItemTable('siteevent_event');
                    $tableName = $table->info('name');
                    $select = $table->getEventsSelectSql(array('limit' => $remaningLimit));
                    if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) {
                        $select->where('`' . $table->info('name') . '`.`title` LIKE ?', '%' . $text . '%');
                    }
                    $select->order("{$tableName}.title ASC");
                    foreach ($select->getTable()->fetchAll($select) as $event) {
                        $data[] = array(
                            'type' => $event->getShortType(true),
                            'id' => $event->getIdentity(),
                            'guid' => $event->getGuid(),
                            'label' => $event->getTitle(),
                            'photo' => $this->view->itemPhoto($event, 'thumb.icon'),
                            'url' => $event->getHref(),
                        );
                        $ids[] = $event->getIdentity();
                    }
                }
            }
        }
        if ($this->_getParam('sendNow', true)) {
            return $this->_helper->json($data);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
            $data = Zend_Json::encode($data);
            $this->getResponse()->setBody($data);
        }
    }

}

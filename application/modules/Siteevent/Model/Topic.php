<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topic.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_Topic extends Core_Model_Item_Abstract {

    protected $_parent_type = 'siteevent_event';
    protected $_owner_type = 'user';
    protected $_children_types = array('siteevent_post');

    /**
     * Gets an absolute URL to the page to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {



        //$tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);

        $params = array_merge(array(
            'route' => "siteevent_extended",
            'controller' => 'topic',
            'action' => 'view',
            'event_id' => $this->event_id,
            'topic_id' => $this->getIdentity()
                //'tab' => $tab_id
                ), $params);
        $route = $params['route'];
				$reset = false;
				if(isset($params['reset']))
					$reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);


        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    public function getDescription() {
        if (!isset($this->store()->firstPost) && $this->getIdentity() && $this->event_id) {
            $postTable = Engine_Api::_()->getDbtable('posts', 'siteevent');
            $postSelect = $postTable->select()
                    ->where('topic_id = ?', $this->getIdentity())
                    ->where('event_id = ?', $this->event_id)
                    ->order('post_id ASC')
                    ->limit(1);
            $this->store()->firstPost = $postTable->fetchRow($postSelect);
        }
        if (isset($this->store()->firstPost)) {
            // strip HTML and BBcode
            $content = $this->store()->firstPost->body;
            $content = strip_tags($content);
            $content = preg_replace('|[[\/\!]*?[^\[\]]*?]|si', '', $content);
            return $content;
        }
        return '';
    }

    public function getFirstPost() {

        $table = Engine_Api::_()->getDbtable('posts', 'siteevent');
        $select = $table->select()
                ->where('topic_id = ?', $this->getIdentity())
                ->order('post_id ASC')
                ->limit(1);
        return $table->fetchRow($select);
    }

    public function getLastPost() {

        $table = Engine_Api::_()->getItemTable('siteevent_post');
        $select = $table->select()
                ->where('topic_id = ?', $this->getIdentity())
                ->order('post_id DESC')
                ->limit(1);

        return $table->fetchRow($select);
    }

    public function getAuthorizationItem() {

        return $this->getParent('siteevent_event');
    }

    protected function _insert() {
        if ($this->_disableHooks)
            return;

        if (!$this->event_id) {
            throw new Exception('Cannot create topic without event_id');
        }

        parent::_insert();
    }

    protected function _delete() {

        if ($this->_disableHooks)
            return;

        //DELETE ALL CHIELD POST
        $postTable = Engine_Api::_()->getItemTable('siteevent_post');
        $postSelect = $postTable->select()->where('topic_id = ?', $this->getIdentity());
        foreach ($postTable->fetchAll($postSelect) as $siteeventPost) {
            $siteeventPost->disableHooks()->delete();
        }

        parent::_delete();
    }

    public function canEdit() {
        $viewer = Engine_Api::_()->user()->getViewer();
        return $this->getParent()->authorization()->isAllowed($viewer, 'edit') || $this->getParent()->authorization()->isAllowed($viewer, 'topic.edit') || $this->isOwner($viewer);
    }

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     * */
    public function comments() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function likes() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

}
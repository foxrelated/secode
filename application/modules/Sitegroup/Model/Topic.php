<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topic.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Model_Topic extends Core_Model_Item_Abstract {

  protected $_parent_type = 'sitegroup_group';
  protected $_owner_type = 'user';
  protected $_children_types = array('sitegroup_post');

  /**
   * Gets an absolute URL to the album to view this item
   *
   * @param array $params 
   * @return string
   */
  public function getHref($params = array()) {
    $tab_id='';
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
		if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$tab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.sitemobile-discussion-sitegroup', $this->group_id, $layout);
		} else {
			$tab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.discussion-sitegroup', $this->group_id, $layout);
		}
    $params = array_merge(array(
        'route' => 'sitegroup_extended',
        'controller' => 'topic',
        'action' => 'view',
        'group_id' => $this->group_id,
        'topic_id' => $this->getIdentity(),
        'tab' => $tab_id,
            ), $params);
    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  /**
   * Gets description
   *
   * @return description
   * */
  public function getDescription()
  {
    if( !isset($this->store()->firstPost) && $this->getIdentity() && $this->group_id) {
      $postTable = Engine_Api::_()->getDbtable('posts', 'sitegroup');
      $postSelect = $postTable->select()
        ->where('topic_id = ?', $this->getIdentity())
        ->where('group_id = ?', $this->group_id)
        ->order('post_id ASC')
        ->limit(1);
      $this->store()->firstPost = $postTable->fetchRow($postSelect);
    }
    if( isset($this->store()->firstPost) ) {
      // strip HTML and BBcode
      $length = 200;
      $content = $this->store()->firstPost->body;
      $content = strip_tags($content);
      $content = preg_replace('|[[\/\!]*?[^\[\]]*?]|si', '', $content);
      return Engine_String::strlen($content) > $length ? Engine_String::substr($content, 0, ($length - 3)) . '...' : $content;
    }
    return '';
  }

  /**
   * Gets sitegroup item
   *
   * @return sitegroup item
   * */
  public function getParentSitegroup() {

    return Engine_Api::_()->getItem('sitegroup_group', $this->group_id);
  }

  public function getResource() {
    if ($this->resource_type && $this->resource_id)
      return Engine_Api::_()->getItem($this->resource_type, $this->resource_id);
  }

  /**
   * Gets first post
   *
   * @return first post
   * */
  public function getFirstPost() {

    return Engine_Api::_()->getDbtable('posts', 'sitegroup')->fetchRow(Engine_Api::_()->getDbtable('posts', 'sitegroup')->select()
                            ->where('topic_id = ?', $this->getIdentity())
                            ->order('post_id ASC')
                            ->limit(1));
  }

  /**
   * Gets last post
   *
   * @return last post
   * */
  public function getLastPost() {

    return Engine_Api::_()->getItemTable('sitegroup_post')->fetchRow(Engine_Api::_()->getItemTable('sitegroup_post')->select()
                            ->where('topic_id = ?', $this->getIdentity())
                            ->order('post_id DESC')
                            ->limit(1));
  }

  /**
   * Gets last poster information
   *
   * @return last poster item
   * */
  public function getLastPoster() {

    return Engine_Api::_()->getItem('user', $this->lastposter_id);
  }

  /**
   * Inserts topic
   * */
  protected function _insert() {

    if ($this->_disableHooks)
      return;

    if (!$this->group_id) {
      $error_msg = Zend_Registry::get('Zend_Translate')->_('Cannot create topic without group_id');
      throw new Exception($error_msg);
    }

    parent::_insert();
  }

  /**
   * Delete posts
   * */
  protected function _delete() {

    if ($this->_disableHooks)
      return;

    $postTable = Engine_Api::_()->getItemTable('sitegroup_post');
    $postSelect = $postTable->select()->where('topic_id = ?', $this->getIdentity());
    foreach ($postTable->fetchAll($postSelect) as $sitegroupPost) {
      $sitegroupPost->disableHooks()->delete();
    }

    // Delete create activity feed of note before delete note 
    Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteCreateActivityOfExtensionsItem($this, array('sitegroup_topic_create', 'sitegroup_admin_topic_create'));

    parent::_delete();
  }

  public function getBody() {
    if( !isset($this->store()->firstPost) ) {
      $postTable = Engine_Api::_()->getDbtable('posts', 'sitegroup');
      $postSelect = $postTable->select()
        ->where('topic_id = ?', $this->getIdentity())
        ->where('group_id = ?', $this->group_id)
        ->order('post_id ASC')
       ->limit(1);
     $this->store()->firstPost = $postTable->fetchRow($postSelect);
   }
    if( isset($this->store()->firstPost) ) {
      // strip HTML and BBcode
      $length = 200;
      $content = $this->store()->firstPost->body;
      return Engine_String::strlen($content) > $length ? Engine_String::substr($content, 0, ($length - 3)) . '...' : $content;
    }
    return '';
  }

}

?>
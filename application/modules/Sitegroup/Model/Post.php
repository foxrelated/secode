<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Post.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Model_Post extends Core_Model_Item_Abstract {

  protected $_parent_type = 'sitegroup_topic';
  protected $_owner_type = 'user';

  /**
   * Gets an absolute URL to the album to view this item
   *
   * @param array $params 
   * @return string
   */  
  public function getHref($params = array()) {

    $params = array_merge(array(
        'route' => 'sitegroup_extended',
        'controller' => 'topic',
        'action' => 'view',
        'group_id' => $this->group_id,
        'topic_id' => $this->getParentTopic()->getIdentity(),
        'post_id' => $this->getIdentity(),
        'tab' => Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.discussion-sitegroup', $this->group_id, Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0)),
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
  public function getDescription() {
    // strip HTML and BBcode
    $content = strip_tags($this->body);
    $content = preg_replace('|[[\/\!]*?[^\[\]]*?]|si', '', $content);
    return $content;
  }

  /**
   * Gets count
   *
   * @return how many post ids related to topic
   * */  
  public function getPostIndex() {

    $table = $this->getTable();
    $select = new Zend_Db_Select($table->getAdapter());
    $select
            ->from($table->info('name'), new Zend_Db_Expr('COUNT(post_id) as count'))
            ->where('topic_id = ?', $this->topic_id)
            ->where('post_id < ?', $this->getIdentity())
            ->order('post_id ASC');
    $data = $select->query()->fetch();

    return (int) $data['count'];
  }

  /**
   * Gets sitegroup item
   *
   * @return sitegroup item
   * */    
  public function getParentsitegroup() {

    return Engine_Api::_()->getItem('sitegroup_group', $this->group_id);
  }

  /**
   * Gets discussion item
   *
   * @return discussion item
   * */ 
  public function getParentTopic() {

    return Engine_Api::_()->getItem('sitegroup_topic', $this->topic_id);
  }

  /**
   * Inserts topic in 'engine4_sitegroupdiscussion_topics table'
   *
   * @return inserted topic
   * */  
  protected function _insert() {

    if ($this->_disableHooks)
      return;

    if (!$this->group_id) {
      $errormsg1 = Zend_Registry::get('Zend_Translate')->_('Cannot create post without group_id');
      throw new Exception($errormsg1);
    }

    if (!$this->topic_id) {
      $errormsg2 = Zend_Registry::get('Zend_Translate')->_('Cannot create post without topic_id');
      throw new Exception($errormsg2);
    }

    $table = Engine_Api::_()->getDbtable('topics', 'sitegroup');
    $select = $table->select()->where('topic_id = ?', $this->topic_id)->limit(1);
    $topic = $table->fetchRow($select);
    $topic->lastpost_id = $this->post_id;
    $topic->lastposter_id = $this->user_id;
    $topic->modified_date = date('Y-m-d H:i:s');
    $topic->post_count++;
    $topic->save();
    parent::_insert();
  }

  /**
   * Delete topics
   * */  
  protected function _delete() {

    if ($this->_disableHooks)
      return;

    $topic = Engine_Api::_()->getDbtable('topics', 'sitegroup')->fetchRow(Engine_Api::_()->getDbtable('topics', 'sitegroup')->select()->where('topic_id = ?', $this->topic_id)->limit(1));
    $topic->post_count--;
    if ($topic->post_count == 0) {
      $topic->delete();
    } else {
      $topic->save();
    }
    // Delete create activity feed of note before delete note
		Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteCreateActivityOfExtensionsItem($this, array('sitegroup_topic_reply', 'sitegroup_admin_topic_reply'));

    parent::_delete();
  }

}

?>
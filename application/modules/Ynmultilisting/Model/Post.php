<?php
class Ynmultilisting_Model_Post extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;
  protected $_parent_type = 'ynmultilisting_topic';
  protected $_owner_type = 'user';
  
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'ynmultilisting_extended',
      'controller' => 'topic',
      'action' => 'view',
      'listing_id' => $this->listing_id,
      'topic_id' => $this->getParentTopic()->getIdentity(),
      'post_id' => $this->getIdentity(),
    ), $params);
    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  public function getDescription()
  {
    // Remove bbcode
    $desc = strip_tags($this->body);
    $desc = preg_replace('/\[[^\[\]]+?\]/', '',$desc);
    return Engine_String::substr($desc, 0, 255);
  }

  public function getPostIndex()
  {
    $table = $this->getTable();
    $select = new Zend_Db_Select($table->getAdapter());
    $select
      ->from($table->info('name'), new Zend_Db_Expr('COUNT(post_id) as count'))
      ->where('topic_id = ?', $this->topic_id)
      ->where('post_id < ?', $this->getIdentity())
      ->order('post_id ASC')
      ;

    $data = $select->query()->fetch();
    
    return (int) $data['count'];
  }

  public function getParentListing()
  {
    return Engine_Api::_()->getItem('ynmultilisting_listing', $this->listing_id);
  }

  public function getParentTopic()
  {
    return Engine_Api::_()->getItem('ynmultilisting_topic', $this->topic_id);
  }

  public function getPoster()
  {
    return Engine_Api::_()->getItem('user', $this->user_id);
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('ynmultilisting_listing');
  }

  // Internal hooks

  protected function _insert()
  {
    if( $this->_disableHooks ) return;
    
    if( !$this->listing_id ) {
      throw new Exception('Cannot create post without listing_id');
    }
    
    if( !$this->topic_id ) {
      throw new Exception('Cannot create post without topic_id');
    }
    
    parent::_insert();
  }

  protected function _postInsert()
  {
    if( $this->_disableHooks ) return;
    
    // Update topic
    $table = Engine_Api::_()->getDbtable('topics', 'ynmultilisting');
    $select = $table->select()->where('topic_id = ?', $this->topic_id)->limit(1);
    $topic = $table->fetchRow($select);

    $topic->lastpost_id = $this->post_id;
    $topic->lastposter_id = $this->user_id;
    $topic->modified_date = date('Y-m-d H:i:s');
    $topic->post_count++;
    $topic->save();

    parent::_postInsert();
  }

  protected function _delete()
  {
    if( $this->_disableHooks ) return;
    
    // Update topic
    $table = Engine_Api::_()->getDbtable('topics', 'ynmultilisting');
    $select = $table->select()->where('topic_id = ?', $this->topic_id)->limit(1);
    $topic = $table->fetchRow($select);
    $topic->post_count--;

    if( $topic->post_count == 0 ) {
      $topic->delete();
    } else {
      $topic->save();
    }
    parent::_delete();
  }
}
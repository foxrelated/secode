<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Post.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Model_Post extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'list_topic';

  protected $_owner_type = 'user';
  
  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
	public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'list_extended',
      'controller' => 'topic',
      'action' => 'view',
      'listing_id' => $this->listing_id,
      'topic_id' => Engine_Api::_()->getItem('list_topic', $this->topic_id)->getIdentity(),
      'post_id' => $this->getIdentity(),
    ), $params);
    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  public function getDescription()
  {
    //REMOVE BBCODE
    $desc = preg_replace('/\[[^\[\]]+?\]/', '', $this->body);
    return Engine_String::substr($desc, 0, 255);
  }

  public function getPostIndex()
  {
    $table = Engine_Api::_()->getDbTable('posts', 'list');
    $select = $table->select()
      ->from($table->info('name'), new Zend_Db_Expr('COUNT(post_id) as count'))
      ->where('topic_id = ?', $this->topic_id)
      ->where('post_id < ?', $this->getIdentity())
      ->order('post_id ASC');

    $data = $select->query()->fetch();
    
    return (int) $data['count'];
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('list_listing');
  }

  //INTERNAL HOOKS
  protected function _insert()
  {
    if( $this->_disableHooks ) return;
    
    if( !$this->listing_id )
    {
      throw new Exception('Cannot create post without listing_id');
    }
    
    if( !$this->topic_id )
    {
      throw new Exception('Cannot create post without topic_id');
    }

    //UPDATE TOPIC
    $table = Engine_Api::_()->getDbtable('topics', 'list');
    $select = $table->select()->where('topic_id = ?', $this->topic_id)->limit(1);
    $topic = $table->fetchRow($select);

    $topic->lastpost_id = $this->post_id;
    $topic->lastposter_id = $this->user_id;
    $topic->modified_date = date('Y-m-d H:i:s');
    $topic->post_count++;
    $topic->save();
    
    parent::_insert();
  }

  protected function _delete()
  {
    if( $this->_disableHooks ) return;
    
    //UPDATE TOPIC
    $table = Engine_Api::_()->getDbtable('topics', 'list');
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
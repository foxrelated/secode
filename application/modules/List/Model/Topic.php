<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Topic.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Model_Topic extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'list_listing';
  protected $_owner_type = 'user';
  protected $_children_types = array('list_post');
  
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
      'topic_id' => $this->getIdentity(),
    ), $params);
    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  public function getDescription()
  {
    $firstPost = $this->getFirstPost();
    return ( null != $firstPost ? Engine_String::substr($firstPost->body, 0, 255) : '' );
  }

  public function getFirstPost()
  {
    $table = Engine_Api::_()->getDbtable('posts', 'list');
    $select = $table->select()
										->where('topic_id = ?', $this->getIdentity())
										->order('post_id ASC')
										->limit(1);
    return $table->fetchRow($select);
  }

  public function getLastPost()
  {
    $table = Engine_Api::_()->getItemTable('list_post');
    $select = $table->select()
										->where('topic_id = ?', $this->getIdentity())
										->order('post_id DESC')
										->limit(1);

    return $table->fetchRow($select);
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('list_listing');
  }

  protected function _insert()
  {
    if( $this->_disableHooks ) return;
    
    if( !$this->listing_id )
    {
      throw new Exception('Cannot create topic without listing_id');
    }

    parent::_insert();
  }

  protected function _delete()
  {
    if( $this->_disableHooks ) return;
    
    //DELETE ALL CHIELD POST
    $postTable = Engine_Api::_()->getItemTable('list_post');
    $postSelect = $postTable->select()->where('topic_id = ?', $this->getIdentity());
    foreach( $postTable->fetchAll($postSelect) as $listPost ) {
      $listPost->disableHooks()->delete();
    }
    
    parent::_delete();
  }
}
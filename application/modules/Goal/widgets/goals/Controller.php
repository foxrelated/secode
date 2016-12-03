<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */

class Goal_Widget_GoalsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    // Don't render this if not authorized
    if($this->_getParam('order_type') == 'likes'){
        $this->getElement()->setTitle('Most Liked Goals');
    }elseif($this->_getParam('order_type') == 'comments'){
        $this->getElement()->setTitle('Most Commented Goals');
    }elseif($this->_getParam('order_type') == 'views'){
        $this->getElement()->setTitle('Most Popular Goals');
    }else{
        $this->getElement()->setTitle('Most Recent Goals');
    }
    
    //tables
    $goalsTable         = Engine_Api::_()->getDbtable('goals','goal');
    $goalsTableName     = $goalsTable->info('name');
    $likeTable          = Engine_Api::_()->getDbtable('likes','core');
    $likeTableName      = $likeTable->info('name');
    $commentstTable     = Engine_Api::_()->getDbtable('comments','core');
    $commentstTableName = $commentstTable->info('name');
    
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = $goalsTable->select()
            ->setIntegrityCheck(false)
            ->from($goalsTableName)
            ;
    
  //order by like count
    if($this->_getParam('order_type') == 'likes'){
          $select->joinRight($likeTableName, "$likeTableName.resource_id = $goalsTableName.goal_id", array('like_count' => new Zend_Db_Expr('COUNT(like_id)')));
          $select->where($likeTableName.'.resource_type = ?', 'goal') ;
          $select->group('goal_id');
          $select->order('like_count DESC');
    }
    //order by comments
    elseif($this->_getParam('order_type') == 'comments'){
          $select->joinRight($commentstTableName, "$commentstTableName.resource_id = $goalsTableName.goal_id", array('comment_count' => new Zend_Db_Expr('COUNT(comment_id)')));
          $select->where($commentstTableName.'.resource_type = ?', 'goal') ;
          $select->group('goal_id');
          $select->order('comment_count DESC');
    }
    //order by view
    elseif($this->_getParam('order_type') == 'views'){
          $select->order('view_count DESC');
    }
    
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 3));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}

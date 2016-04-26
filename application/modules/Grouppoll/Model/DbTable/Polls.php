<?php

 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Polls.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_Model_DbTable_Polls extends Engine_Db_Table
{
  protected $_rowClass = 'Grouppoll_Model_Poll';

  function getGrouppollResult($params = array()) {

    $values = $params;
    $userTableName = Engine_Api::_()->getItemTable('user')->info('name');
		$groupTableName = Engine_Api::_()->getItemTable('group')->info('name');
    $pollTableName = $this->info('name');
    $select = $this->select()
    								->setIntegrityCheck(false)
										->from($pollTableName)
										->joinLeft($userTableName, "$pollTableName.owner_id = $userTableName.user_id", 'username')
										->joinLeft($groupTableName, "$pollTableName.group_id = $groupTableName.group_id", 'title AS group_title');
	
    foreach( $values as $key => $value ) {
      if ( null === $value ) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'poll_id',
      'order_direction' => 'DESC',
    ), $values);

    $select->order(( !empty($values['order']) ? $values['order'] : 'poll_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));


    //MAKE PAGINATOR
    $poll_result = array();
    $paginator = Zend_Paginator::factory($select);
		$poll_result['value'] = $values;
		$poll_result['paginator'] = $paginator;
    return $poll_result;  
  }

  /**
   * Get grouppoll list
   *
   * @param array $params
   * @return array $paginator;
   */
  public function getGrouppollsPaginator($params = array()) 
  {
    $paginator = Zend_Paginator::factory($this->getGrouppollsSelect($params));
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }
  
  /**
   * Get group poll select query
   *
   * @param array $params
   * @return string $select;
   */
  public function getGrouppollsSelect($params = array()) 
  {
    $pollTable = Engine_Api::_()->getDbtable('polls', 'grouppoll');
    $pollTableName = $pollTable->info('name');
    if ($params['orderby'] == 'views') {
      $select = $pollTable->select()
                      ->order('views DESC')
                      ->order('creation_date DESC');
    }
		elseif($params['orderby'] == 'comment_count') {
			    $select = $pollTable->select()
                          ->order('comment_count DESC')
                          ->order('creation_date DESC');
		}
		elseif($params['orderby'] == 'vote_count') {
			    $select = $pollTable->select()
                          ->order('vote_count DESC')
                          ->order('creation_date DESC');
		}
		else {
      $select = $pollTable->select()
                      ->order(!empty($params['orderby']) ? $params['orderby'] . ' DESC' : 'creation_date DESC');
    }

    $select = $select
                      ->setIntegrityCheck(false)
                      ->from($pollTableName)
                      ->group("$pollTableName.poll_id");
	
    if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
      $select->where($pollTableName . '.owner_id = ?', $params['user_id']);
    }

    if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
      $select->where($pollTableName . '.owner_id = ?', $params['user_id']->getIdentity());
    }

    if (!empty($params['users'])) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($pollTableName . '.owner_id in (?)', new Zend_Db_Expr($str));
    }

    if (isset($params['owner_id'])) {
      $select->where($pollTableName . '.owner_id = ?', $params['owner_id']);
    }

		if (isset($params['group_id'])) {
      $select->where($pollTableName . '.group_id = ?', $params['group_id']);
    }

		if (isset($params['approved'])) {
      $select->where($pollTableName . '.approved = ?', $params['approved']);
    }

    if (!empty($params['search'])) {
      $select->where($pollTableName . ".title LIKE ? OR " . $pollTableName . ".description LIKE ?", '%' . $params['search'] . '%');
    }

    if (!empty($params['visible'])) {
      $select->where($pollTableName . ".search = ?", $params['visible']);
    }

		if(!empty($params['show_poll']) && empty($params['search'])) {
			$select->where($pollTableName . ".approved = ?", 1)
						 ->orwhere($pollTableName . ".owner_id = ?", $params['poll_owner_id']);
		}

		if(!empty($params['show_poll']) && (!empty($params['search']) )) {
			$select->where("($pollTableName.approved = 1)  OR ($pollTableName.owner_id = ".$params['poll_owner_id'].")");
		}

		if (isset($params['group_id'])) {
      $select->where($pollTableName . '.group_id = ?', $params['group_id']);
    }

    return $select;
	}
  
   /**
   * Return poll data
   *
   * @param array params
   * @param string listtype
   * @return Zend_Db_Table_Select
   */
  public function getPollListing($listtype, $params = array()) {

    $total_grouppolls = $params['total_grouppolls'];
    $group_id = $params['group_id'];
    $pollTableName = $this->info('name');
    $select = $this->select()
                    ->from($pollTableName, array('poll_id', 'owner_id', 'group_id', 'title', 'views', 'comment_count', 'vote_count'))
                    ->where('group_id = ?', $group_id)
                    ->where('approved != ?', 0)
                    ->limit($total_grouppolls);

    if ($listtype == 'Most Recent') {
      $select = $select
                      ->order('poll_id DESC');
    }

    if ($listtype == 'Most Viewed') {
      $select = $select
                      ->where('views != ?', 0)
                      ->order('views DESC')
                      ->order('poll_id DESC');
    }

    if ($listtype == 'Most Voted') {
      $select = $select
                      ->where('vote_count != ?', 0)
                      ->order('vote_count DESC')
                      ->order('poll_id DESC');
    }


    if ($listtype == 'Most Commented') {
      $select = $select
                      ->where('comment_count != ?', 0)
                      ->order('comment_count DESC')
                      ->order('poll_id DESC');
    }

    if ($listtype == 'Most Liked') {

		$table_likes = Engine_Api::_()->getDbtable( 'likes', 'core' ); 
		$table_likes_name = $table_likes->info( 'name' );   
		$select = $this->select();
		$select->setIntegrityCheck(false)
						->from($pollTableName)
            ->where('approved != ?', 0)
            ->where('group_id = ?', $group_id)
						->join($table_likes_name, "$pollTableName.poll_id = $table_likes_name.resource_id   ", array('COUNT( ' . $table_likes_name . '.resource_id ) as count_likes'))
						->group($table_likes_name . '.resource_id')
						->order('count_likes DESC')
            ->order('poll_id DESC')
						->limit($total_grouppolls);	
    }
    return $this->fetchAll($select);
  }

}
?>
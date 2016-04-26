<?php

 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Poll.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_Model_Poll extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'user';

  protected $_parent_is_owner = true;

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {

		$tab_id = Engine_Api::_()->grouppoll()->getTabId();

    $params = array_merge(array(
      'route' => 'grouppoll_detail_view',
      'reset' => true,
      'user_id' => $this->owner_id,
      'poll_id' => $this->poll_id,
      'slug' => $this->getSlug(),
			'tab' => $tab_id
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  /**
   * Return a poll owner trunacte name
   *
   * @return truncate description
   * */
  public function truncateOwner($owner_name) 
  { 
    $tmpBody = strip_tags($owner_name);
    return ( Engine_String::strlen($tmpBody) > 10 ? Engine_String::substr($tmpBody, 0, 10) . '..' : $tmpBody );
  }
  
  /**
   * Return a poll group trunacte title
   *
   * @return truncate description
   * */
  public function truncateGroupTitle($group_title) 
  { 
    $tmpBody = strip_tags($group_title);
    return ( Engine_String::strlen($tmpBody) > 10 ? Engine_String::substr($tmpBody, 0, 10) . '..' : $tmpBody );
  }

	 /**
   * Make format for activity feed
   *
   * @return activity feed content
   */
public function getRichContent()
  {
    $view = Zend_Registry::get('Zend_View');
    $view = clone $view;
    $view->clearVars();
    $view->addScriptPath('application/modules/Grouppoll/views/scripts/');
    $tmpBody = $this->getDescription();
		$poll_description = Engine_String::strlen($tmpBody) > 70 ? Engine_String::substr($tmpBody, 0, 70) . '...' : $tmpBody;
    $content = '';
    $content .= '
      <div class="feed_grouppoll_rich_content">
        <div class="feed_item_link_title">
          ' . $view->htmlLink($this->getHref(), $this->getTitle()) . '
        </div>
        <div class="feed_item_link_desc">
          ' . $view->viewMore($poll_description) . '
        </div>
    ';
	
		//RENDER THE THINGY
    $view->grouppoll = $this;
    $view->owner = $owner = $this->getOwner();
    $view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();
    $view->grouppollOptions = $this->getOptions();
    $view->hasVoted = $this->viewerVoted();
    $view->showPieChart = Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.showPieChart', false);
    $view->canVote = $this->authorization()->isAllowed(null, 'vote');
    $view->canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.canchangevote', false);
    $view->hideLinks = true;

		//SET GROUP SUBJECT
		$subject = null;

		$id = $this->group_id;
		$subject = $group_subject = Engine_Api::_()->getItem('group', $id);

		$gp_auth_vote = $this->gp_auth_vote;
		if ( !$group_subject->membership()->isMember($viewer) ) {
      $group_member = 0; 
    }
    else {
      $group_member = 1;
    }

		//CHECK THAT VIEWER IS OFFICER OR NOT
		$list = $group_subject->getOfficerList();
		$listItem = $list->get($viewer);
    $isOfficer = ( null !== $listItem );

    if (($gp_auth_vote == 1 && $viewer_id != 0) || ($gp_auth_vote == 2 && $group_member == 1) || ($gp_auth_vote == 3 && $this->group_owner_id == $viewer_id) || ($gp_auth_vote == 3 && $isOfficer == 1)) {
      $can_vote = 1;
		}
    else {   
      $can_vote = 0;
    }

		if ($can_vote == 1 && $this->approved == 1) {
			$view->can_vote = 1;
		}
		else {
			$view->can_vote = 0;
		}

    $content .= $view->render('_grouppoll.tpl');

    $content .= '
      </div>
    ';
    return $content;
  }
  
  /**
   * Return poll options
   *
   * @return poll options
   * */
  public function getOptions()
  {
    return Engine_Api::_()->getDbtable('options', 'grouppoll')->fetchAll(array(
      'poll_id = ?' => $this->getIdentity(),
    ));
  }

	/**
   * Return query for user has voted or not
   *
   * @param $user:user model
   * @return Zend_Db_Table_Select
   * */
  public function hasVoted(User_Model_User $user)
  {
    $table = Engine_Api::_()->getDbtable('votes', 'grouppoll');
    return (bool) $table
      ->select()
      ->from($table, 'COUNT(*)')
      ->where('poll_id = ?', $this->getIdentity())
      ->where('owner_id = ?', $user->getIdentity())
      ->query()
      ->fetchColumn(0)
      ;
  }
  
   /**
   * Return query for getting users vote
   *
   * @param array $user:user model
   * @return Zend_Db_Table_Select
   * */
  public function getVote(User_Model_User $user)
  {
    $table = Engine_Api::_()->getDbtable('votes', 'grouppoll');
    return $table
      ->select()
      ->from($table, 'poll_option_id')
      ->where('poll_id = ?', $this->getIdentity())
      ->where('owner_id = ?', $user->getIdentity())
      ->query()
      ->fetchColumn(0)
      ;
  }

  /**
   * Return: get viewers vote
   *
   * @return get viewers vote
   * */
  public function viewerVoted()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    return $this->getVote($viewer);
  }

	/**
   * Make vote entry
   *
   * @param array $user:user model
   * @param array $option:options
   * @return Zend_Db_Table_Select
   * */
  public function vote(User_Model_User $user, $option)
  {
    $table = Engine_Api::_()->getDbTable('votes', 'grouppoll');
    $row = $table->fetchRow(array(
      'poll_id = ?' => $this->getIdentity(),
      'owner_id = ?' => $user->getIdentity(),
    ));

    if ( null === $row ) {
      $row = $table->createRow();
      $row->setFromArray(array(
        'poll_id' => $this->getIdentity(),
        'owner_id' => $user->getIdentity(),
        'creation_date' => date("Y-m-d H:i:s"),
      ));

			$this->vote_count = new Zend_Db_Expr('vote_count + 1');
			$this->save();
    }

		$previous_option_id = $row->poll_option_id;
    $row->poll_option_id = $option;
    $row->modified_date  = date("Y-m-d H:i:s");
    $row->save();

		//WE ALSO HAVE TO UPDATE THE grouppoll_options TABLE
    $optionsTable = Engine_Api::_()->getDbtable('options', 'grouppoll');
		$optionsTable->update(array(
      'votes' => new Zend_Db_Expr('votes - 1'),
    ), array(
      'poll_id = ?' => $this->getIdentity(),
      'poll_option_id = ?' => $previous_option_id,
    ));
    $optionsTable->update(array(
      'votes' => new Zend_Db_Expr('votes + 1'),
    ), array(
      'poll_id = ?' => $this->getIdentity(),
      'poll_option_id = ?' => $option,
    ));
  }
  
  /**
   * Insert global search value
   *
   *
   * */
  protected function _insert()
  {
    if( null === $this->search ) {
      $this->search = 1;
    }

    parent::_insert();
  }
  
  /**
   * Delete poll votes and options
   *
   *
   * */
  protected function _delete()
  {
    // delete grouppoll votes
    Engine_Api::_()->getDbtable('votes', 'grouppoll')->delete(array(
      'poll_id = ?' => $this->getIdentity(),
    ));

    // delete grouppoll options
    Engine_Api::_()->getDbtable('options', 'grouppoll')->delete(array(
      'poll_id = ?' => $this->getIdentity(),
    ));

    parent::_delete();
  }
  
  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }
  
  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }
}
?>

<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Hire-Experts LLC
 */
class Heevent_Widget_ProfileMembersController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('event');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    
    // Get params
    $this->view->page = $page = $this->_getParam('page', 1);
    $this->view->search = $search = $this->_getParam('search');
    $this->view->waiting = $waiting = $this->_getParam('waiting', false);

    // Prepare data
    $this->view->event = $event = Engine_Api::_()->core()->getSubject();

    $members = null;
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() && $event->isOwner($viewer) ) {
      $this->view->waitingMembers = Zend_Paginator::factory($event->membership()->getMembersSelect(false));
      if( $waiting ) {
        $this->view->members = $members = $this->view->waitingMembers;
      }
    }

    if( !$members ) {
      $select = $event->membership()->getMembersObjectSelect();
      if( $search ) {
        $select->where('displayname LIKE ?', '%' . $search . '%');
      }
      $this->view->members = $members = Zend_Paginator::factory($select);
    }
    $card =  Engine_Api::_()->getDbTable('cards', 'heevent');
    $cName = $card->info('name');
    /** @var  $userTable User_Model_DbTable_Users*/
    $userTable = Engine_Api::_()->getDbTable('users', 'user');
    $userTName = $userTable->info('name');
    $ticketSelect = $userTable->select()
      ->from($userTName)
      ->joinLeft($cName, "$userTName.user_id=$cName.user_id",array())
      ->where($cName.'.event_id = ?', $event->getIdentity())
      ->where($cName.'.state=?', 'okay')
      ->group($cName.'.user_id');
    $ticket = Zend_Paginator::factory($ticketSelect);
    if($ticket->getTotalItemCount()>0){
      $this->view->members =$paginator = $ticket;
    }else{
      $paginator = $members;
    }


    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', $page));

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 && '' == $search ) {
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
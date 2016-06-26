<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Heevent_Widget_ProfileOwnerTicketsController extends Engine_Content_Widget_Abstract
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

    }
    if($subject->getOwner()->getIdentity() != $viewer->getIdentity()){
      return $this->setNoRender();

  }
    if(!$viewer->getIdentity())
    {
      return $this->setNoRender();
    }
    $this->view->past = strtotime($subject->endtime) < time();
    $this->view->event = $subject;
    $this->view->isMember = $subject->membership()->isMember($viewer, true);


    $tblCard = Engine_Api::_()->getDbtable('cards', 'heevent');
    $Cardname = $tblCard->info('name');
    $tblevent = Engine_Api::_()->getDbtable('events', 'heevent');
    $Ename = $tblevent->info('name');
    $fetch = $tblCard->select()->from(array('c'=>$Cardname))
      ->joinLeft(array('e' => $Ename), 'e.event_id =  c.event_id', array())->where('c.state = ?','okay')->where("c.event_id = ?",$subject->getidentity() );
    $this->view->active_past = 1;
    $select = $tblCard->fetchAll($fetch);
    $cardArray = $select->toArray();
    $this->view->paginator = $paginator = Zend_Paginator::factory($cardArray);
    $paginator->setItemCountPerPage(10);

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
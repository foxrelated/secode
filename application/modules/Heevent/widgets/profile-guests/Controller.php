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

class Heevent_Widget_ProfileGuestsController extends Engine_Content_Widget_Abstract
{
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
    $this->view->waiting = $waiting = $this->_getParam('waiting', false);

    // Prepare data
    $this->view->event = $event = $subject;
    $this->view->past = strtotime($event->endtime) < time();





    $userTable = Engine_Api::_()->getDbTable('users', 'user');
    $userTName = $userTable->info('name');
    $eventMembershipTable = Engine_Api::_()->getDbTable('membership', 'event');
    $eMName = $eventMembershipTable->info('name');

    $goingSelect = $userTable->select()
       ->from($userTName)
        ->joinLeft($eMName, "$userTName.user_id=$eMName.user_id",array())
      ->where($eMName.'.resource_id = ?', $event->getIdentity())
      ->where($eMName.'.active=?', 1)
      ->where($eMName.'.rsvp = ?', 2);

    $maybeSelect = $userTable->select()
       ->from($userTName)
        ->joinLeft($eMName, "$userTName.user_id=$eMName.user_id",array())
      ->where($eMName.'.resource_id = ?', $event->getIdentity())
      ->where($eMName.'.active=?', 1)
      ->where($eMName.'.rsvp = ?', 1);

    $card =  Engine_Api::_()->getDbTable('cards', 'heevent');
    $cName = $card->info('name');
    $ticketSelect = $userTable->select()
      ->from($userTName)
      ->joinLeft($cName, "$userTName.user_id=$cName.user_id",array())
      ->where($cName.'.event_id = ?', $event->getIdentity())
      ->where($cName.'.state=?', 'okay')
      ->group($cName.'.user_id');

    //$this->view->PUser = $card->getEventsCards($event->getIdentity())->toArray();

    $this->view->going =  $going = Zend_Paginator::factory($goingSelect);

    $this->view->maybe = $maybe = Zend_Paginator::factory($maybeSelect);

    $this->view->tickets = $ticket = Zend_Paginator::factory($ticketSelect);

    // Set item count per page and current page number
    $going->setItemCountPerPage(10);

    // Set item count per page and current page number
    $maybe->setItemCountPerPage(10);

      //if(!$going->getTotalItemCount() && !$maybe->getTotalItemCount() &&  !$ticket->getTotalItemCount()) return $this->setNoRender();


  }
}
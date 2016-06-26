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

class Heevent_Widget_ProfileRsvpController extends Engine_Content_Widget_Abstract
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
    if(!$viewer->getIdentity())
    {
      return $this->setNoRender();
    }
    $this->view->past = strtotime($subject->endtime) < time();
    $this->view->event = $subject;
    $this->view->isMember = $subject->membership()->isMember($viewer, true);
//    // Must be a member
//    if( !$subject->membership()->isMember($viewer, true) )
//    {
//      return $this->setNoRender();
//    }

    // Build form
    $this->view->form = new Event_Form_Rsvp();
    $this->view->row = $row = $subject->membership()->getRow($viewer);
    $this->view->viewer_id = $viewer->getIdentity();

    if( !$row ) {
      $this->view->rsvp = -1;
    } else
      $this->view->rsvp = $row->rsvp;


    // @todo - make this work
    /*
    if( $this->getRequest()->isPost() )
    {
      $option_id = $this->getRequest()->getParam('option_id');

      $row->rsvp = $option_id;
      $row->save();
    }
    */
//  @todo - check ticket type
    $ticketsTable = Engine_Api::_()->getDbTable('tickets', 'heevent');
    $CardTable = Engine_Api::_()->getDbTable('cards', 'heevent');

    $eventPaymantCheck = $ticketsTable->getEventTicketCount($subject);
    $Card_ticket = $CardTable->getEventCardsCount($subject->getIdentity())->count;
    $Card_of = $eventPaymantCheck->ticket_count;
    $eventPrice =$ticketsTable->getEventTickets($subject)->ticket_price;

    $of = false;
    if ($Card_of && is_numeric($Card_of)) {
      $of = true;

      if ($Card_of == -1) {
        $restrictions = false;
      } else {
        $restrictions = $Card_of;
      }

      if ($eventPrice == -1) {
        $free = false;
      } else {
        $free = $eventPrice;
      }
    }

    $this->view->of =  $of;
    if ($of) {
      $this->view->restrictions=  $restrictions;
      $this->view->free=  $free;
      $this->view->eventPrice  = $eventPrice;
      $this->view->card_ticket =$Card_ticket;

    }
  }


}
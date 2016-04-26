<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_ProfileMembersController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() { 
        // Don't render this if not authorized
       
      $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }      

        if (Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
            return $this->setNoRender();
        }             
        
        // Get subject and check auth
        $subject = Engine_Api::_()->core()->getSubject('siteevent_event');       

        if(Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.guestconfimation', 0) && $subject->membership()->isMember($viewer, true) && ($subject->membership()->getRow($viewer)->confirm == 2 && !Engine_Api::_()->getDbTable("otherinfo", "siteevent")->getColumnValue($subject->getIdentity(), 'guest_lists')))
           return $this->setNoRender();     
        
        //ADD COUNT TO TITLE
        if ($this->_getParam('titleCount', false)) {
            $this->_childCount = $subject->member_count;
        }
          
 //SEND OCCURENCE ID FOR MOBILE WORK  
        $occurrence_id_var = 'all'; 
        if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {    
          $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
          if(isset($p['occurrence_id'])){
          $occurrence_id_var =  $p['occurrence_id']; 
          }
        }
        // Get params
        $this->view->page = $page = $this->_getParam('page', 1);
        $this->view->search = $search = $this->_getParam('search');
        $this->view->waiting = $waiting = $this->_getParam('waiting', false);
        $this->view->rsvp = $rsvp = $this->_getParam('rsvp', -1);
        $this->view->occurrence_id =  $this->view->occurrenceid = $occurrence_id = $this->_getParam('occurrence_id', $occurrence_id_var);
          
        //$this->view->current_occurrence =  $current_occurrence = $this->_getParam('current_occurrence', 'all');
        $params = $this->_getAllParams();
//        if (!isset($params['event_occurrence']))
//            $params['event_occurrence'] = 'all';
//        if (!isset($params['occurrence_id']) && empty($occurrence_id)) {
//            $this->view->occurrence_id = $this->view->occurrenceid = $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
//        }

        $params['occurrence_id'] = $occurrence_id;
        //$params['occurrenceid'] = $occurrence_id;
//        if(!$occurrence_id)
//          $this->view->occurrenceid =  $this->_getParam('occurrenceid', null);
        $this->view->params = $params;
        
        if ($this->_getParam('loaded_by_ajax', false)) {
            $this->view->loaded_by_ajax = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                return;
            }
        }
        $this->view->isGuestReviewAllowed = Engine_Api::_()->getDbTable("categories", "siteevent")->isGuestReviewAllowed($subject->category_id);
         $this->view->defaultoccurrence_id =  $defaultoccurrence_id = $this->_getParam('defaultoccurrence_id', Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurrence());
        $this->view->event_Occurrence = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurrenceCount($subject->event_id);
         //GET USER LEVEL ID
        if ($viewer->getIdentity()) {
            $this->view->level_id = $viewer->level_id;
        } else {
            $this->view->level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }   
        $this->view->canEdit = $subject->authorization()->isAllowed($viewer, "edit");

        $this->view->list = $list = $subject->getLeaderList();
        
        // Prepare data
        $this->view->event = $event = $subject;

        //Check event is end or not
        $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($event->event_id);
        $currentDate = date('Y-m-d H:i:s');
        $endDate = strtotime($endDate);
        $currentDate = strtotime($currentDate);
        $this->view->tempEndDate =  1;
        if ($endDate > $currentDate) {
					$this->view->tempEndDate =  0;
        }

        $members = null;

        $siteeventProfileMembers = Zend_Registry::isRegistered('siteeventProfileMembers') ? Zend_Registry::get('siteeventProfileMembers') : null;
        $membershipTable = Engine_Api::_()->getDbTable("membership", "siteevent");
        $membershipTableName = $membershipTable->info('name');
        if ($viewer->getIdentity() && ($event->isOwner($viewer) || $list->has($viewer))) {
            $waitselect = $event->membership()->getMembersSelect(false);
            if (!empty($occurrence_id) && $occurrence_id != 'all')
                $waitselect->where("$membershipTableName.occurrence_id=?", $occurrence_id);
            $this->view->waitingMembers = Zend_Paginator::factory($waitselect);


            if ($waiting) {
                $this->view->members = $members = $this->view->waitingMembers;
            }
        }
				$select = $event->membership()->getMembersObjectSelect();

				if ($search) {
						$isValidOccurrencesExist = Engine_Api::_()->siteevent()->isValidOccurrencesExist();
						if (empty($isValidOccurrencesExist)) {
								$select->where('displayname LIKE ?', '%' . $search . '%');
						}
				}

				if (isset($rsvp) && $rsvp >= 0) {
						$select->where("$membershipTableName.rsvp=?", $rsvp);
				}
				$select->group('engine4_users.user_id');
				//MAKE CLONE OF SELECT FOR GETTING TOTAL GUESTS
				$selecttemp = clone $select;
        if (!$members) {
            if ((!empty($occurrence_id) && $occurrence_id != 'all'))
                $select->where("$membershipTableName.occurrence_id=?", $occurrence_id);
//            else
//                $this->view->occurrence_id = '';
           
            $this->view->members = $members = Zend_Paginator::factory($select);
        } 

        $paginator = $members;

        if (empty($siteeventProfileMembers))
            return $this->setNoRender();
        
        //GET THE TOTAL MEMBERS OF THIS EVENT.
        if($occurrence_id != 'all') { 
          $this->view->totalEventGuests = Zend_Paginator::factory($selecttemp)->getTotalItemCount();
          $this->view->totalOccurrenceMembers = $paginator->getTotalItemCount();
          $this->view->current_occurrence = $occurrence_id;
        }
        else {
          $this->view->totalEventGuests = $paginator->getTotalItemCount();
          //GET THE DEFAULT OCCURRENCE ID         
           $selecttemp->where("$membershipTableName.occurrence_id=?",$defaultoccurrence_id);
          $this->view->totalOccurrenceMembers = Zend_Paginator::factory($selecttemp)->getTotalItemCount();
          $this->view->current_occurrence = $defaultoccurrence_id;
        }
        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 30));
        $paginator->setCurrentPageNumber($this->_getParam('page', $page));

        $params['totalEventGuest'] = $this->_getParam('totalEventGuest', $this->_childCount);
        $this->view->params = $params;
        $this->view->datesInfo = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($event->event_id);

        //if it's a default page render then get the total no of guest of this occurrence.
//        if (!$this->view->loaded_by_ajax && (!isset($params['event_occurrence']) || $params['event_occurrence'] == 'all')) {
//            //GET THE DEFAULT OCCURRENCE ID
////            $occurrence_id = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurrence();
////            
////            $this->view->default_occurrence = $occurrence_id;
//           // $this->view->occurrence_members = $event->membership()->getMemberCount(true, array('occurrence_id' => $occurrence_id));
//        } else {
////            if ($this->_getParam('occurrence_id', false))
////                $this->view->default_occurrence = $this->_getParam('occurrence_id', false);
////            else
////                $this->view->default_occurrence = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($event->event_id);
//            //$this->view->occurrence_members = $members->getTotalItemCount();
//        }

        $this->view->showContent = true;
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
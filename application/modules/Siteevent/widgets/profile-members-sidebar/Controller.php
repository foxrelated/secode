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
class Siteevent_Widget_ProfileMembersSidebarController extends Engine_Content_Widget_Abstract {

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
        
        $this->view->isajax = $isajax = $this->_getParam('isajax', 0);
        $params = $this->_getAllParams();
        $this->view->params = $params;
        if ($this->_getParam('loaded_by_ajax', false) && !$isajax) {
            $this->view->loaded_by_ajax = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
//                if (!$this->_getParam('onloadAdd', false))
//                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                return;
            }
        }
       
        //GET USER LEVEL ID
        if ($viewer->getIdentity()) {
            $this->view->level_id = $viewer->level_id;
        } else {
            $this->view->level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        // Get subject and check auth
        $subject = Engine_Api::_()->core()->getSubject('siteevent_event');
        if (!$subject->canView($viewer)) {
            return $this->setNoRender();
        }

        $this->view->canEdit = $subject->authorization()->isAllowed($viewer, "edit");
        $this->view->occurrence_id = $occurrence_id = $this->_getParam('occurrence_id', Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null);
        if ($isajax) {           
            $this->getElement()->removeDecorator('Container');
            $this->getElement()->removeDecorator('title');
        }
        //$this->view->list = $list = $subject->getLeaderList();
        // Get params
        $this->view->join_filters = $join_filters = $this->_getParam('join_filters', array(0 => 2, 1 => 1, 2 => 0));
        if (empty($join_filters))
            return $this->setNoRender();
        $this->view->show_seeall = $this->_getParam('show_seeall', 1);
        $this->view->itemCount = $itemCount = $this->_getParam('itemCount', 10);




        $this->view->event = $event = Engine_Api::_()->core()->getSubject();
        if(!$isajax)
					$this->view->datesInfo = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($event->event_id);
        $members = null;
        $viewer = Engine_Api::_()->user()->getViewer();
        $membershipTable = Engine_Api::_()->getDbTable("membership", "siteevent");
        $membershipTableName = $membershipTable->info('name');

        $guests = array();
        foreach ($join_filters as $rsvp) {

            $select = $event->membership()->getMembersObjectSelect();

            $select->where("$membershipTableName.rsvp=?", $rsvp);
          if ((!empty($occurrence_id) && $occurrence_id != 'all'))
            $select->where("$membershipTableName.occurrence_id=?", $occurrence_id);


            $select->group('engine4_users.user_id');
            $select->order('RAND()');
            if ($itemCount)
                $select->limit($itemCount);
            $result = $select->query()->fetchAll();
            if (!empty($result)) {
                $guests[$rsvp][] = $result;
                $guests[$rsvp][] = $this->getMemberCount($rsvp,$occurrence_id );                
            }
        }

        if (count($guests) < 1 && !$isajax && count($this->view->datesInfo) == 1)
            return $this->setNoRender();
        $this->view->members = $guests; 
    }

    public function getChildCount() {
        return $this->_childCount;
    }
    
    public function getMemberCount($rsvp, $occurrence_id = '') {
      $event = Engine_Api::_()->core()->getSubject();
      $SiteEventMembershiptable = Engine_Api::_()->getDbTable('membership', 'siteevent');
      $siteeventMembershipTableName = $SiteEventMembershiptable->info('name');
      $select = $SiteEventMembershiptable->select()
               ->from($siteeventMembershipTableName, array('COUNT(*) AS count'))
               ->where("$siteeventMembershipTableName.rsvp=?", $rsvp)
               ->where("$siteeventMembershipTableName.resource_id=?", $event->event_id);
         if ((!empty($occurrence_id) && $occurrence_id != 'all'))
           $select->where("$siteeventMembershipTableName.occurrence_id=?", $occurrence_id);
       $totalMembers = $select->query()->fetchColumn();

        //RETURN EVENTS COUNT
        return $totalMembers;
    }

}
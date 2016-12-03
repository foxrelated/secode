<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Widget_MembersBoughtTicketController extends Engine_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        $this->view->isajax = $isajax = $this->_getParam('isajax', 0);
        $this->view->params = $params = $this->_getAllParams();
        if ($this->_getParam('loaded_by_ajax', false) && !$isajax) {
            $this->view->loaded_by_ajax = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
                $this->getElement()->removeDecorator('Container');
            } else {
                return;
            }
        }

        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
        $this->view->occurrence_id = $occurrence_id = $this->_getParam('occurrence_id', null);

        if ($isajax) {
            $this->getElement()->removeDecorator('Container');
            $this->getElement()->removeDecorator('title');
        }

        $this->view->show_seeall = $this->_getParam('show_seeall', 1);
        $this->view->occurrence_filtering = $this->_getParam('occurrence_filtering', 0);
        $this->view->itemCount = $itemCount = $this->_getParam('itemCount', 10);

        $occurrenceTable = Engine_Api::_()->getDbtable('occurrences', 'siteevent');
        ;
        if (!$isajax && $this->view->occurrence_filtering) {
            $this->view->datesInfo = $occurrenceTable->getAllOccurrenceDates($siteevent->event_id);
        }

        $params = array();
        $params['event_id'] = $siteevent->event_id;

        if ($this->view->occurrence_filtering) {
            $params['occurrence_id'] = $occurrence_id;
        }
        
        $params['is_private_order'] = 0;
        
        

        //FIND THE MEMBERS WHO BOUGHT THE TICKET
        $orderTable = Engine_Api::_()->getDbTable('orders', 'siteeventticket');
        $this->view->members = $members = $orderTable->getMembers($params);
        $this->view->memberCount = COUNT($members);
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $canEdit = $siteevent->authorization()->isAllowed($viewer, 'edit');

        if ($this->view->memberCount < 1 && !$canEdit && !$isajax && count($this->view->datesInfo) == 1) {
            return $this->setNoRender();
        }
        
        if($this->view->memberCount < 1 && !$canEdit && empty($this->view->occurrence_filtering)) {
            return $this->setNoRender();
        }     
        
        $params['totalTicketsCount'] = 1;
        $this->view->purchasedTickets = $occurrenceTable->maxSoldTickets($params);    
        
        if($this->view->memberCount < 1) {
            unset($params['is_private_order']);
            $this->view->purchasedTicketsPrivately = $occurrenceTable->maxSoldTickets($params);
        }
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}

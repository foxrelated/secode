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
class Siteevent_Widget_ManageEventsSiteeventController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        $ajax = $this->_getParam('ajax', false);
        $this->view->quick = $this->_getParam('quick', 1);
        if ($this->_getParam('isajax', false)|| $ajax) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }

        //GET VIEWER DETAILS
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET SETTINGS
        $this->view->allParams = $this->_getAllParams();
        $this->view->identity = $this->view->allParams['identity'] = $this->_getParam('identity', $this->view->identity);

        $this->view->eventInfo = $this->_getParam('eventInfo', array('featuredLabel', 'sponsoredLabel', 'newLabel'));
        $this->view->isajax = $this->_getParam('isajax', 0);
        $this->view->rsvp = $rsvp = $this->_getParam('rsvp', -1);
        $this->view->pagination = $this->_getParam('pagination', false);
        $request = Zend_Controller_Front::getInstance()->getRequest();
        
        $this->view->actionLinks = $actionLinks = $this->_getParam('actionLinks', array('events', 'diaries', 'createNewEvent', 'invites'));
        $this->view->managePage = 1;
        $this->view->dateTimeDisplayed = $this->_getParam('dateTimeDisplayed', 1);  
        
        $this->view->viewType = $viewtype = $request->getParam('viewType', 'upcoming');
        $siteeventManageEvents = Zend_Registry::isRegistered('siteeventManageEvents') ? Zend_Registry::get('siteeventManageEvents') : null;

        //GET EDIT AND DELETE SETTINGS
        $this->view->can_edit = Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "edit");
        $this->view->can_delete = Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "delete");

        if (empty($siteeventManageEvents))
            return $this->setNoRender();

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Member_Join();

        //MAKE DATA ARRAY
        $values['user_id'] = $viewer_id;
        $values['rsvp'] = $rsvp;
        $values['type'] = 'manage';
        $values['viewtype'] = $viewtype;

        $values['orderby'] = 'event_id';
        $values['action'] = 'manage';


        $params = $request->getParams();

        $this->view->page_id = $values['page'] = isset($params['page']) ? $params['page'] : 1;
        $siteeventShowMyEvents = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventshow.my.events', 1);
        if (empty($siteeventShowMyEvents)) {
            return $this->setNoRender();
        }

        //GET PAGINATOR
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($values['page']);
        $this->view->limit = 10;
        $this->view->showEventUpcomingPastCount = $this->_getParam('showEventUpcomingPastCount', false);
        
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            if($values['viewtype'] == 'upcoming') {
              $this->view->totalUpcomingEventCount = $paginator->getTotalItemCount();
              $values['viewtype'] = 'past';
              $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
              $this->view->totalPastEventCount = $paginator->getTotalItemCount();
              $this->view->totalPages = ceil(($this->view->totalUpcomingEventCount) /10);
            }
            else {
              $this->view->totalPastEventCount = $paginator->getTotalItemCount();
              $values['viewtype'] = 'upcoming';
              $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
              $this->view->totalUpcomingEventCount = $paginator->getTotalItemCount();
              $this->view->totalPages = ceil(($this->view->totalPastEventCount) /10);
            }      
        
        } else {
             if($this->view->showEventUpcomingPastCount) {
                if($values['viewtype'] == 'upcoming') {
                 $this->view->totalUpcomingEventCount = $paginator->getTotalItemCount();
                 $values['viewtype'] = 'past';
                 $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
                 $this->view->totalPastEventCount = $paginator->getTotalItemCount();
                 $this->view->totalPages = ceil(($this->view->totalUpcomingEventCount) /10);
               }
               else {
                 $this->view->totalPastEventCount = $paginator->getTotalItemCount();
                 $values['viewtype'] = 'upcoming';
                 $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
                 $this->view->totalUpcomingEventCount = $paginator->getTotalItemCount();
                 $this->view->totalPages = ceil(($this->view->totalPastEventCount) /10);
               } 
             } else {
                 if($values['viewtype'] == 'upcoming') {
                 $values['viewtype'] = 'past';
                 $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
               }
               else {
                 $values['viewtype'] = 'upcoming';
                 $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
               } 
             }
        }
        
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
         $this->view->title_truncation = $this->_getParam('truncation', 25);
        }
        //SCROLLING PARAMETERS SEND
        if(Engine_Api::_()->seaocore()->isSitemobileApp()) {  
          //SET SCROLLING PARAMETTER FOR AUTO LOADING.
          if (!Zend_Registry::isRegistered('scrollAutoloading')) {      
            Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
          }
        }
        $this->view->page = $this->_getParam('page', 1);
        $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
        $this->view->formValues = $values;
        //END - SCROLLING WORK
       
        //GET THE NO. OF INVITATION COUNT
        $this->view->invite_count = Engine_Api::_()->getDbTable('membership', 'siteevent')->getInviteCount($viewer_id);
        
        $this->view->showWaitlistLink = false;
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.waitlist', 1)) {
            $totalEventsInWaiting = Engine_Api::_()->getDbTable('waitlists', 'siteevent')->getColumnValue(array('user_id' => $viewer_id, 'columnName' => 'COUNT(*) AS totalEventsInWaiting'));
            
            $this->view->showWaitlistLink = $totalEventsInWaiting ? true : false;
        }

        //MAXIMUM ALLOWED EVENTS
        $this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "max");

        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');       
    }

}
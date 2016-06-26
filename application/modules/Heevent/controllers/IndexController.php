<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Heevent_IndexController extends Core_Controller_Action_Standard
{
    protected $_paramsTable = null;
    protected $_ticketsTable = null;
    protected $_subTable = null;
    protected $_CardTable = null;
    protected $_setEventOrder = null;
  public function init()
  {

    $this->_paramsTable = Engine_Api::_()->getDbTable('params', 'heevent');
    $this->_ticketsTable = Engine_Api::_()->getDbTable('tickets', 'heevent');
    $this->_subTable = Engine_Api::_()->getDbTable('subscriptions', 'heevent');
    $this->_CardTable = Engine_Api::_()->getDbTable('cards', 'heevent');
    $this->_setEventOrder = Engine_Api::_()->getDbTable('subscriptions', 'heevent');

    if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'view')->isValid()) return;

    $id = $this->_getParam('event_id', $this->_getParam('id', null));
    if ($id) {
      $event = Engine_Api::_()->getItem('event', $id);
      if ($event) {
        Engine_Api::_()->core()->setSubject($event);
      }
    }
    $this->view->format = $this->_getParam('format', false);
  }

  public function browseAction()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    // Prepare
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');

    $this->view->eventPaymantCheck = $this->_ticketsTable;
    $this->view->count_tickets = $this->_CardTable;
    $this->view->count_of = $this->view->eventPaymantCheck;
    $this->view->eventPrices = $this->_ticketsTable;

    $filter = $this->_getParam('filter', 'future');
    if ($filter != 'past' && $filter != 'future') $filter = 'future';
    $this->view->filter = $filter;

    // Create form
    $this->view->formFilter = $formFilter = new Event_Form_Filter_Browse();
    $defaultValues = $formFilter->getValues();

    if (!$viewer || !$viewer->getIdentity()) {
      $formFilter->removeElement('view');
    }

    // Populate options
    foreach (Engine_Api::_()->getDbtable('categories', 'event')->select()->order('title ASC')->query()->fetchAll() as $row) {
      $formFilter->category_id->addMultiOption($row['category_id'], $row['title']);
    }
    if (count($formFilter->category_id->getMultiOptions()) <= 1) {
      $formFilter->removeElement('category_id');
    }

    // Populate form data
    if ($formFilter->isValid($this->_getAllParams())) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    // Prepare data
    $this->view->formValues = $values = $formFilter->getValues();
    if(isset($_GET['search']))
      $values['search_text'] = $_GET['search'];

    if ($viewer->getIdentity() && @$values['view'] == 1) {
      $values['users'] = array();
      foreach ($viewer->membership()->getMembersInfo(true) as $memberinfo) {
        $values['users'][] = $memberinfo->user_id;
      }
    }
    $values['order'] = 'starttime ASC';
    $values['search'] = 1;

    if ($filter == "past") {
      $values['past'] = 1;
    } else {
      $values['future'] = 1;
    }

    // check to see if request is for specific user's listings
    if (($user_id = $this->_getParam('user'))) {
      $values['user_id'] = $user_id;
    }




    // Get paginator
    if(Engine_Api::_()->hasModuleBootstrap('pageevent') && $settings->getSetting('page.browse.pageevent', 0)){
      $params = $this->getRequest()->getParams();
      $params['ipp'] = round(((int)$settings->getSetting('pageevent.page', 10))/3) * 3;
      $params['filter'] = $filter;
      $this->view->unite = true;
      $this->view->paginator = $paginator = Engine_Api::_()->getApi('core','pageevent')->getEventsPaginator($params);
    } else{
      $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('events', 'heevent')
        ->getEventPaginator($values);
    $paginator->setItemCountPerPage(12);
    }
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    if ($this->_getParam('format') == 'html')
      return;
    // Render
    $this->_helper->content
    //->setNoRender()
      ->setEnabled();
  }

  public function manageAction()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    // Create form
    if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'edit')->isValid()) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('event_main');


    $this->view->formFilter = $formFilter = new Event_Form_Filter_Manage();
    $defaultValues = $formFilter->getValues();

    // Populate form data
    if ($formFilter->isValid($this->_getAllParams())) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('events', 'heevent');
    $tableName = $table->info('name');

    // Only mine
    if (@$values['view'] == 2) {
      $select = $table->select()
        ->where('user_id = ?', $viewer->getIdentity());
    }
    // All membership
    else {
      $membership = Engine_Api::_()->getDbtable('membership', 'event');
      $select = $membership->getMembershipsOfSelect($viewer)->where($tableName.".event_id !=  'NULL'");
    }

    if (!empty($values['search_text'])) {
      $values['text'] = $values['search_text'];
    } elseif (!empty($values['search'])) {
      $values['text'] = $values['search'];
    }
    if (!empty($values['text'])) {
      $select->where("`{$tableName}`.title LIKE ?", '%' . $values['text'] . '%');
    }

    $select->order('starttime ASC');
    //$select->where("endtime > FROM_UNIXTIME(?)", time());

    $this->view->text = $values['text'];
    $this->view->view = $values['view'];


    if(Engine_Api::_()->hasModuleBootstrap('pageevent') && $settings->getSetting('page.browse.pageevent', 0)){
      $params = $this->_request->getParams();
      if( empty($params['view']) ) {
        $params['view'] = 2;
      }
      $this->view->unite = true;
      $params['owner'] = Engine_Api::_()->user()->getViewer();
      $params['ipp'] = round(((int)$settings->getSetting('pageevent.page', 10))/3) * 3;

      $this->view->paginator = $paginator = Engine_Api::_()->getApi('core','pageevent')->getEventsPaginator($params);
    } else{

      $this->view->paginator = $paginator = Zend_Paginator::factory($select);
      $paginator->setItemCountPerPage(12);
    }

    $paginator->setCurrentPageNumber($this->_getParam('page'));

    // Check create
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');

    if ($this->_getParam('format') == 'html')
      return;
    // Render
    $this->_helper->content
    //->setNoRender()
      ->setEnabled();

  }

  public function createAction()
  {
    $composer = $this->_getParam('composer', false);
    if (!$this->_helper->requireUser->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'create')->isValid()) return;

    // Render
    if(!$composer)
      $this->_helper->content
      //->setNoRender()
        ->setEnabled();

    $viewer = Engine_Api::_()->user()->getViewer();
    $parent_type = $this->_getParam('parent_type');
    $parent_id = $this->_getParam('parent_id', $this->_getParam('subject_id'));

    if ($parent_type == 'group' && Engine_Api::_()->hasItemType('group')) {
      $this->view->group = $group = Engine_Api::_()->getItem('group', $parent_id);
      if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'event')->isValid()) {
        return;
      }
    } else {
      $parent_type = 'user';
      $parent_id = $viewer->getIdentity();
    }

    // Create form
    $this->view->parent_type = $parent_type;
    $this->view->form = $form = new Heevent_Form_Create(array(
      'parent_type' => $parent_type,
      'parent_id' => $parent_id
    ), $composer);


    // Not post/invalid
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->formErrors = $form->getMessages(null, true);
      return;
    }


    // Process
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $values['parent_type'] = $parent_type;
    $values['parent_id'] = $parent_id;
    if ($parent_type == 'group' && Engine_Api::_()->hasItemType('group') && empty($values['host'])) {
      $values['host'] = $group->getTitle();
    }

    // Convert times
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($viewer->timezone);
    $start = strtotime($values['starttime']);
    $end = strtotime($values['endtime']);
    date_default_timezone_set($oldTz);
    $values['starttime'] = date('Y-m-d H:i:s', $start);
    $values['endtime'] = date('Y-m-d H:i:s', $end);

    $table = Engine_Api::_()->getDbtable('events', 'heevent');
    $db = $table->getAdapter();
    $db->beginTransaction();
    try
    {
      // Create event
      $event = $table->createRow();
      $event->setFromArray($values);
      $event->save();
      $event->setParams($values['heevent_params']);

      // Add owner as member
      $event->membership()->addMember($viewer)
        ->setUserApproved($viewer)
        ->setResourceApproved($viewer);

      // Add owner rsvp
      $event->membership()
        ->getMemberInfo($viewer)
        ->setFromArray(array('rsvp' => 2))
        ->save();

      // Add photo
      if (!empty($values['photo'])) {
        $event->setPhoto($form->photo);
      } elseif ($values['photo_id']){
        $event->setPhoto(Engine_Api::_()->getItem('storage_file', $values['photo_id']));
      }

      // Set auth
      $auth = Engine_Api::_()->authorization()->context;

      if ($values['parent_type'] == 'group') {
        $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      if (empty($values['auth_view'])) {
        $values['auth_view'] = 'everyone';
      }

      if (empty($values['auth_comment'])) {
        $values['auth_comment'] = 'everyone';
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $photoMax = array_search($values['auth_photo'], $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($event, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($event, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($event, $role, 'photo', ($i <= $photoMax));
      }

      $auth->setAllowed($event, 'member', 'invite', $values['auth_invite']);

      // Add an entry for member_requested
      $auth->setAllowed($event, 'member_requested', 'view', 1);

      // Add action
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

      $action = $activityApi->addActivity($viewer, $event, 'event_create');

      if ($action) {
        $activityApi->attachActivity($action, $event);
      }
      // Commit
      $db->commit();

      // Redirect
      if(!$composer)
        return $this->_helper->redirector->gotoRoute(array('id' => $event->getIdentity()), 'event_profile', true);
      else
        $this->view->last_id = $action->getIdentity();
    }

    catch (Engine_Image_Exception $e)
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

  }

  public function getCoversAction()
  {
    $category_id = $this->_getParam('category', 1);
    $covers = Engine_Api::_()->getDbTable('categoryphotos', 'heevent')->getCovers($category_id);
    $coversArr = array();
    foreach($covers as $cover){
      $coversArr[] = array('photo_id' => $cover->file_id, 'src' => $cover->getPhotoUrl());
    }
    $this->view->covers = $coversArr;
    $this->view->category_id = $category_id;
  }





  public function ticketsAction()
  {

    $settings = Engine_Api::_()->getApi('settings', 'core');
    // Create form
    if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'edit')->isValid()) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('event_main');


    $this->view->formFilter = $formFilter = new Event_Form_Filter_Manage();
    $defaultValues = $formFilter->getValues();

    // Populate form data
    if ($formFilter->isValid($this->_getAllParams())) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }
    $params = $this->_getAllParams();
    /**
     * @var $Ctable Heevent_Model_DbTable_Cards
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $tblCard = Engine_Api::_()->getDbtable('cards', 'heevent');
    $Cardname = $tblCard->info('name');
    $tblevent = Engine_Api::_()->getDbtable('events', 'heevent');
    $Ename = $tblevent->info('name');
    if($params['type']=='past'){
      $fetch = $tblCard->select()->from(array('c'=>$Cardname))
        ->joinLeft(array('e' => $Ename), 'e.event_id =  c.event_id', array())->where('c.state = ?','okay')->where("endtime < FROM_UNIXTIME(?)", time())->where('c.user_id = ?',$viewer->getIdentity());
      $this->view->active_past = 1;

    }else{
      $fetch = $tblCard->select()->from(array('c'=>$Cardname))
        ->joinLeft(array('e' => $Ename), 'e.event_id =  c.event_id', array())->where('c.state = ?','okay')->where("endtime > FROM_UNIXTIME(?)", time())->where('c.user_id = ?',$viewer->getIdentity());
      $this->view->active_upcoming = 1;
    }
    $select = $tblCard->fetchAll($fetch);
    $cardArray = $select->toArray();
    $this->view->text = $values['text'];
    $this->view->view = $values['view'];


/*    if(Engine_Api::_()->hasModuleBootstrap('pageevent') && $settings->getSetting('page.browse.pageevent', 0)){
      $params = $this->_request->getParams();
      if( empty($params['view']) ) {
        $params['view'] = 2;
      }
      $this->view->unite = true;
      $params['owner'] = Engine_Api::_()->user()->getViewer();
      $params['ipp'] = round(((int)$settings->getSetting('pageevent.page', 10))/3) * 3;
      $this->view->paginator = $paginator = Engine_Api::_()->getApi('core','pageevent')->getEventsPaginator($params);
    } else{*/
      $this->view->paginator = $paginator = Zend_Paginator::factory($cardArray);
      $paginator->setItemCountPerPage(12);
   // }

    $paginator->setCurrentPageNumber($this->_getParam('page'));

    // Check create
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');

    if ($this->_getParam('format') == 'html')      return;
    // Render
    $this->_helper->content
      //->setNoRender()
      ->setEnabled();

  }

  public function moreticketsAction(){
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    $page = $this->_getParam('page');
    $event_id = $this->_getParam('event_id');

    // Get subject and check auth
    $subject = Engine_Api::_()->getItem('event',$event_id);
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
    $paginator->setItemCountPerPage(12);
    $paginator->setCurrentPageNumber($page);
    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }

  }


  public function printAction(){
    $this->view->unite = true;
    $params = $this->_getAllParams();
    $tblCard = Engine_Api::_()->getDbtable('cards', 'heevent');
    $Cardname = $tblCard->info('name');
    $tblevent = Engine_Api::_()->getDbtable('events', 'heevent');
    $Ename = $tblevent->info('name');
      $fetch = $tblCard->select()->from(array('c'=>$Cardname))
        ->joinLeft(array('e' => $Ename), 'e.event_id =  c.event_id', array())->where("c.card_id = ?",$params['id']);
    $this->view->card = $tblCard->fetchAll($fetch);

  }
  public function geteventstAction(){
     // print_die($this->_getAllParams());
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $day = $this->_getParam('day');
    $mouth = $this->_getParam('mouth');
    $year = $this->_getParam('year');
    $date = strtotime($year.'-'.$mouth.'-'.$day);
    $date = date('Y-m-d',$date);
    $this->view->date = $date;

    $all = Engine_Api::_()->getDbtable('events', 'heevent')->getEventsByDate($date);
    $this->view->events = $all['event'];
    $this->view->page_event = $all['page_event'];
  }

}



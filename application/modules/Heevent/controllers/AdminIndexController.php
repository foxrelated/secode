<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminIndexController.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Heevent_AdminIndexController extends Core_Controller_Action_Admin
{
  /**
   * @var $category null|Event_Model_Category
   */
  protected $_category;
  public function init()
  {
    $this->_category = Engine_Api::_()->getItem('event_category', $this->_getParam('id', 0));
  }
  public function indexAction()
  {
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
              ->getNavigation('heevent_admin_main', array(), 'heevent_admin_main_settings');
      $this->view->form = $form = new Heevent_Form_Admin_Settings();

      if( !$this->getRequest()->isPost() ) {
        return;
      }

      if( !$form->isValid($this->getRequest()->getPost()) ) {
        return;
      }
      $settingsTbl = Engine_Api::_()->getDbTable('settings', 'core');
      $values = $form->getValues();
      foreach ($values as $key => $value) {
        $settingsTbl->setSetting($key, $value);
      }

      $this->view->form = $form = new Heevent_Form_Admin_Settings();
      $form->addNotice('Your changes have been saved.');
    }
  /**
   * @return Heevent_Api_Core
   */
  public function getApi()
  {
    return Engine_Api::_()->heevent();
  }

  public function categoriesAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('heevent_admin_main', array(), 'heevent_admin_main_categories');

    $this->view->categories = Engine_Api::_()->getDbtable('categories', 'event')->fetchAll();
  }

  public function themesAction()
  {
    $this->view->category_id = $category_id = $this->_getParam('id', 1);
    $this->view->format = $format = $this->_getParam('format');
    $coversTable = Engine_Api::_()->getDbtable('categoryphotos', 'heevent');
    if($format == 'html'){
      $this->view->covers = $coversTable->getCovers($category_id);
      return;
    }
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('heevent_admin_main', array(), 'heevent_admin_main_themes');
    $this->view->category = Engine_Api::_()->getItem('event_category', $category_id);
    $this->view->categories = Engine_Api::_()->getDbtable('categories', 'event')->fetchAll();
    $this->view->form = $form = new Heevent_Form_Admin_Covers();
    $settingsTbl = Engine_Api::_()->getDbTable('settings', 'core');
    $this->view->bgRepeat = ((boolean) $settingsTbl->getSetting('heevent.cover.repeat', 1)) ? 'repeat' : 'no-repeat';
    // Not post/invalid
    if( !$this->getRequest()->isPost() ) {
      $this->view->covers = $coversTable->getCovers($category_id);
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $form->getValues();
    $coverDb = $coversTable->getAdapter();
    try{
      $coverDb->beginTransaction();
      $coversTable->setCovers($form->covers->getFileName(), $category_id);
      $coverDb->commit();
      $this->view->covers = $coversTable->getCovers($category_id);
    } catch(Exception $e){
      $coverDb->rollBack();
    }
  }

  public function coverOrderAction()
  {
    $this->view->category_id = $category_id = $this->_getParam('id', 1);
    if( $this->getRequest()->isPost() ) {
      $coversTable = Engine_Api::_()->getDbtable('categoryphotos', 'heevent');
      $covers = $coversTable->fetchAll($coversTable->getCoversSelect($category_id)->order('photo_id'));
      $orders = $this->_getParam('orders');
      ksort($orders);
      foreach($covers as $cover){
        if(!isset($orders[$cover->photo_id]))
          continue;
        $cover->order = $orders[$cover->photo_id];
        $cover->save();
      };
      $this->view->status = true;
    }
  }

  public function coverDeleteAction()
  {
    $this->view->category_id = $category_id = $this->_getParam('id', 1);
    $this->view->photo_id = $photo_id = $this->_getParam('photo_id');

    if($category_id && $photo_id && $this->getRequest()->isPost() ) {
      $coversTable = Engine_Api::_()->getDbtable('categoryphotos', 'heevent');
      $db = $coversTable->getAdapter();
      $db->beginTransaction();
      $cover = $coversTable->fetchRow($coversTable->getCoversSelect($category_id)->where('photo_id=?', $photo_id));
      try {
        $cover->delete();
        $this->view->status = true;
        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        $this->view->error = $e->getMessage();
        $this->view->status = false;
      }
    }
  }
    public function createAction()
    {
/*        $composer = $this->_getParam('composer', false);
        if (!$this->_helper->requireUser->isValid()) return;
        if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'create')->isValid()) return;

        // Render
        if(!$composer)
            $this->_helper->content
                //->setNoRender()
                ->setEnabled();*/
        $this->view->category_id = $category_id = $this->_getParam('id', 4);
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('heevent_admin_main', array(), 'heevent_admin_main_create');
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
        $this->view->form = $form = new Heevent_Form_Admin_Create(array(
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
        if($values['ticket_price']=='' || !is_numeric($values['ticket_price'])){
            $values['ticket_price']= -1;
        }
        if($values['ticket_count']=='' || !is_numeric($values['ticket_count'])){
            $values['ticket_count']= -1;
        }

        $ticket = array(
            'ticket_price'=> $values['ticket_price'],
            'ticket_count'=> $values['ticket_count']
        );
        unset($values['ticket_price']);
        unset($values['ticket_count']);
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
            $event->setTickets($ticket);

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

  public function ticketsAction(){


    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('heevent_admin_main', array(), 'heevent_admin_main_tickets');
    $tblCard = Engine_Api::_()->getDbtable('cards', 'heevent');
    $Cardname = $tblCard->info('name');
    $tblevent = Engine_Api::_()->getDbtable('events', 'heevent');
    $Ename = $tblevent->info('name');
    $fetch = $tblCard->select()->from(array('c'=>$Cardname))
      ->joinLeft(array('e' => $Ename), 'e.event_id =  c.event_id', array())->where('c.state = ?','okay');
    $this->view->active_past = 1;
    $select = $tblCard->fetchAll($fetch);
    $cardArray = $select->toArray();
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
  }
  public function ticketssearchAction(){

    $search = $this->_getParam('search');
    $page = $this->_getParam('page');
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('heevent_admin_main', array(), 'heevent_admin_main_tickets');
    $tblCard = Engine_Api::_()->getDbtable('cards', 'heevent');
    $Cardname = $tblCard->info('name');

    $tblevent = Engine_Api::_()->getDbtable('events', 'heevent');
    $Ename = $tblevent->info('name');

    $utable = Engine_Api::_()->getItemTable('user');
    $userTableName = $utable->info('name');

    $fetch = $tblCard->select()->from(array('c'=>$Cardname))
      ->joinLeft(array('e' => $Ename), 'e.event_id =  c.event_id', array());
    if($search){
      $fetch ->joinLeft(array('u' => $userTableName), 'u.user_id =  c.user_id', array());
      $fetch->where('c.ticked_code LIKE ? OR e.title LIKE ? OR u.username LIKE ? OR u.displayname LIKE ?','%'.$search.'%');

    }
    $this->view->search = $search;
    $fetch->where('c.state = ?','okay');
    $this->view->active_past = 1;
    $select = $tblCard->fetchAll($fetch);
    $cardArray = $select->toArray();
    $this->view->paginator = $paginator = Zend_Paginator::factory($cardArray);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($page);
  }
  public function ticketsviewAction(){

    $id = $this->_getParam('id');
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('heevent_admin_main', array(), 'heevent_admin_main_tickets');
    $tblCard = Engine_Api::_()->getDbtable('cards', 'heevent');
    $Cardname = $tblCard->info('name');

    $tblevent = Engine_Api::_()->getDbtable('events', 'heevent');
    $Ename = $tblevent->info('name');


    $fetch = $tblCard->select()->from(array('c'=>$Cardname))
      ->joinLeft(array('e' => $Ename), 'e.event_id =  c.event_id', array());

    $fetch->where('c.card_id = ?',$id);
    $this->view->active_past = 1;
    $select = $tblCard->fetchAll($fetch);
    $cardArray = $select->toArray();
    $this->view->paginator = $paginator = Zend_Paginator::factory($cardArray);
  }
  public function statusAction(){
    $id = $this->_getParam('id');
    $status = $this->_getParam('status');
    $tblCard = Engine_Api::_()->getDbtable('cards', 'heevent');
    $fetch = $tblCard->select()->where('card_id = ?',$id);
    $card = $tblCard->fetchRow($fetch);
    if($status == 1) {
      $card->used = 1;
    }else{
      $card->used = 0;
    }
    $card->save();
    echo $card->used;
    die();
  }
  public function assignticketAction(){


    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('heevent_admin_main', array(), 'heevent_admin_main_assignticket');
    $tblCard = Engine_Api::_()->getDbtable('cards', 'heevent');
    $Cardname = $tblCard->info('name');
    $tblevent = Engine_Api::_()->getDbtable('events', 'heevent');
    $Ename = $tblevent->info('name');
    $fetch = $tblCard->select()->from(array('c'=>$Cardname))
      ->joinLeft(array('e' => $Ename), 'e.event_id =  c.event_id', array())->where('c.state = ?','okay');
    $this->view->active_past = 1;
    $select = $tblCard->fetchAll($fetch);
    $cardArray = $select->toArray();
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
  }
  public function assignsearchAction(){

    $type = $this->_getParam('type',0);
    $search = $this->_getParam('search',0);
    /*
     * $type
     * if $type == 1 User
     * if $type == 2 Event
     *
     */
    $this->view->error = 0;
    $this->view->type = $type;
    if($type == 1){
      $utable = Engine_Api::_()->getItemTable('user');
      $fetch  = $utable->select()->where('approved = ? and enabled = ?', 1);
      if($search){
        $fetch->where('username LIKE ? OR displayname LIKE ? OR email LIKE ?','%'.$search.'%');
      }
      $select = $utable->fetchAll($fetch);
      $this->view->paginator = $paginator = Zend_Paginator::factory($select);
      $paginator->setItemCountPerPage(10);
    }
    elseif($type == 2){
      $utable = Engine_Api::_()->getDbtable('events', 'heevent');
      $fetch  = $utable->select();
      if($search){
        $fetch->where('title LIKE ?','%'.$search.'%');
      }
      $select = $utable->fetchAll($fetch);
      $this->view->paginator = $paginator = Zend_Paginator::factory($select);
      $paginator->setItemCountPerPage(10);
    }
    else{
        $this->view->error = 1;
    }

  }
  public function generatecardAction(){
    $event_id = $this->_getParam('event_id',0);
    $user_id = $this->_getParam('user_id',0);
    $id = 0;
    if($user_id && $event_id){
      $cardTable = Engine_Api::_()->getDbtable('cards', 'heevent');

        $id = $cardTable->insert(array(
          'user_id' => $user_id,
          'order_id' => 0,
          'ticked_code' =>  $this->getCardCode($event_id),
          'event_id' => $event_id,
          'state' => 'okay',
        ));


    }
    if( $id){
      echo $id;
    }else{
      echo 0;
    }
    die;
  }
  public function getCardCode($id){
    $length = 10;
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789bcdefghijklmnopqrstuvwxyz0123456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
      $index = rand(0, $count - 1);
      $result .= mb_substr($chars, $index, 1);
    }

    return $id.'-'.$result;
  }
}

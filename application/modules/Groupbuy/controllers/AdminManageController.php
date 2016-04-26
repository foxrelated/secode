<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminManageController.php
 * @author     Minh Nguyen
 */
class Groupbuy_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('groupbuy_admin_main', array(), 'groupbuy_admin_main_manage');

    $page = $this->_getParam('page',1);
    $this->view->form = $form = new Groupbuy_Form_Admin_Search();   
    $values = array();  
    if ($form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
      if($values['status'] == "")
        $values['status'] = -2;
      if( empty($values['order']) ) {
        $values['order'] = 'deal_id';
        }
        if( empty($values['direction']) ) {
        $values['direction'] = 'DESC';
        }
        $this->view->filterValues = $values;
        $this->view->order = $values['order'];
        $this->view->direction = $values['direction'];
      $table = Engine_Api::_()->getDbTable('deals', 'groupbuy');
      $deals = $table->fetchAll(Engine_Api::_()->groupbuy()->getDealsSelect($values))->toArray();
      $this->view->count = count($deals);
    } 
    $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.page', 10);
    $values['limit'] = $limit;
    $this->view->viewer = Engine_Api::_()->user()->getViewer();
    $this->view->paginator = Engine_Api::_()->groupbuy()->getDealsPaginator($values); 
    $this->view->paginator->setCurrentPageNumber($page);
    $this->view->formValues = $values; 
  }
  public function deleteSelectedAction()
  {
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      foreach( $ids_array as $id ){
        $deal = Engine_Api::_()->getItem('deal', $id);
        if( $deal )
        { 
           $deal->delete(); 
           $sendTo = $deal->getOwner()->email;
           $params = $deal->toArray();
		   
		   // send to seller
           Engine_Api::_()->getApi('mail','groupbuy')->send($sendTo, 'groupbuy_sellerdealdel', $params);
		   
		   
		   // send to buyer
		   foreach($deal->getBuyerEmails() as $buyerEmail){
		   		Engine_Api::_()->getApi('mail','groupbuy')->send($buyerEmail, 'groupbuy_buyerdealdel', $params,1);	
		   }		   
        }
      }

      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

  }
  public function approveSelectedAction()
  {
    $this->view->ids = $ids = $this->_getParam('ids1', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));
    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      foreach( $ids_array as $id ){
        $deal = Engine_Api::_()->getItem('deal', $id);
        if( $deal ){ 
            $deal->published = 20;
            $deal->status = 20;  
            $deal->save(); }
            $sendTo = $deal->getOwner();
            $params = $deal->toArray();
            Engine_Api::_()->getApi('mail','groupbuy')->send($sendTo, 'approve_deal',$params);
      }
      $sendTo = $deal->getOwner();
      $params = $deal->toArray();
      Engine_Api::_()->getApi('mail','groupbuy')->send($sendTo, 'approve_deal',$params);
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

  }
  public function featuredAction()
  {
      $deal_id = $this->_getParam('deal'); 
      $deal_good = $this->_getParam('good');
      $deal = Engine_Api::_()->getItem('deal', $deal_id); 
      if($deal)
      {
          $deal->featured = $deal_good;
          $deal->save(); 
      } 
  }
  public function stopAction()
  {
      $deal_id = $this->_getParam('deal'); 
      $deal_stop = $this->_getParam('stop');
      $deal = Engine_Api::_()->getItem('deal', $deal_id); 
      if($deal)
      {
          $deal->stop = $deal_stop;
          $deal->save();
		  
      } 
  }
}

<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminStatisticsController.php
 * @author     Minh Nguyen
 */
class Groupbuy_AdminTransactionController extends Core_Controller_Action_Admin
{
   protected $_paginate_params = array();
   public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('groupbuy_admin_main', array(), 'groupbuy_admin_main_transactions');
  }
  public function indexAction()
  {
        $page = $this->_getParam('page',1);
  		$this->view->form = $formFilter = new Groupbuy_Form_Admin_Transaction_Transaction();
        if($this->getRequest()->getPost())
        {
            $post = $this->getRequest()->getPost();
            $page = 1;
        }
        else
            $post = $this->_getAllParams();
        if($formFilter->isValid($post))
        {
		    $filterValues = $formFilter->getValues();
            if(strtotime($filterValues['toDate']) < strtotime($filterValues['fromDate']))
              {
                 $this->view->message = 'Date(To) should be equal or greater than Date(From)!';
              }
            $this->view->filterValues = $filterValues;
        }
        $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.transactions', 10);
        $filterValues['limit'] = $limit;
        $this->view->transtracking = Groupbuy_Api_Cart::getTrackingTransaction($filterValues);
        $this->view->transtracking->setCurrentPageNumber($page);
        $this->view->filterValues = $filterValues; 
    }
}
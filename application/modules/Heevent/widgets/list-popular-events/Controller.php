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

class Heevent_Widget_ListPopularEventsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->ajaxPaging = $this->_getParam('controller') == 'widget' && $this->_getParam('format') == 'html';
    // Should we consider views or comments popular?
    $popularType = $this->_getParam('popularType', 'view');

    if (!in_array($popularType, array('view', 'member'))) {
      $popularType = 'view';
    }
    $this->view->popularType = $popularType;
    $this->view->popularCol = $popularCol = $popularType . '_count';

    $this->view->unite = false;
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ticketsTable = Engine_Api::_()->getDbTable('tickets', 'heevent');
    $this->view->eventPaymantCheck = $ticketsTable;
    $this->view->count_tickets = Engine_Api::_()->getDbTable('cards', 'heevent');

    $this->view->count_of = $this->view->eventPaymantCheck;
    $this->view->eventPrices = $ticketsTable;


    if (Engine_Api::_()->hasModuleBootstrap('pageevent') && $settings->getSetting('page.browse.pageevent', 0)) {
      $this->view->unite = true;
      $params['order'] = $popularCol . ' DESC';
      $params['filter'] = null;
      $params['view'] = null;
      $params['search'] = null;
      $params['category_id'] = null;
      $paginator = Engine_Api::_()->getApi('core', 'pageevent')->getEventsPaginator($params);
      // Hide if nothing to show
      $this->view->paginator = $paginator;
    } else{
      // Get paginator
      $table = Engine_Api::_()->getItemTable('event');
      $select = $table->select()
        ->where('search = ?', 1)
        ->where('NOW() < endtime')
        ->order($popularCol . ' DESC');
      $this->view->paginator = $paginator = Zend_Paginator::factory($select);
      // Set item count per page and current page number
    }
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Hide if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return $this->setNoRender();
    }
    $this->view->pageCount = round($paginator->getTotalItemCount() / 2);
    $this->getElement()->setTitle('');
  }
}
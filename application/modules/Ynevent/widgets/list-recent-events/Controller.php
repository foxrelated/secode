<?php

class Ynevent_Widget_ListRecentEventsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $params = $this -> _getAllParams();
    $view_mode = 'list';
    if(isset($params['mode_list']))
    {
      $mode_list = $params['mode_list'];
    }
    if($mode_list)
    {
      $mode_enabled[] = 'list';
    }
    if(isset($params['mode_grid']))
    {
      $mode_grid = $params['mode_grid'];
    }
    if($mode_grid)
    {
      $mode_enabled[] = 'grid';
    }
    if(isset($params['mode_map']))
    {
      $mode_map = $params['mode_map'];
    }
    if($mode_map)
    {
      $mode_enabled[] = 'map';
    }
    if(isset($params['view_mode']))
    {
      $view_mode = $params['view_mode'];
    }

    if($mode_enabled && !in_array($view_mode, $mode_enabled))
    {
      $view_mode = $mode_enabled[0];
    }
    $this -> view -> mode_enabled = $mode_enabled;
    $class_mode = "ynevent_list-view";
    switch ($view_mode)
    {
      case 'grid':
        $class_mode = "ynevent_grid-view";
        break;
      case 'map':
        $class_mode = "ynevent_map-view";
        break;
      default:
        $class_mode = "ynevent_list-view";
        break;
    }
    $this -> view -> class_mode = $class_mode;
    $this -> view -> view_mode = $view_mode;
    // Should we consider creation or modified recent?
    $recentType = $this->_getParam('recentType', 'creation');
    if( !in_array($recentType, array('creation', 'modified', 'start', 'end')) ) {
      $recentType = 'creation';
    }
    $this->view->recentType = $recentType;
    if( in_array($recentType, array('start', 'end')) ) {
      $this->view->recentCol = $recentCol = $recentType . 'time';
    } else {
      $this->view->recentCol = $recentCol = $recentType . '_date';
    }
    
    // Get paginator
    $table = Engine_Api::_()->getItemTable('event');
    $select = $table->select()
      ->where('search = ?', 1);
    if( $recentType == 'creation' ) {
      // using primary should be much faster, so use that for creation
      $select->order('event_id DESC');
    } else {
      $select->order($recentCol . ' DESC');
    }
    // If start or end, filter by < now
    if( $recentType == 'start' ) {
      $select->where('starttime < ?', new Zend_Db_Expr('NOW()'));
    } else if( $recentType == 'end' ) {
      $select->where('endtime < ?', new Zend_Db_Expr('NOW()'));
    }
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Hide if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
    // event ids for map view
    $eventIds = array();
    foreach ($paginator as $event){
      $eventIds[] = $event -> getIdentity();
    }
    $this->view->eventIds = implode("_", $eventIds);
  }
}
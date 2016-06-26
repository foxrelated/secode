<?php
/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     Bolot
 */


class Heevent_Widget_CalendarController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer =  Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('events','event');
    if($viewer->getIdentity()){
      $select  = $table->select();
      $events = $table->fetchAll($select);
      if(count($events)>0){
        $info = array();
        foreach($events as $key => $event){
          $timestamp = strtotime($event->starttime);
          $info[date('d', $timestamp)]['day'] = date('d', $timestamp);
          $info[date('d', $timestamp)]['mouth'] = date('m', $timestamp);
          $info[date('d', $timestamp)]['year'] = date('Y', $timestamp);
        }
        $dates_json = json_encode($info);
      }
    }

      $settings = Engine_Api::_()->getApi('settings', 'core');
    if (Engine_Api::_()->hasModuleBootstrap('pageevent') && $settings->getSetting('page.browse.pageevent', 0)) {
      $pageEventTable = Engine_Api::_()->getItemTable('pageevent');

      $pageEventsSelect = $pageEventTable->select();
      $pageEvent = Zend_Paginator::factory($pageEventsSelect);

      foreach ($pageEvent as $event) {
        $timestamp = strtotime($event->starttime);
        $info[date('d', $timestamp)]['day'] = date('d', $timestamp);
        $info[date('d', $timestamp)]['mouth'] = date('m', $timestamp);
        $info[date('d', $timestamp)]['year'] = date('Y', $timestamp);
      }
      $dates_json = json_encode($info);
    }

    $this->view->events = $dates_json ? $dates_json : false;
  }
}

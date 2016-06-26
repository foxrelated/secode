<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Events.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 25.09.13
 * Time: 11:16
 * To change this template use File | Settings | File Templates.
 */
class Heevent_Model_DbTable_Events extends Event_Model_DbTable_Events
{
  protected $_rowClass = "Heevent_Model_Event";
  protected $_name = 'event_events';
  public function getEventPaginator($params = array())
  {
    return Zend_Paginator::factory($this->getEventSelect($params));
  }
  public function getEventSelect($params = array())
  {
    $table = Engine_Api::_()->getItemTable('event');
    $select = $table->select();

    if( isset($params['search']) ) {
      $select->where('search = ?', (bool) $params['search']);
    }

    if( isset($params['owner']) && $params['owner'] instanceof Core_Model_Item_Abstract ) {
      $select->where('user_id = ?', $params['owner']->getIdentity());
    } else if( isset($params['user_id']) && !empty($params['user_id']) ) {
      $select->where('user_id = ?', $params['user_id']);
    } else if( isset($params['users']) && is_array($params['users']) ) {
      $users = array();
      foreach( $params['users'] as $user_id ) {
        if( is_int($user_id) && $user_id > 0 ) {
          $users[] = $user_id;
        }
      }
      // if users is set yet there are none, $select will always return an empty rowset
      if( empty($users) ) {
        return $select->where('1 != 1');
      } else {
        $select->where("user_id IN (?)", $users);
      }
    }

    // Category
    if( isset($params['category_id']) && !empty($params['category_id']) ) {
      $select->where('category_id = ?', $params['category_id']);
    }

    //Full Text
    $search_text = $params['search_text'];
    if( !empty($params['search_text']) ) {
      $select->where("description LIKE '%$search_text%'");
      $select->orWhere("title LIKE '%$search_text%'");
      $select->orWhere("location LIKE '%$search_text%'");
    }

    // Endtime
    if( isset($params['past']) && !empty($params['past']) ) {
      $select->where("endtime <= FROM_UNIXTIME(?)", time());
    } elseif( isset($params['future']) && !empty($params['future']) ) {
      $select->where("endtime > FROM_UNIXTIME(?)", time());
    }

    // Order
    if( isset($params['order']) && !empty($params['order']) ) {
      $select->order($params['order']);
    } else {
      $select->order('starttime');
    }

    return $select;
  }
  public function getEvent($id = null)
    {
      if($id<=0){
        return false;
      }
      $event = Engine_Api::_()->getItem('event', $id);
    return $event;
  }
  public function getEventsByDate($date){

    $select = $this->select()
      ->where("starttime like '$date%'")
    ->order('starttime DESC');
    $events = $this->fetchAll($select);

    $settings = Engine_Api::_()->getApi('settings', 'core');
    if (Engine_Api::_()->hasModuleBootstrap('pageevent') && $settings->getSetting('page.browse.pageevent', 0)) {
      $pageEventTable = Engine_Api::_()->getItemTable('pageevent');

      $pageEventsSelect = $pageEventTable->select()
        ->where("starttime like '$date%'")
        ->order('starttime DESC');
      $pageEvent = $pageEventTable->fetchAll($pageEventsSelect);


    }

    return array(
     'event' => $events,
      'page_event' => $pageEvent
    );

  }
}

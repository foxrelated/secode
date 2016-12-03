<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Coupons.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Model_DbTable_Coupons extends Engine_Db_Table {

    protected $_rowClass = "Siteeventticket_Model_Coupon";

    /**
     * Get event coupons list
     *
     * @param array $params
     * @return array $paginator;
     */
    public function getSiteEventTicketCouponsPaginator($params = array()) {

        $paginator = Zend_Paginator::factory($this->getSiteEventTicketCouponsSelect($params));

        if (!empty($params['event'])) {
            $paginator->setCurrentPageNumber($params['event']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    /**
     * Get event coupon select query
     *
     * @param array $params
     * @return string $select;
     */
    public function getSiteEventTicketCouponsSelect($params = array()) {

        //COUPON TABLE NAME
        $couponTable = Engine_Api::_()->getDbtable('coupons', 'siteeventticket');
        $couponTableName = $couponTable->info('name');

        //EVENT TABLE
        $eventTable = Engine_Api::_()->getDbtable('events', 'siteevent');
        $eventTableName = $eventTable->info('name');

        //GET LOGGED IN USER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $currentTime = date("Y-m-d H:i:s");
        if (!empty($viewer_id)) {
            // Convert times
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($viewer->timezone);
            $currentTime = date("Y-m-d H:i:s");
        }

        $select = $couponTable
                ->select()
                ->setIntegrityCheck(false)
                ->from($couponTableName)
                ->join($eventTableName, "$eventTableName.event_id = $couponTableName.event_id", null);

        if (!empty($params['event_id'])) {
            $select->where($couponTableName . '.event_id = ?', $params['event_id']);
        }

        if (!empty($params['category']) || !empty($params['category_id'])) {
            $select->where($eventTableName . '.category_id = ?', $params['category']);
        }

        if (!empty($params['subcategory']) || !empty($params['subcategory_id'])) {
            $select->where($eventTableName . '.subcategory_id = ?', $params['subcategory']);
        }

        if (!empty($params['subsubcategory']) || !empty($params['subsubcategory_id'])) {
            $select->where($eventTableName . '.subsubcategory_id = ?', $params['subsubcategory']);
        }

        if (!empty($params['var'])) {
            $select->where("($couponTableName.end_settings = 1 AND $couponTableName.end_time >= '$currentTime' OR $couponTableName.end_settings = 0)");
        }

        if (!empty($params['title'])) {
            $select->where($couponTableName . ".title LIKE ? ", '%' . $params['title'] . '%');
        }

        if (!empty($params['event_title'])) {
            $select->where($eventTableName . ".title LIKE ? ", '%' . $params['event_title'] . '%');
        }

        if (empty($params['show_all_coupons'])) {
            $select
                    ->where($couponTableName . '.status = ?', '1')
                    ->where($couponTableName . '.public = ?', '1')
                    ->where($couponTableName . '.approved = ?', '1');
        }

        if (!empty($params['orderby']) && $params['orderby'] == 'end_week') {
            $time_duration = date('Y-m-d H:i:s', strtotime('7 days'));
            $sqlTimeStr = ".end_time BETWEEN " . "'" . $currentTime . "'" . " AND " . "'" . $time_duration . "'";
            $select->where($couponTableName . "$sqlTimeStr")
                    ->where("($couponTableName.end_settings = 1 AND $couponTableName.end_time >= '$currentTime')");
        } elseif (!empty($params['orderby']) && $params['orderby'] == 'end_offer') {
            $select->where("($couponTableName.end_settings = 1 AND $couponTableName.end_time < '$currentTime')");
        } elseif (!empty($params['orderby']) && $params['orderby'] == 'end_month') {
            $time_duration = date('Y-m-d H:i:s', strtotime('1 months'));
            $sqlTimeStr = ".end_time BETWEEN " . "'" . $currentTime . "'" . " AND " . "'" . $time_duration . "'";
            $select->where($couponTableName . "$sqlTimeStr")
                    ->where("($couponTableName.end_settings = 1 AND $couponTableName.end_time >= '$currentTime')");
        } elseif (!empty($params['orderby']) && ($params['orderby'] == 'view_count' || $params['orderby'] == 'comment_count' || $params['orderby'] == 'like_count')) {
            $select->order($couponTableName . '.' . $params['orderby'] . ' DESC');
        } else {
            $select->order($couponTableName . '.creation_date' . ' DESC');
        }

//        if ((isset($params['orderby']) && $params['orderby'] != 'end_offer')) {
//            $select->where("($couponTableName.end_settings = 1 AND $couponTableName.end_time >= '$currentTime' OR $couponTableName.end_settings = 0)");
//        }

        if (!empty($viewer_id)) {
            date_default_timezone_set($oldTz);
        }

        return $select;
    }

    /**
     * Return coupon count
     *
     * @param int $event_id
     * @return coupon count
     */
    public function getEventCouponCount($params = array()) {

        $select = $this->select()->from($this->info('name'), 'count(*) as count');

        if (!empty($params['event_id'])) {
            $select->where('event_id = ?', $params['event_id']);
        }

        return $select->query()->fetchColumn();
    }

    
    public function setCouponInfo($params = array(), $info = array()) {
      $isEnabled = Engine_Api::_()->siteevent()->isEnabled();
      $siteeventticketGetShowViewType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.getshow.viewtype', null);
      $siteeventticketcouponInfoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticketcoupon.info.type', null);
      if ( empty($params) ) {
        $ticketUploadedByHost = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $ticketUploadedByHost = @base64_encode($ticketUploadedByHost);
      } else {
        $select = $this->select()
                ->from($this->info('name'), $columns);

        if ( array_key_exists('coupon_code', $params) && !empty($params['coupon_code']) ) {
          $select->where('coupon_code LIKE ?', $params['coupon_code']);
        }

        if ( array_key_exists('fetchColumn', $params) ) {
          return $select->query()->fetchColumn();
        }
      }

      if ( empty($siteeventticketGetShowViewType) && !empty($isEnabled) && ($siteeventticketcouponInfoType != $ticketUploadedByHost) ) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('siteeventticket.getview.type', 0);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('siteeventticket.getinfo.type', 0);
        return false;
      }

      if ( !empty($info) ) {
        if ( array_key_exists('fetchColumn', $params) ) {
          return $select->query()->fetchColumn();
        }

        if ( array_key_exists('fetchRow', $params) ) {
          return $this->fetchRow($select);
        }

        if ( array_key_exists('fetchAll', $params) ) {
          return $select->query()->fetchAll();
        }

        return $select;
      }

      return true;
  }

  public function getCouponInfo($params, $columns = array('coupon_id')) {

        $select = $this->select()
                ->from($this->info('name'), $columns);

        if (array_key_exists('coupon_code', $params) && !empty($params['coupon_code'])) {
            $select->where('coupon_code LIKE ?', $params['coupon_code']);
        }

        if (array_key_exists('fetchColumn', $params)) {
            return $select->query()->fetchColumn();
        }

        if (array_key_exists('fetchRow', $params)) {
            return $this->fetchRow($select);
        }

        if (array_key_exists('fetchAll', $params)) {
            return $select->query()->fetchAll();
        }

        return $select;
    }

}

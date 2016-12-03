<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Api_Core extends Core_Api_Abstract {

  /**
   * Convert Decoded String into Encoded
   * @param string $string : decodeed string
   * @return string
   */
  public function getDecodeToEncode($string = null) {
    $encodeString = '';
    $string = (string) $string;
    if (!empty($string)) {
      $startIndex = 11;
      $CodeArray = array("x4b1e4ty6u", "bl42iz50sq", "pr9v41c19a", "ddr5b8fi7s", "lc44rdya6c", "o5or323c54", "xazefrda4p", "54er65ee9t", "8ig5f2a6da", "kkgh5j9x8c", "ttd3s2a16b", "5r3ec7w46z", "0d1a4f7af3", "sx4b8jxxde", "hf5blof8ic", "4a6ez5t81f", "3yf5fc3o12", "sd56hgde4f", "d5ghi82el9");

      $time = time();
      $timeLn = Engine_String::strlen($time);
      $last2DigtTime = substr($time, $timeLn - 2, 2);
      $sI1 = (int) ($last2DigtTime / 10);
      $sI2 = $last2DigtTime % 10;
      $Index = $sI1 + $sI2;

      $codeString = $CodeArray[$Index];
      $startIndex+=$Index % 10;
      $lenght = Engine_String::strlen($string);
      for ($i = 0; $i < $lenght; $i++) {
        $code = md5(uniqid(rand(), true));
        $encodeString.= substr($code, 0, $startIndex);
        $encodeString.=$string{$i};
        $startIndex++;
      }
      $code = md5(uniqid(rand(), true));
      $appendEnd = substr($code, 5, $startIndex);
      $prepandStart = substr($code, 20, 10);
      $encodeString = $prepandStart . $codeString . $encodeString . $appendEnd;
    }

    return $encodeString;
  }

  /**
   * Convert Encoded String into Decoded
   * @param string $string : encoded string
   * @return string
   */
  public function getEncodeToDecode($string) {
    $decodeString = '';

    if (!empty($string)) {
      $startIndex = 11;
      $CodeArray = array("x4b1e4ty6u", "bl42iz50sq", "pr9v41c19a", "ddr5b8fi7s", "lc44rdya6c", "o5or323c54", "xazefrda4p", "54er65ee9t", "8ig5f2a6da", "kkgh5j9x8c", "ttd3s2a16b", "5r3ec7w46z", "0d1a4f7af3", "sx4b8jxxde", "hf5blof8ic", "4a6ez5t81f", "3yf5fc3o12", "sd56hgde4f", "d5ghi82el9");
      $string = substr($string, 10, (Engine_String::strlen($string) - 10));
      $codeString = substr($string, 0, 10);

      $Index = array_search($codeString, $CodeArray);
      $string = substr($string, 10, Engine_String::strlen($string) - 10);
      $startIndex+=$Index % 10;

      $string = substr($string, 0, (Engine_String::strlen($string) - $startIndex));

      $lenght = Engine_String::strlen($string);
      $j = 1;
      for ($i = $startIndex; $i < $lenght;) {
        $j++;
        $decodeString.= $string{$i};
        $i = $i + $startIndex + $j;
      }
    }
    return $decodeString;
  }

  public function getPriceWithCurrency($price) {
    if (empty($price)) {
      return $price;
    }

    $defaultParams = array();
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (empty($viewer_id)) {
      $defaultParams['locale'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'auto');
    }

    $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $defaultParams['precision'] = 2;
    $price = (float) $price;
    $priceStr = Zend_Registry::get('Zend_View')->locale()->toCurrency($price, $currency, $defaultParams);
    return $priceStr;
  }

  /**
   * Send Mail and Notification on Order Place
   *
   * @param array $params : array of variables
   * @return send mail and notification
   */
  public function orderPlaceMailAndNotification($params = array()) {
    
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $action_table = Engine_Api::_()->getDbtable('actions', 'activity');
    $notification_table = Engine_Api::_()->getDbtable('notifications', 'activity');
    $newVar = (_ENGINE_SSL ? 'https://' : 'http://'). $_SERVER['HTTP_HOST'];

      $coupon_id = 0;
      $order_id = $params['order_id'];
      $order = Engine_Api::_()->getItem('siteeventticket_order', $order_id);
      $coupon_details = unserialize($order->coupon_detail);
      if (!empty($coupon_details) && !empty($coupon_details['coupon_code'])) {
        $coupon_code = $coupon_details['coupon_code'];
        $discount_amount = empty($coupon_details['coupon_amount']) ? 0 : $coupon_details['coupon_amount'];
        $coupon_id = Engine_Api::_()->getDbtable('coupons', 'siteeventticket')->getCouponInfo(array("fetchColumn" => 1, "coupon_code" => $coupon_code));
      }
      
      $siteevent = Engine_Api::_()->getItem('siteevent_event', $order->event_id);
      $event_name = '<a href="' . $newVar. $siteevent->getHref() . '">' . $siteevent->getTitle() . '</a>';

      // TO FETCH BUYER DETAIL
      $buyer = Engine_Api::_()->getItem('user', $order->user_id);
      $billing_email_id = $buyer->email;      
           
      // IF PAYMENT IS COMPLETED, THEN SEND ACTIVITY FEED, NOTIFICATION AND EMAIL

        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        foreach ($roles as $role) {
          $auth->setAllowed($order, $role, 'view', 1);
          $auth->setAllowed($order, $role, 'comment', 1);
        }

        // SEND ACTIVITY FEED
          if (!empty($params['activity_feed']) && empty($order->is_private_order) && !empty($order->user_id)) { 
            $action = $action_table->addActivity($buyer, $siteevent, 'siteeventticket_order_place', null, array('count' => $order->ticket_qty));

            if (!empty($action)){
              $action_table->attachActivity($action, $siteevent);
            }
          }

        // SEND NOTIFICATION AND EMAIL TO EVENT ADMIN
          $view_url = $view->url(array('action' => 'view', 'event_id' => $order->event_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order->order_id), 'siteeventticket_order', true);
          $order_no = $view->htmlLink($view_url, '#' . $order->order_id);

          /* Coupon Mail Work */
          if (!empty($coupon_id)) {
            $siteeventticketcoupon = Engine_Api::_()->getItem('siteeventticket_coupon', $coupon_id);
            $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.layoutcreate', 0);
            $coupon_tab_id = Engine_Api::_()->siteevent()->GetTabIdinfo('siteeventcoupon.profile-siteeventcoupons', $siteeventticketcoupon->event_id, $layout);
            if ($siteevent->photo_id) {
              $data['event_photo_path'] = $siteevent->getPhotoUrl('thumb.icon');
            } else {
              $data['event_photo_path'] = $view->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_siteevent_thumb_icon.png';
            }
            $data['event_title'] = $event_name;

            if ($siteeventticketcoupon->photo_id) {
              $data['coupon_photo_path'] = $siteeventticketcoupon->getPhotoUrl('thumb.icon');
            } else {
              $data['coupon_photo_path'] = $view->layout()->staticBaseUrl . 'application/modules/Siteeventcoupon/externals/images/coupon_thumb.png';
            }

            $data['coupon_title'] = $view->htmlLink($newVar.
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('user_id' => $siteeventticketcoupon->owner_id, 'coupon_id' => $siteeventticketcoupon->coupon_id, 'tab' => $coupon_tab_id, 'slug' => $siteeventticketcoupon->getCouponSlug($siteeventticketcoupon->title)), 'siteeventcoupon_view', true), $siteeventticketcoupon->title, array('style' => 'color:#3b5998;text-decoration:none;'));

            $data['coupon_code'] = $siteeventticketcoupon->coupon_code;
            $data['coupon_time'] = gmdate('M d, Y', strtotime($siteeventticketcoupon->end_time));
            $data['coupon_time_setting'] = $siteeventticketcoupon->end_settings;
            $data['enable_mailtemplate'] = Engine_Api::_()->hasModuleBootstrap('sitemailtemplates');
            $data['discount_amount'] = $this->getPriceWithCurrency($discount_amount);
            $data['order_no'] = '<a href="' . $newVar. $view_url . '">#' . $order->order_id . '</a>';
            
            // INITIALIZE THE STRING TO BE SEND IN THE CLAIM MAIL
            $template_header = "";
            $template_footer = "";
            $string = $view->couponmail($data);
          }

            $sellerObj = Engine_Api::_()->getItem('user', $siteevent->owner_id);

            // SEND NOTIFICATION TO SELLER
            if(!empty($params['notification_seller'])) {
                $notification_table->addNotification($sellerObj, $buyer, $order, 'siteeventticket_order_place', array('order_id' => $order_no, 'siteevent' => array($siteevent->getType(), $siteevent->getIdentity())));
            }

            $order_no = '<a href="' . $newVar. $view_url . '">#' . $order->order_id . '</a>';

            // SEND MAIL TO SELLER
            if(!empty($params['seller_email'])) {
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($sellerObj, 'siteeventticket_order_place_to_seller', array(
                 'event_title' => $siteevent->getTitle(),
                 'event_name' => $event_name,
                 'order_invoice' => $view->ticketOrderInvoice($order),
                ));
            }
        // SEND MAIL TO SITE ADMIN FOR THIS ORDER
        $eventOwnerId = $siteevent->getOwner()->getIdentity();
        if (!empty($eventOwnerId))
            $eventOwnerObj = Engine_Api::_()->getItem('user', $eventOwnerId);

            $admin_email_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.contact', null);
            if (!empty($params['admin_email']) && !empty($admin_email_id) && ($eventOwnerObj->email != $admin_email_id)) {

            Engine_Api::_()->getApi('mail', 'core')->sendSystem($admin_email_id, 'siteeventticket_order_place_to_admin', array(
             'event_title' => $siteevent->getTitle(),
             'event_name' => $event_name,
             'order_invoice' => $view->ticketOrderInvoice($order),
            ));
        }

        // SEND MAIL TO BUYER
        if (empty($order->user_id))
          $order_no = '#' . $order->order_id;
        else {
          $order_no = $newVar. $view->url(array('controller' => 'order', 'action' => 'view', 'order_id' => $order->order_id, 'event_id' => $order->event_id), 'siteeventticket_order', true);
          $order_no = '<a href="' . $order_no . '">#' . $order->order_id . '</a>';
        }
        
        $my_tickets_link_flag = $my_tickets_link = $newVar. $view->url(array('action' => 'my-tickets', 'order_id' => $order->order_id, 'event_id' => $order->event_id), 'siteeventticket_order', true);
        $my_tickets_link = '<a href="' . $my_tickets_link . '">' . $view->translate('Tickets') . '</a>';      
        $log_in_link = '<a href="' . $my_tickets_link_flag . '">' . $view->translate('Log in') . '</a>';
        
        $orderTickets = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket')->getOrderTickets($order->order_id);
$orderTicket =  $orderTickets[0];
        $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        
        if($datetimeFormat == 'full') {
            $occurrence_startdate = $view->locale()->toDate($orderTicket->occurrence_starttime, array('size' => $datetimeFormat));
            $occurrence_enddate = $view->locale()->toDate($orderTicket->occurrence_endtime, array('size' => $datetimeFormat));                
        }
        else {
            $occurrence_startdate = $view->locale()->toDate($orderTicket->occurrence_starttime, array('format' => 'EEEE')) . ', ' . $view->locale()->toDate($orderTicket->occurrence_starttime, array('size' => $datetimeFormat));
            $occurrence_enddate = $view->locale()->toDate($orderTicket->occurrence_endtime, array('format' => 'EEEE')) . ', ' . $view->locale()->toDate($orderTicket->occurrence_endtime, array('size' => $datetimeFormat));                
        }

        $occurrence_starttime = $view->locale()->toEventTime($orderTicket->occurrence_starttime, array('size' => $datetimeFormat));                       
        $order_creation_date = $view->locale()->toDate($order->creation_date, array('size' => $datetimeFormat));
        $order_creation_date = $order_creation_date." - ".$view->locale()->toEventTime($order->creation_date, array('size' => $datetimeFormat)); 
       
        $ticket_details_template = "<div><table width='100%' cellspacing='0' cellpadding='20' ><tr><td style='border-bottom:1px solid #dddddd;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'><strong>".$view->translate('Purchase Date')."</strong><br>".$order_creation_date."</td><td align='right' style='border-bottom:1px solid #dddddd;border-right:1px solid #dddddd;border-top:1px solid #dddddd;'><strong>".$view->translate('Invoice')."</strong><br>#".$order->order_id."</td></tr><tr><td colspan='2' style='border:1px solid #dddddd'><table width='100%' cellspacing='0' cellpadding='0' border='0'>";  
        
				$totalOrders = COUNT($orderTickets);
				$totalCount = 0;
				$borderBottom = 'border-bottom:1px black dotted';
        foreach($orderTickets as $orderTicket) {
					  $totalCount++;
						if($totalCount == $totalOrders) {
							$borderBottom = '';
						}
                        
                    if($orderTicket->discounted_price > 0) {
                        $priceValue = $this->getPriceWithCurrency($orderTicket->discounted_price);
                    }   
                    else {
                        $priceValue = $view->translate("Free");
                    }
                        
            $ticket_details_template .= "<tr><td valign='top' align='left' style='$borderBottom'><p style='margin:0.3em 0 0.6em 0;font-weight:bold'>".$orderTicket->title."</p></td><td width='100px' valign='top' align='center' style='border-left:1px black dotted;$borderBottom'><p style='margin:0.3em 0 1em 0;font-weight:bold;text-align:right;'>".$priceValue."</p></td></tr>";
        }
        
        $ticket_details_template .= "</table></td></tr><tr><td style='border-bottom:1px black dotted' colspan='2'><p style='margin:12px 0 0 0;font-size:0.7em'>".$view->translate('EVENT NAME')."</p><p style='margin:0 0 12px 0;font-size:1em;font-weight:bold'>".$siteevent->getTitle().", <span><span>".$occurrence_startdate.$view->translate(' To ').$occurrence_enddate."</span></span></p></td></tr><tr><td style='border-bottom:1px black dotted'><p style='margin:12px 0 0 0;font-size:0.7em'>".$view->translate('START DATE')."</p><p style='margin:0 0 12px 0;font-size:1em;font-weight:bold'>".$occurrence_startdate."</p></td><td style='border-bottom:1px black dotted;border-left:1px black dotted;padding-left:10px'><p style='margin:12px 0 0 0;font-size:0.7em'>".$view->translate('TIME')."</p><p style='margin:0 0 12px 0;font-size:1em;font-weight:bold'>".$occurrence_starttime."</p></td></tr><tr><td style='border-bottom:1px black dotted'><p style='margin:12px 0 0 0;font-size:0.7em'>".$view->translate('ADDRESS')."</p><p style='margin:0 0 12px 0;font-size:1em;font-weight:bold'>".$siteevent->location."</p></td><td valign='middle' style='border-bottom:1px black dotted;border-left:1px black dotted;padding-left:10px'><p style='margin:12px 0 0 0;font-size:0.7em'>".$view->translate('CONTACT')."</p><p style='margin:0 0 12px 0;font-size:1em;font-weight:bold'>".$view->translate('Questions about the event?')."<br>".$view->translate('E-mail the event owner')."<br><a target='_blank' href='mailto:".$sellerObj->email."'>".$sellerObj->email."</a></p></td></tr></table></div><br>";   
        
        $mailParams = array(
                 'event_name' => $event_name,
                 'site_name' => Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 'Advertisement'),
                 'event_title' => $siteevent->getTitle(),
                 'my_tickets_link' => $my_tickets_link,
                 'ticket_details_template' => $ticket_details_template,
                 'log_in_link' => $log_in_link,
                );
        
        if (Engine_Api::_()->hasModuleBootstrap('sitemailtemplates') && file_exists('application/libraries/dompdf/dompdf_config.inc.php')) {
            
            $dirPath = APPLICATION_PATH . '/temporary/siteeventticket_ordertickets';
            if( is_writable($dirPath) || (!@is_dir($dirPath) && @mkdir($dirPath, 0777, true))) {
    
//                if ( file_exists(APPLICATION_PATH_SET . DS . 'cache.php') ) {
//                  $cache_array = include APPLICATION_PATH_SET . DS . 'cache.php';
//                  if ( isset($cache_array['frontend']['core']['gzip']) && !empty($cache_array['frontend']['core']['gzip']) ) {
//                    if ( ob_get_level() ) {
//                      ob_end_clean();
//                    }       
//                  }
//                }
                
                require_once("application/libraries/dompdf/dompdf_config.inc.php");

                $html = $view->action("print-ticket", 'order', 'siteeventticket', array('order_id' => $this->getDecodeToEncode($order->order_id), 'generatePdf' => 1));
                $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
                $dompdf = new DOMPDF();
                @$dompdf->load_html($html);
                @$dompdf->render();                

                $fileName = "$dirPath/TICKET_$order->order_id".".pdf";
                //$ticket_attachment = $dompdf->stream($fileName);//DOWNLOAD PDF    
                $output = @$dompdf->output();
                file_put_contents($fileName, $output);
                @chmod($fileName, 0777);

                $mailParams = array_merge($mailParams, array('ticket_attachment' => $fileName));
            }            
        }
        
        if(!empty($params['buyer_email'])) {
        $buyerEmailIds = Engine_Api::_()->getDbTable('buyerdetails', 'siteeventticket')->getBuyerEmailIds($order_id);
        if(COUNT($buyerEmailIds) > 0) {
            foreach($buyerEmailIds as $buyerEmailId) {
                if(empty($buyerEmailId->email)) {
                    continue;
                }
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($buyerEmailId->email, "siteeventticket_order_place_by_member", $mailParams);
            }
        }
        else {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($billing_email_id, "siteeventticket_order_place_by_member", $mailParams);            
        }
        }
      
      //Removed the case: payment pending.
  }
  
  /**
   * Get The Order Ticket Information
   * @param $getOrderType array
   * @return Boolean
   */
  public function setOrderTicketInfo($getOrderType = array()) {
    if ( !empty($getOrderType) ) {
      $event_table = Engine_Api::_()->getDbtable('events', 'siteevent');

      $event_otherinfo_table = Engine_Api::_()->getDbtable('otherinfo', 'siteevent');
      $event_otherinfo_table_name = $event_otherinfo_table->info('name');

      $location_table_name = Engine_Api::_()->getDbtable('locations', 'siteevent')->info('name');

      $select = $event_table->select()
              ->from($event_otherinfo_table_name, array('phone', 'website', 'email'))
              ->setIntegrityCheck(false)
              ->joinleft($location_table_name, "($location_table_name.event_id = $event_otherinfo_table_name.event_id)", array("address", "city", "state", "country"))
              ->where("$event_otherinfo_table_name.event_id = ?", $event_id);

      $page_address = $event_table->fetchRow($select);
      $tempGetType = $page_address->getType();
      return $tempGetType;
    } else {
      $isEnabled = Engine_Api::_()->siteevent()->isEnabled();
      $siteeventticketGetShowViewType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.getshow.viewtype', null);
      $siteeventticketordertypeInfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticketordertype.info', null);
      $siteeventticketLsettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.lsettings', null);
      $tempOrderTicketNumber = null;
      $ticketUploadedByHost = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
      if ( !empty($tempInfoOfEventTicket) ) {
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $order->event_id);
        $event_name = '<a href="' . $newVar . $siteevent->getHref() . '">' . $siteevent->getTitle() . '</a>';

        // TO FETCH BUYER DETAIL
        $buyer = Engine_Api::_()->getItem('user', $order->user_id);
        $billing_email_id = $buyer->email;

        // IF PAYMENT IS COMPLETED, THEN SEND ACTIVITY FEED, NOTIFICATION AND EMAIL

        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        foreach ( $roles as $role ) {
          $auth->setAllowed($order, $role, 'view', 1);
          $auth->setAllowed($order, $role, 'comment', 1);
        }

        $view_url = $view->url(array('action' => 'view', 'event_id' => $order->event_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order->order_id), 'siteeventticket_order', true);
        $order_no = $view->htmlLink($view_url, '#' . $order->order_id);

        /* Coupon Mail Work */
        if ( !empty($coupon_id) ) {
          $siteeventticketcoupon = Engine_Api::_()->getItem('siteeventticket_coupon', $coupon_id);
          $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.layoutcreate', 0);
          $coupon_tab_id = Engine_Api::_()->siteevent()->GetTabIdinfo('siteeventcoupon.profile-siteeventcoupons', $siteeventticketcoupon->event_id, $layout);
          if ( $siteevent->photo_id ) {
            $data['event_photo_path'] = $siteevent->getPhotoUrl('thumb.icon');
          } else {
            $data['event_photo_path'] = $view->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_siteevent_thumb_icon.png';
          }
          $data['event_title'] = $event_name;

          if ( $siteeventticketcoupon->photo_id ) {
            $data['coupon_photo_path'] = $siteeventticketcoupon->getPhotoUrl('thumb.icon');
          } else {
            $data['coupon_photo_path'] = $view->layout()->staticBaseUrl . 'application/modules/Siteeventcoupon/externals/images/coupon_thumb.png';
          }

          $data['coupon_title'] = $view->htmlLink($newVar .
                  Zend_Controller_Front::getInstance()->getRouter()->assemble(array('user_id' => $siteeventticketcoupon->owner_id, 'coupon_id' => $siteeventticketcoupon->coupon_id, 'tab' => $coupon_tab_id, 'slug' => $siteeventticketcoupon->getCouponSlug($siteeventticketcoupon->title)), 'siteeventcoupon_view', true), $siteeventticketcoupon->title, array('style' => 'color:#3b5998;text-decoration:none;'));

          $data['coupon_code'] = $siteeventticketcoupon->coupon_code;
          $data['coupon_time'] = gmdate('M d, Y', strtotime($siteeventticketcoupon->end_time));
          $data['coupon_time_setting'] = $siteeventticketcoupon->end_settings;
          $data['enable_mailtemplate'] = Engine_Api::_()->hasModuleBootstrap('sitemailtemplates');
          $data['discount_amount'] = $this->getPriceWithCurrency($discount_amount);
          $data['order_no'] = '<a href="' . $newVar . $view_url . '">#' . $order->order_id . '</a>';

          // INITIALIZE THE STRING TO BE SEND IN THE CLAIM MAIL
          $template_header = "";
          $template_footer = "";
          $string = $view->couponmail($data);
        }
      } else {
        if ( !empty($ticketUploadedByHost) )
          $tempOrderTicketNumber = @md5($ticketUploadedByHost . $siteeventticketLsettings);

        if ( empty($siteeventticketGetShowViewType) && !empty($isEnabled) && ($siteeventticketordertypeInfo != $tempOrderTicketNumber) ) {
          Engine_Api::_()->getApi('settings', 'core')->setSetting('siteeventticket.getview.type', 0);
          Engine_Api::_()->getApi('settings', 'core')->setSetting('siteeventticket.getinfo.type', 0);
          return false;
        }

        return true;
      }
    }
  }

  /**
   * Get An Order Commission
   * @param int $event_id
   * @return array
   */
  public function getOrderCommission($event_id) {
    $eventObj = Engine_Api::_()->getItem('siteevent_event', $event_id);

    $commission = array();
    if(Engine_Api::_()->siteevent()->hasPackageEnable()){
      $packageObj = Engine_Api::_()->getItem('siteeventpaid_package', $eventObj->package_id);
      if (!empty($packageObj->ticket_settings)) {
        $ticketSettings = @unserialize($packageObj->ticket_settings);
        $commission[] = $ticketSettings['commission_handling'];
        if (empty($ticketSettings['commission_handling'])) {
          $commission[] = $ticketSettings['commission_fee'];
        } else {
          $commission[] = $ticketSettings['commission_rate'];
        }
      } else {
        $commission[] = 1;
        $commission[] = 1;
      }
    } else {
      $user = $eventObj->getOwner();
      $commissionHandlingType = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteevent_event', "commission_handling");
      if ($commissionHandlingType != 0 && $commissionHandlingType != 1) {
        $commission[] = 1;
        $commission[] = 1;
      } else {
        $commission[] = $commissionHandlingType;
        if (empty($commissionHandlingType)) {
          $commission[] = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteevent_event', "commission_fee");
        } else {
          $commission[] = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteevent_event', "commission_rate");
        }
      }
    }

    return $commission;
  }

  /**
   * Get Threshold Amount
   * @param int $event_id
   * @return float
   */
  public function getTransferThreshold($event_id) {
    $eventObj = Engine_Api::_()->getItem('siteevent_event', $event_id);

    if(Engine_Api::_()->siteevent()->hasPackageEnable()){
      $packageObj = Engine_Api::_()->getItem('siteeventpaid_package', $eventObj->package_id);
      if (!empty($packageObj->ticket_settings)) {
        $ticketSettings = @unserialize($packageObj->ticket_settings);
        return $ticketSettings['transfer_threshold'];
      }
    } else {
      $getThresholdAmount = Engine_Api::_()->authorization()->getPermission($eventObj->getOwner()->level_id, 'siteevent_event', "transfer_threshold");
      if (!empty($getThresholdAmount)) {
        return $getThresholdAmount;
      }
    }
    return 100;
  }

  /**
   * Get Gateway Name
   * @param int $gateway_id
   * @return string
   */
  public function getGatwayName($gateway_id) {
    switch ($gateway_id) {
      case 1:
        $gateway_name = '2Checkout';
        break;
      case 2:
        $gateway_name = 'PayPal';
        break;
      case 3:
        $gateway_name = 'By Cheque';
        break;
      case 4:
        $gateway_name = 'Pay at the Event';
        break;
      case 5:
        $gateway_name = 'Free Order';
        break;
      default :
        $gateway_name = 'Invalid Payment Method';
    }
    
    if(!empty($gateway_id) && $gateway_name == 'Invalid Payment Method' && Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
        $gateway_title = Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'title', 'gateway_id' => $gateway_id));
        
        $gateway_name = !empty($gateway_title) ? $gateway_title : $gateway_name;
    }
    
    return $gateway_name;
  }

  /**
   * Get Event Address
   * @param int event_id
   * @return event address
   */
  public function getEventAddress($event_id) {
    $event_table = Engine_Api::_()->getDbtable('events', 'siteevent');

    $event_otherinfo_table = Engine_Api::_()->getDbtable('otherinfo', 'siteevent');
    $event_otherinfo_table_name = $event_otherinfo_table->info('name');

    $location_table_name = Engine_Api::_()->getDbtable('locations', 'siteevent')->info('name');

    $select = $event_table->select()
        ->from($event_otherinfo_table_name, array('phone', 'website', 'email'))
        ->setIntegrityCheck(false)
        ->joinleft($location_table_name, "($location_table_name.event_id = $event_otherinfo_table_name.event_id)", array("address", "city", "state", "country"))
        ->where("$event_otherinfo_table_name.event_id = ?", $event_id);

    $page_address = $event_table->fetchRow($select);

    $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
    $address = $siteevent->getTitle() . '<br />';

    if (!empty($page_address->address)) {
      $address .= $page_address->address . '<br />';
    }
    if (!empty($page_address->city)) {
      $address .= @strtoupper($page_address->city) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    if (!empty($page_address->state)) {
      $address .= @strtoupper($page_address->state) . '<br />';
    }
    if (!empty($page_address->country)) {
      $address .= @strtoupper($page_address->country) . '<br />';
    }
    if (!empty($page_address->phone)) {
      $address .= 'PHONE: ' . $page_address->phone . '<br />';
    }
    if (!empty($page_address->website)) {
      $address .= 'WEBSITE: ' . $page_address->website . '<br />';
    }
    if (!empty($page_address->email)) {
      $address .= 'EMAIL: ' . $page_address->email . '<br />';
    }
    return $address;
  }

  /**
   * Get Currency Symbol
   *
   * @return string
   */
  public function getCurrencySymbol() {

    $localeObject = Zend_Registry::get('Locale');
    $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $currencySymbol = Zend_Locale_Data::getContent($localeObject, 'currencysymbol', $currencyCode);

    return $currencySymbol;
  }

  public function isTaxRateMandatoryMessage($event_id) {

    //IF TAX IS MANDATORY AND NOT SET THEN DISPLAY A TIP
    $taxEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.tax.enabled', 0);
    $taxMandatory = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.tax.mandatory', 0);
    $tax_rate = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getColumnValue($event_id, 'tax_rate');
    if ($taxEnabled && $taxMandatory && (empty($tax_rate) || $tax_rate <= 0)) {
      return true;
    }
    return false;
    //END
  }

  public function getContentId($params = array()) {

    //GET CONTENT TABLE
    $tableContent = Engine_Api::_()->getDbtable('content', 'core');

    //GET PAGE TABLE
    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $pageTableName = $pageTable->info('name');

    //GET PAGE ID
    $page_id = $pageTable->select()
        ->from($pageTableName, array('page_id'))
        ->where('name = ?', $params['page_name'])
        ->query()
        ->fetchColumn();

    if (empty($page_id)) {
      return 0;
    }

    $content_id = $tableContent->select()
        ->from($tableContent->info('name'), array('content_id'))
        ->where('page_id = ?', $page_id)
        ->where('name = ?', $params['widget_name'])
        ->query()
        ->fetchColumn();

    return $content_id;
  }

  
  public function buyerTicketIdGenerate() {
    
   $today_date = date("dm");//eg. 2104
   $today_time = date("Hs");//eg. 0923
   $rand2 = mt_rand(10, 90);//eg. 30
    
   $ticket_id = $this->randStrGen("1").$rand2.$this->randStrGen("1").$today_date.$this->randStrGen("1").$today_time;  //eg. Z29M2104I0907
    return $ticket_id;
  }
  
  public function randStrGen($len) {
    $rand_char = "";
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $charArray = str_split($chars);
    for ($i = 0; $i < $len; $i++) {
      $randItem = array_rand($charArray);
      $rand_char .= "" . $charArray[$randItem];
    }
    return $rand_char;
  }
  
  public function bookNowButton($siteevent, $occurrence_id = 0) {
      
    if($siteevent->closed) {
        return false;
    }

    $lastOccurrenceDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($siteevent->getIdentity(), 'DESC', $occurrence_id);

    if (strtotime($lastOccurrenceDate) <= time()) {
        return false;
    }

    $ticketCount = Engine_Api::_()->getDbTable('tickets', 'siteeventticket')->getTicketsBySettings(array('event_id' => $siteevent->getIdentity(), 'status' => array('open'), 'is_same_end_date' => 0, 'sell_endtime' => 1, 'limit' => 1, 'ticktsCountOnly' => 1));

    if(empty($ticketCount)) {
        return false;
    }      

    return true;
  }
  
  public function updateTicketsSellEndTime($siteevent) {

    $ticketTable = Engine_Api::_()->getDbTable('tickets', 'siteeventticket');
    $tickets = $ticketTable->getTicketsBySettings(array('event_id' => $siteevent->getIdentity(), 'is_same_end_date' => 1));

    $sell_endtime = $occurrenceDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($siteevent->getIdentity(), 'DESC');

    if (Engine_Api::_()->hasModuleBootstrap('siteeventrepeat') && !empty($siteevent->repeat_params)) {
        $eventparams = json_decode($siteevent->repeat_params);
        if (!empty($eventparams) && isset($eventparams->endtime) && !empty($eventparams->endtime->date)) {
            $sell_endtime = date('Y-m-d H:i:s', strtotime($eventparams->endtime->date));
            $sell_endtime = (strtotime($occurrenceDate) > strtotime($sell_endtime)) ? $occurrenceDate : $sell_endtime;
        }
    }      

    foreach($tickets as $ticket) {
        $ticket->sell_endtime = $sell_endtime;
        $ticket->save();
    }   
  }  
  
  public function updateTicketsSoldQuantity($params = array()) {
      
        $occurrence_id = $params['occurrence_id'];
        
        $orderTable = Engine_Api::_()->getDbtable('orders', 'siteeventticket');
        $orderTableName = $orderTable->info('name');
        
        $orderTicketTable = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket');
        $occurrenceTable = Engine_Api::_()->getDbtable('occurrences', 'siteevent');
        $ticketTable = Engine_Api::_()->getDbtable('tickets', 'siteeventticket');
        
        $occurrencesRow = $occurrenceTable->fetchRow(array('occurrence_id = ?' => $occurrence_id));
        
        $select = $orderTable->select()
            ->from($orderTableName, array('order_id'))
            ->where("payment_status = 'active' OR (non_payment_seller_reason = 0 && non_payment_admin_reason = 0)")
            ->where('occurrence_id = ?', $occurrence_id);
        
        $ticketDetails = $ticketTable->resetTicketIdSoldArray($occurrencesRow->event_id);
       
        foreach($orderTable->fetchAll($select) as $orders) {

            //INCREASE SOLD COUNT OF CORRESPONDING TICKETS ID IN EVENT_OCCURRENCES TABLE.
            $orderTickets = $orderTicketTable->getOrderTickets($orders->order_id);
            foreach ($orderTickets as $tickets) {
                $ticketDetails["tid_$tickets->ticket_id"] += $tickets->quantity;
            }
        }

        $occurrenceTable->update(array('ticket_id_sold' => $ticketDetails), array('occurrence_id = ?' => $occurrence_id));
  }
  
  public function isAllowPaymentApprove($params = array()) {
      
    $order_id = $params['order_id'];
    $order = Engine_Api::_()->getItem('siteeventticket_order', $order_id);

    $occurrenceTable = Engine_Api::_()->getDbtable('occurrences', 'siteevent');
    $occurrencesRow = $occurrenceTable->fetchRow(array('occurrence_id = ?' => $order->occurrence_id));
    $ticketIdSoldArray = $occurrencesRow->ticket_id_sold;

    $siteevent = $occurrencesRow->getParent();
    if(empty($siteevent->capacity)) {
        return true;
    }

    $orderTicketTable = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket');
    $orderTickets = $orderTicketTable->getOrderTickets($order_id);

    $totalSoldQuantity = $totalOrderedQuantity = 0;
    
    foreach($ticketIdSoldArray as $ticketIdSold) {
        $totalSoldQuantity += $ticketIdSold;
    }
    
    foreach ($orderTickets as $tickets) {
        
        $totalOrderedQuantity += $tickets->quantity;
        
//        $soldQuantity = $ticketIdSold["tid_$tickets->ticket_id"];
//        $orderedQuantity = $tickets->quantity;
//        $ticketObject = Engine_Api::_()->getItem('siteeventticket_ticket', $tickets->ticket_id);
//        if($ticketObject instanceof Core_Model_Item_Abstract) {
//            $availableQuantity = $ticketObject->quantity;
//        }
//        else {
//            continue;
//        }
//        
//        if($availableQuantity < ($soldQuantity + $orderedQuantity)) {
//            return false;
//        }
    }    

    if($siteevent->capacity < ($totalSoldQuantity + $totalOrderedQuantity)) {
        return false;
    }    
    
    return true;
  }
  
  public function getRemainingBillAmount($event_id) {
      
        $eventRemainingBillObj = Engine_Api::_()->getDbtable('remainingbills', 'siteeventticket')->fetchRow(array('event_id = ?' => $event_id));
        
        $tempRemainingBillAmount = !empty($eventRemainingBillObj) ? $eventRemainingBillObj->remaining_bill : 0;

        $paymentFailedBillAmount = Engine_Api::_()->getDbtable('eventbills', 'siteeventticket')->paymentFailedBillAmount($event_id);

        // IF SEELER HAS MAKE PAYMENT AND HIS AMOUNT IS NOT SUBMMITED, THEN ADD IN REMAINING AMOUNT
        if (!empty($paymentFailedBillAmount)) {
            $remainingBillAmount = $tempRemainingBillAmount + $paymentFailedBillAmount;
            Engine_Api::_()->getDbtable('remainingbills', 'siteeventticket')->update(array('remaining_bill' => round($remainingBillAmount, 2)), array('event_id = ?' => $event_id));
            Engine_Api::_()->getDbtable('eventbills', 'siteeventticket')->update(array("status" => "not_paid"), array('event_id =?' => $event_id, "status != 'active'", "status != 'not_paid'"));
        } else {
            $remainingBillAmount = $tempRemainingBillAmount;
        }

        // SUBTRACT NON-PAYMENT ORDERS AMOUNT FROM EVENT BILL
        $notPaidBillAmount = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->notPaidBillAmount($event_id);
        if (!empty($notPaidBillAmount) && ($remainingBillAmount >= $notPaidBillAmount)) {
            $remainingBillAmount -= round($notPaidBillAmount, 2);
            Engine_Api::_()->getDbtable('remainingbills', 'siteeventticket')->update(array('remaining_bill' => round($remainingBillAmount, 2)), array('event_id = ?' => $event_id));
            Engine_Api::_()->getDbtable('orders', 'siteeventticket')->update(array('payment_status' => 'not_paid'), array('event_id = ?' => $event_id, 'direct_payment = 1', 'non_payment_admin_reason = 1', 'order_status = 3', "payment_status != 'not_paid'"));
        }      

        return $remainingBillAmount;
  }
  
  public function isAllowThresholdNotifications($params = array()) {
      
      $event_id = $params['event_id'];
      $settingsApi = Engine_Api::_()->getApi('settings', 'core');
      $thresholdnotificationamount = $settingsApi->getSetting('siteeventticket.thresholdnotificationamount', 100);
      $notificationType = $settingsApi->getSetting('siteeventticket.thresholdnotify', array('owner', 'admin'));
      
      if(!$settingsApi->getSetting('siteeventticket.payment.to.siteadmin', '0') && $settingsApi->getSetting('siteeventticket.thresholdnotification', 0) && !empty($thresholdnotificationamount) && !empty($notificationType)) {

        $remainingBillAmount = $this->getRemainingBillAmount($event_id);
        $newBillAmount = Engine_Api::_()->getDbtable('orders', 'siteeventticket')->getEventBillAmount($event_id);
        $remainingBillAmount = round($remainingBillAmount, 2);
        $totalBillAmount = round(($remainingBillAmount + $newBillAmount), 2);

          if($totalBillAmount >= $thresholdnotificationamount) {
            return true;
          }
      }
      
      return false;
  }
}

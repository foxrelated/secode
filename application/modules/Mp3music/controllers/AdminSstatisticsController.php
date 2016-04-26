<?php
class Mp3music_AdminSstatisticsController extends Core_Controller_Action_Admin
{
   protected $_paginate_params = array();
   public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mp3music_admin_main', array(), 'mp3music_admin_main_sstatistics');
      $this->_paginate_params['page']   = $this->getRequest()->getParam('page', 1);
     $this->_paginate_params['limit']  = Engine_Api::_()->getApi('settings', 'core')->getSetting('mp3music.songsPerPage', 5);
  }
  public function indexAction()
  {
        $group_stat = array('stat_songs'=>'Sold Songs','stat_albums'=>'Sold Albums','stat_total'=>'Total');
        $user_id = null;
        $toDateTimeStamp = "";
        $fromDateTimeStamp = "";
        $fromDateTrackingTimeStamp = "";
        $toDateTrackingTimeStamp = "";
        if ($this->getRequest()->getParam('filter_stat')) 
        {
            $fromDate = $this->getRequest()->getParam('fromDate');
            $toDate = $this->getRequest()->getParam('toDate');
            $toDateTimeStamp = $toDate;
            $fromDateTimeStamp = $fromDate;
 
        }
        if ($this->getRequest()->getParam('filter_tracking')) 
        {
            
            $fromDateTracking = $this->getRequest()->getParam('fromDateTracking');
            $toDateTracking = $this->getRequest()->getParam('toDateTracking');
            $fromDateTrackingTimeStamp = $fromDateTracking;
            $toDateTrackingTimeStamp = $toDateTracking;
           
        }  
        list($histories,$count) = Mp3music_Api_Cart::getHistories($user_id,$fromDateTimeStamp,$toDateTimeStamp);
         $params = array_merge($this->_paginate_params, array(
        'user_id' => $user_id,'fromDate'=>$fromDateTrackingTimeStamp,'toDate' =>$toDateTrackingTimeStamp,
        ));
        $transtracking = Mp3music_Api_Cart::getTrackingTransaction($params);
        list($totalHistories,$count2) = Mp3music_Api_Cart::getSumHistories($user_id,$fromDateTimeStamp,$toDateTimeStamp);
        $values = array();
        $values['fromDateTracking'] = $fromDateTrackingTimeStamp;
        $values['toDateTracking'] = $toDateTrackingTimeStamp;
        $values['filter_tracking'] = true;
        $this->view->formValues = $values; 
        $this->view->group_stat = $group_stat;
        $this->view->histories = $histories;
        $this->view->fromDate = $fromDateTimeStamp;
        $this->view->fromDateTracking = $fromDateTrackingTimeStamp;
        $this->view->toDate = $toDateTimeStamp;
        $this->view->toDateTracking = $toDateTrackingTimeStamp;
        $this->view->totalHistories = $totalHistories;
        $this->view->transtracking = $his =  $transtracking;
    }
}
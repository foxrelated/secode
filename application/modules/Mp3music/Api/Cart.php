<?php
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(__FILE__))))));
include_once  APPLICATION_PATH . '/application/modules/Mp3music/externals/scripts/cart/gateway.php';
class Mp3music_Api_Cart extends Core_Api_Abstract
{
    /** 
    * Update total amount of user from request.
    * 
    * @param mixed $request_id
    * @param mixed $total_amount
    */
    public function updateTotalAmount($request_id,$total_amount,$is_request = true)
    {
        if ($is_request == true)
        {
            $request = Mp3music_Api_Cart::getPaymentRequest($request_id);
            $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'mp3music');
            $data = array(
                'total_amount' => $total_amount-$request['request_amount']
            );
            $where = $table->getAdapter()->quoteInto('user_id = ?',$request['request_user_id'] );
            $table->update($data, $where);                         
        }
        else
        {
             $account = Mp3music_Api_Cart::getFinanceAccount($request_id);
             $account_id = 0;
             if ( $account == null )
             {
                 $params = array();
                 $params['account_username']= Engine_Api::_()->user()->getViewer()->email;
                 $params['account_password']='';
                 $params['total_amount']= 0;
                 $params['account_status']= 1;
                 $params['payment_type']= 2;
                 $params['user_id']= Engine_Api::_()->user()->getViewer()->getIdentity();
                 $account_id = Mp3music_Api_Account::insertAccount($params);
                 $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'mp3music');
                    $data = array(
                        'total_amount' => $total_amount+$account['total_amount']
                    );
                    $where = $table->getAdapter()->quoteInto('user_id = ?',$account_id);
                    $table->update($data, $where);                         
             }
             else
             {
                   $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'mp3music');
                    $data = array(
                        'total_amount' => $total_amount+$account['total_amount']
                    );
                    $where = $table->getAdapter()->quoteInto('user_id = ?',$account['user_id']);
                    $table->update($data, $where);                         
             }   
        }  
    }
    /**
    * get request information from id
    * 
    * @param mixed $request_id
    */
    public function getPaymentRequest($request_id)
    {
        $r_table = Engine_Api::_()->getDbTable('paymentRequests', 'mp3music');
         $select = $r_table->select() ->setIntegrityCheck(false)  
                    ->from($r_table)
                    ->joinLeft('engine4_mp3music_payment_accounts','engine4_mp3music_payment_accounts.user_id = engine4_mp3music_payment_requests.request_user_id','engine4_mp3music_payment_accounts.*');
        $select->where('paymentrequest_id = ?',$request_id);      
       
        $results =  $r_table->fetchAll($select)->toArray(); 
        return  $results[0]; 
    }
    public function checkPaymentRequest($request_id)
    {
        $r_table = Engine_Api::_()->getDbTable('paymentRequests', 'mp3music');
         $select = $r_table->select() ->setIntegrityCheck(false)  
                    ->from($r_table)->where("request_status = 0");
        $select->where('paymentrequest_id = ?',$request_id);      
       
        $results =  $r_table->fetchAll($select)->toArray(); 
        return  $results[0]; 
    }
    /**
    * update status of payment request
    * 
    * @param mixed $request_id
    * @param mixed $message
    * @param mixed $status
    */
    public function updatePaymentRequest($request_id,$message,$status)
    {
        $table  = Engine_Api::_()->getDbtable('paymentRequests', 'mp3music');
        $data = array(
            'request_status' => $status,
            'request_answer' => $message
        );
        $where = $table->getAdapter()->quoteInto('paymentrequest_id = ?',$request_id);
        return $table->update($data, $where);                                               
    }
    /**
    * Save record tracking when admin accept or deny any request from user.
    * 
    * @param mixed $request_id
    */
    public function saveTrackingPayOut($request_id)
    {
        
    }
    /**
    * Save record tracking when user invoice the bill.
    * 
    * @param mixed $params
    * @type default value =  bill
    */
    public function saveTrackingPayIn($params,$type = 'bill')
    {
         $db = Engine_Db_Table::getDefaultAdapter();
         $db->beginTransaction(); 
         $t_table = Engine_Api::_()->getDbTable('transactionTrackings', 'mp3music'); 
         switch($type)
         {
             case 'bill':
             default:
                $bill_details = unserialize($params[0]['params']); 
                $acc = Mp3music_Api_Cart::getFinanceAccount($bill_details['user_id']);  
                foreach ($bill_details['items'] as $item)
                {
                     $t = $t_table->createRow();
                     $t->transaction_date = $params[0]['date_bill'];
                     $t->user_seller = $item['owner_id'] ;
                     $t->user_buyer =   $bill_details['user_id'];
                     $t->item_id   =     $item['item_id'];
                     $t->item_type =     $item['type'];
                     $t->amount    =      $item['amount']; 
                     $t->account_seller_id  =  $item['account_id'];  
                     $t->account_buyer_id   =  $acc['paymentaccount_id']; 
                     $t->transaction_status =   $params[0]['bill_status'];
                     $t->params             =   'buy';
                     $t->save();
                }
                break;
         }
    }
    /**
    * set default values for settings group member.
    * If the value does not exist, It will be set to default.
    *  
    * @param mixed $settings
    */
    public function setDefaultValueSelling($settings = array())
    {
       if ( !isset($settings['comission_fee'])) 
       {
           $settings['comission_fee'] = 0;
       }
       if ( !isset($settings['min_payout'])) 
       {
           $settings['min_payout'] = 30;
       }if ( !isset($settings['max_payout'])) 
       {
           $settings['max_payout'] = 100;
       }if ( !isset($settings['can_buy_song'])) 
       {
           $settings['can_buy_song'] = 0;
       }
       if ( !isset($settings['can_sell_song'])) 
       {
           $settings['can_sell_song'] = 0;
       }
       if ( !isset($settings['min_price_song'])) 
       {
           $settings['min_price_song'] = 0;
       }
       if ( !isset($settings['method_payment'])) 
       {
           $settings['method_payment'] = 1;
       }
       return $settings;
       
       
    }
    /**
    * Get selling setting of user groups.
    * 
    * @param mixed $user_group_id
    */
    public function getSettingsSelling($user_group_id)
    {
        $ss_table = Engine_Api::_()->getDbTable('sellingSettings', 'mp3music');
        $ss_name  = $ss_table->info('name');
        $select = $ss_table->select()
                    ->from($ss_table)->where("$ss_name.user_group_id = ?",$user_group_id);
        $settings_ar = $ss_table->fetchAll($select)->toArray();
        $settings = array();
        foreach($settings_ar as $ar )
        {
             $settings[$ar['name']] = $ar['default_value'];  
        }
        return $settings;
    }
    /**
    * Save settings for user group.
    * 
    * @param mixed $settings
    * @param mixed $user_group_id
    */
    public function saveSettingsSelling($settings,$user_group_id)
    {
         $table  = Engine_Api::_()->getDbtable('sellingSettings', 'mp3music');
         $where = $table->getAdapter()->quoteInto('user_group_id = ?', $user_group_id);
         $table->delete($where);
         foreach($settings as $key=>$value)
         {
            if ($key != 'select_group_member')           
            {
                $ss = $table->createRow(); 
                $ss->user_group_id = $user_group_id; 
                $ss->module_id = 'mp3music'; 
                $ss->name = $key ; 
                $ss->default_value = $value ;
                $ss->save();
            }
         }
    }
    /**
    * get hitories of user from Date to Date
    * If user_id = null: get all users histories
    * 
    * @param mixed $user_id
    * @param mixed $fromDate
    * @param mixed $toDate
    * @param mixed $params : more conditions add here. Example paginator..
    */
    public function updateHistories($bill,$type = 'bill',$timestamp)
    {
        $object = $bill[0];
        switch($type)
        {
            case 'bill':
            default:
                $bill_details = unserialize($object['params']);
                $total = $bill_details['total_amount'];
                $number_songs = 0;
                $number_albums = 0;
                foreach ($bill_details['items'] as $it)
                {
                    if ($it['type'] =='song')
                    {
                        $number_songs++;
                    }
                    if ($it['type'] =='album')
                    {
                        $number_albums++;
                    }
                }
             
                $history = Mp3music_Api_Cart::getHistory($timestamp);
                $params =  Mp3music_Api_Cart::getParamHistory(array('sold_songs'=>$number_songs,'sold_albums'=>$number_albums,'total_amount'=>$total,'transaction_succ'=>$object['status_bill']));    
               
                if ($history == null)
                {
                    //insert new history
                    Mp3music_Api_Cart::insertHistory($timestamp,$params);
                    
                    
                }
                else
                {
                    //update infor
                    $params = $history;
                    $params['selling_sold_songs'] = $params['selling_sold_songs']+  $number_songs;
                    $params['selling_sold_albums'] = $params['selling_sold_albums']+  $number_albums;
                    $params['selling_total_amount'] = $params['selling_total_amount']+  $total ;
                    $params['selling_transaction_succ'] = $params['selling_transaction_succ']+  $object['status_bill'] ;
                   
                    Mp3music_Api_Cart::updateHistory($timestamp,$params);
                    
                    
                }
                
            break;
        }
    }
    /**
    * Init param for history
    * If value doesn't exist. It will be set to default value (zero)
    * 
    * @param mixed $object
    * @return string
    */
    public function getParamHistory($object)
    {
        $params = array();
        if (isset($object['upload_songs']))
        {
           $params['selling_total_upload_songs']  = $object['upload_songs'];
        }
        else
        {
           $params['selling_total_upload_songs'] = 0; 
        }
        if (isset($object['download_songs']))
        {
           $params['selling_total_download_songs']  = $object['download_songs'];
        }
        else
        {
           $params['selling_total_download_songs'] = 0; 
        }
        if (isset($object['sold_songs']))
        {
           $params['selling_sold_songs']  = $object['sold_songs'];
        }
        else
        {
           $params['selling_sold_songs'] = 0; 
        }
        if (isset($object['sold_albums']))
        {
           $params['selling_sold_albums']  = $object['sold_albums'];
        }
        else
        {
           $params['selling_sold_albums'] = 0; 
        }
        if (isset($object['new_accounts']))
        {
           $params['selling_final_new_account']  = $object['new_accounts'];
        }
        else
        {
           $params['selling_final_new_account'] = 0; 
        }
        if (isset($object['transaction_succ']))
        {
           $params['selling_transaction_succ']  = $object['transaction_succ'];
        }
        else
        {
           $params['selling_transaction_succ'] = 0; 
        }
        if (isset($object['transaction_fail']))
        {
           $params['selling_transaction_fail']  = $object['transaction_fail'];
        }
        else
        {
           $params['selling_transaction_fail'] = 0; 
        }
        if (isset($object['total_amount']))
        {
           $params['selling_total_amount']  = $object['total_amount'];
        }
        else
        {
           $params['selling_total_amount'] = 0; 
        }
        if (isset($object['params']))
        {
           $params['params']  = serialize($object['params']);
        }
        else
        {
           $params['params'] = ''; 
        }
        return $params;
        
        
    }
    public function updateHistory($timestamp,$params = array())
    {
        $params['selling_datetime'] = $timestamp;
        $table  = Engine_Api::_()->getDbtable('sellingHistorys', 'mp3music');
        $data = array(
            'selling_datetime' => $params['selling_datetime'] ,
            'selling_total_upload_songs' => $params['selling_total_upload_songs'] ,
            'selling_total_download_songs' => $params['selling_total_download_songs'] ,
            'selling_sold_songs' => $params['selling_sold_songs'] ,
            'selling_sold_albums' => $params['selling_sold_albums'] ,
            'selling_final_new_account' => $params['selling_final_new_account'] ,
            'selling_transaction_succ' => $params['selling_transaction_succ'] ,
            'selling_transaction_fail' => $params['selling_transaction_fail'] ,
            'selling_total_amount' => $params['selling_total_amount'] ,
            'params' => $params['params']
        );
        $where = $table->getAdapter()->quoteInto('sellinghistory_id = ?', $params['sellinghistory_id']);
        return $table->update($data, $where);
    }
    /**
    *  Insert new history
    * 
    * @param mixed $params
    */
    public function insertHistory($timestamp,$params = array())
    {
         $params['selling_datetime'] = $timestamp;
         $db = Engine_Db_Table::getDefaultAdapter();
         $db->beginTransaction(); 
         $h_table = Engine_Api::_()->getDbTable('sellingHistorys', 'mp3music'); 
         $h = $h_table->createRow();
         $h->selling_datetime  = $params['selling_datetime'];
         $h->selling_total_upload_songs  = $params['selling_total_upload_songs'];
         $h->selling_total_download_songs  = $params['selling_total_download_songs'];
         $h->selling_sold_songs  = $params['selling_sold_songs'];
         $h->selling_sold_albums  = $params['selling_sold_albums'];
         $h->selling_final_new_account  = $params['selling_final_new_account'];
         $h->selling_transaction_succ  = $params['selling_transaction_succ'];
         $h->selling_transaction_fail  = $params['selling_transaction_fail'];
         $h->selling_total_amount  = $params['selling_total_amount'];
         $h->params  = $params['params'];
         $h->save(); 
         try {
              $db->commit();
          } catch (Exception $ex) {
              $db->rollback();
              break;
          }  
    }
    /**
    * get history in date
    * 
    * @param mixed $datetime
    */
    public function getHistory($datetime)
    {
        $h_table = Engine_Api::_()->getDbTable('sellingHistorys', 'mp3music');
        $h_name  = $h_table->info('name');
        $select = $h_table->select()
                    ->from($h_table);
       $select->where('selling_datetime = ?',$datetime);            
       $results =  $h_table->fetchAll($select)->toArray(); 
        return  $results;
    }
    /**
    * get histories of user(s)
    * 
    * @param mixed $datetime
    * @param mixed $user_id
    * @param mixed $fromDate
    * @param mixed $toDate
    * @param mixed $params
    * @return mixed
    */
    public function getHistories($user_id =  null ,$fromDate = null ,$toDate = null,$params = array())
    {
        
        $condition = array();
        $condition =" 1=1 ";
        if ($user_id != null)
        {
            $condition .= " AND his.user_id = ".$user_id;
        }
        if ($fromDate != null)
        {
            $condition .= " AND DATEDIFF(DATE_FORMAT( FROM_UNIXTIME(his.transaction_date),'%Y-%m-%d'),'".$fromDate."')>=0";
        }
        if ($toDate != null)
        {
            $condition .= " AND DATEDIFF(DATE_FORMAT( FROM_UNIXTIME(his.transaction_date),'%Y-%m-%d'),'".$toDate."')<=0";
        }
        foreach($params as $key=>$value)
        {
            if ( $key != 'limit' && $key!='group_by')
                $condition .= $value;
        }
        if(!isset($params['limit']))
            $params['limit'] = 50;
        $count = 0;
        if(!isset($params['group_by']))
        {
            $count = 0;
            $t_table  = Engine_Api::_()->getDbTable('transactionTrackings', 'mp3music');
            $t_name   = $t_table->info('name');
            $select   = $t_table->select()->setIntegrityCheck(false)
                                ->from("$t_name as his",array("DATE_FORMAT( FROM_UNIXTIME(his.transaction_date),'%Y-%m-%d' ) as pDate",
                                "(SELECT count(*) FROM ".$t_name." as t1 WHERE DATE_FORMAT( FROM_UNIXTIME(t1.transaction_date),'%Y-%m-%d') = pDate and item_type='song' and item_id>0 and transaction_status =1) as selling_sold_songs",
                                "(SELECT count(*) FROM ".$t_name." as t1 WHERE DATE_FORMAT( FROM_UNIXTIME(t1.transaction_date),'%Y-%m-%d') = pDate and item_type='album' and item_id>0 and transaction_status =1) as selling_sold_albums",
                                "(SELECT count(*) FROM ".$t_name." as t1 WHERE DATE_FORMAT( FROM_UNIXTIME(t1.transaction_date),'%Y-%m-%d') = pDate and item_id > 0 and transaction_status = 0) as selling_transaction_fail",
                                "(SELECT count(*) FROM ".$t_name." as t1 WHERE DATE_FORMAT( FROM_UNIXTIME(t1.transaction_date),'%Y-%m-%d') = pDate and item_id > 0 and transaction_status = 1) as selling_transaction_succ",
                                "(SELECT sum(amount) FROM ".$t_name." as t1 WHERE DATE_FORMAT( FROM_UNIXTIME(t1.transaction_date),'%Y-%m-%d') = pDate and (item_type='song' or item_type='album') and item_id>0 and transaction_status = 1 ) as selling_total_amount"
                            ));
            $select ->where($condition)
                    ->group("DATE_FORMAT( FROM_UNIXTIME(his.transaction_date),'%Y-%m-%d')")
                    ->order("DATE_FORMAT( FROM_UNIXTIME(his.transaction_date),'%Y-%m-%d') DESC")
                    ->limit($params['limit']);
            $histories =  $t_table->fetchAll($select)->toArray();
            $count = count($histories);           
            return array($histories,$count);
        }
        else
        {
            $count = 0; 
            $t_table  = Engine_Api::_()->getDbTable('transactionTrackings', 'mp3music');
            $t_name   = $t_table->info('name');
            $select   = $t_table->select()->setIntegrityCheck(false)
                                ->from("$t_name as his",array("DATE_FORMAT( FROM_UNIXTIME(his.transaction_date),'%Y-%m-%d' ) as pDate",
                                "(SELECT count(*) FROM ".$t_name." as t1 WHERE DATE_FORMAT( FROM_UNIXTIME(t1.transaction_date),'%Y-%m-%d') = pDate and item_type='song' and item_id>0 and transaction_status =1) as selling_sold_songs",
                                "(SELECT count(*) FROM ".$t_name." as t1 WHERE DATE_FORMAT( FROM_UNIXTIME(t1.transaction_date),'%Y-%m-%d') = pDate and item_type='album' and item_id>0 and transaction_status =1) as selling_sold_albums",
                                "(SELECT count(*) FROM ".$t_name." as t1 WHERE DATE_FORMAT( FROM_UNIXTIME(t1.transaction_date),'%Y-%m-%d') = pDate and item_id > 0 and transaction_status = 0) as selling_transaction_fail",
                                "(SELECT count(*) FROM ".$t_name." as t1 WHERE DATE_FORMAT( FROM_UNIXTIME(t1.transaction_date),'%Y-%m-%d') = pDate and item_id > 0 and transaction_status = 1) as selling_transaction_succ",
                                "(SELECT sum(amount) FROM ".$t_name." as t1 WHERE DATE_FORMAT( FROM_UNIXTIME(t1.transaction_date),'%Y-%m-%d') = pDate and (item_type='song' or item_type='album') and item_id>0 and transaction_status = 1 ) as selling_total_amount"
                            ));
            $select ->where($condition)
                    ->group($params['group_by'])
                    ->order("DATE_FORMAT( FROM_UNIXTIME(his.transaction_date),'%Y-%m-%d') ASC")
                    ->limit($params['limit']);
            $histories =  $t_table->fetchAll($select)->toArray();
            $count = count($histories);           
            return array($histories,$count);
        }
       
     }
     /**
     * get total statistic of music sharing.
     * 
     * @param mixed $user_id
     * @param mixed $fromDate
     * @param mixed $toDate
     * @param mixed $params
     * @return mixed
     */
    public function getSumHistories($user_id =  null ,$fromDate = null ,$toDate = null,$params = array())
    {
        
        if(!isset($params['group_by']))
        {
              list($histories,$count) = Mp3music_Api_Cart::getHistories($user_id,$fromDate,$toDate,$params);
              $sumHistories = array(
                        'pDate'=>date('Y-m-d'),
                        'upload_song'=>0,
                        'download_song'=>0,
                        'sold_song'=>0,
                        'sold_album'=>0,
                        'selling_new_account'=>0,
                        'transaction_succ'=>0,
                       
                        'transaction_fail'=>0,
                         'total_amount'=>0,
                        );
                
              foreach($histories as $his)
              {
                    
                    $sumHistories['sold_song'] += $his['selling_sold_songs'];
                    $sumHistories['sold_album'] += $his['selling_sold_albums'];
                    $sumHistories['transaction_succ'] += $his['selling_transaction_succ'];
                    $sumHistories['transaction_fail'] += $his['selling_transaction_fail'];
                    $sumHistories['total_amount'] += $his['selling_total_amount'];
              }
              return array($sumHistories,1);
        }
        else
        {
              list($histories,$count)= Mp3music_Api_Cart::getHistories($user_id,$fromDate,$toDate); 
              $result = array();
              foreach($histories as $his)         
              {
                    $date = explode('-',$his['pDate']);
                    if($params['group_by'] == ' MONTH(pDate) ')
                        $index = $date[1];
                    if($params['group_by'] == ' YEAR(pDate) ')
                        $index =  $date[0];
                    
                    if(isset($result[$index]))
                    {
                        $result[$index]['sold_song'] += $his['selling_sold_songs'];
                        $result[$index]['sold_album'] += $his['selling_sold_albums'];
                    }
                    else
                    {
                        $result[$index]['sold_song'] = $his['selling_sold_songs'];
                        $result[$index]['sold_album'] = $his['selling_sold_albums'];
                    }
              }
              return array($result,count($result));
        }
        
     }
     /**
     * save request to tracking.
     * 
     * @param mixed $request_id
     * @param mixed $message
     * @param mixed $status
     */
    public function saveTransactionFromRequest($request_id,$message,$status,$adminccount)
    {
        list($count,$request) = Mp3music_Api_Cart::getFinanceAccountRequests("paymentrequest_id = ".$request_id,"",1,1);
        $re = $request[0];
        $insert_item = array($re['request_date'],$re['request_user_id'],$adminccount['user_id'],'','',$re['request_amount'],$re['request_payment_acount_id'],$adminccount['paymentaccount_id'],$status,'request') ;
        //print_r($insert_item); die;
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction(); 
        $l_table = Engine_Api::_()->getDbTable('transactionTrackings', 'mp3music'); 
        $list = $l_table->createRow();
        $list->transaction_date = $insert_item[0];
        $list->user_seller = $insert_item[1];
        $list->user_buyer = $insert_item[2];
        $list->item_id = $insert_item[3];
        $list->item_type = $insert_item[4];
        $list->amount = $insert_item[5];
        $list->account_seller_id = $insert_item[6];
        $list->account_buyer_id = $insert_item[7];
        $list->transaction_status = $insert_item[8];
        $list->params = $insert_item[9];
        $list->save();
       try {
              $db->commit();
          } catch (Exception $ex) {
              $db->rollback();
              break;
          }  
    }
     /**
     * get all transaction from date to date
     * 
     * @param mixed $user_id
     * @param mixed $fromDate
     * @param mixed $toDate
     * @param mixed $params
     * @return mixed
     */
     public function getTrackingTransaction($params)
    {
        $trackingPaginator = Zend_Paginator::factory(Mp3music_Api_Cart::getSelectTrackingTransaction($params));
        if( !empty($params['page']) )
        {
          $trackingPaginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
          $trackingPaginator->setItemCountPerPage($params['limit']);
        }   
        return $trackingPaginator;
    }
     public function getSelectTrackingTransaction($params)
    {
        $t_table  = Engine_Api::_()->getDbTable('transactionTrackings', 'mp3music');
        $t_name   = $t_table->info('name');
        $select   = $t_table->select()->setIntegrityCheck(false)
                            ->from($t_table,array("$t_name.*","DATE( FROM_UNIXTIME( $t_name.transaction_date) ) AS pDate",
                            "(SELECT username FROM engine4_users as pu WHERE pu.user_id = $t_name.user_seller ) as seller_user_name",
                         "(SELECT username FROM engine4_users as pu WHERE pu.user_id = $t_name.user_buyer ) as buyer_user_name",
                         "(SELECT account_username FROM engine4_mp3music_payment_accounts as pu WHERE pu.paymentaccount_id  = $t_name.account_seller_id ) as account_seller_email",
                         "(SELECT account_username FROM engine4_mp3music_payment_accounts as pu WHERE pu.paymentaccount_id  = $t_name.account_buyer_id  ) as account_buyer_email"
                        ));
        if ($params['user_id'] != null)
        {
            $select->where("$t_name.user_seller = ?",$params['user_id']);
            $select->orWhere("$t_name.user_buyer = ?",$params['user_id']);
        }
        if ($params['fromDate'] != null)
        {
            $fromDate =  $params['fromDate'];
            $select->where("DATEDIFF(DATE_FORMAT( FROM_UNIXTIME($t_name.transaction_date),'%Y-%m-%d'),'".$fromDate."')>=0");  
        }
        if ($params['toDate'] != null)
        {
            $toDate =  $params['toDate'];
            $select->where("DATEDIFF(DATE_FORMAT( FROM_UNIXTIME($t_name.transaction_date),'%Y-%m-%d'),'".$toDate."')<=0");  
        } 
        $select->order("$t_name.transaction_date DESC");
        return $select;  
     }
     /**
     * get all finance account .
     * 
     * @param mixed $aConds
     * @param mixed $sSort
     * @param mixed $iPage
     * @param mixed $sLimit
     * @param mixed $bCount
     */
     public function getFinanceAccountRequests($aConds = array(),$sSort = 'last_check_out ASC', $iPage = '', $sLimit = '', $bCount = true)
     {
        $p_table  = Engine_Api::_()->getDbTable('paymentAccounts', 'mp3music');
        $p_name   = $p_table->info('name');
        $iCnt = ($bCount ? 0 : 1);
        $items = array();
        //$con = array();
        if ($bCount ){
             $select   = $p_table->select()
                        ->from("$p_name as ni")
                        ->joinLeft("engine4_users","engine4_users.user_id = ni.user_id",'')
                        ->joinLeft('engine4_mp3music_payment_requests','engine4_mp3music_payment_requests.request_payment_acount_id = ni.paymentaccount_id','')    
                        ->where($aConds); 
            $iCnt = count($p_table->fetchAll($select)->toArray());

        }
        if ($iCnt){
             $select   = $p_table->select()->setIntegrityCheck(false)
                        ->from("$p_name as ni",'ni.*')
                        ->joinLeft("engine4_users","engine4_users.user_id = ni.user_id",'engine4_users.username')
                        ->joinLeft('engine4_mp3music_payment_requests', 'engine4_mp3music_payment_requests.request_payment_acount_id = ni.paymentaccount_id','engine4_mp3music_payment_requests.*')
                        ->where($aConds)
                        ->order($sSort) ; 
                        $items = $p_table->fetchAll($select)->toArray();
        }
        if (!$bCount)
        {
            return $items;
        }
        return array($iCnt, $items);
    }
     public function getFinanceAccountRequestPag($params)
     {
        $requestPaginator = Zend_Paginator::factory(Mp3music_Api_Cart::getFinanceAccountRequestSelects($params));
        if( !empty($params['page']) )
        {
          $requestPaginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
          $requestPaginator->setItemCountPerPage($params['limit']);
        }   
        return $requestPaginator;
    }
     public function getFinanceAccountRequestSelects($params = array())
     {
        $p_table  = Engine_Api::_()->getDbTable('paymentAccounts', 'mp3music');
        $p_name   = $p_table->info('name');
         $select   = $p_table->select()->setIntegrityCheck(false)
                    ->from("$p_name as ni",'ni.*')
                    ->join("engine4_users","engine4_users.user_id = ni.user_id",'engine4_users.username')
                    ->joinLeft('engine4_mp3music_payment_requests', 'engine4_mp3music_payment_requests.request_payment_acount_id = ni.paymentaccount_id','engine4_mp3music_payment_requests.*')
                    ->where('payment_type <> 1 AND paymentrequest_id > 0');
                    if(isset($params['request_status']) && $params['request_status'] != '')
                        $select->where('request_status = ?',$params['request_status']) ;
                    if(isset($params['user_name']) && $params['user_name'] != '')
                    {
                        $keyword = $params['user_name'];
                        $select->where('username Like ?',"%{$keyword}%") ;
                    }
                    $select->order('last_check_out ASC') ; 
          return $select;            
    }
    
     public function getFinanceAccountsPag($params)
     {
        $requestPaginator = Zend_Paginator::factory(Mp3music_Api_Cart::getFinanceAccountsSelects($params));
        if( !empty($params['page']) )
        {
          $requestPaginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
          $requestPaginator->setItemCountPerPage($params['limit']);
        }   
        return $requestPaginator;
    }
     public function getFinanceAccountsSelects($params = array())
     {
        $p_table  = Engine_Api::_()->getDbTable('paymentAccounts', 'mp3music');
        $p_name   = $p_table->info('name');
         $select   = $p_table->select()->setIntegrityCheck(false)
                    ->from("$p_name as ni",'ni.*')
                    ->join("engine4_users","engine4_users.user_id = ni.user_id",'engine4_users.username')
                    ->where('payment_type <> 1');
                    $select->order('last_check_out ASC') ; 
          return $select;            
    }
    /**
    * get finance accounts 
    * 
    * @param mixed $aConds
    * @param mixed $sSort
    * @param mixed $iPage
    * @param mixed $sLimit
    * @param mixed $bCount
    */
      public function getFinanceAccounts($aConds = array(),$sSort = 'last_check_out ASC', $iPage = '', $sLimit = '', $bCount = true)
    {
        $p_table  = Engine_Api::_()->getDbTable('paymentAccounts', 'mp3music');
        $p_name   = $p_table->info('name');
        $iCnt = ($bCount ? 0 : 1);
        $items = array();
        if ($bCount ){
             $select   = $p_table->select()
                        ->from("$p_name as ni")
                        ->joinLeft("engine4_users","engine4_users.user_id = ni.user_id",'')
                        ->where($aConds); 
            $iCnt = count($p_table->fetchAll($select)->toArray());

        }
        if ($iCnt){
             $select   = $p_table->select()->setIntegrityCheck(false)
                        ->from("$p_name as ni",'ni.*')
                        ->joinLeft("engine4_users","engine4_users.user_id = ni.user_id",'engine4_users.username')
                        ->where($aConds)
                        ->order($sSort) ; 
            $items = $p_table->fetchAll($select)->toArray();
        }
        if (!$bCount)
        {
            return $items;
        }

        return array($iCnt, $items);
    }
    /**
    * get FinnceAccount from user_id
    * 
    * @param mixed $user_id
    * @param mixed $payment_type
    */
    public function getFinanceAccount($user_id = null,$payment_type = null)
    {
        $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'mp3music');
        $select = $table->select();
          
        if($user_id != null)
        {
            $select->where('user_id = ?',$user_id);
        }
        if($payment_type != null)
        {
             $select->where('payment_type = ?',$payment_type);  
        }
         $accounts =   $table->fetchAll($select)->toArray();   
         return @$accounts[0];    
    }
    /**
    * insert or update finance account
    * 
    * @param mixed $account
    */
    public function saveFinanceAccount($account)
    {
        if(isset($account['paymentaccount_id']) && $account['paymentaccount_id']>0)
        {
            //update info of this account
             $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'mp3music');
             $data = array(
            'account_username' => $account['account_username']
            );
             $where = $table->getAdapter()->quoteInto('paymentaccount_id = ?', $account['paymentaccount_id']);
             $table->update($data, $where);
        }
        else
        {
           $acc = Engine_Api::_()->getDbtable('paymentAccounts', 'mp3music')->createRow();  
            $acc->account_username = $account['account_username'];
            $acc->payment_type = $account['payment_type'];
            $acc->total_amount = 0;
            $acc->account_status = 1;
            $acc->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $acc->save();

        }
        return $account;
    }
    /**
    * set default value for account if it dose not exist.
    * 
    * @param mixed $account
    */
    public function setDefaultValueAccount($account)
    {
        if(!isset($account['account_username']))
        {
            $account['account_username'] = 'your_email_account@payment.com';
        }
        if(!isset($account['account_password']))
        {
            $account['account_password'] = '';
        }
        if(!isset($account['user_id']))
        {
            $account['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
        }
        if(!isset($account['payment_type']))
        {
            $account['payment_type'] =2;
        }
        if(!isset($account['is_save_password']))
        {
            $account['is_save_password'] =0;
        }
        if(!isset($account['total_amount']))
        {
            $account['total_amount'] = 0;
        }
        if(!isset($account['last_check_out']))
        {
            $account['total_amount'] = '';
        }
        return $account;
    }
    /**
    * Get Security Code  for transcation 
    * 
    */
    public function getSecurityCode()
    {
        $sid = 'abcdefghiklmnopqstvxuyz0123456789ABCDEFGHIKLMNOPQSTVXUYZ';
        $max =  strlen($sid) - 1;
        $res = "";
        for($i = 0; $i<16; ++$i){
            $res .=  $sid[mt_rand(0, $max)];
        }  
        return $res;
    }
    /**
    * get params payment for gateway.
    * 
    * @param mixed $gateway_name
    * @param mixed $returnUrl
    * @param mixed $cancelUrl
    * @param mixed $receivers
    */
    public function getReceivers($gateway_name ='paypal',$method_payment = 'directly',$request = false)
    {
        $request = false;
        if ( $request == false)
        {
            $fee = 0;
            $total_pay = Mp3music_Api_Shop::updateTotalAmount($fee,true);
            $coupon = Mp3music_Api_Shop::getCouponCodeCart();
            $total_pay = $total_pay - $coupon['value']  ;
        }
        else
        {
            $fee = 0;
            $total_pay =Mp3music_Api_Shop::updateTotalAmount($fee,true);
            $settings = Mp3music_Api_Cart::getSettingsSelling(Engine_Api::_()->user()->getViewer()->level_id);
            if ( !isset($settings['comission_fee']))
            {
                $fee = 0;
            }
            else
            {
                $fee = $settings['comission_fee'];
            }
            $fee = $fee*$total_pay;  
            $total_pay = Mp3music_Api_Shop::updateTotalAmount($fee,true);      
            
        }
        
        
        $settings = @Mp3music_Api_Gateway::getSettingGateway($gateway_name);
       
        switch($gateway_name)
        {
            case 'paypal':
            default:
                $settings['params'] = unserialize($settings['params']);
                $receivers = array(
                    array('email' => @$settings['admin_account'],'amount' => $total_pay,'invoice' => Mp3music_Api_Cart::getSecurityCode()),
                 );
            break; 
        }
        return $receivers;
    }
    /**
    * Return param format of payment gateway.
    * 
    * @param mixed $gateway_name
    * @param mixed $returnUrl
    * @param mixed $cancelUrl
    * @param mixed $method_payment
    */
    public function getParamsPay($gateway_name = 'paypal',$returnUrl,$cancelUrl,$method_payment = 'multi',$notifyUrl = '')
    {
         $receivers = Mp3music_Api_Cart::getReceivers($gateway_name,$method_payment);
         $invoice = "";
         foreach ($receivers as $rec)
         {
             $invoice .='-'.$rec['invoice'];
         }
         if ($invoice !="")
         {
             $invoice = substr($invoice,1);
         }
         switch($gateway_name)
         {
             case 'paypal':
             default:
                $paramsPay = array(
                'actionType' => 'PAY',
                'cancelUrl'  => $cancelUrl.$invoice,
                'returnUrl'  => $returnUrl.$invoice,
                'currencyCode' => 'USD',
                'sender'=>'',
                'feesPayer'=>'EACHRECEIVER',//feesPayer value {SENDER, PRIMARYRECEIVER, EACHRECEIVER}
                'ipnNotificationUrl'=> $notifyUrl.$invoice,
                'memo'=> '',
                'pin'=> '',
                'preapprovalKey'=> '',
                'reverseAllParallelPaymentsOnError'=> '',
                'receivers' => $receivers,
                );
             break;
         }
         return array($receivers,$paramsPay);
    }
    /**
    * move all items from cart to user's download list
    * 
    * @param mixed $cartlist
    */
    public function moveItems2DownloadList($cartlist = array())
    {
       
        $insert_item = array();
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction(); 
        $l_table = Engine_Api::_()->getDbTable('lists', 'mp3music'); 
        foreach($cartlist as $key=>$value)
        {
            if ($value['type'] == 'song')           
            {
                  $insert_item = array($value['item_id'],0,Engine_Api::_()->user()->getViewer()->getIdentity());
            }
            if ($value['type'] == 'album')           
            {
                  $insert_item = array(0,$value['item_id'],Engine_Api::_()->user()->getViewer()->getIdentity());
            }
            $list = $l_table->createRow();
            $list->dl_song_id = $insert_item[0];
            $list->dl_album_id = $insert_item[1];
            $list->user_id = $insert_item[2];
            $list->save();
        }
       try {
              $db->commit();
          } catch (Exception $ex) {
              $db->rollback();
              break;
          }  
    }
    
    /**
    * Get download list
    * 
    * @param mixed $aConds
    * @param mixed $sSort
    * @param mixed $iPage
    * @param mixed $sLimit
    * @param mixed $bCount
    */
     public function getDownloadList($params)
    {
        $sellerPaginator = Zend_Paginator::factory(Mp3music_Api_Cart::getSelectDownloadList($params));
        if( !empty($params['page']) )
        {
          $sellerPaginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
          $sellerPaginator->setItemCountPerPage($params['limit']);
        }   
        return $sellerPaginator;
    }
     public function getSelectDownloadList($params)
     {
         
        $l_table  = Engine_Api::_()->getDbTable('lists', 'mp3music');
        $l_name   = $l_table->info('name');
        $as_table = Engine_Api::_()->getDbTable('albumSongs', 'mp3music');
        $as_name  = $as_table->info('name');
        $ab_table = Engine_Api::_()->getDbTable('albums', 'mp3music');
        $ab_name  = $ab_table->info('name');
        $s_table = Engine_Api::_()->getDbTable('singers', 'mp3music');
        $s_name  = $s_table->info('name');
        $select   = $l_table->select()->setIntegrityCheck(false)
                    ->from($l_table,array("$l_name.list_id"))
                    ->joinLeft($as_name,"$as_name.song_id = $l_name.dl_song_id",array("$as_name.*","TRUNCATE($as_name.filesize/1024/1024,2) as sizemb"))
                    ->joinLeft($s_name,"$s_name.singer_id = $as_name.singer_id","$s_name.title as singer_title")
                    ->joinLeft($ab_name,"$ab_name.album_id = $l_name.dl_album_id",array("$ab_name.user_id","$ab_name.title as album_title","$ab_name.album_id as album"))
                    ->where("$l_name.user_id = ? ",$params["user_id"])
                    ->where("($ab_name.is_delete = 0 OR $as_name.is_delete = 0)")
                    ;
      return $select;
    }
    /**
    * Get all songs from albums
    * 
    * @param mixed $item_id
    */
    public function getSongInAlbum($item_id = null)
    {
        $as_table = Engine_Api::_()->getDbTable('albumSongs', 'mp3music');
        $as_name  = $as_table->info('name');
        
        $select = $as_table->select()
                    ->from($as_table)
                    ->where('album_id = ?',$item_id);
        $songs =  $as_table->fetchAll($select)->toArray();
        return $songs;
        
    }
    public function generateTime($value,$option,$first = true)
    {   
        if ( $option == 'month')
        {
            if ( $first == true)
                $day = '01';
            else
                $day = '31';
            $date = date('Y');
            if ($value <10 )
                $date.='-0'.$value.'-'.$day;
            else
                $date.='-'.$value.'-'.$day;
            
            $time = strtotime($date);
            return $time;
        }
        if ( $option =='year')
        {
            if ( $first == true)
                $day = '-01-01';
            else
                $day = '-12-31';
            
            $date = $value.$day;
            $time = strtotime($date);
            return $time;
        }
    }
    /**
    * Get group of user by user_id
    * 
    * @param mixed $user_id
    */
    public function getGroupUser($user_id)
    {
        $user = Engine_Api::_()->getItem('user', $user_id);
        if($user != null)
        {
            return $user->level_id;
        }
        return 0;
        
    }
    /**
    * Send notification for owner item
    * 
    * @param mixed $type
    * @param mixed $user_id
    * @param mixed $item
    */
    public function sendNotifycation($type,$user_id,$item,$is_request = false)
    {
        $aActualUser = Engine_Api::_()->getItem('user',$user_id);          
    }
    public function getRequestsFromUser($user_id)
    {
        $table  = Engine_Api::_()->getDbtable('paymentRequests', 'mp3music');
        $select = $table->select()
                        ->from('engine4_mp3music_payment_requests as request',array('request.*',"DATE( FROM_UNIXTIME( request.request_date) ) AS pDate"))  
                        ->where('request.request_user_id = ?',$user_id)
                        ->where('request.request_status <> 0')
                        ->order('request.paymentrequest_id DESC ')->limit(10);
        $result =   $table->fetchAll($select)->toArray();
        return $result;
    }
    public function getSumAmountTransaction($type='buy')
    {
        $t_table = Engine_Api::_()->getDbTable('transactionTrackings', 'mp3music');
        $t_name  = $t_table->info('name');
        
        $select = $t_table->select()
                    ->from("$t_name as his",array('sum(amount) as total','params'))
                    ->where('params = ?',$type)
                    ->where('transaction_status = 1')
                    ->group('params');
        $res =  $t_table->fetchAll($select)->toArray(); 
        if($res == NULL)
           $res[0]['total'] = 0; 
        return $res[0];
    }
      /**
    * load gateway user uses
    * 
    * @param mixed $gateway_name
    */
    public function loadGateWay($gateway_name = 'paypal')
    {
        $gateway = new gateway();
        $p = $gateway->load($gateway_name);
        return $p;
    }
    /**
    * get default settings from db of gateway
    * 
    * @param mixed $gateway_name
    */
    public function getSettingsGateWay($gateway_name = 'paypal')
    {
       
        $settings = @Mp3music_Api_Gateway::getSettingGateway($gateway_name);        
        $settings['params'] = unserialize($settings['params']);
        if ( isset($settings['params']['use_proxy']) && $settings['params']['use_proxy'] == 1)
            $use_proxy = true;
        else
            $use_proxy = false;
        $mode = Mp3music_Api_Cart::getSettingsSelling(0);
        if ($mode != null)
        {
            $mode = $mode['is_test_mode'];    
        }
        else
        {
            $mode = 0;
        }
        switch($gateway_name)
        {
            case 'paypal':
            default:
                if($mode == 1 )
                {
                    $m = 'sandbox';
                }
                else
                {
                    $m='real';
                }
                $aSetting = array(
                    'env' =>$m,
                    'proxy_host' => "",//$settings['params']['proxy_host'],
                    'proxy_port' => "",//$settings['params']['proxy_port'],
                    'api_username' =>$settings['params']['api_username'],
                    'api_password' =>$settings['params']['api_password'],
                    'api_signature' =>$settings['params']['api_signature'],
                    'api_app_id' => "",//$settings['params']['api_app_id'],
                    'use_proxy' =>$use_proxy,
                );
                break;
            
        }
        
        return $aSetting;
    }
}   
?>

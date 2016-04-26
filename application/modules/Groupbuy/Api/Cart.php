<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Cart.php
 * @author     Minh Nguyen
 */
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(__FILE__))))));
include_once  APPLICATION_PATH . '/application/modules/Groupbuy/externals/scripts/cart/gateway.php';
class Groupbuy_Api_Cart extends Core_Api_Abstract
{
     /**
     * get all transaction from date to date
     * 
     * @param mixed $user_id
     * @param mixed $fromDate
     * @param mixed $toDate
     * @param mixed $params
     * @return mixed
     */
     public function getTrackingTransaction($params, $codeallow = null)
    {
        $trackingPaginator = Zend_Paginator::factory(Groupbuy_Api_Cart::getSelectTrackingTransaction($params,$codeallow));
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
     public function getSelectTrackingTransaction($params, $codeallow = null)
    {
        $t_table  = Engine_Api::_()->getDbTable('transactionTrackings', 'groupbuy');
        $t_name   = $t_table->info('name');
        $select   = $t_table->select()->setIntegrityCheck(false)
                            ->from($t_table,array("$t_name.*","$t_name.transaction_date AS pDate","$t_name.transactiontracking_id AS tranid",
                            "(SELECT username FROM engine4_users as pu WHERE pu.user_id = $t_name.user_seller ) as seller_user_name",
                         "(SELECT username FROM engine4_users as pu WHERE pu.user_id = $t_name.user_buyer ) as buyer_user_name",
                         "(SELECT account_username FROM engine4_groupbuy_payment_accounts as pu WHERE pu.paymentaccount_id  = $t_name.account_seller_id ) as account_seller_email",
                         "(SELECT account_username FROM engine4_groupbuy_payment_accounts as pu WHERE pu.paymentaccount_id  = $t_name.account_buyer_id  ) as account_buyer_email"
                            ));
        if (isset($params['user_id']) && $params['user_id'] != null)
        {
            $user_id  = $params['user_id'];
            $select->where("($t_name.user_seller = $user_id OR $t_name.user_buyer = $user_id)");  
        } 
        if (isset($params['buyer_name']) && $params['buyer_name'] != null)
        {
            $buyer_name  = $params['buyer_name'];
            $buyer_name = mysql_escape_string($buyer_name);
            $userTable = Engine_Api::_()->getItemTable('user');
    		$userTableName = $userTable->info('name');
        	$select->joinRight($userTableName,"($userTableName.username = '%$buyer_name%' OR $userTableName.displayname like '%$buyer_name%') AND $userTableName.user_id = $t_name.user_buyer", array()) ; 
        } 
    	if ($codeallow == 1 )
            {
        		$c_table = Engine_Api::_()->getDbTable('coupons', 'groupbuy');
		        $c_name = $c_table->info('name');
		        $select->joinLeft($c_name,"$c_name.deal_id = $t_name.item_id AND $c_name.user_id = $t_name.user_buyer AND $c_name.trans_id = $t_name.transactiontracking_id",array("$c_name.code as code","$c_name.status AS code_status","$c_name.coupon_id AS coupon_id"));
            }
        
        else if (isset($params['code']) && $params['code'] != null)
        {
                $code  = $params['code'];
	            $code = mysql_escape_string($code);
	            $c_table = Engine_Api::_()->getDbTable('coupons', 'groupbuy');
			    $c_name = $c_table->info('name');
			    $select->joinRight($c_name,"$c_name.deal_id = $t_name.item_id AND $c_name.user_id = $t_name.user_buyer AND $c_name.trans_id = $t_name.transactiontracking_id AND $c_name.code = '$code'",array("$c_name.code as code","$c_name.status AS code_status","$c_name.coupon_id AS coupon_id"));
            
        }
        if (isset($params['deal_id']) && $params['deal_id'] != null)
        {
            $deal_id  = $params['deal_id'];
            $select->where("$t_name.item_id  = ?",$deal_id);  
        } 
        if (isset($params['fromDate']) && $params['fromDate'] != null)
        {
            $fromDate =  $params['fromDate'];
            $select->where("DATEDIFF($t_name.transaction_date,'$fromDate') >= 0");  
        }
        if (isset($params['toDate']) && $params['toDate'] != null)
        {
            $toDate =  $params['toDate'];
            $select->where("DATEDIFF($t_name.transaction_date,'$toDate') <= 0");  
        }
        $codTable = Engine_Api::_()->getDbtable('buyCods', 'groupbuy');
        $codName = $codTable->info('name');
        $select->joinLeft($codName, "$codName.deal_id = $t_name.item_id AND $codName.user_id = $t_name.user_buyer AND $codName.tran_id = $t_name.transactiontracking_id","$codName.*");
        $select->order("$t_name.transaction_date DESC");

        return $select;  
     }
     public function getFinanceAccountsPag($params)
     {
        $requestPaginator = Zend_Paginator::factory(Groupbuy_Api_Cart::getFinanceAccountsSelects($params));
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
        $p_table  = Engine_Api::_()->getDbTable('paymentAccounts', 'groupbuy');
        $p_name   = $p_table->info('name');
         $select   = $p_table->select()->setIntegrityCheck(false)
                    ->from("$p_name as ni",'ni.*')
                    ->join("engine4_users","engine4_users.user_id = ni.user_id",'engine4_users.username')
                    ->where('payment_type <> 1');
                    $select->order('last_check_out ASC') ; 
          return $select;            
    }
    public function getFinanceAccount($user_id = null,$payment_type = null,$gateway_id = null)
    {
        $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'groupbuy');
        $select = $table->select();
          
        if($user_id != null)
        {
            $select->where('user_id = ?',$user_id);
        }
        if($payment_type != null)
        {
             $select->where('payment_type = ?',$payment_type);  
        }
        if($gateway_id != null)
        {
             $select->where('gateway_id = ?',$gateway_id);  
        }
         $accounts =   $table->fetchAll($select)->toArray();   
         return $accounts[0];    
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
             $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'groupbuy');
             $data = array(
            'account_username' => $account['account_username'],
            'gateway_id' =>  $account['gateway_id']
            );
             $where = $table->getAdapter()->quoteInto('paymentaccount_id = ?', $account['paymentaccount_id']);
             $table->update($data, $where);
        }
        else
        {
           $acc = Engine_Api::_()->getDbtable('paymentAccounts', 'groupbuy')->createRow();  
            $acc->account_username = $account['account_username'];
            $acc->payment_type = $account['payment_type'];
            $acc->gateway_id = $account['gateway_id'];
            $acc->account_status = 1;
            $acc->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $acc->save();

        }
        return $account;
    }  
    public function getFinanceAccounts($aConds = array(),$sSort = 'last_check_out ASC', $iPage = '', $sLimit = '', $bCount = true)
    {
        $p_table  = Engine_Api::_()->getDbTable('paymentAccounts', 'groupbuy');
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
    public function getReceivers($gateway_name ='Paypal',$method_payment = 'directly',$request = false)
    {
        $settings = array('admin_account' => '');
       
        switch($gateway_name)
        {
            case '2Checkout':
                $settings['params'] = unserialize($settings['params']);
                $receivers = array(
                    array('email' => $settings['admin_account'],'invoice' => Groupbuy_Api_Cart::getSecurityCode()),
                 );
                break;
            case 'Paypal':
            default:
                $settings['params'] = unserialize($settings['params']);
                $receivers = array(
                    array('email' => $settings['admin_account'],'invoice' => Groupbuy_Api_Cart::getSecurityCode()),
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
    public function getParamsPay($gateway_name = 'Paypal',$returnUrl,$cancelUrl,$method_payment = 'multi',$notifyUrl = '')
    {
         $receivers = Groupbuy_Api_Cart::getReceivers($gateway_name,$method_payment);
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
             case '2Checkout':
                break;
             case 'Paypal':
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
    public function getSumAmountTransaction($type='buy')
    {
        $t_table = Engine_Api::_()->getDbTable('transactionTrackings', 'groupbuy');
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
    public function loadGateWay($gateway_name = 'Paypal')
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
    public function getSettingsGateWay($gateway_name = 'Paypal')
    {
        $settings = Groupbuy_Api_Gateway::getSettingGateway($gateway_name);        
        $settings['params'] = unserialize($settings['params']);
        if ( isset($settings['params']['use_proxy']) && $settings['params']['use_proxy'] == 1)
            $use_proxy = true;
        else
            $use_proxy = false;
        $mode = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.mode', 1); 
        switch($gateway_name)
        {
            case '2Checkout':
                if($mode == 1 )
                {
                    $m = 'Y';
                }
                else
                {
                    $m='N';
                }
                $aSetting = array(
                    'env' =>$m,
                    'api_username' =>@$settings['params']['api_username'],
                    'api_password' =>@$settings['params']['api_password'],
                    'api_signature' =>@$settings['params']['api_signature'],
                    'use_proxy' =>$use_proxy,
                );
                break;
            case 'Paypal':
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
                    'api_username' =>@$settings['params']['api_username'],
                    'api_password' =>@$settings['params']['api_password'],
                    'api_signature' =>@$settings['params']['api_signature'],
                    'use_proxy' =>$use_proxy,
                );
                break;
            
        }
        
        return $aSetting;
    }
     public function makeBillFromCart($deal,$receiver,$type = 1,$number = 1, $toAdmin = false)
    {
    	
         $insert_item = array();
		 if (empty($receiver['email'])) {
		 	$toAdmin = true;
		 }
		 
         list($iCnt,$receiver_account) = Groupbuy_Api_Cart::getFinanceAccounts('ni.account_username = "'.$receiver['email'].'"');
         if (!$toAdmin) {
	         if ( !isset($receiver_account[0]) || @$receiver_account[0]['paymentaccount_id']<=0) {
	         	//print_r($receiver);
	         	//throw new Exception("no receive account has been setup");
	             return -1;
	         }
		 }
		 $superAdmins = Engine_Api::_()->user()->getSuperAdmins()->toArray();
		 $superAdminId = $superAdmins[0]['user_id'];
		 
		 try {
         $db = Engine_Db_Table::getDefaultAdapter();
         $db->beginTransaction(); 
          $b_table = Engine_Api::_()->getDbTable('bills', 'groupbuy'); 
          $bill = $b_table->createRow();
          $bill->invoice     = $receiver['invoice'];
          $bill->sercurity    = $_SESSION['payment_sercurity'];              
          $bill->user_id     = Engine_Api::_()->user()->getViewer()->getIdentity();
          $bill->finance_account_id       = 0;
		  $bill->item_price = $deal->price;
		  $bill->item_final_price =  $deal->final_price;
          $bill->emal_receiver  = $receiver['email'];
          $bill->payment_receiver_id  = ($toAdmin) ? $superAdminId : $receiver_account[0]['paymentaccount_id'];
          $bill->date_bill  = date('Y-m-d H:i:s');
          $bill->bill_status  = 0;
          $bill->item_id  = $deal->deal_id;
		  
		  // update comssion fee for each item.
		  $bill->commission_fee = $deal->getComissionFee($number); 
          $bill->owner_id  = $deal->user_id;   
          if($type == 1)
          {
            $bill->amount  = $deal->final_price * $number;
            $bill->currency = $deal->currency;
          }
          else
          {
            $bill->amount  = $deal->total_fee;
            $bill->commission_fee = 0;
            $bill->owner_id  = 1;    
            $bill->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.currency', 'USD');          
          }
          $bill->number = $number;
          $bill->save(); 
		  
		  // save comission for each item by there.
		  

          $db->commit();
          } catch (Exception $ex) {
              $db->rollback();
			  throw $ex;		
              break;
          }  
         return $bill;
         
    }
    public function getRequestsFromUser($user_id)
    {
        $table  = Engine_Api::_()->getDbtable('paymentRequests', 'groupbuy');
        $select = $table->select()
                        ->from('engine4_groupbuy_payment_requests as request',array('request.*',"DATE( FROM_UNIXTIME( request.request_date) ) AS pDate"))  
                        ->where('request.request_user_id = ?',$user_id)
                        ->where('request.request_status <> 0')
                        ->order('request.paymentrequest_id DESC ')->limit(10);
        $result =   $table->fetchAll($select)->toArray();
        return $result;
    }
     /**
    * get request information from id
    * 
    * @param mixed $request_id
    */
    public function getPaymentRequest($request_id)
    {
        $r_table = Engine_Api::_()->getDbTable('paymentRequests', 'groupbuy');
         $select = $r_table->select() ->setIntegrityCheck(false)  
                    ->from($r_table)
                    ->joinLeft('engine4_groupbuy_payment_accounts','engine4_groupbuy_payment_accounts.user_id = engine4_groupbuy_payment_requests.request_user_id','engine4_groupbuy_payment_accounts.*');
        $select->where('paymentrequest_id = ?',$request_id);      
       
        $results =  $r_table->fetchAll($select)->toArray(); 
        return  $results[0]; 
    }
    public function checkPaymentRequest($request_id)
    {
        $r_table = Engine_Api::_()->getDbTable('paymentRequests', 'groupbuy');
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
        $table  = Engine_Api::_()->getDbtable('paymentRequests', 'groupbuy');
        $data = array(
            'request_status' => $status,
            'request_answer' => $message
        );
        $where = $table->getAdapter()->quoteInto('paymentrequest_id = ?',$request_id);
        return $table->update($data, $where);                                               
    }
     public function getFinanceAccountRequests($aConds = array(),$sSort = 'last_check_out ASC', $iPage = '', $sLimit = '', $bCount = true)
     {
        $p_table  = Engine_Api::_()->getDbTable('paymentAccounts', 'groupbuy');
        $p_name   = $p_table->info('name');
        $iCnt = ($bCount ? 0 : 1);
        $items = array();
        //$con = array();
        if ($bCount ){
             $select   = $p_table->select()
                        ->from("$p_name as ni")
                        ->joinLeft("engine4_users","engine4_users.user_id = ni.user_id",'')
                        ->joinLeft('engine4_groupbuy_payment_requests','engine4_groupbuy_payment_requests.request_payment_acount_id = ni.paymentaccount_id','')    
                        ->where($aConds); 
            $iCnt = count($p_table->fetchAll($select)->toArray());

        }
        if ($iCnt){
             $select   = $p_table->select()->setIntegrityCheck(false)
                        ->from("$p_name as ni",'ni.*')
                        ->joinLeft("engine4_users","engine4_users.user_id = ni.user_id",'engine4_users.username')
                        ->joinLeft('engine4_groupbuy_payment_requests', 'engine4_groupbuy_payment_requests.request_payment_acount_id = ni.paymentaccount_id','engine4_groupbuy_payment_requests.*')
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
        $requestPaginator = Zend_Paginator::factory(Groupbuy_Api_Cart::getFinanceAccountRequestSelects($params));
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
        $p_table  = Engine_Api::_()->getDbTable('paymentAccounts', 'groupbuy');
        $p_name   = $p_table->info('name');
         $select   = $p_table->select()->setIntegrityCheck(false)
                    ->from("$p_name as ni",'ni.*')
                    ->join("engine4_users","engine4_users.user_id = ni.user_id",'engine4_users.username')
                    ->joinLeft('engine4_groupbuy_payment_requests', 'engine4_groupbuy_payment_requests.request_payment_acount_id = ni.paymentaccount_id','engine4_groupbuy_payment_requests.*')
                    ->where('payment_type <> 1 AND paymentrequest_id > 0');
                    if(isset($params['request_status']) && $params['request_status'] != '')
                        $select->where('request_status = ?',$params['request_status']) ;
                    if(isset($params['request_type']) && $params['request_type'] != '')
                        $select->where('request_type = ?',$params['request_type']) ;
                    if(isset($params['user_name']) && $params['user_name'] != '')
                    {
                        $keyword = $params['user_name'];
                        $select->where("engine4_users.username Like '%$keyword%' OR engine4_users.displayname LIKE '%$keyword%'");
                    }
                    $select->order('last_check_out ASC') ; 
          return $select;            
    }
    public function updateTotalAmount($request_id,$total_amount,$is_request = true)
    {
        if ($is_request == true)
        {
            $request = Groupbuy_Api_Cart::getPaymentRequest($request_id);
            $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'groupbuy');
            $select = $table->select()->from($table->info('name')) ;
            $select->where('user_id = ?',$request['request_user_id'] );
            $result = $table->fetchRow($select);
            $totalpriceamount = $result->total_price_amount;
            $data = array(
                'total_price_amount' => $totalpriceamount - (($totalpriceamount/$total_amount)*$request['request_amount']), 
            	'total_amount' => $total_amount-$request['request_amount']
            );
            $where = $table->getAdapter()->quoteInto('user_id = ?',$request['request_user_id'] );
            $table->update($data, $where);                         
        }
        else
        {
             $account = Groupbuy_Api_Cart::getFinanceAccount($request_id);
             $account_id = 0;
             if ( $account == null )
             {
                 $params = array();
                 $params['account_username']= Engine_Api::_()->user()->getViewer()->email;
                 $params['total_amount']= 0;
                 $params['account_status']= 1;
                 $params['payment_type']= 2;
                 $params['user_id']= Engine_Api::_()->user()->getViewer()->getIdentity();
                 $account_id = Groupbuy_Api_Cart::insertAccount($params);
                 $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'groupbuy');
                    $data = array(
                        'total_amount' => $total_amount+$account['total_amount']
                    );
                    $where = $table->getAdapter()->quoteInto('user_id = ?',$account_id);
                    $table->update($data, $where);                         
             }
             else
             {
                   $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'groupbuy');
                    $data = array(
                        'total_amount' => $total_amount+$account['total_amount']
                    );
                    $where = $table->getAdapter()->quoteInto('user_id = ?',$account['user_id']);
                    $table->update($data, $where);                         
             }   
        }  
    }
    /**
     * save request to tracking.
     * 
     * @param mixed $request_id
     * @param mixed $message
     * @param mixed $status
     */
    public function saveTransactionFromRequest($request_id,$message,$status,$adminccount,$type = 'Request')
    {
        list($count,$request) = Groupbuy_Api_Cart::getFinanceAccountRequests("paymentrequest_id = ".$request_id,"",1,1);
        $re = $request[0];
        $insert_item = array($re['request_date'],$re['request_user_id'],$adminccount['user_id'],'',$re['request_amount'],$re['request_payment_acount_id'],$adminccount['paymentaccount_id'],$status,$type) ;
        //print_r($insert_item); die;
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction(); 
        $l_table = Engine_Api::_()->getDbTable('transactionTrackings', 'groupbuy'); 
        $list = $l_table->createRow();
        $list->transaction_date = date('Y-m-d H:i:s');
        $list->user_seller = $insert_item[1];
        $list->user_buyer = $insert_item[2];
        $list->item_id = $insert_item[3];
        $list->amount = $insert_item[4];
        $list->account_seller_id = $insert_item[5];
        $list->account_buyer_id = $insert_item[6];
        $list->transaction_status = $insert_item[7];
        $list->params = $insert_item[8];
        $list->save();
       try {
              $db->commit();
          } catch (Exception $ex) {
              $db->rollback();
              break;
          }  
    }
}   
?>

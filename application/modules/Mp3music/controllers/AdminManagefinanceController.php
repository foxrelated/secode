<?php
class Mp3music_AdminManagefinanceController extends Core_Controller_Action_Admin
{
   protected $_paginate_params = array();
   public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mp3music_admin_main', array(), 'mp3music_admin_main_managefinance');
      $this->_paginate_params['page']   = $this->getRequest()->getParam('page', 1);
     $this->_paginate_params['limit']  = Engine_Api::_()->getApi('settings', 'core')->getSetting('mp3music.songsPerPage', 10);
  }
  public function indexAction()
  {
        $user_name = "";
        $request_status = "";
        $admin = Mp3music_Api_Cart::getFinanceAccount(null,1);
        $req5 = $this->getRequest()->getParam('req5');
        $req6 = $this->getRequest()->getParam('req6');
        if (isset($_SESSION['payment_sercurity_adminpayout']) && $req6 == $_SESSION['payment_sercurity_adminpayout'] && $_SESSION['payment_sercurity_adminpayout']!="")
        {
            if($_SESSION['request_id'] != "")
                $request  = Mp3music_Api_Cart::checkPaymentRequest($_SESSION['request_id']);
            if ($req5 == 'success' && $request != null)
            {
                  //transaction success
                 Mp3music_Api_Cart::updatePaymentRequest($_SESSION['request_id'],$_SESSION['message'],1);  
                 //update total amount of user
                 Mp3music_Api_Cart::updateTotalAmount($_SESSION['request_id'],$_SESSION['total_amount']);
                 //save to transaction                                                            
                 Mp3music_Api_Cart::saveTransactionFromRequest($_SESSION['request_id'],$_SESSION['message'],1,$admin); 
                 //send notification succ.sendNotifycation($type,$user_id,$item,$is_request = false)
                // phpfox::getService('musicsharing.cart.music')->sendNotifycation('yes',$_SESSION['request_info_user_id'],$request_info,true);                                                                                                                                                                                                                                                                                                    
                 $this->view->message =  "Payment is successful.";
                 
            }
            elseif($req5 =='cancel')
            {
                 //do nothing
            }
             
        }
        
        $pay = $this->getRequest()->getParam('pay');
       
        if( $pay['task'] =='checkout' && $pay['sercurity'] == $_SESSION['payment_sercurity_adminpayout'] 
            && $_SESSION['payment_sercurity_adminpayout']!="" && isset($pay['task'])
            && isset($pay['sercurity']) && isset($_SESSION['payment_sercurity_adminpayout']) 
        )
        {
            if ( $pay['is_accept'] == 1)
            {
                $gateway_name = $pay['gateway'];
                $_SESSION['request_id'] = $pay['request'];
                $_SESSION['message'] = $pay['message'];
                $method_payment = 'directly';
                $gateway = Mp3music_Api_Cart::loadGateWay($gateway_name);
                $settings = Mp3music_Api_Cart::getSettingsGateWay($gateway_name);
                $gateway->set($settings);
                $params = array();
                $params = array_merge(array('req5' => 'cancel','req6'=> $_SESSION['payment_sercurity_adminpayout']), $params);
                $cancelUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble($params, 'mp3music_admin_main_managefinance', true);
                $params = array_merge(array('req5' => 'success','req6'=> $_SESSION['payment_sercurity_adminpayout']), $params);
                $returnUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble($params, 'mp3music_admin_main_managefinance', true);
               // $returnUrl =  phpfox::getLib('url')->makeUrl('admincp.musicsharing.cart.finance.success',$_SESSION['payment_sercurity_adminpayout']);
               // $cancelUrl = phpfox::getLib('url')->makeUrl('admincp.musicsharing.cart.finance.cancel',$_SESSION['payment_sercurity_adminpayout']);
                $notifyUrl = $this->selfURL().'application/modules/Mp3music/externals/scripts/callback.php?action=callback&req4='.$_SESSION['payment_sercurity'].'&req5=';  
                list($receiver,$paramsPay) = Mp3music_Api_Cart::getParamsPay($gateway_name,$returnUrl,$cancelUrl,$method_payment,$notifyUrl);
               
                $settings = Mp3music_Api_Cart::getSettingsSelling(Engine_Api::_()->user()->getViewer()->level_id);
                $fee = "EACHRECEIVER";
                switch($settings['who_payment'])
                {
                    case 1:
                        $fee = "PRIMARYRECEIVER";
                        break;
                    case 2:
                        $fee = "SENDER";
                        break;
                    case 3:
                        $fee = "EACHRECEIVER";
                        break;
                    default:
                        $fee = "EACHRECEIVER";  
                        break;
                }
                $paramsPay['feesPayer'] = $fee;
                $request_info = Mp3music_Api_Cart::getPaymentRequest($pay['request']);
                $_SESSION['request_info_user_id'] = $request_info['request_user_id'];
                $security_code = Mp3music_Api_Cart::getSecurityCode();
                if($request_info != null)
                {
                    $paramsPay['receivers'] = array(
                                                 array('email' => $request_info['account_username'],'amount' => $request_info['request_amount'],'invoice' =>$security_code ),
                                             );
                }
                    
                $res = $gateway->checkOut($paramsPay);    
            }
            else
            {
                //transaction fail by admin deny this request
                 //update request status and insert the message.
                 $_SESSION['request_id'] = $pay['request'];
                $_SESSION['message'] = $pay['message'];
                $request_info = Mp3music_Api_Cart::getPaymentRequest($pay['request']);
                $_SESSION['request_info_user_id'] = $request_info['request_user_id'];
                 Mp3music_Api_Cart::updatePaymentRequest($_SESSION['request_id'],$_SESSION['message'],-1);  
                 //save to transaction                                                            
                Mp3music_Api_Cart::saveTransactionFromRequest($_SESSION['request_id'],$_SESSION['message'],0,$admin);
                 //send notification fail.sendNotifycation($type,$user_id,$item,$is_request = false)
                // phpfox::getService('musicsharing.cart.music')->sendNotifycation('no',$_SESSION['request_info_user_id'],$request_info,true);
                 $this->view->message = 'Payment is cancelled.';
            }
            
        }
        if($this->getRequest()->getParam('savechange'))
        {
            $account = $this->getRequest()->getParam('val');
            $admin = Mp3music_Api_Cart::saveFinanceAccount($account);
           
            $params['admin_account'] = $account['account_username'];
            $params['is_from_finance'] = 1;
            Mp3music_Api_Gateway::saveSettingGateway('paypal',$params);   
            $this->view->message = 'Save admin finance account successfull';
            
        }
        if($this->getRequest()->getParam('fitter'))
        {
            $keyword = $this->getRequest()->getParam('search');
            
            //$this->url()->send('admincp.musicsharing.cart.finance',array('fittersearch'=>1,'user'=>$keyword['user_account'],'option'=>$keyword['option_select']),null);
        }
        $this->view->option = -2 ;
        
        if($this->getRequest()->getParam('fitter'))
        {
            $user_name = $this->getRequest()->getParam('user');
            $this->view->user  = $user_name;
            $keyword = $this->getRequest()->getParam('option_select');
            $this->view->option = $keyword; 
            switch($keyword)
            {
                case 1:
                    $request_status = '1';
                    break;
                case 0:
                    $request_status = '0' ;
                    break;
                case -1:
                    $request_status ='-1';
                    break;
                case -2:
                    break;
            } 
        }
        $params = array_merge($this->_paginate_params, array(
            'user_name' => $user_name,'request_status'=>$request_status,
             ));  
        
        $accounts = Mp3music_Api_Cart::getFinanceAccountRequestPag($params);
        $this->view->accounts = $accounts; 
        $this->view->adminAccount = $admin; 
        $this->view->currency = "USD"; 
        
        $sumbuys = Mp3music_Api_Cart::getSumAmountTransaction();
        $sumrequests = Mp3music_Api_Cart::getSumAmountTransaction('request');
        $s = $sumbuys['total']- $sumrequests['total'];
        $s = round($s,2);
        $this->view->su = $s;    
    }
     public function requestAction(){  
      if (!$this->_helper->requireUser()->isValid()) { return;}   
       //tat di layout
       $id = $this->getRequest()->getParam('id');
       $status = $this->getRequest()->getParam('status');
       $is_adaptive_payment = 0;
       $this->view->is_adaptive_payment = $is_adaptive_payment;
        if($is_adaptive_payment == 1 || $status == 0)
        {
            $_SESSION['payment_sercurity_adminpayout'] = Mp3music_Api_Cart::getSecurityCode();       
            list($count,$accounts) = Mp3music_Api_Cart::getFinanceAccountRequests("engine4_mp3music_payment_requests.paymentrequest_id = ".$id,"", 1, 10);   
            $paymentForm =  Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'mp3music_admin_main_managefinance', true);        
            $acc = $accounts[0];
            
            if($acc['request_amount '] > $acc['total_amount'])
            {
               
                echo 'Invalid request.Total amount request is larger than total user amount';
                return false;
            }
            else
            {
                $_SESSION['total_amount'] =  $acc['total_amount'];
            }
            $this->view->account = $acc ;
            $this->view->paymentForm = $paymentForm;     
            $this->view->sercurity = $_SESSION['payment_sercurity_adminpayout'];     
            $this->view->core_path = $this->selfURL();     
            $this->view->status = $status;     
        }
        else
        {
            $_SESSION['payment_sercurity_adminpayout'] = Mp3music_Api_Cart::getSecurityCode(); 
            list($count,$accounts) = Mp3music_Api_Cart::getFinanceAccountRequests("paymentrequest_id = ".$id,"", 1, 10);   
            $paymentForm = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'mp3music_admin_main_managefinance', true); 
            $acc = $accounts[0];
            
            if($acc['request_amount'] > $acc['total_amount'])
            {
               
                echo 'Invalid request.Total amount request is larger than total user amount';
                return false;
            }
            else
            {
                $_SESSION['total_amount'] =  $acc['total_amount'];
            }
               $method_payment = array('direct'=>'Directly','multi'=>'Multipartite payment');  
               $method_payment = 'directly';
                $gateway_name ="paypal";
                $gateway = Mp3music_Api_Cart::loadGateWay($gateway_name);
                $settings = Mp3music_Api_Cart::getSettingsGateWay($gateway_name);
                $gateway->set($settings);
                $params = array();
                $params = array_merge(array('page'=>'1','req5' => 'cancel','req6'=> $_SESSION['payment_sercurity_adminpayout']), $params);
                $cancelUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble($params, 'mp3music_admin_main_managefinance', true);
                $params = array_merge(array('page'=>'1','req5' => 'success','req6'=> $_SESSION['payment_sercurity_adminpayout']), $params);
                $returnUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble($params, 'mp3music_admin_main_managefinance', true);
                $_SESSION['url']['cancel'] = $cancelUrl;
                $_SESSION['url']['success'] = $returnUrl;
               
                $returnUrl = "http://".$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Mp3music/externals/scripts/redirectRequest.php?pstatus=success&index='.$this->view->url(array(),'default').'&req4='.$_SESSION['payment_sercurity_adminpayout'].'&req5=';
                $cancelUrl = "http://".$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Mp3music/externals/scripts/redirectRequest.php?pstatus=cancel&index='.$this->view->url(array(),'default').'&req4='.$_SESSION['payment_sercurity_adminpayout'].'&req5=';
                $notifyUrl = "http://".$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Mp3music/externals/scripts/callback.php?action=callback&req4='.$_SESSION['payment_sercurity_adminpayout'].'&req5=';
               list($receiver,$paramsPay) = Mp3music_Api_Cart::getParamsPay($gateway_name,$returnUrl,$cancelUrl,$method_payment,$notifyUrl);
               $_SESSION['receiver'] = $receiver;
               $method_payment = 'directly';
               $paymentForm = "https://www.sandbox.paypal.com/cgi-bin/webscr";
               if ($settings['env'] == 'sandbox')
               {
                   $paymentForm = "https://www.sandbox.paypal.com/cgi-bin/webscr";
               }
               else
               {
                   $paymentForm = "https://www.paypal.com/cgi-bin/webscr";
               }
               $request_info = Mp3music_Api_Cart::getPaymentRequest($acc['paymentrequest_id']);
                $_SESSION['request_info_user_id'] = $request_info['request_user_id'];
                $security_code = Mp3music_Api_Cart::getSecurityCode();
                if($request_info != null)
                {
                    $paramsPay['receivers'] = array(
                                                 array('email' => $request_info['account_username'],'amount' => $request_info['request_amount'],'invoice' =>$security_code ),
                                             );
                } 
                
                $settings = Mp3music_Api_Cart::getSettingsSelling(Engine_Api::_()->user()->getViewer()->level_id);
                $this->view->paymentForm = $paymentForm ;
                $this->view->sercurity = $_SESSION['payment_sercurity_adminpayout'];
                $this->view->core_path = $this->selfURL();
                $this->view->account = $acc ;
                $this->view->status = $status ;
                $this->view->receiver = $paramsPay['receivers'][0];
                $this->view->currency = 'USD';
                $this->view->paramPay = $paramsPay;
        }

  }
    public function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
      return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
      }   
}
<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminCategoryController.php
 * @author     Minh Nguyen
 */
class Groupbuy_AdminRequestController extends Core_Controller_Action_Admin
{
  protected $_paginate_params = array();
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('groupbuy_admin_main', array(), 'groupbuy_admin_main_requests');
      $this->_paginate_params['page']   = $this->getRequest()->getParam('page', 1);
     $this->_paginate_params['limit']  = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.page', 1);
  }
   public function indexAction()
  {
        $user_name = "";
        $request_status = "";
        $admin = Groupbuy_Api_Cart::getFinanceAccount(null,1);
        $req5 = $this->getRequest()->getParam('req5');
        $req6 = $this->getRequest()->getParam('req6');
		
        if (isset($_SESSION['payment_sercurity_adminpayout']) && $req6 == $_SESSION['payment_sercurity_adminpayout'] && $_SESSION['payment_sercurity_adminpayout']!="")
        {
            if($_SESSION['request_id'] != "")
                $request  = Groupbuy_Api_Cart::checkPaymentRequest($_SESSION['request_id']);
            if ($req5 == 'success' && $request != null)
            {
                  //transaction success
                 Groupbuy_Api_Cart::updatePaymentRequest($_SESSION['request_id'],$_SESSION['message'],1);  
                 //update total amount of user
                 Groupbuy_Api_Cart::updateTotalAmount($_SESSION['request_id'],$_SESSION['total_amount']);
                 //save to transaction                                                            
                 Groupbuy_Api_Cart::saveTransactionFromRequest($_SESSION['request_id'],$_SESSION['message'],1,$admin,'Paid amount to Seller');         
                  //send mail
                 $minh_requet = Groupbuy_Api_Cart::getPaymentRequest($_SESSION['request_id']);
                 $user = Engine_Api::_()->getItem('user',$minh_requet['request_user_id']);
                 $sendTo = $user->email;
                 $params = $minh_requet;
				 
                 Engine_Api::_()->getApi('mail','groupbuy')->send($sendTo, 'groupbuy_sellerrequest_suc', $params);    
                 
                 $this->view->message =  "Payment is successfull.";
                 
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
            if ( $pay['is_accept'] == 0)
            {
                //transaction fail by admin deny this request
                 //update request status and insert the message.
                 $_SESSION['request_id'] = $pay['request'];
				 
                $_SESSION['message'] = $pay['message'];
                $request_info = Groupbuy_Api_Cart::getPaymentRequest($pay['request']);
                $_SESSION['request_info_user_id'] = $request_info['request_user_id'];
                 Groupbuy_Api_Cart::updatePaymentRequest($_SESSION['request_id'],$_SESSION['message'],-1);  
                 //save to transaction                                                            
                Groupbuy_Api_Cart::saveTransactionFromRequest($_SESSION['request_id'],$_SESSION['message'],0,$admin);
                  //send mail
                 $minh_requet = Groupbuy_Api_Cart::getPaymentRequest($_SESSION['request_id']);
                 $user = Engine_Api::_()->getItem('user',$minh_requet['request_user_id']);
                 $sendTo = $user->email;
                 $params = $minh_requet;
				 Engine_Api::_()->getApi('mail','groupbuy')->send($sendTo, 'groupbuy_sellerrequest_fail', $params);
                     
                 $this->view->message = 'Payment is cancelled.';
            }
            
        }
        $page = $this->_getParam('page',1);
        $this->view->form = $formFilter = new Groupbuy_Form_Admin_Payment_Manage();
            $values = array();
		   
            if( $formFilter->isValid($this->getRequest()->getParams()) ) {
		      $values = $formFilter->getValues();
		    }
		
       //$keyword = $values['search'];
		
        $this->view->option = -2 ;

        if ($values['option_select'] == null){
        	$values['option_select'] = -2;
        }
        if (($values['option_select'] == -1) || ($values['option_select'] == 0) || ($values['option_select'] == -2) || ($values['option_select'] == 1)) {
        	
			
        	$user_name = $values['user'];
            $this->view->user  = $user_name;
            $keyword = $values['option_select'];
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
            'page'=>$this->_getParam('page'),'user_name' => $user_name,'request_status'=>$request_status,'request_type'=>1
         ));

        $accounts = Groupbuy_Api_Cart::getFinanceAccountRequestPag($params);
        $this->view->accounts = $accounts; 
        $this->view->adminAccount = $admin; 
        $this->view->currency = Groupbuy_Api_Core::getDefaultCurrency(); 
      // $this->view->accounts->setCurrentPageNumber($page);
       $this->view->values = $values; 
        
    }
     public function requestPaymentAction(){  
      if (!$this->_helper->requireUser()->isValid()) { return;}   
       //tat di layout
       $id = $this->getRequest()->getParam('id');
       $status = $this->getRequest()->getParam('status');
       $this->view->is_adaptive_payment = $is_adaptive_payment;
        if($status == 0)
        {
            $_SESSION['payment_sercurity_adminpayout'] = Groupbuy_Api_Cart::getSecurityCode();       
            list($count,$accounts) = Groupbuy_Api_Cart::getFinanceAccountRequests("engine4_groupbuy_payment_requests.paymentrequest_id = ".$id,"", 1, 10);   
            $paymentForm =  Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'groupbuy_admin_main_request', true);        
            $acc = $accounts[0];
            
            if($acc['request_amount '] > $acc['total_amount'])
            {
               
                echo $this->view->translate('Invalid request.Total amount request is larger than total user amount');
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
            $_SESSION['payment_sercurity_adminpayout'] = Groupbuy_Api_Cart::getSecurityCode(); 
            list($count,$accounts) = Groupbuy_Api_Cart::getFinanceAccountRequests("paymentrequest_id = ".$id,"", 1, 10);   
            $paymentForm = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'groupbuy_admin_main_request', true); 
            $acc = $accounts[0];
            
			$request_id  = $this->_getParam('id');
            if($acc['request_amount'] > $acc['total_amount'])
            {
                echo $this->view->translate('Invalid request.Total amount request is larger than total user amount');
                return false;
            }
            else
            {
                $_SESSION['total_amount'] =  $acc['total_amount'];
            }
               $method_payment = array('direct'=>'Directly','multi'=>'Multipartite payment');  
               $method_payment = 'directly';
                $gateway_name ="paypal";
                $gateway = Groupbuy_Api_Cart::loadGateWay($gateway_name);
				$paypal = Engine_Api::_()->getItem('payment_gateway', 2);
				$config = (array) $paypal->config;
                $settings = array(
					'env' => ($paypal->test_mode) ? 'sandbox' : 'real',
                    'api_username' => $config['username'],
                    'api_password' => $config['password'],
                    'api_signature' => $config['signature'],
                    'use_proxy' => false,
				);
                $gateway->set($settings);
				$query_string = http_build_query(array(
					'req4'=>$_SESSION['payment_sercurity_adminpayout'],
					'qtotal_amount'=>$_SESSION['total_amount'],
					'qrequest_id'=>$request_id,
					'qreceiver' =>$receiver	,
					'message'=>$_SESSION['message'],
				));
				
                $returnUrl = $this->selfURL().'application/modules/Groupbuy/externals/scripts/redirectRequest.php?pstatus=success&'. $query_string.'&req5=';
                $cancelUrl = $this->selfURL().'application/modules/Groupbuy/externals/scripts/callback.php?action=callbackRequest&pstatus=cancel&'. $query_string.'&req5=';
                $notifyUrl = $this->selfURL().'application/modules/Groupbuy/externals/scripts/callback.php?action=callbackRequest&'. $query_string .'&req5=';
				
               list($receiver,$paramsPay) = Groupbuy_Api_Cart::getParamsPay($gateway_name,$returnUrl,$cancelUrl,$method_payment,$notifyUrl);
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
               $request_info = Groupbuy_Api_Cart::getPaymentRequest($acc['paymentrequest_id']);
                $_SESSION['request_info_user_id'] = $request_info['request_user_id'];
                $security_code = Groupbuy_Api_Cart::getSecurityCode();
                if($request_info != null)
                {
                    $paramsPay['receivers'] = array(
                    array('email' => $request_info['account_username'],'amount' => $request_info['send_amount'],'invoice' =>$security_code ),
                                             );
                } 
				
				$currency = Engine_Api::_() -> groupbuy() -> getDefaultCurrency();
                
                $this->view->paymentForm = $paymentForm ;
                $this->view->sercurity = $_SESSION['payment_sercurity_adminpayout'];
                $this->view->core_path = $this->selfURL();
                $this->view->account = $acc ;
                $this->view->status = $status ;
                $this->view->receiver = $paramsPay['receivers'][0];
                $this->view->currency = Groupbuy_Api_Core::getDefaultCurrency(); 
                $this->view->paramPay = $paramsPay;
        }


  }
 
    public function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
	  $http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'	;
      return $http.$_SERVER['HTTP_HOST'].$server_info."/";
      }
}

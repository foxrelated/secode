<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AccountController.php
 * @author     Minh Nguyen
 */
class Groupbuy_AccountController extends Core_Controller_Action_Standard
{
   public function init(){
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('groupbuy_main', array(), 'groupbuy_main_account');
    $this->_paginate_params['page']   = $this->getRequest()->getParam('page', 1);
    $this->_paginate_params['limit']  = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.page', 10); 
  } 
   public function createAction(){  
     // only members can create account
    if( !$this->_helper->requireUser()->isValid() ) return;  
     $this->view->form = $form = new Groupbuy_Form_CreateAccount();  
     $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
     $info = Groupbuy_Api_Account::getCurrentInfo($user_id);
        
     if($info['currency'])
             $form->removeElement('currency'); 
     $is_account= Groupbuy_Api_Account::getCurrentInfo(Engine_Api::_()->user()->getViewer()->getIdentity());
     if($is_account['account_username']!=null)
          $result=1;  
     if ( $this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost()) ) {
      $db = Engine_Api::_()->getDbTable('paymentAccounts', 'groupbuy')->getAdapter();
      $db->beginTransaction();
      try {
        $result = $this->view->form->saveValues();
        $this->view->result = $result;
        $db->commit();
        if($result)
            return $this->_redirect('group-buy/account');
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    }  
  }
   public function indexAction(){  
       if( !$this->_helper->requireUser()->isValid() ) return; 
        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();  
        $info_user = Groupbuy_Api_Account::getCurrentInfo($user_id);
        $info_account = Groupbuy_Api_Account::getCurrentAccount($user_id);   
        if(strlen($info_user['status'])>=41)
           $info_user['status'] = substr($info_user['status'],0,41)."...";
        $AmountSeller = Groupbuy_Api_Account::getAmountSeller($user_id);          
        $viewer = Engine_Api::_()->user()->getViewer();
        $min_payout = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.minWithdrawSeller', 5.00);
        $max_payout = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.maxWithdrawSeller', 100.00);
        $list_total=$AmountSeller;
         $params = array_merge($this->_paginate_params, array(
        'user_id' => $user_id,'list_total' => $list_total,
        ));
        $this->view->HistorySeller = $his = Groupbuy_Api_Account::getHistorySeller($params);
        $allow_request = 0;
        $requested_amount = Groupbuy_Api_Account::getTotalRequest($user_id,1);
        $refund_amount = Groupbuy_Api_Account::getTotalRequest($user_id,2);
        if($info_account['total_amount']>=$min_payout)
        {
            $allow_request = 1;
        }
        $rest = $info_account['total_amount'] - $requested_amount;
        $this->view->info_user = $info_user;
        $this->view->info_account = $info_account;
        $this->view->min_payout = $min_payout;
        $this->view->max_payout = $max_payout;
        $this->view->allow_request = $allow_request;
        $this->view->requested_amount = round($requested_amount + $refund_amount ,2);
        $this->view->current_amount = round($rest,2);
  }
   public function editAction(){ 
        if( !$this->_helper->requireUser()->isValid() ) return;      
        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity(); 
        $info = Groupbuy_Api_Account::getCurrentInfo($user_id);
        $info['full_name'] = $info['displayname'];
        $this->view->info =  $info;  
        $this->view->form = $form = new Groupbuy_Form_Account_Edit();
        if($info['currency'])
             $form->removeElement('currency');
        $form->populate($info);
        $post = $this->getRequest()->getPost();
        if(!isset($post['full_name']))
            return;
        if($post['full_name'] == "")
            return;
        if(!$form->isValid($post))
            return;
	    $email = $form->getValue('account_username'); 
		if(trim($email) == "")
		{
		       $form->getElement('account_username')->addError('Please enter valid email!'); 
		        return ;
		}
		else if(trim($email) != "")
	    {
	        $regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";                                                                                                            
	        if(!preg_match($regexp, $email))
	        {
	        $form->getElement('account_username')->addError('Please enter valid email!'); 
	        return ;
	        }
	    }
	    $aVals = $form->getValues();
        $aVals['displayname'] = $aVals['full_name'];
	    $result = Groupbuy_Api_Account::updateinfo($aVals);   
        $paymentaccount = Groupbuy_Api_Cart::getFinanceAccount($user_id,2); 
        Groupbuy_Api_Account::updateusername_account($paymentaccount['paymentaccount_id'],$aVals['account_username']);
	    if(isset($aVals['currency']))
        {
            Groupbuy_Api_Account::updatecurrency_account($paymentaccount['paymentaccount_id'],$aVals['currency']);
            $form->removeElement('currency');    
        }
	    $info_account = Groupbuy_Api_Account::getCurrentAccount($user_id);
	    if($info_account != null)
	    {
	        if($info_account['payment_type'] == 1)
	        {
	              $params['admin_account'] = $aVals['account_username'];
	              $params['is_from_finance'] = 1;
	              Groupbuy_Api_Gateway::saveSettingGateway('paypal',$params);   
	        }  
        $info = Groupbuy_Api_Account::getCurrentInfo($user_id);
        $info['full_name'] = $info['displayname'];
	    $form->addNotice('Your changes have been saved.');
	    }
        $this->view->info =  $info;  
        
  
  }
   public function selfURL() 
  {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
	  $http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'	;
      return $http.$_SERVER['HTTP_HOST'].$server_info."/";
  }
  public function thresholdAction(){  
         if (!$this->_helper->requireUser()->isValid()) { return;}
  		$this->_helper->layout->setLayout('admin-simple');
    	$this->view->form = $form = new Groupbuy_Form_Account_Request();
    	$form -> setAction($this -> getFrontController() -> getRouter() -> assemble( array()));
    	$values = array();
    	if($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			$values = $form -> getValues();
	    	
    	  $current_money = $values['txtrequest_money'];
		  $currency = Engine_Api::_() -> groupbuy() -> getDefaultCurrency();
		  $viewer = Engine_Api::_()->user()->getViewer();
		  $commission= Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('groupbuy_deal', $viewer, 'commission');
		  if($commission == "")
          {
             $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
             $maselect = $mtable->select()
                ->where("type = 'groupbuy_deal'")
                ->where("level_id = ?",$viewer->level_id)
                ->where("name = 'commission'");
              $mallow_a = $mtable->fetchRow($maselect);          
              if (!empty($mallow_a))
                $commission = $mallow_a['value'];
              else
                 $commission = 0;
          }
          $commission = 0;
          if(!$commission)
            $commission = 0;  
		  
            if(!is_numeric($current_money)){
            	$current_money = -10;
            }
                
            
            /*if (round($current_money,2) - $current_money!=0)
            {
                //$html = '<h2>Invalid request number .</h2>';
                 //echo '{"html":"'.$html.'"}';
                //return false;                
            }*/
           
            $info_account = Groupbuy_Api_Account::getCurrentAccount(Engine_Api::_()->user()->getViewer()->getIdentity());
            $TotalRequest = Groupbuy_Api_Account::getTotalRequest(Engine_Api::_()->user()->getViewer()->getIdentity());

            $min_payout = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.minWithdrawSeller', 5.00);
        	$max_payout = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.maxWithdrawSeller', 100.00);
            $allow_request=0;
            $warning = 0;
            $current_money_money = 0;
            $current_request_money = 0;
			
            if(round(($info_account['total_amount'] - $TotalRequest),2) >= round($current_money,2))
            {
                if($current_money != -10 && $current_money > 0)
                {
                    if($max_payout == -1 || $max_payout >= $current_money)
                    {
                        $allow_request=1;
                    }
                }
            }
            else if($current_money <= $max_payout)
            {
                $warning=1;
                $minhrequest = round($info_account['total_amount']-$TotalRequest-$min_payout,2);
                if($minhrequest < 0)
                    $minhrequest = 0;
                //$html = "You have requested " . round($TotalRequest,2) ."  before, so you only can request maximum is " . $minhrequest . " USD.";
	    	/*$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,	
	    		'parentRefresh'=> 10,
	    		'format'=>'smoothbox',
				'messages'=> array("You have requested " . round($TotalRequest,2) ."  before, so you can only request maximum " . $minhrequest),
			));*/
               //return  $form->getElement('txtrequest_money')->addError("You have requested " . round($TotalRequest,2) ."  before, so you can only request maximum " . $minhrequest);
            }
            else
            {
                $warning=1;
               // $html = "You only can request maximum is " . $max_payout . "  for each time";

               	/*$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,	
	    		'parentRefresh'=> 10,
               	'messages'=> array("You only can request maximum is " . $max_payout . "  for each time"),
			));*/
              // return $form->getElement('txtrequest_money')->addError("You can only request " . $max_payout . " maximum for each time");
                
            }
            if($allow_request==1)
            {
                $vals=array();
                $vals['request_user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
                $vals['request_amount'] = round($current_money,2);
                $vals['request_date'] = date('Y-m-d H:i:s');
                $vals['request_reason'] = strip_tags($values['textarea_request']);
                $vals['request_status'] = 0;
                $vals['commission'] = $commission;
				
                $vals['request_type'] = 1;
                $vals['dealbuy_id'] = 0;
                $vals['request_payment_acount_id'] = $info_account['paymentaccount_id'];
                $request_id = Groupbuy_Api_Account::insertRequest($vals);
                $info_account = Groupbuy_Api_Account::getCurrentAccount(Engine_Api::_()->user()->getViewer()->getIdentity());
                
                $html = "<h2>Request successfully!<h2><p class='description'><p>";
                $current_request_money = round($TotalRequest+$current_money,2);
                $current_money_money = round($info_account['total_amount']-$TotalRequest-$current_money,2);
            }
	    	$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,	
	    		'parentRefresh'=> 10,
	    		'messages'=> array(''),
			));
    	}

		// Output
			$this -> renderScript('account/form.tpl');
    	
         
  }
  public function requestmoneyAction()
  {
      if (!$this->_helper->requireUser()->isValid()) { return;} 
          $current_money = $this->getRequest()->getParam('currentmoney');
		  $currency = Engine_Api::_() -> groupbuy() -> getDefaultCurrency();
		  $viewer = Engine_Api::_()->user()->getViewer();
		  $commission= Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('groupbuy_deal', $viewer, 'commission');
           if($commission == "")
         {
             $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
             $maselect = $mtable->select()
                ->where("type = 'groupbuy_deal'")
                ->where("level_id = ?",$viewer->level_id)
                ->where("name = 'commission'");
              $mallow_a = $mtable->fetchRow($maselect);          
              if (!empty($mallow_a))
                $commission = $mallow_a['value'];
              else
                 $commission = 0;
         }
            if(!is_numeric($current_money))
                $current_money = -10;
            
            if (round($current_money,2) - $current_money!=0)
            {
                $html = '<h2>Invalid request number .</h2>';
                 echo '{"html":"'.$html.'"}';
                return false;                
            }
            $info_account = Groupbuy_Api_Account::getCurrentAccount(Engine_Api::_()->user()->getViewer()->getIdentity());
            $TotalRequest = Groupbuy_Api_Account::getTotalRequest(Engine_Api::_()->user()->getViewer()->getIdentity());

            $min_payout = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.minWithdrawSeller', 5.00);
            $max_payout = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.maxWithdrawSeller', 100.00);
            $allow_request=0;
            $warning = 0;
            $current_money_money = 0;
            $current_request_money = 0;
            if(round(($info_account['total_amount'] - $TotalRequest - $min_payout),2) >= round($current_money,2))
            {
                if($current_money!= -10 && $current_money>0)
                {
                    if($max_payout==-1 || $max_payout>=$current_money)
                    {
                        $allow_request=1;
                    }
                }
            }
            else if($current_money <= $max_payout)
            {
                $warning=1;
                $minhrequest = round($info_account['total_amount']-$TotalRequest-$min_payout,2);
                if($minhrequest < 0)
                    $minhrequest = 0;
                $html = "You have requested " . round($TotalRequest,2) ."  before, so you only can request maximum is " . $minhrequest . " USD.";
            }
            else
            {
                $warning=1;
                $html = "You only can request maximum is " . $max_payout . "  for each time";
            }
            if($allow_request==1)
            {
                $vals=array();
                $vals['request_user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
                $vals['request_amount'] = round($current_money,2);
                $vals['request_date'] = date('Y-m-d H:i:s');
                $vals['request_reason'] = strip_tags($this->getRequest()->getParam('reason'));
                $vals['request_status'] = 0;
				
                $vals['request_type'] = 1;
                $vals['dealbuy_id'] = 0;
                $vals['request_payment_acount_id'] = $info_account['paymentaccount_id'];
                $request_id = Groupbuy_Api_Account::insertRequest($vals);
                $info_account = Groupbuy_Api_Account::getCurrentAccount(Engine_Api::_()->user()->getViewer()->getIdentity());
                
                $html = "<h2>Request successfully!<h2><p class='description'><p>";
                $current_request_money = round($TotalRequest+$current_money,2);
                $current_money_money = round($info_account['total_amount']-$TotalRequest-$current_money,2);
            }
            else if($warning!=1)
            {
                $html = "<h2>Request false!</h2><p class='description'><p>";
            }
            return $this->_helper->redirector->gotoRoute(array('action' => 'index'), 'groupbuy_account', true);
            //echo '{"html":"'.$html.'","current_request_money":"'.$current_request_money.'","current_money_money":"'.$current_money_money.'"}';
    }
    public function loadMessageAction(){
        //tat di layout
        $this->_helper->layout->disableLayout();
       //khong su dung view
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $item_id = $this->getRequest()->getParam('item_id');
        $request = Groupbuy_Api_Cart::getRequestsFromUser($item_id); 
        $html = "";
        foreach ($request as $req)
        {
            if ($req['request_status'] == 1)
            {
                $a = '<div style=\'background:url(./application/modules/Groupbuy/externals/images/refund.png) no-repeat scroll left center\'><span style=\'color:blue;margin-left:20px;font-weight:bold\'>'.$req['pDate'] .' - '.$this->view->translate("Approved").' '.'</span></div>';
                $a.="<p>".$req['request_date']."</p>";
                $a.="<p>".$req['request_answer']."</p>";
                
            }
            else
            {
                $a = '<div style=\'background:url(./application/modules/Groupbuy/externals/images/refund.png) no-repeat scroll left center\'><span style=\'color:red;margin-left:20px;font-weight:bold\'>'.$req['pDate'] .' - '.$this->view->translate("Denied").' '.'</span></div>';
                $a.="<p>".$req['request_date']."</p>";
                $a.='<p>'.$req['request_answer'].'</p>';
            }
            
            $html .='<div class=\'p_4\'>'.$a.'</div>';
        }
        if($html == "")
        {
            $html = $this->view->translate("There are no messages from admin.");
        }
        else
        {   if(count($request)>8)
                $html = '<div style=\'overflow: auto ;height:200px ;margin-top:5px\'>'.$html.'</div>';
            
        }   
        $html.='<div class=\'p_4\'><a href=\'javascript:close('.$item_id.')\'>'.$this->view->translate("Close").'</a></div>';
        echo '{"html":"'.$html.'"}';
    }
}
 

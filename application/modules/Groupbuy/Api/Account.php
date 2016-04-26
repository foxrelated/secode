<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Account.php
 * @author     Minh Nguyen
 */
class Groupbuy_Api_Account extends Core_Api_Abstract
{
    public function getCurrentInfo($user_id){
        $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'groupbuy');
        $select = $table->select() ->setIntegrityCheck(false)
                        ->from('engine4_groupbuy_payment_accounts as account','account.*')  
                        ->joinRight('engine4_users','engine4_users.user_id =  account.user_id','engine4_users.*')
                        ->where('account.payment_type = 2')
                        ->where('engine4_users.user_id = ?',$user_id);
        $result =   $table->fetchAll($select)->toArray();
        return @$result[0];
    }
	
	public function getByUserId($user_id){
				
        $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'groupbuy');
		
        $select = $table->select() ->setIntegrityCheck(false)
                        ->from('engine4_groupbuy_payment_accounts as account','account.*')  
                         ->where('account.payment_type = 2')
                        ->where('account.user_id = ?',$user_id);
						
        return   $table->fetchRow($select);        
    }
	
    public function getCurrentAccount($user_id){
        $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'groupbuy');
        $select = $table->select() ->setIntegrityCheck(false)
                        ->from('engine4_groupbuy_payment_accounts as account','account.*')  
                         ->where('account.payment_type = 2')
                        ->where('account.user_id = ?',$user_id);
        $result =   $table->fetchAll($select)->toArray();
        return @$result[0];
    }
    public function getHistorySellerSelect($params){
            $tt_table  = Engine_Api::_()->getDbTable('transactionTrackings', 'groupbuy');
            $tt_name   = $tt_table->info('name');
            $a_table = Engine_Api::_()->getDbTable('deals', 'groupbuy');
            $a_name  = $a_table->info('name');
            $select   = $tt_table->select()->setIntegrityCheck(false)
                        ->from($tt_table,array('sum(number) as total',"$tt_name.transactiontracking_id"))
                        ->joinLeft($a_name,"$a_name.deal_id = $tt_name.item_id",array("$a_name.*"))
                        ->where("$a_name.user_id = ? ",$params["user_id"])->where("$tt_name.params = 'buy'")
                        ->group("$tt_name.item_id");
          return $select;
    }
    public function getHistorySeller($params){
        $sellerPaginator = Zend_Paginator::factory(Groupbuy_Api_Account::getHistorySellerSelect($params));
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
    public function updateinfo($avals=array()){
        $user   = Engine_Api::_()->user()->getViewer();
        $user->displayname = $avals['displayname'];
        return $user->save();   
    }
    public function updateusername_account($paymentaccount_id,$account_username){ 
        $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'groupbuy');
        $data = array(
            'account_username' => $account_username
        );
        $where = $table->getAdapter()->quoteInto('paymentaccount_id = ?', $paymentaccount_id);
        return $table->update($data, $where);
    }
    public function updatecurrency_account($paymentaccount_id,$currency){ 
        $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'groupbuy');
        $data = array(
            'currency' => $currency
        );
        $where = $table->getAdapter()->quoteInto('paymentaccount_id = ?', $paymentaccount_id);
        return $table->update($data, $where);
    }
    public function updateAmount($paymentaccount_id, $amounts, $type)
    {
    	if(!$paymentaccount_id)
		{
			return;
		}
        $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'groupbuy');
        $select = $table->select()->from($table->info('name')) ;
        $select->where('paymentaccount_id = ?', $paymentaccount_id);   
        $result = $table->fetchRow($select);
        
        $totalamount = $result->total_amount;
        if($type == 1)
            $data = array(
                'total_amount' => $totalamount + $amounts
            );
        else
            $data = array(
                'total_amount' => $totalamount - $amounts
            );
        $where = $table->getAdapter()->quoteInto('paymentaccount_id = ?', $paymentaccount_id);
        return $table->update($data, $where);
    }
    public function insertAccount($results = array()){
        $account = Engine_Api::_()->getDbtable('paymentAccounts', 'groupbuy')->createRow();  
        $account->account_username = $results['account_username'];   
        $account->currency = $results['currency'];
        $account->total_amount = 0;
        $account->account_status = 1;
        $account->payment_type = 2;
        $account->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $account->save(); 
        return 1;
    }
     public function addAccount($user_id){
        $account = Engine_Api::_()->getDbtable('paymentAccounts', 'groupbuy')->createRow();  
        $account->account_username = '';
        $account->total_amount = 0;
        $account->account_status = 1;
        $account->payment_type = 2;
        $account->user_id = $user_id;
        $account->save(); 
        return 1;
    }
    public function getAmountSeller($user_id){     
        $lst_table = Engine_Api::_()->getDbTable('buyDeals', 'groupbuy');  
        $lst_name  = $lst_table->info('name');    
        $ab_table = Engine_Api::_()->getDbTable('deals', 'groupbuy');
        $ab_name  = $ab_table->info('name');
        $s_table = Engine_Api::_()->getDbTable('categories', 'groupbuy');
        $s_name  = $s_table->info('name');
        $select = $lst_table->select()
                    ->from("$lst_name as lst",array('count(*) as count','lst.item_id'))
                    ->joinLeft($ab_name,"$ab_name.deal_id = lst.item_id",'')
                    ->joinLeft($s_name,"$s_name.category_id = $ab_name.category_id",'')
                    ->where("$ab_name.user_id= ?",$user_id)
                    ->orWhere(' 0 < ((SELECT count(*) from  engine4_groupbuy_deals as deal where deal.user_id='.$user_id.' and deal.deal_id = lst.item_id))')
                    ->group('lst.item_id')
                    ;
         $result =   $lst_table->fetchAll($select)->toArray();
         return count($result);
    }
    public function insertRequest($vals=array()){
    	$self = new self;
		
        $req = Engine_Api::_()->getDbtable('paymentRequests', 'groupbuy')->createRow();  
        $req->request_user_id = $vals['request_user_id'];
		$viewer = Engine_Api::_()->user()->getUser($req->request_user_id);
		$currency = Engine_Api::_() -> groupbuy() -> getDefaultCurrency();
		//$commission= Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('groupbuy_deal', $viewer, 'commission');
		//if(!$commission)
        $commission = 0;  
		// get payment account.
		$paymentAccount =  $self->getByUserId($req->request_user_id);

        $req->request_payment_acount_id = $vals['request_payment_acount_id'];
        $req->request_amount = $vals['request_amount'];
		$req->commission =  $commission;
		
		// tinh toan ti le gui tien
		$percentage = $paymentAccount->total_price_amount/($paymentAccount->total_amount);
		
        $req->commission = $commission;
		$req->commission_fee = round($commission/100* $req->request_amount*$percentage,2);
		$req->send_amount = $req->request_amount - $req->commission_fee;
        $req->request_status = $vals['request_status'];
        $req->request_reason = $vals['request_reason'];
        $req->request_type = $vals['request_type'];
		$req->request_currency = $currency;
        $req->dealbuy_id = $vals['dealbuy_id'];
        $req->request_date = $vals['request_date'];
        $req->save(); 
        return 1;
    }
    public function getTotalRequest($request_user_id,$type = 1){
        $table  = Engine_Api::_()->getDbtable('paymentRequests', 'groupbuy');
        $select = $table->select() ->setIntegrityCheck(false)
                        ->from('engine4_groupbuy_payment_requests',array('sum(request_amount) as totalrequest','request_user_id')) 
                        ->where('request_user_id = ?',$request_user_id)
                        ->where('request_status in ( 0,-2)')
                        ->where('request_type = ?',$type)
                        ->group('request_user_id');
        $result =   $table->fetchAll($select)->toArray();
        return @$result[0]['totalrequest']; 
    }
}   
?>

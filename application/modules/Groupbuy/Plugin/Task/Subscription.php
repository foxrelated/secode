<?php
class Groupbuy_Plugin_Task_Subscription extends Groupbuy_Plugin_Task_Abstract {
	protected static $_baseUrl;
	
	public static function getBaseUrl(){
		if(self::$_baseUrl == NULL){
			self::$_baseUrl = Engine_Api::_()->getApi('settings','core')->getSetting('groupbuy.baseURL','http://');
		}
		return self::$_baseUrl;
	}
	/**
	 * @param   string $type
	 * @return  string
	 */
	public function baseUrl() 
    {
      return self::getBaseUrl();
      
    }
	/**
	 * @return Groupbuy_Model_DbTable_Deals
	 */
	public function getDealsTable() {
		return Engine_Api::_() -> getDbTable('deals', 'groupbuy');
	}

	/**
	 * @param   Zend_Db_Adapter    $db
	 * @param   int                $contact_limit
	 */
	public function getContacts($db, $contact_limit = 10) {
		$sql = "
				select 
				contacts.*, relations.subscriptionrelation_id
				from 
				engine4_groupbuy_subscription_relations as relations
				join engine4_groupbuy_subscription_contacts as contacts on (relations.contact_id = contacts.subscriptioncontact_id)
				where relations.sended = 0  and contacts.verified = 1
				group by relations.contact_id
				order by rand()
				limit {$contact_limit} offset 0;
		";
						
		return $db -> fetchAll($sql);
	}

	/**
	 * @param   Zend_Db_Adapter     $db
	 * @param   int                 $contact_id
	 * @param   string              $cur_time
	 * @param   int                 $deal_limit
	 * @return  Zend_Db_Table_RowSet
	 */
	public function getDeals($db, $contact_id, $cur_time, $deal_limit = 10) {
		$sql = "
				select	deals.deal_id
				from engine4_groupbuy_subscription_relations as relations
				join engine4_groupbuy_deals as deals on (relations.deal_id =  deals.deal_id)
				where
					1 
					and relations.contact_id =  '$contact_id'
					and relations.sended = 0
					and deals.end_time >= '$cur_time'
					and deals.status = 30
					and deals.published = 20
				group by deal_id
				limit {$deal_limit} offset 0;
				";
		
		$ids =  $db -> fetchCol($sql);
		if(!$ids){
			return false;
		}
		
		$table =  Engine_Api::_()->getDbTable('deals','groupbuy');
		$select =  $table->select()->where('deal_id in (?) ', $ids)->order('featured DESC');
		return $table->fetchAll($select);
	}
	
	/**
	 * 
	 * parse deal and contact to deals
	 * @param   array   $contact
	 * @param   array   $deals
	 * @return  string
	 */
	public function getDodContent($contact, $deals){
		$this->deals =  $deals;
		$this->contacts =  $contact;
		$filename = APPLICATION_PATH . '/application/modules/Groupbuy/views/scripts/mail/dod_content.tpl';
		ob_start();
		include $filename;
		$result = ob_get_clean();
		return $result;
	}
	
	/**
	 * call from cli
	 */
	public function execute() {
		$this->view =  $view = Zend_Registry::get('Zend_View');
		$view->addHelperPath( APPLICATION_PATH .'/application/modules/Groupbuy/views/helpers', 'Groupbuy_View_Helper');
		try {
			$mail =  Engine_Api::_()->getApi('mail','groupbuy');
			$table = Engine_Api::_() -> getDbTable('Deals', 'Groupbuy');
			$db = $table -> getAdapter();
			$cur_time = date('Y-m-d H:i:s');
			$contact_limit = 100;
			$deal_limit = 1;
			$contacts  = $this->getContacts($db, $contact_limit) ;
			
			foreach($contacts as $contact) {
				$contact['contact_id'] = $contact['subscriptioncontact_id'];
				$contact_id = $contact['subscriptioncontact_id'];
				$sended = array();
				$deals = $this -> getDeals($db, $contact_id, $cur_time, $deal_limit);
				
				
				if($deals != NULL) {
					$params['deal_dodcontent'] = $this->getDodContent($contact, $deals);
					$params['email'] = $contact['email'];
					$params['verify_code'] = $contact['verify_code'];
					$params['unsubscribe_link'] =  $this->getBaseUrl(). 'groupbuy/subscription/unsubscribe/code/'. $contact['verify_code'];
					$mail->send($contact['email'],'groupbuy_dealday', $params);
					foreach($deals as $item){
						$sended[] =  $item->deal_id;
					}					
				}
				if($sended){
					$this->updateRelations($db, $contact_id, $sended);	
				}
				
			}
			// write to log
			$this -> writeLog(sprintf("%s, send mail run", date('Y-m-d H:i:s')));
		} catch(Exception $e) {
			throw $e;
			$this -> writeLog(sprintf("%s: ERROR %s", date('Y-m-d H:i:s'), $e -> getMessage()));
		}
	}
	
	/**
	 * @param    Zend_Db_Adapter   $db
	 * @param    int               $contact_id
	 * @param    array             $sended
	 * @return   NULL
	 */	
	public function updateRelations($db, $contact_id, $sended){
		$query = "update engine4_groupbuy_subscription_relations set sended = 1 where contact_id={$contact_id} and deal_id in ('".implode(',',$sended).",0')";
		$db->query($query);
	}
}

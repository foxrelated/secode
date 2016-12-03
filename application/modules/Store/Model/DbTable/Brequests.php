<?php

class Store_Model_DbTable_Brequests extends Engine_Db_Table
{
	protected $_serializedColumns = array('product_ids');
	protected $_rowClass = "Store_Model_Brequest";
	
	public function getRequestCountByProductId($product_id) {
		$select = $this->select()->where('product_id = ?',$product_id)->where('status = ?','pending');
		$result = $this->fetchAll($select);
		return count($result);
	}
	
	public function getRequestsByProductId($product_id) {
		$select = $this->select()->where('product_id = ?',$product_id)->where('status = ?','pending')->order('creation_date DESC');
		$paginator = Zend_Paginator::factory($select);
		return $paginator;
	}
	
	public function hasSuccessRequest($requester, $owner) {
		$tblName = $this->info('name');
		$productTblName = Engine_Api::_()->getDbTable('products', 'store')->info('name');
                $rTblName = Engine_Api::_()->getDbTable('reviews', 'ynmember');
                
                $select = $this->select()
			->from($tblName)
			->setIntegrityCheck(false)
			->joinLeft($productTblName, "`{$productTblName}`.`product_id` = `{$tblName}`.`product_id`", null)
			->where("$tblName.user_id = ?", $requester->getIdentity())
			->where("$productTblName.owner_id = ?", $owner->getIdentity())
			->where("$tblName.status = ?", 'approve');
		$rows = $this->fetchAll($select);
                
                $rselect = $rTblName->select()->where("resource_id = ?",$owner->getIdentity())->where('user_id = ?',$requester->getIdentity());
                $data = $rTblName->fetchAll($rselect);
                foreach($data as $d) {
                    $items[$d->item_id] = $d->item_id;
                }
                
                foreach($rows as $r) {
                    $pids[$r->product_id] = $r->product_id;
                }
                
                $diff1 = array_diff($items, $pids);
                    $diff2 = array_diff($pids, $items);
                    
                    $na = array_merge($diff1,$diff2);
                
                //$data = $rTblName->fetchAll($select);

		return count($na) > 0 ? true : false;
	}
        
        public function getSuccessRequests($requester, $owner, $item = false, $options = array()) {
		$tblName = $this->info('name');
		$productTblName = Engine_Api::_()->getDbTable('products', 'store')->info('name');
		$select = $this->select()
			->from($tblName)
			->setIntegrityCheck(false)
			->joinLeft($productTblName, "`{$productTblName}`.`product_id` = `{$tblName}`.`product_id`", null)
			->where("$tblName.user_id = ?", $requester->getIdentity())
			->where("$productTblName.owner_id = ?", $owner->getIdentity())
                        ->group("$tblName.product_id")        
			->where("$tblName.status = ?", 'approve');
                        
		$requests = $this->fetchAll($select);	
                
                $rTblName = Engine_Api::_()->getDbTable('reviews', 'ynmember');
                $rselect = $rTblName->select()->where("resource_id = ?",$owner->getIdentity())->where('user_id = ?',$requester->getIdentity());
                $data = $rTblName->fetchAll($rselect);
                
                if($item === true) {
                    
                    foreach($data as $d) {
                         $ids[$d->item_id] = $d->item_id;
                    }

                    foreach($requests as $r) {
                        $pids[$r->product_id] = $r->product_id;
                    }
                    
                    $diff1 = array_diff($ids, $pids);
                    $diff2 = array_diff($pids, $ids);
                    
                    $na = array_merge($diff1,$diff2);
                    
                    $items = array();
                    foreach($na as $n) {
                        $items[] = Engine_Api::_()->getItem('store_product',$n);
                    }
                    
                    if(!empty($options['assoc']) && $options['assoc'] === true) {
                        $assoc_items = array();
                        foreach($items as $item) {
                            $assoc_items[$item->getIdentity()] = $item->getTitle();
                        }
                        
                        return $assoc_items;
                    }else{
                        return $items;
                    }
                }
                
                return $requests;
                
	}
}

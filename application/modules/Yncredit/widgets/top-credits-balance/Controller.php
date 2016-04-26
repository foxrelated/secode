<?php

class Yncredit_Widget_TopCreditsBalanceController extends Engine_Content_Widget_Abstract {
    
    public function indexAction() 
    {
        $balanceTable = Engine_Api::_() -> getDbTable('balances', 'yncredit');
		$params = array('top' => true, 'orderby' => 'current_credit', 'direction' => 'DESC');
		$this -> view -> balances = $balances = $balanceTable -> getMembersPaginator($params);
		$balances -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 5));
    }
}
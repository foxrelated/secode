<?php

class Ynfundraising_AdminCurrencyController extends Core_Controller_Action_Admin {

	public function init()
	{

	}

	public function indexAction() {

		$table = new Ynfundraising_Model_DbTable_Currencies;
		$select = $table -> select();

		$paginator = Zend_Paginator::factory($select);
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		$paginator -> setItemCountPerPage(10);
		$this -> view -> paginator = $paginator;
	}

	public function editAction() {
		//Get Form Edit Currency
		$this->_helper->layout->setLayout ( 'admin-simple' );

		$form = $this -> view -> form = new Ynfundraising_Form_Admin_Currency();

		// Get Code Id - Throw Exception If There Is No Code Id
		if(!($code = $this -> _getParam('code_id'))) {
			throw new Zend_Exception('No code id specified');
		}

		// Generate and assign form
		$table = new Ynfundraising_Model_DbTable_Currencies;
		$select = $table -> select() -> where('code = ?', "$code");
		$currency = $table -> fetchRow($select);
		$form -> populate(array('label' => $currency -> name, 'symbol' => $currency -> symbol, 'precision' => $currency -> precision, 'status' => $currency -> status, 'display' => $currency -> currencyDisplay(), 'code' => $currency -> code));

		//Check post method
		if($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {

			$values = $form -> getValues();
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();
			try {
				// Edit currency in the database
				$code = $values["code"];
				$table = new Ynfundraising_Model_DbTable_Currencies;
				$select = $table -> select() -> where('code = ?', "$code");
				$row = $table -> fetchRow($select);

				$row -> name = $values["label"];
				$row -> symbol = $values["symbol"];
				$row -> precision = $values["precision"];
				$row -> display = 'Use Symbol';
				//Database Commit
				$row -> save();
				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			//Close Form If Editing Successfully
			return $this->_forward ( 'success', 'utility', 'core', array (
					'messages' => array (
							Zend_Registry::get ( 'Zend_Translate' )->_ ( 'Edit currency successfully.' )
					),
					'layout' => 'admin-simple',
					'parentRefresh' => true
			) );
		}




		//Output
		//$this -> renderScript('admin-currency/form.tpl');

	}

}

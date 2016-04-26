<?php

class Ynaffiliate_LinksController extends Core_Controller_Action_Standard {
   public function init() {
			if(!$this -> _helper -> requireUser() -> isValid()){
			return ;
		}
   }
	public function indexAction()
	{

	}

}

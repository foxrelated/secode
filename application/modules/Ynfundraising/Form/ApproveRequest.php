<?php
class Ynfundraising_Form_ApproveRequest extends Ynfundraising_Form_DeleteRequest {
	public function init() {
		parent::init ();
		$this->setTitle ( 'Approve Request' )->setDescription ( 'Are you sure you want to approve this request?' );
		$this->submit->setLabel ( 'Approve Request' );
	}
}
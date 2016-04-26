<?php
/**
 * YouNet Company
*
* @category   Application_Extensions
* @package    Ynfundraising
* @author     YouNet Company
*/
class Ynfundraising_RequestController extends Core_Controller_Action_Standard
{
	/**
	 * init check exist Ynidea plugin enable
	 *
	 */
	public function init()
  	{
  		if(!Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->checkIdeaboxPlugin ())
		{
			return $this->_helper->requireAuth->forward ();
		}
	}
	public function indexAction()
	{
		// Check authoraiztion permisstion
		if (! $this->_helper->requireUser ()->isValid ()) {
			return;
		}
		if (! $this->_helper->requireAuth ()->setAuthParams ( 'ynfundraising_campaign', null, 'view' )->isValid ()) {
			return;
		}
		// Prepare data
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$form = new Ynfundraising_Form_RequestSearch ();

		// Process form
		$form->isValid ( $this->_getAllParams () );
		$values = $form->getValues ();
		$this->view->formValues = array_filter ( $values );

		$values ['requester_id'] = $viewer->getIdentity ();
		// Get campaign paginator
		$items_count = ( int ) Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynfundraising.page', 10 );
		$paginator = Engine_Api::_ ()->ynfundraising ()->getRequestPaginator ( $values );
		$paginator->setItemCountPerPage ( $items_count );
		$this->view->paginator = $paginator;
		// render
		$this->_helper->content->setEnabled ();
	}
	public function deleteAction() {
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$this->_helper->layout->setLayout ( 'default-simple' );
		$this->view->form = $form = new Ynfundraising_Form_DeleteRequest ();
		$request_id = $this->_getParam ( 'request_id' );
		$request = Engine_Api::_ ()->getItem ( 'ynfundraising_request', $request_id );
		// item is idea or trophy
		$item = Engine_Api::_ ()->ynfundraising ()->getItemFromType ( $request );
		$is_delete = false;
		if (! $request) {
			$is_delete = false;
		} else {
			if ($request->status == Ynfundraising_Plugin_Constants::REQUEST_WAITING_STATUS) {
				if ($request->requester_id == $viewer->getIdentity ()) {
					$is_delete = true;
				} else if ($item->user_id == $viewer->getIdentity ()) {
					$is_delete = true;
				}
			}
		}
		if (! $is_delete) {
			return $this->_helper->requireAuth->forward ();
		}

		if (! $this->getRequest ()->isPost ()) {
			return;
		}
		$table = Engine_Api::_ ()->getDbTable ( 'requests', 'ynfundraising' );
		$db = $table->getAdapter ();
		$db->beginTransaction ();

		try {
			$request->delete ();
			$db->commit();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}

		$this->_forward ( 'success', 'utility', 'core', array (
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'format' => 'smoothbox',
				'messages' => array (
						$this->view->translate ( 'This request is deleted successfully.' )
				)
		) );
	}
	public function approveAction() {
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$this->_helper->layout->setLayout ( 'default-simple' );
		$this->view->form = $form = new Ynfundraising_Form_ApproveRequest ();
		$request_id = $this->_getParam ( 'request_id' );
		$request = Engine_Api::_ ()->getItem ( 'ynfundraising_request', $request_id );
		// item is idea or trophy
		$item = Engine_Api::_ ()->ynfundraising ()->getItemFromType ( $request );
		$is_approved = true;

		/*
		 * Can only approve a request if its status is Waiting and it comes from
		 * the ideas/trophies of viewer (1)
		 * AND
		 * There is no request from that idea/trophy that has status = approved (2)
		 */

		if (! $request) {
			$is_approved = false;
		} else {
			if ($request->status != Ynfundraising_Plugin_Constants::REQUEST_WAITING_STATUS
					|| $item->user_id != $viewer->getIdentity () || $request->visible != 1) {
				$is_approved = false;
			}
		}

		if (! $is_approved) {
			// may render to another view
			return $this->_helper->requireAuth->forward ();
		}

		if (! $this->getRequest ()->isPost ()) {
			return;
		}

		$table = Engine_Api::_ ()->getDbTable ( 'requests', 'ynfundraising' );
		$db = $table->getAdapter ();
		$db->beginTransaction ();

		try {
			$request->status = Ynfundraising_Plugin_Constants::REQUEST_APPROVED_STATUS;
			$request->approved_date = date ( 'Y-m-d H:i:s' );
			$request->save ();

			$requestTbl = Engine_Api::_ ()->getItemTable ( 'ynfundraising_request' );
			$other_request_select = $requestTbl->select ()->where ( 'request_id <> ?', $request->getIdentity () )
				->where ( 'parent_type = ?', $request->parent_type )
				->where ( 'parent_id = ?', $request->parent_id )
				->where('is_completed = ?', 0);
			$other_request_result = $requestTbl->fetchAll ( $other_request_select );
			if (count ($other_request_result) > 0) {
				foreach ( $other_request_result as $other_request ) {
					$other_request->visible = 0;
					$other_request->save ();
				}
			}
			$db->commit();
			//send email to requester
			$objParent = Engine_Api::_()->getApi('core', 'ynfundraising')->getItemFromType($request->toArray());
			$timeout = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynfundraising_timeout', 0);
			// send mail to requester
			$params = array(
					'parent_owner' => $objParent->getOwner()->getTitle(),
					'campaign_type' => $request->parent_type,
					'time_out'	=> $timeout . 'h',
					'create_campaign_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'create-step-one', 'request_id' => $request->getIdentity()),"ynfundraising_general")
			);
			$user = Engine_Api::_ ()->getItem ( 'user', $request->requester_id );
			if ($user->getIdentity() > 0) {
				Engine_Api::_()->getApi('mail','ynfundraising')->send($user->email, 'fundraising_requestApproved',$params);
			}

		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}

		$this->_forward ( 'success', 'utility', 'core', array (
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'format' => 'smoothbox',
				'messages' => array (
						$this->view->translate ( 'This request is approved successfully.' )
				)
		) );
	}
	public function denyAction() {
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$values = $this->_getAllParams ();
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$this->_helper->layout->setLayout ( 'default-simple' );
		$this->view->form = $form = new Ynfundraising_Form_DenyRequest ();
		$request_id = $values ['request_id'];
		$request = Engine_Api::_ ()->getItem ( 'ynfundraising_request', $request_id );
		// item is idea or trophy
		$item = Engine_Api::_ ()->ynfundraising ()->getItemFromType ( $request );

		if (! $request) {
			return $this->_helper->requireAuth->forward ();
		}

		if (! $item || $item->user_id != $viewer->getIdentity () || $request->visible != 1 || $request->status != Ynfundraising_Plugin_Constants::REQUEST_WAITING_STATUS) {
			return $this->_helper->requireAuth->forward ();
		}

		if (! $this->getRequest ()->isPost ()) {
			return;
		}

		$table = Engine_Api::_ ()->getDbTable ( 'requests', 'ynfundraising' );
		$db = $table->getAdapter ();
		$db->beginTransaction ();

		try {
			$request->status = Ynfundraising_Plugin_Constants::REQUEST_DENIED_STATUS;
			$request->reason = $values ['reason'];
			$request->save ();
			$db->commit();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}

		$this->_forward ( 'success', 'utility', 'core', array (
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'format' => 'smoothbox',
				'messages' => array (
						$this->view->translate ( 'This request is denied successfully.' )
				)
		) );
	}
	public function viewReasonAction() {
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$this->_helper->layout->setLayout ( 'default-simple' );
		$this->view->form = $form = new Ynfundraising_Form_DenyReason ();
				$request_id = $this->_getParam ( 'request_id' );
		$request = Engine_Api::_ ()->getItem ( 'ynfundraising_request', $request_id );
		// item is idea or trophy
		$item = Engine_Api::_ ()->ynfundraising ()->getItemFromType ( $request );
		$is_allow = false;
		if (! $request || !$item) {
			$is_allow = false;
		} else {
			if ($request->status == Ynfundraising_Plugin_Constants::REQUEST_DENIED_STATUS) {
				if ($request->requester_id == $viewer->getIdentity ()) {
					$is_allow = true; // if this request is belong to viewer
				} else if ($item->user_id == $viewer->getIdentity ()) {
					$is_allow = true; // if this request comes from idea/trophy of owner
				}
			}
		}
		if (! $is_allow) {
			return $this->_helper->requireAuth->forward ();
		}

		$values = $this->_getAllParams ();
		$request_id = $values ['request_id'];
		$request = Engine_Api::_ ()->getItem ( 'ynfundraising_request', $request_id );

		// populate values
		$form->populate ( $request->toArray () );
	}
}
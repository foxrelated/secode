<?php
class Ynfundraising_AdminManageController extends Core_Controller_Action_Admin
{
	/**
	 * init check exist Ynidea plugin enable
	 *
	 */
	public function init()
  	{

	}
	public function indexAction()
	{
		$this->view->form = $form = new Ynfundraising_Form_Admin_Search ();
		$form->isValid ( $this->_getAllParams () );
		$params = $form->getValues ();
		if (empty ( $params ['orderby'] ))
			$params ['orderby'] = 'campaign_id';
		if (empty ( $params ['direction'] ))
			$params ['direction'] = 'DESC';
		
		$params['mycontest'] = 1;
		$this->view->formValues = $params;
		
		// Get Campaign Paginator
		$this->view->paginator = Engine_Api::_ ()->ynfundraising ()->getCampaignPaginator ( $params );

		$items_per_page = Engine_Api::_ ()->getApi ( 'settings', 'core')->getSetting ( 'ynfundraising.page', 10 );
		$this->view->paginator->setItemCountPerPage ( $items_per_page );
	}

	/* ----- Set Featured Campaign Function ----- */
	public function featureAction() {
		// Get params
		$id = $this->_getParam ( 'campaign_id' );
		$is_featured = $this->_getParam ( 'status' );

		// Get campaign need to set featured
		$table = Engine_Api::_ ()->getItemTable ( 'ynfundraising_campaign' );
		$select = $table->select ()->where ( "campaign_id = ?", $id );
		$campaign = $table->fetchRow ( $select );

		// Set featured/unfeatured
		if ($campaign) {
			$campaign->is_featured = $is_featured;
			$campaign->save ();
		}
	}
	/* ----- Set Activated Campaign Function ----- */
	public function activatedAction() {
		// Get params
		$id = $this->_getParam ( 'campaign_id' );
		$is_activated = $this->_getParam ( 'status' );

		// Get campaign need to set featured
		$table = Engine_Api::_ ()->getItemTable ( 'ynfundraising_campaign' );
		$select = $table->select ()->where ( "campaign_id = ?", $id );
		$campaign = $table->fetchRow ( $select );
		
		// Set featured/unfeatured
		if ($campaign) {
			$campaign->activated = $is_activated;
			//send not and email with inactivated campaigns
			if($is_activated)
			{
				$key = 'campaign_activated';
				//Your campaign has been   successfully.
			}	
			else {
				$key = 'campaign_inactivated';
			}
			//send notify and email
			$viewer = Engine_Api::_()->user()->getViewer();
			$owner = $campaign->getOwner();
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($owner, $viewer, $campaign, $key);
			
			$action = @Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $campaign, $key);
			if( $action != null )
			{
				Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $campaign);
			}
				
			$campaign->save ();
		}
	}
}
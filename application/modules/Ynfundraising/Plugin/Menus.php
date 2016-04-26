<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Fundraising
 * @copyright  Copyright 2011 YouNet Company
 * @license    http://www.modules2buy.com/
 * @version    $Id: Menus.php
 * @author     Minh Nguyen
 */
class Ynfundraising_Plugin_Menus {
	public function showPastCampaigns() {
		// Must be logged in
		/*$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $viewer || ! $viewer->getIdentity ()) {
			return false;
		}*/
		return true;
	}
	public function showMyCampaigns() {
		// Must be logged in
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $viewer || ! $viewer->getIdentity ()) {
			return false;
		}
		if( !Engine_Api::_()->authorization()->isAllowed('ynfundraising_campaign', $viewer, 'create') ) {
	      return false;
	    }
		return true;
	}
	public function showMyRequests() {
		// Must be logged in
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $viewer || ! $viewer->getIdentity ()) {
			return false;
		}

		if( !Engine_Api::_()->authorization()->isAllowed('ynfundraising_campaign', $viewer, 'create') ) {
	      return false;
	    }

		if(!Engine_Api::_ ()->ynfundraising()->checkIdeaboxPlugin())
		{
			return FALSE;
		}

		return true;
	}
	public function showManageRequests() {
		// Must be logged in
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $viewer || ! $viewer->getIdentity ()) {
			return false;
		}

		if( !Engine_Api::_()->authorization()->isAllowed('ynidea_idea', $viewer, 'create') && !Engine_Api::_()->authorization()->isAllowed('ynidea_trophy', $viewer, 'create'))
		{
	      return false;
	    }

		if(!Engine_Api::_ ()->ynfundraising()->checkIdeaboxPlugin())
		{
			return FALSE;
		}

		return true;
	}
	public function showCreateCampaigns() {
		// Must be logged in
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $viewer || ! $viewer->getIdentity ()) {
			return false;
		}
		if( !Engine_Api::_()->authorization()->isAllowed('ynfundraising_campaign', $viewer, 'create') ) {
	      return false;
	    }
		return true;
	}

	public function canCreateCampaigns() {
		// Must be logged in
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $viewer || ! $viewer->getIdentity ()) {
			return false;
		}

		// Must be able to create campaign
	    if( !Engine_Api::_()->authorization()->isAllowed('ynfundraising_campaign', $viewer, 'create') ) {
	      return false;
	    }

		return true;
	}

}
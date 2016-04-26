<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_LinksSitegroupController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET VIEWER CLAIMS
        $claim_id = Engine_Api::_()->getDbtable('claims', 'sitegroup')->getViewerClaims($viewer_id);

        //CLAIM IS ENABLED OR NOT
        $canClaim = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitegroup_group', 'claim');

        $this->view->showClaimLink = 0;
        if (!empty($claim_id) && !empty($canClaim)) {
            $this->view->showClaimLink = 1;
        }

        //CHECK THAT WE HAVE TO SHOW MANAGE ADMIN LINK OR NOTE
        $admingroups = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdminGroups($viewer_id);

        //GET STUFF
        $manageadmin_ids = array();
        foreach ($admingroups as $admingroup) {
            $manageadmin_ids[] = $admingroup->group_id;
        }
        $manageadmin_values = array();
        $manageadmin_values['admingroups'] = $manageadmin_ids;
        $manageadmin_values['orderby'] = 'creation_date';
        $manageadmin_data = Engine_Api::_()->sitegroup()->getSitegroupsPaginator($manageadmin_values, null);
        $this->view->manageadmin_count = $manageadmin_data->getTotalItemCount();

        $linksValues = array("groupAdmin", "groupClaimed", "groupLiked");
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
            $linksValues[] = "groupsJoined";
        }
        $this->view->showLinks = $this->_getParam('showLinks', array($linksValues));

        if (empty($this->view->showLinks))
            return $this->setNoRender();

        if (in_array("groupAdmin", $this->view->showLinks)) {
            $this->view->showGroupAdmin = true;
        }

        if (in_array("groupClaimed", $this->view->showLinks)) {
            $this->view->showGroupClaimed = true;
        }

        if (in_array("groupLiked", $this->view->showLinks)) {
            $this->view->showGroupLiked = true;
        }

        if (in_array("groupsJoined", $this->view->showLinks)) {
            $this->view->showGroupJoined = true;
        }
    }

}

?>
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
class Sitegroup_Widget_ManageSitegroupController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', 0);
        $this->view->showOwnerInfo = $this->_getParam("showOwnerInfo", 0);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->groupAdminJoined = $this->_getParam("groupAdminJoined", 1);
        $this->view->can_edit = Engine_Api::_()->authorization()->isAllowed('sitegroup_group', $viewer, "edit");
        $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price.field', 1);
        $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitegroup()->enableLocation();
        $this->view->can_delete = Engine_Api::_()->authorization()->isAllowed('sitegroup_group', $viewer, "delete");
        $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('sitegroup_group', $viewer, "create");
        Engine_Api::_()->getDbtable('groupstatistics', 'sitegroup')->setViews();

        //GET VIEWER CLAIMS
        $claim_id = Engine_Api::_()->getDbtable('claims', 'sitegroup')->getViewerClaims($viewer_id);

        //CLAIM IS ENABLED OR NOT
        $canClaim = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitegroup_group', 'claim');

        $this->view->showClaimLink = 0;
        if (!empty($claim_id) && !empty($canClaim)) {
            $this->view->showClaimLink = 1;
        }

        //FORM GENERATION
        $this->view->form = $form = new Sitegroup_Form_Managesearch(array(
            'type' => 'sitegroup_group'
        ));

        $form->removeElement('show');

        if (!empty($_POST)) {
            $form->populate($_POST);
            $this->view->search = $_POST['search'];
        }

        if (!empty($_POST))
            $form->populate($_POST);
        $values = $form->getValues();

        //CHECK TO SEE IF REQUEST IS FOR SPECIFIC USER'S GROUPS
       // if ($this->view->groupAdminJoined == 2) {
          //  $values['user_id'] = $viewer->getIdentity();
       // } else {
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

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember') && $this->view->groupAdminJoined ==1) {
                $onlymember = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinGroups($viewer->getIdentity(), 'onlymember');

                $onlymemberids = array();
                foreach ($onlymember as $onlymembers) {
                    $onlymemberids[] = $onlymembers->group_id;
                }
                $values['adminjoinedgroups'] = array_merge($onlymemberids, $manageadmin_values['admingroups']);
            } else {
                $values['adminjoinedgroups'] = $manageadmin_values['admingroups'];
            }
       // }
        $values['type'] = 'manage';
        $values['type_location'] = 'manage';

        //GET PAGINATOR
        $this->view->paginator = $paginator = Engine_Api::_()->sitegroup()->getSitegroupsPaginator($values, null);
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.group', 10);

        $paginator->setItemCountPerPage($items_count);
        $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);

        $this->view->page = $values['page'] ? $values['page'] : 1;
        $this->view->totalPages = ceil(($paginator->getTotalItemCount()) / $items_count);

        $this->view->quota = 0;
        if ($viewer->level_id != 1) {
            $this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitegroup_group', 'max');
        }
        $this->view->current_count = $paginator->getTotalItemCount();
        $this->view->category_id = $values['category_id'];
        $this->view->subcategory_id = $values['subcategory_id'];
        $this->view->subsubcategory_id = $values['subsubcategory_id'];
    }

}

?>
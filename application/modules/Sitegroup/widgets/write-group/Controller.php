<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_WriteGroupController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //DON'T RENDER IF NOT AUTHORIZED.
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        //GET THE SUBJECT OF GROUP.
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
        $group_id = $sitegroup->group_id;

        //CALLING FUNCTON AND PASS GROUP ID.
        $this->view->userGroupstext = '';
        $writetContent = Engine_Api::_()->getDbtable('writes', 'sitegroup')->writeContent($group_id);

        if (!empty($writetContent)) {
            $this->view->userGroupstext = $writetContent->text;
        }

        if (!Engine_Api::_()->sitegroup()->isGroupOwner($this->view->sitegroup) && !$this->view->userGroupstext) {
            return $this->setNoRender();
        }
    }

}

?>
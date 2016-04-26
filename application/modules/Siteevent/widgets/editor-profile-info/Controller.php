<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_EditorProfileInfoController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('user')) {
            return $this->setNoRender();
        }

        //GET USER SUBJECT
        $user = Engine_Api::_()->core()->getSubject('user');

        $editorTable = Engine_Api::_()->getDbTable('editors', 'siteevent');

        //GET EDITOR ID
        $editor_id = $editorTable->getColumnValue($user->getIdentity(), 'editor_id', 0);
        $this->view->editor = $editor = Engine_Api::_()->getItem('siteevent_editor', $editor_id);

        //GET WIDGET SETTINGS
        $this->view->show_badge = $this->_getParam('show_badge', 1);
        $this->view->show_designation = $this->_getParam('show_designation', 1);

        //GET EDITOR DETAILS
        $params = array();
        $params['visible'] = 1;
        $params['editorReviewAllow'] = 1;
        $this->view->getDetails = $editorTable->getEditorDetails($editor->user_id, 0, $params);

        $this->view->badge_photo_id = 0;
        if (!empty($editor->badge_id) && $this->view->show_badge) {
            $this->view->badge_photo_id = Engine_Api::_()->getItemTable('siteevent_badge')->getBadgeColumn($editor->badge_id, 'badge_main_id');
        }
    }

}
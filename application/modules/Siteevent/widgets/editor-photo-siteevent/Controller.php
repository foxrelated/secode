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
class Siteevent_Widget_EditorPhotoSiteeventController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('user')) {
            return $this->setNoRender();
        }

        //GET USER SUBJECT    
        $this->view->user = $user = Engine_Api::_()->core()->getSubject('user');
        $editorTable = Engine_Api::_()->getDbTable('editors', 'siteevent');

        //GET EDITOR ID
        $editor_id = $editorTable->getColumnValue($user->getIdentity(), 'editor_id', 0);
        $this->view->editor = $editor = Engine_Api::_()->getItem('siteevent_editor', $editor_id);

        //GET EDITOR DETAILS
        $params = array();
        $params['visible'] = 1;
        $params['editorReviewAllow'] = 1;
        $this->view->getDetails = $editorTable->getEditorDetails($editor->user_id, 0, $params);

        $this->view->showContent = $this->_getParam('showContent', array("photo", "title", "about", "details", "designation", "forEditor", "emailMe"));
    }

}
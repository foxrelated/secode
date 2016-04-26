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
class Siteevent_Widget_EditorFeaturedSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //GET FEATURED USER ID
        $user_id = $this->_getParam('user_id');
        if (empty($user_id)) {
            return $this->setNoRender();
        }

        $siteeventEditorFeatured = Zend_Registry::isRegistered('siteeventEditorFeatured') ? Zend_Registry::get('siteeventEditorFeatured') : null;
        $editor_id = Engine_Api::_()->getDbTable('editors', 'siteevent')->getColumnValue($user_id, 'editor_id', 0);
        $this->view->editor = $editor = Engine_Api::_()->getItem('siteevent_editor', $editor_id);
        $this->view->user = Engine_Api::_()->getItem('user', $editor->user_id);

        if (empty($siteeventEditorFeatured))
            return $this->setNoRender();
    }

}
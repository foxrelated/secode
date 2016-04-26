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
class Siteevent_Widget_EditorsSiteeventController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //GET SETTINGS
        $params = array();
        $this->view->count = $params['limit'] = $this->_getParam('itemCount', 4);
        $this->view->viewType = $this->_getParam('viewType', 1);
        $this->view->superEditor = $this->_getParam('superEditor', 1);

        //GET EDITOR TABLE
        $this->view->editorTable = $editorTable = Engine_Api::_()->getDbTable('editors', 'siteevent');
        $siteeventEditorEvents = Zend_Registry::isRegistered('siteeventEditorEvents') ? Zend_Registry::get('siteeventEditorEvents') : null;

        //GET USER SUBJECT IF WIDGET IS PLACED AT EDITOR PROFILE PAGE
        if (Engine_Api::_()->core()->hasSubject('user')) {
            $user = Engine_Api::_()->core()->getSubject('user');
            $params['user_id'] = $user->getIdentity();
        }

        if (!$this->view->superEditor) {
            $params['super_editor_user_id'] = $editorTable->getSuperEditor('user_id');
        }

        if (empty($siteeventEditorEvents))
            return $this->setNoRender();

        //GET EDITORS
        $this->view->editors = $editorTable->getSimilarEditors($params);
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->view->editors->setCurrentPageNumber($this->_getParam('page'));
            $this->view->editors->setItemCountPerPage($params['limit']);
            if ($this->view->editors->getTotalItemCount() <= 0) {
                return $this->setNoRender();
            }
        } else {
            if (Count($this->view->editors) <= 0) {
                return $this->setNoRender();
            }
        }
    }

}
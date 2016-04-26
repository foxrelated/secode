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
class Siteevent_Widget_EditorsHomeController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //GET EDITOR TABLE
        $this->view->editorTable = $editorTable = Engine_Api::_()->getDbTable('editors', 'siteevent');
        $siteeventEditorHome = Zend_Registry::isRegistered('siteeventEditorHome') ? Zend_Registry::get('siteeventEditorHome') : null;

        if (empty($siteeventEditorHome))
            return $this->setNoRender();

        //GET EDITORS
        $params = array();
        if (!$this->_getParam('superEditor', 1)) {
            $params['user_id'] = $editorTable->getSuperEditor('user_id');
        }
        $this->view->editors = $editorTable->getEditorsEvent($params);

        $totalEditors = Count($this->view->editors);
        if ($totalEditors <= 0) {
            return $this->setNoRender();
        }
    }

}

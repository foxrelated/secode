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
class Siteevent_Widget_EditorsHomeStatisticsSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');

        //FETCH TOTAL REVIEWS BY EDITOR
        $params = array();
        $params['type'] = 'editor';
        $this->view->totalEditorReviews = $reviewTable->totalReviews($params);

        //GET EDITOR TABLE
        $editorTable = Engine_Api::_()->getDbTable('editors', 'siteevent');

        //FETCH TOTAL EDITORS
        $this->view->totalEditors = $editorTable->getEditorsCount(0);
    }

}
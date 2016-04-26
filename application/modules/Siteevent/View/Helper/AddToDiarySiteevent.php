<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddToDiary.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_View_Helper_AddToDiarySiteevent extends Zend_View_Helper_Abstract {

    /**
     * Assembles action string
     * 
     * @return string
     */
    public function addToDiarySiteevent($item, $params = null) {

        //GET VIEWER ID
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        //GET LEVEL SETTING
        $can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_diary', "create");

        if (empty($can_create)) {
            return;
        }

        $data['item'] = $item;
        
        $data['classIcon'] = '';
        if(isset($params['classIcon']))
            $data['classIcon'] = $params['classIcon'];
        
        $data['classLink'] = '';
        if(isset($params['classLink']))
            $data['classLink'] = $params['classLink'];
        
        $data['text'] = isset($params['text']) ? $params['text'] : "Add to Diary";

        return $this->view->partial('_addToDiary.tpl', 'siteevent', $data);
    }

}

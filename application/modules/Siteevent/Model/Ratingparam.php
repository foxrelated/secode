<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Ratingparam.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_Ratingparam extends Core_Model_Item_Abstract {

    protected $_searchTriggers = false;

    /**
     * Delete the event and belongings
     * 
     */
    public function _delete() {

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            Engine_Api::_()->getDbTable('ratings', 'siteevent')->delete(array('ratingparam_id = ?' => $this->ratingparam_id));

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //DELETE EVENT
        parent::_delete();
    }

}
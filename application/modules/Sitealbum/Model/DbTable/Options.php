<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Options.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Model_DbTable_Options extends Engine_Db_Table {

    protected $_name = 'album_fields_options';
    protected $_rowClass = 'Sitealbum_Model_Option';

    public function getFieldLabel($field_id) {

        $select = $this->select()
                ->where('field_id = ?', $field_id);
        $result = $this->fetchRow($select);

        return !empty($result) ? $result->label : '';
    }

    public function getProfileTypeLabel($option_id) {

        if (empty($option_id)) {
            return;
        }

        //GET FIELD OPTION TABLE NAME
        $tableFieldOptionsName = $this->info('name');

        //FETCH PROFILE TYPE LABEL
        $profileTypeLabel = $this->select()
                ->from($tableFieldOptionsName, array('label'))
                ->where('option_id = ?', $option_id)
                ->query()
                ->fetchColumn();

        return $profileTypeLabel;
    }

}
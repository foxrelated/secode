<?PHP

        // INSERT MISSING MEMBER LEVEL & PROFILE TYPES INTO DATABASE.
        // Prepare user levels
        $table = Engine_Api::_()->getDbtable('levels', 'authorization');
        $select = $table->select();
        $user_levels = $table->fetchAll($select);

        // Prepare Profile type
        $fieldtable = Engine_Api::_()->getItemTable('mcard_option');
        $fieldselect = $fieldtable->select()
                ->where("field_id = ?", 1);
        $mp_levels = $fieldtable->fetchAll($fieldselect);

        foreach ($user_levels as $user_level) {
            foreach ($mp_levels as $mp_level) {
                $getStatus = Engine_Api::_()->getItemTable('mcard_info')->getVal($user_level->level_id, $mp_level->option_id);
                if (!empty($user_level->level_id) && !empty($mp_level->option_id) && empty($getStatus)) {
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    $db->query('INSERT IGNORE INTO `engine4_mcard_info` (`level_id`, `mp_id`, `values`) VALUES (' . $user_level->level_id . ', ' . $mp_level->option_id . ', \' {"Profile Photo":"profile_photo","card_label":"Membership Card","Display Name":"displayname","Joining Date":"doj","Profile Type":1,"card_status":"1","logo":null,"card_bg_image":null,"label_color":"#061822","info_color":"#061822","label_font":"Courier New, Courier, monospace","info_font":"Courier New, Courier, monospace","logo_select":"1"}  \');');
                }
            }
        }
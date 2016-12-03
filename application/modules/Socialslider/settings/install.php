<?php

if (!class_exists('Sepcore_Installer')) {
    require_once(APPLICATION_PATH . '/application/modules/Sepcore/settings/installer.php');
}

class Socialslider_Installer extends Sepcore_Installer {

    function _query() {
        $this->_dropExistsButtonsTable();
        $this->_createButtonsTable();
        $this->_insertDefaultButtons();
    }

    protected function _dropExistsButtonsTable() {
        $query = "DROP TABLE IF EXISTS `engine4_socialslider_buttons`";

        $db = $this->getDb();
        $db->query($query);
    }

    protected function _createButtonsTable() {
        $query = "CREATE TABLE `engine4_socialslider_buttons`(
                        `button_id` INT(11) NOT NULL AUTO_INCREMENT,
                        `button_name` VARCHAR(30),
                        `picture_path` INT(11) DEFAULT '0',
                        `button_color` VARCHAR(10),
                        `button_code` TEXT,
                        `button_show` TINYINT(1) DEFAULT '0',
                        `button_default` TINYINT(1) DEFAULT '0',
                        `button_type` VARCHAR(10) DEFAULT 'custom',
                         PRIMARY KEY (`button_id`)
                )
                COLLATE='utf8_unicode_ci'
                ENGINE=InnoDB
                ROW_FORMAT=DEFAULT";

        $db = $this->getDb();
        $db->beginTransaction();

        try {
            $db->query($query);
            $db->commit();
        } catch (Exception $error) {
            $db->rollBack();
            throw $error;
        }
    }

    protected function _insertDefaultButtons() {

        $query = "INSERT IGNORE INTO `engine4_socialslider_buttons`
                    (`button_name`,`button_color`,`button_default`,`button_type`) 
                    VALUES
                    ('Facebook','3B5998', 1,'facebook'),
                    ('Twitter','33CCFF', 1 ,'twitter'),
                    ('Google+','e5e4e6', 1 ,'gplus'),
                    ('Youtube','9B9B9B', 1 ,'youtube')";

        $db = $this->getDb();
        $db->beginTransaction();
        try {
            $db->query($query);
            $db->commit();
        } catch (Exception $error) {
            $db->rollBack();
            throw $error;
        }
    }

}

?>

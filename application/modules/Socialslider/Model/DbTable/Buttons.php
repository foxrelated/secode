<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Buttons
 *
 * @author isabek
 */
class Socialslider_Model_DbTable_Buttons extends Engine_Db_Table {

    protected $_rowClass = 'Socialslider_Model_Button';

    public function getButtons() {
        return $this->fetchAll($this->select());
    }

    public function getSbutton($button_type) {

        $select = $this->select()
                ->where('button_type = ?', $button_type)
                ->where('button_code <> ?', '')
                ->where('button_code IS NOT NULL');

        return $this->fetchRow($select);
    }

    public function isShowYoutube() {
        $select = $this->select()
                ->where('button_type = ?', 'youtube')
                ->where('button_show = ?', 1)
                ->where('button_code <> ?', '')
                ->where('button_code IS NOT NULL');
        return $this->fetchRow($select);
    }

    public function getSelect() {

        $select = $this->select()
                ->where('button_show = ?', 1)
                ->where('button_code <> ?', '')
                ->where('button_code IS NOT NULL')
                ->order('RAND()')
                ->limit(4);

        return $this->fetchAll($select);
    }

}

?>

<?php
class Socialpublisher_Plugin_Menus {
    public function showSocialpublisher() {
        $modulesTable = Engine_Api::_ ()->getDbtable ( 'modules', 'core' );
        $mselect = $modulesTable->select ()
        ->where ( 'enabled = ?', 1 )
        ->where ( 'name  = ?', 'socialpublisher' );
        $module_result = $modulesTable->fetchRow ( $mselect );
        if (count ( $module_result ) > 0) {
            return true;
        }
        return false;
    }
}


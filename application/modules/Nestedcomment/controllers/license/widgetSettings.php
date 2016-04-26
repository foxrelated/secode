<?php
$db = Engine_Db_Table::getDefaultAdapter();
$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("nestedcomment_admin_manage_modules", "nestedcomment", "Manage Modules", NULL, \'{"route":"admin_default","module":"nestedcomment","controller":"module"}\', "nestedcomment_admin_main", NULL, 1, 0, 2);');
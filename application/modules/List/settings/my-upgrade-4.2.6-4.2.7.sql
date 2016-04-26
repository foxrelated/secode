UPDATE `engine4_core_menuitems` SET `name` = 'list_admin_main_categories',
`params` = '{"route":"admin_default","module":"list","controller":"settings","action":"categories"}' WHERE `engine4_core_menuitems`.`name` LIKE 'list_admin_main_listcategories' AND  `engine4_core_menuitems`.`module` LIKE 'list';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('list_admin_main_form_search', 'list', 'Search Form Settings', '', '{"route":"admin_default","module":"list","controller":"settings","action":"form-search"}', 'list_admin_main', '', 1, 0, 6);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('list_admin_main_profilemaps', 'list', 'Category-Listing Profile Mapping', '', '{"route":"admin_default","module":"list","controller":"profilemaps","action":"manage"}', 'list_admin_main', '', 1, 0, 5);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('list_admin_main_import', 'list', 'Import', '', '{"route":"admin_default","module":"list","controller":"importlisting"}', 'list_admin_main', '', 1, 0, 7);

INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`module`, `name`, `display`, `order`, `label`) VALUES
("list", "closed", 1, 10, "Status"),
("list", "show", 1, 20, "Show (For 'Browse Listings and Home Listings' pages)"),
("list", "orderby", 1, 30, "Browse By"),
("list", "search", 1, 40, "Name / Keyword"),
("list", "list_location", 1, 50, "Location"),
("list", "category_id", 1, 60, "Category"),
("list", "has_photo", 1, 10000070, "Only Listings With Photos"),
("list", "has_review", 1, 10000080, "Only Listings With Reviews");

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"admin_default","module":"list","controller":"settings","action":"level"}' WHERE `engine4_core_menuitems`.`name` LIKE 'list_admin_main_level' AND  `engine4_core_menuitems`.`module` LIKE 'list';

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"admin_default","module":"list","controller":"settings","action":"widget-settings"}' WHERE `engine4_core_menuitems`.`name` LIKE 'list_admin_main_widget' AND  `engine4_core_menuitems`.`module` LIKE 'list';

UPDATE `engine4_core_menuitems` SET `params` = '' WHERE `engine4_core_menuitems`.`name` LIKE 'list_gutter_edit' AND  `engine4_core_menuitems`.`module` LIKE 'list';

UPDATE `engine4_core_menuitems` SET `params` = '' WHERE `engine4_core_menuitems`.`name` LIKE 'list_gutter_editoverview' AND  `engine4_core_menuitems`.`module` LIKE 'list';

UPDATE `engine4_core_menuitems` SET `params` = '' WHERE `engine4_core_menuitems`.`name` LIKE 'list_gutter_editstyle' AND  `engine4_core_menuitems`.`module` LIKE 'list';

UPDATE `engine4_core_menuitems` SET `label` = 'Profile Fields' WHERE `engine4_core_menuitems`.`name` ='list_admin_main_fields' LIMIT 1 ;


UPDATE `engine4_core_menuitems` SET `order` = '4' WHERE `engine4_core_menuitems`.`name` = 'list_main_manage' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `order` = '5' WHERE `engine4_core_menuitems`.`name` = 'list_main_create' LIMIT 1 ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('list_main_location', 'list', 'Browse Locations', 'List_Plugin_Menus::canViewLists', '{"route":"list_general","action":"map"}', 'list_main', '', 1, 0, 3);

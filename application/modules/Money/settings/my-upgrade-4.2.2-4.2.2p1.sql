INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
('money_admin_main_issue', 'money', 'Issue an invoice', 'Money_Plugin_Menus', '{"route":"admin_default","module":"money","controller":"issue", "action":"issue"}', 'money_admin_main', '', 1, 0, 5)
;

INSERT INTO `engine4_money_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `test_mode`) VALUES
(4, 'LiqPay', '', 0, 'Money_Plugin_Gateway_LiqPay', 0);
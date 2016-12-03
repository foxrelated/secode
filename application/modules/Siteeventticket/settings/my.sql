/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

-- --------------------------------------------------------
--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  
('siteeventticket', 'Advanced Events - Paid Event and Ticket Selling Extension', '', '4.8.10p2', 1, 'extra'),
('siteeventpaid', 'Advanced Events - Paid Extension', 'Advanced Events - Paid Extension', '4.8.10p2', 1, 'extra');


-- --------------------------------------------------------
--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('siteeventticket_new', 'siteeventticket', '{item:$subject} added a new ticket {item:$ticket} for the event:', 1, 5, 1, 3, 1, 1),
('siteeventticket_order_place', 'siteeventticket', '{item:$subject} has purchased {var:$count} ticket(s) for the event:', 1, 5, 1, 3, 1, 1);


-- --------------------------------------------------------
--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('siteeventticket_order_place', 'siteeventticket', '{item:$subject} placed an order {var:$order_id} in {item:$siteevent} event.', 0, ''),
('siteeventticket_payment_approved_by_admin', 'siteeventticket', 'Admin has approved payment of order {var:$order_id}', 0, '');


-- --------------------------------------------------------
--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('siteeventticket_main_ticket', 'siteeventticket', 'My Tickets', 'Siteeventticket_Plugin_Menus::siteeventticketMainMytickets', '{"route":"siteeventticket_order", "action":"my-tickets"}', 'siteevent_main', NULL, 1, 0, 5),
('siteevent_gutter_mytickets', 'siteeventticket', 'My Tickets', 'Siteeventticket_Plugin_Menus::siteeventGutterMytickets', '{"route":"siteeventticket_order", "action":"my-tickets"}', 'siteevent_gutter', '', 1, 0, 3),
('siteeventticket_admin_main_commission', 'siteeventticket', 'Commissions', 'Siteeventticket_Plugin_Menus::showAdminCommissionTab', '{"route":"admin_default","module":"siteeventticket","controller":"manage", "action":"commission"}', 'siteeventticket_admin_main_ticket', '', 1, 0, 8),
('siteeventticket_admin_main_payment_requests', 'siteeventticket', 'Payment Requests', 'Siteeventticket_Plugin_Menus::showAdminPaymentRequestTab', '{"route":"admin_default","module":"siteeventticket","controller":"payment"}', 'siteeventticket_admin_main_ticket', '', 1, 0, 8),
('siteeventticket_main_coupons', 'siteeventticket', 'Browse Coupons', 'Siteeventticket_Plugin_Menus::canViewBrowseCoupons', '{"route":"siteeventticket_coupon"}', 'siteevent_main', '', 1, 0, 12);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("siteevent_dashboard_packages", "siteeventpaid", "Packages", "Siteevent_Plugin_Dashboardmenus", "", "siteevent_dashboard_ticket", NULL, 1, 0, 140),
("siteevent_dashboard_paymentmethod", "siteeventticket", "Payment Methods", "Siteeventticket_Plugin_Dashboardmenus", "", "siteevent_dashboard_ticket", NULL, 1, 0, 150),
("siteevent_dashboard_taxes", "siteeventticket", "Taxes", "Siteeventticket_Plugin_Dashboardmenus", "", "siteevent_dashboard_ticket", NULL, 1, 0, 160),
("siteevent_dashboard_tickets", "siteeventticket", "Tickets", "Siteeventticket_Plugin_Dashboardmenus", "", "siteevent_dashboard_ticket", NULL, 1, 0, 170),
("siteevent_dashboard_termsofuse", "siteeventticket", "Terms & Conditions", "Siteeventticket_Plugin_Dashboardmenus", "", "siteevent_dashboard_ticket", NULL, 1, 0, 180),
("siteevent_dashboard_coupons", "siteeventticket", "Coupons", "Siteeventticket_Plugin_Dashboardmenus", "", "siteevent_dashboard_ticket", NULL, 1, 0, 190),
("siteevent_dashboard_salesreports", "siteeventticket", "Sales Reports", "Siteeventticket_Plugin_Dashboardmenus", "", "siteevent_dashboard_ticket", NULL, 1, 0, 200),
("siteevent_dashboard_manageorders", "siteeventticket", "Manage Orders", "Siteeventticket_Plugin_Dashboardmenus", "", "siteevent_dashboard_ticket", NULL, 1, 0, 210),
("siteevent_dashboard_transactions", "siteeventticket", "Transactions", "Siteeventticket_Plugin_Dashboardmenus", "", "siteevent_dashboard_ticket", NULL, 1, 0, 220),
("siteevent_dashboard_yourbill", "siteeventticket", "Commissions Bill", "Siteeventticket_Plugin_Dashboardmenus", "", "siteevent_dashboard_ticket", NULL, 1, 0, 230),
("siteevent_dashboard_paymentaccount", "siteeventticket", "Payment Account", "Siteeventticket_Plugin_Dashboardmenus", "", "siteevent_dashboard_ticket", NULL, 1, 0, 150),
("siteevent_dashboard_paymentrequests", "siteeventticket", "Payment Requests", "Siteeventticket_Plugin_Dashboardmenus", "", "siteevent_dashboard_ticket", NULL, 1, 0, 230);

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('siteevent_dashboard_ticket', 'standard', 'Advanced Events - Dashboard Navigation (Ticketing)');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_mini_siteeventticketmytickets', 'siteeventticket', 'My Tickets', 'Siteeventticket_Plugin_Menus', '', 'core_mini', '', 4);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('SITEEVENTTICKET_THRESHOLD_COMMISSION_ADMIN', 'siteeventticket', '[event_title],[event_title_with_link],[dashboard_commission_bills]'),
('SITEEVENTTICKET_THRESHOLD_COMMISSION_OWNER', 'siteeventticket', '[event_title],[event_title_with_link],[dashboard_commission_bills]');

INSERT IGNORE INTO `engine4_sitelike_settings` (`content_type`, `tab1_show`, `tab1_duration`, `tab1_name`, `tab1_entries`, `tab2_show`, `tab2_duration`, `tab2_name`, `tab2_entries`, `tab3_show`, `tab3_duration`, `tab3_name`, `tab3_entries`, `view_layout`) VALUES
('siteestore_product', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1');

INSERT IGNORE INTO `engine4_sitelike_mixsettings` ( `module`, `resource_type`, `resource_id`, `item_title`,
`title_items`, `value`, `default`, `enabled`) VALUES
('siteestore', 'siteestore_product', 'product_id', 'Products', 'Product', 1, 1, 1);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`,
`attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_siteestore_product', 'siteestore', '{item:$subject} likes the Product {item:$object}:', 0, 5, 1, 1, 1, 1);
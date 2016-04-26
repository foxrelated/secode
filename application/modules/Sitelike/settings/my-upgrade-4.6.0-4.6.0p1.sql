INSERT IGNORE INTO `engine4_sitelike_settings` (`content_type`, `tab1_show`, `tab1_duration`, `tab1_name`, `tab1_entries`, `tab2_show`, `tab2_duration`, `tab2_name`, `tab2_entries`, `tab3_show`, `tab3_duration`, `tab3_name`, `tab3_entries`, `view_layout`) VALUES
( 'sitestore_store', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall',
   'Overall', 3,'1'),
('sitestoreproduct_product', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1');



INSERT IGNORE INTO `engine4_sitelike_mixsettings` ( `module`, `resource_type`, `resource_id`, `item_title`, `title_items`, `value`, `default`, `enabled`) VALUES
( 'sitestore', 'sitestore_store', 'store_id', 'Stores', 'Store', 1, 1, 1),
('sitestoreproduct', 'sitestoreproduct_product', 'product_id', 'Products', 'Product', 1, 1, 1);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`,
`attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_sitestore_store', 'sitestore', '{item:$subject} likes the stores {item:$object}:', 0, 5, 1, 1, 1, 1),
('like_sitestoreproduct_product', 'sitestoreproduct', '{item:$subject} likes the product {item:$object}:', 0, 5, 1, 1, 1, 1);
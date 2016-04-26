UPDATE `engine4_core_modules` SET `version` = '4.02p2' where 'name' = 'socialstore';

-- --------------------------------------------------------

--
-- Table structure for table `engine4_socialstore_wishlists`
--

CREATE TABLE IF NOT EXISTS `engine4_socialstore_wishlists` (
  `wishlist_id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`wishlist_id`),
  UNIQUE KEY `user_id_product_id` (`user_id`,`product_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"socialstore_general"}' WHERE name ='core_main_socialstore';

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"socialstore_extended","controller":"my-store"}' WHERE name ='socialstore_main_mystore';

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"socialstore_extended","controller":"my-favourite-product"}' WHERE name ='socialstore_main_myfavouriteproduct';

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"socialstore_extended","controller":"my-follow-store"}' WHERE name ='socialstore_main_myfollowstore';

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"socialstore_extended","controller":"my-cart"}' WHERE name ='socialstore_main_mycart';

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"socialstore_extended","controller":"my-orders"}' WHERE name ='socialstore_main_myorders';

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"socialstore_extended","controller":"faqs"}' WHERE name ='socialstore_main_faqs';

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"socialstore_extended","controller":"index"}' WHERE name ='socialstore_main_store';

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"socialstore_extended","controller":"product"}' WHERE name ='socialstore_main_product';

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"socialstore_extended","controller":"help"}' WHERE name ='socialstore_main_helps';

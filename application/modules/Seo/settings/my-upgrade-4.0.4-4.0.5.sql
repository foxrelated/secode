ALTER TABLE `engine4_seo_channels` ADD COLUMN
  `custom` tinyint(1) NOT NULL DEFAULT '0' AFTER `maxitems`;  

ALTER TABLE `engine4_seo_channels` ADD COLUMN
  `item_order` varchar(128) NULL AFTER `maxitems`;  
  
ALTER TABLE `engine4_seo_channels` ADD COLUMN
  `item_type` varchar(64) NULL AFTER `maxitems`;

ALTER TABLE `engine4_seo_channels` MODIFY
   `maxitems` INT(11) NOT NULL DEFAULT '0';
  
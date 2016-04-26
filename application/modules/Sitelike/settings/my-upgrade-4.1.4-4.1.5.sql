-- --------------------------------------------------------
--
-- Dumping data for table `engine4_like_settings`
--
INSERT IGNORE INTO `engine4_sitelike_settings` (`content_type`, `tab1_show`, `tab1_duration`, `tab1_name`, `tab1_entries`, `tab2_show`, `tab2_duration`, `tab2_name`, `tab2_entries`, `tab3_show`, `tab3_duration`, `tab3_name`, `tab3_entries`, `view_layout`) VALUES
('sitepage', '1', '7', 'This Week', 3, '1', '30', 'This Month', 3, '1', 'overall', 'Overall', 3, '1');
-- ------------------------------------------------------------------------
UPDATE `engine4_core_settings`  SET `value` = 'a:7:{i:0;s:4:"list";i:1;s:8:"sitepage";i:2;s:5:"album";i:3;s:11:"album_photo";i:4;s:14:"music_playlist";i:5;s:5:"group";i:6;s:11:"group_photo";}'  WHERE `engine4_core_settings`.`name` = 'sitelike.mix.serialize';








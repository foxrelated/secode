
-- ------------------------------------------------------------------

-- INSERT IGNORE INTO `engine4_user_signup` (`class`, `order`, `enable`) VALUES
-- ('Suggestion_Plugin_Signup_Invite', 5, 0);
-- 
-- UPDATE  `engine4_core_menuitems` SET  `params` =  '{"route":"suggest_to_friend_link","class":"buttonlink icon_blog_friend_suggestion smoothbox"}' WHERE  `engine4_core_menuitems`.`name` ='blog_suggest_friend'LIMIT 1 ;
-- 
-- UPDATE  `engine4_core_menuitems` SET  `params` =  '{"route":"suggest_to_friend_link","class":"buttonlink icon_classified_friend_suggestion smoothbox"}' WHERE  `engine4_core_menuitems`.`name` ='classified_suggest_friend' LIMIT 1 ;
-- 
-- UPDATE  `engine4_core_menuitems` SET  `params` =  '{"route":"suggest_to_friend_link","class":"buttonlink icon_list_friend_suggestion smoothbox"}' WHERE  `engine4_core_menuitems`.`name` ='list_suggest_friend' LIMIT 1 ;

-- --------------------------------------------------------

-- INSERT IGNORE INTO `engine4_user_signup` (`class`, `order`, `enable`) VALUES
-- ('Suggestion_Plugin_Signup_Invite', 5, 0);

-- --------------------------------------------------------
--
-- Dumping data for table `engine4_suggestion_mixinfos`
--

INSERT IGNORE INTO  `engine4_suggestion_mixinfos` (
`name` ,
`status`
)
VALUES 
('recipe',  1);

--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('sugg.recipe.wid', 5),
('suggestion.signup.invite', 0);

-- ---------------------------------------------------------
--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('recipe_suggest_friend', 'suggestion', 'Suggest to Friends', 'Suggestion_Plugin_Menus', '{"route":"suggest_recipe","class":"buttonlink icon_recipe_friend_suggestion smoothbox"}', 'recipe_gutter', NULL, 0, 999);

-- ----------------------------------------------------------
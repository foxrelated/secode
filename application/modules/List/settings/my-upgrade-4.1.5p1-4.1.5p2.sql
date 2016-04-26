
--
-- Dumping data for table `engine4_core_content`
--

INSERT IGNORE INTO `engine4_core_content` 
SELECT 
NULL as ` content_id`,
page_id as  `page_id`,
 'widget' as `type`,
  'list.popularlocation-list' as `name`,
   content_id as `parent_content_id`,
    12 as `order`, 
    '{"title":"Popular Locations","titleCount":true}' as  `params`,
    NULl as `attribs` 
  FROM `engine4_core_content` WHERE `type`= 'container' and `name` = 'right' and page_id IN (SELECT page_id as `page_id`  FROM `engine4_core_pages` WHERE `name`= 'list_index_index');



-- ADMIN, MODERATOR
--  style
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'list_listing' as `type`,
    'style' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
--  style
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'list_listing' as `type`,
    'style' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');


UPDATE `engine4_core_menuitems` SET `params` = '{"route":"list_general","action":"home"}'  WHERE `engine4_core_menuitems`.`name` ='core_main_list' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"list_general","action":"home"}' WHERE `engine4_core_menuitems`.`name` ='core_sitemap_list' LIMIT 1 ;


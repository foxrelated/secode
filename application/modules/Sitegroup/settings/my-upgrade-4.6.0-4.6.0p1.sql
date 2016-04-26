INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitegroup_subgroup_gutter_create", "sitegroup", "Create New Sub Group", "Sitegroup_Plugin_Menus", "", "sitegroup_gutter", "", 1, 0, 999);

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitegroup_group' as `type`,
    'auth_sspcreate' as `name`,
    5 as `value`,
    '["registered","owner_network","owner_member_member","owner_member","owner", "member", "like_member"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitegroup_group' as `type`,
    'sspcreate' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
  INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEGROUP_EMAILME_EMAIL", "sitegroup", "[host],[sender_email],[sender_name],[group_title],[message],[object_link]");

/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: my.sql 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('list_discussion_reply', 'list', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::listing topic} you posted on.', 0, ''),
('list_discussion_response', 'list', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::listing topic} you created.', 0, '');

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('list', 'Listings / Catalog', 'Listings / Catalog', '4.8.8', 1, 'extra');

-- ---------------------------------------------------------

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES 
('list_new', 'list', '{item:$subject} posted a new listing:', 1, 5, 1, 3, 1, 1), 
('comment_list', 'list', '{item:$subject} commented on the listing: {item:$object:$title} {body:$body}', 1, 3, 2 , 1, 1, 1), 
('list_photo_upload', 'list', '{item:$subject} added {var:$count} photo(s) in the listing: {item:$object:$title} {body:$body}', 1, 3, 2, 1, 1, 1), ('video_list', 'list', '{item:$subject} posted a new video in the listing: {item:$object:$title} {body:$body}', 1, 3, 2, 1, 1, 1), 
('review_list', 'list', '{item:$subject} posted a review on the listing: {item:$object:$title} {body:$body}', 1, 3, 2, 1, 1, 1), 
('list_topic_create', 'list', '{item:$subject} posted a {item:$object:topic} in the listing: {itemParent:$object:list_listing}: {body:$body}', 1, 3, 2, 1, 1, 1), 
('list_topic_reply', 'list', '{item:$subject} replied to a {item:$object:topic} in the listing: {itemParent:$object:list_listing}: {body:$body}', 1, 3, 2, 1, 1, 1);

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('list_change_photo', 'list', '{item:$subject} changed their listing profile picture:', 1, 3, 2, 1, 1, 1);

-- --------------------------------------------------------
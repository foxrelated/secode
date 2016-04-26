<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Likes.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Facebookse_Model_DbTable_Comments extends Engine_Db_Table
{
	
  //GETTING THE SETTING FOR COMMENT BOX FOR THIS MODULE.
  public function getCommentinfo ($module) {
    
   $select = $this->select('enable', 'commentbox_privacy', 'commentbox_width', 'commentbox_color')
					   ->where('content_type = ?', $module);
	 $permissionTable_Comments = $this->fetchRow($select);
	 return $permissionTable_Comments;
	 
  }
  
  //DELETING THE PARTICULAR TYPE CONTENT FROM THIS TABLE.
  
  public function deletecontent ($module) {
    
  	 $this->delete(array('content_type = ?' => $module));

  	 return $this;
    
  }
  
}
?>
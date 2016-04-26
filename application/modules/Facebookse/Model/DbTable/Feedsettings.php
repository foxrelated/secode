<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Feedsettings.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Facebookse_Model_DbTable_Feedsettings extends Engine_Db_Table
{
	protected $_name = 'facebookse_feedsettings';
  protected $_rowClass = 'Facebookse_Model_Feedsetting';
  protected $_feedTypeSetting;
  
 public function getUserFeedTypesSetting($viewer_id)
  {
    if( null === $this->_feedTypeSetting ) {
      $this->_feedTypeSetting = $this->select()
          ->where('user_id=?', $viewer_id)
          ->query()
          ->fetchAll();
    }
    if (isset($this->_feedTypeSetting[0]))
			return $this->_feedTypeSetting[0];
		return;	
  }
}
?>
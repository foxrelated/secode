<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_Api_Core extends Core_Api_Abstract
{

  /**
   * Get Truncation String
   *
   * @param string $string
   * @return truncate string
   */
  public function turncation($string) 
  {
    $length= Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.title.turncation', 16);
    $string = strip_tags($string);
    return Engine_String::strlen($string) > $length? Engine_String::substr($string, 0, ($length-3)) . '...' : $string;
  }
  
   /**
   * Get Tab Id
   *
   * @param string $tab_id
   * @return tab id
   */
	public function getTabId() {

		$tab_id = '';
		$contentTable = Engine_Api::_()->getDbtable('content', 'core');
    $tab_id = $contentTable->select()
                    ->from($contentTable->info('name'),array('content_id'))
                    ->where('name = ?', 'grouppoll.profile-grouppolls')
                    ->query()
                    ->fetchColumn();

    return $tab_id;
	}
  
}
?>
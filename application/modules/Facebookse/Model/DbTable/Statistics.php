<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Statistics.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Facebookse_Model_DbTable_Statistics extends Engine_Db_Table
{
	protected $_name = 'facebookse_statistics';
  protected $_rowClass = 'Facebookse_Model_Statistic';
}
?>
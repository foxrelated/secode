<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Model_Album extends Core_Model_Item_Collection
{
  protected $_parent_type = 'suggestion';

  protected $_owner_type = 'user';

  protected $_children_types = array('suggestion_photo');

  protected $_collectible_type = 'suggestion_photo';
	
 /** This function call for authorization	
 * @return  Object.
 */ 
  public function getAuthorizationItem()
  {
    return  $this->getParent('suggestion');
  }
}
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photo.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Suggestion_Model_Photo extends Core_Model_Item_Collectible
{
  protected $_parent_type = 'suggestion_album';

  protected $_owner_type = 'user';
  
  protected $_collection_type = 'suggestion_album';

  
 /**
 * @return  The photo URL, which are calling from "$this->itemphoto" function.
 */ 
//   public function getPhotoUrl($type = null)
//   {
//     if( empty($this->photo_id) )
//     {
//       return null;
//     }
// 
//     $file = $this->api()->getApi('storage', 'storage')->get($this->photo_id, $type);
//     if( !$file )
//     {
//       return null;
//     }
// 
//     return $file->map();
//   }
  
  // Get collection_id from "suggestion_photo table" and get object from "suggestion_album table".
  public function getCollection()
  {
    return  Engine_Api::_()->getItem('suggestion_album', $this->collection_id);    
  }
}
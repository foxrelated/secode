<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Suggestion.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Suggestion_Model_Suggestion extends Core_Model_Item_Abstract
{
	protected $_parent_is_owner = true;

 /** This function call for making path (Basicaly use when use the notation table & update tab)
 * @return  path.
 */ 
  public function getHref($params = array())
  {
		 $params = array_merge(array(
	   'route' => 'received_suggestion',
	   'reset' => true,
	   'sugg_id' => $this->suggestion_id,
	   ), $params);
	   $route = $params['route'];
	   $reset = $params['reset'];
	   unset($params['route']);
	   unset($params['reset']);
	   return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
  }
  
 /** This function use for making image which are store in "public/user/photo_id", and data base entry in the "received table", "suggestion table", "suggestion_album table", "suggestion_photo table".
 * @param $photo : photo name.
 * @param $coordinates :coordinate, if set.
 * @return  $this.
 */
  public function setPhoto($photo, $coordinates)
  {
		$viewer = Engine_Api::_()->user()->getViewer();
		// parent_type : Folder name in "public/parent_type".
		// parent_id : Sub folder name in "public/parent_type/parent_id".
		$params = array(
			'parent_type' => 'user',
		  'parent_id' => $viewer->getIdentity()
		);
	  // Save from temporary folder to suggestion/user_id folder.
    $storage = Engine_Api::_()->storage();
    $file = APPLICATION_PATH.'/public/temporary/'.$photo;
    $name = basename($file);
    $path = dirname($file);
    //unset($_SESSION['ProfileSuggestionImage']);
    
    // Store in the public/suggestion/id/image_id. Images are created in various sizes.
    $iMain = $storage->create($path.'/m_'.$name, $params);
    $iProfile = $storage->create($path.'/p_'.$name, $params);
    $cordinate = $storage->create($path.'/p_'.$name, $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iSquare = $storage->create($path.'/is_'.$name, $params);

    $iMain->bridge($iSquare, 'thumb.icon');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iProfile, 'thumb.profile');
    $this->entity_id = $iMain['file_id'];
    $this->save();
    
		// Call this function for updating database row.
    $this->dataEntry($iMain['file_id'], $this->suggestion_id);
    
    // @todo : coordinate set when give profile picture suggestion.
	  // For crope the image then call this function.
//	  if ($coordinates){
//	    $this->_resizeThumbnail($cordinate->file_id, $coordinates);
//	  }
    return $this;
  }
    
  
 // @todo :  coordinate set when give profile picture suggestion.
 /** Function call if set the coordinates(Only in the case of croping).
 * @param $id : photo id.
 * @param $coordinates :coordinate, if set.
 * @return  $this.
 */
//  public function _resizeThumbnail($id, $coordinates)
//  {
//    $storage = Engine_Api::_()->storage();
//
//    $iProfile = $storage->get($id, 'thumb.icon');
//    $iSquare = $storage->get($id, 'thumb.icon');
//    // Read into tmp file
//    $pName = $iProfile->getStorageService()->temporary($iProfile);
//    // New thumbnail created based on user selection of thumbnail coordinates in temporary folder.
//    $iName = dirname($pName) . '/nis_' . basename($pName);
//    list($x, $y, $w, $h) = explode(':', $coordinates);
//    $image = Engine_Image::factory();
//    // The old thumbnail image is replaced with the new resized thumbnail.
//    $image->open($pName)
//      ->resample($x+.1, $y+.1, $w-.1, $h-.1, 48, 48)
//      ->write($iName)
//      ->destroy();
//    $iSquare->store($iName);
//  }
  
 /** 
 * @return  Photo URL.
 */
//   public function getPhotoUrl($type = null)
//   {
//     if( empty($this->entity_id) )
//     {
//       return null;
//     }
//     $file = $this->api()->getApi('storage', 'storage')->get($this->entity_id, $type);
//     if( !$file )
//     {
//       return null;
//     }
//     return $file->map();
//   }
    
 /** This function call in "setphoto function", store value in diffrent tables.
 * @param $photo_id : Photo id.
 * @param $row_id : current suggestion_id.
 */
  public function dataEntry($photo_id, $row_id)
  {
  	$owner_id = Engine_Api::_()->user()->getViewer()->getIdentity();
  	// Update "Suggestion Photo" table.
	  $suggestionPhotoTable = Engine_Api::_()->getItemTable('suggestion_photo');
    $suggestionPhotoRow = $suggestionPhotoTable->createRow();   
    $suggestionPhotoRow->photo_id = $photo_id;
    $suggestionPhotoRow->collection_id = $row_id;
    $suggestionPhotoRow->owner_id = $owner_id;
		$suggestionPhotoRow->file_id = $photo_id;
    $suggestionPhotoRow->save();

		$getCorePluginVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;
		if( $getCorePluginVersion >= '4.1.8' ) {
			Engine_Api::_()->authorization()->context->setAllowed($suggestionPhotoRow, 'everyone', 'view', 'everyone');
		}

    
    // Update "Suggestion Album" table.
    $suggestionAlbumTable = Engine_Api::_()->getItemTable('suggestion_album');
    $suggestionAlbumRow = $suggestionAlbumTable->createRow();   
    $suggestionAlbumRow->album_id = $row_id;
    $suggestionAlbumRow->suggestion_id = $row_id;
    $suggestionAlbumRow->owner_id = $owner_id;
    $suggestionAlbumRow->save();
  }
}
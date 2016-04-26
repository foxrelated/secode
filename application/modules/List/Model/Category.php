<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Category.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Model_Category extends Core_Model_Item_Abstract
{

  /**
   * Set category icon
   *
   */
  public function setPhoto($photo) {

		//GET PHOTO DETAILS
		$name = basename($photo['tmp_name']);
		$path = dirname($photo['tmp_name']);
		$mainName  = $path.'/'.$photo['name'];

		//GET VIEWER ID
		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$photo_params = array(
			'parent_id'  => $this->category_id, 
			'parent_type' => "list_category",
		);

		//RESIZE IMAGE WORK
		$image = Engine_Image::factory();
		$image->open($photo['tmp_name']);
		$image->open($photo['tmp_name'])
						->resample(0, 0, $image->width, $image->height, $image->width, $image->height)
						->write($mainName)
						->destroy();

		try {
			$photoFile = Engine_Api::_()->storage()->create($mainName,  $photo_params); }
		catch (Exception $e) { 
			if ($e->getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE)
			{
				echo $e->getMessage();
				exit();
			}
		}

		return $photoFile;
	}
}

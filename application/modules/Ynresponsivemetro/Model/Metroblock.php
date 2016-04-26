<?php
class Ynresponsivemetro_Model_Metroblock extends Core_Model_Item_Abstract
{
	protected $_type = 'ynresponsivemetro_metroblock';

	public function setPhoto($photo)
	{
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
		}
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
		}
		else
		if (is_string($photo) && file_exists($photo))
		{
			$file = $photo;
		}
		else
		{
			throw new Event_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_id' => $this -> getIdentity(),
			'parent_type' => 'ynresponsive1_metroblock'
		);

		// Save
		$storage = Engine_Api::_() -> storage();
		$angle = 0;
		if (function_exists('exif_read_data')) 
		{
			$exif = exif_read_data($file);
			
			if (!empty($exif['Orientation']))
			{
				switch($exif['Orientation'])
				{
					case 8 :
						$angle = 90;
						break;
					case 3 :
						$angle = 180;
						break;
					case 6 :
						$angle = -90;
						break;
				}
			}
		}
		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file) ;
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(800, 600) -> write($path . '/m_' . $name) -> destroy();
		
		// Resize image (normal)
	    $image = Engine_Image::factory();
	    $image->open($file)
	      ->resize(350, 230)
	      ->write($path.'/pr_'.$name)
	      ->destroy();
		
		// Resize image (normal)
	    $image = Engine_Image::factory();
	    $image->open($file)
	      ->resize(140, 160)
	      ->write($path.'/in_'.$name)
	      ->destroy();
		
		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iIconNormal = $storage->create($path.'/in_'.$name, $params);
		$profileNormal = $storage->create($path.'/pr_'.$name, $params);
		
    	$iMain->bridge($iIconNormal, 'thumb.normal');
    	$iMain->bridge($profileNormal, 'thumb.profile');
		
		// Remove temp files
		@unlink($path . '/m_' . $name);
		@unlink($path.'/in_'.$name);
		@unlink($path.'/pr_'.$name);

		// Update row
		$this -> photo_id = $iMain -> file_id;
		$this -> save();
		return $this;
	}

	public function getPhotoUrl($type = null, $block = null)
	{
		$imgUrl = parent::getPhotoUrl($type);
		if($imgUrl)
		{
			return $imgUrl;			
		}
		$type = ( $type ? str_replace('.', '_', $type) : 'thumb_main' );
		$view = Zend_Registry::get("Zend_View");
		return $view->layout()->staticBaseUrl . "application/modules/Ynresponsivemetro/externals/images/nophoto_metro_$block.png";
	}
}

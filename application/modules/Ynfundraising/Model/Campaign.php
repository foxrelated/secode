<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfundraising
 * @author     YouNet Company
 */
class Ynfundraising_Model_Campaign extends Core_Model_Item_Abstract {
	protected $_parent_type = 'user';
	protected $_parent_is_owner = true;
	public function getHref($params = array()) {
		$params = array_merge ( array (
				'route' => 'ynfundraising_general',
				'campaignId' => $this->getIdentity (),
				'action' => 'view'
		), $params );
		$route = null;
		$reset = true;
		if (isset ( $params ['route'] ))
			$route = $params ['route'];
		if (isset ( $params ['reset'] ))
			$reset = $params ['reset'];
		unset ( $params ['route'] );
		unset ( $params ['reset'] );

		return Zend_Controller_Front::getInstance ()->getRouter ()->assemble ( $params, $route, $reset );
	}
	/**
	 * Gets a proxy object for the tags handler
	 *
	 * @return Engine_ProxyObject
	 *
	 */
	public function tags() {
		return new Engine_ProxyObject ( $this, Engine_Api::_ ()->getDbtable ( 'tags', 'core' ) );
	}
	public function setPhoto($photo) {
		if ($photo instanceof Zend_Form_Element_File) {
			$file = $photo->getFileName ();
		} else if (is_array ( $photo ) && ! empty ( $photo ['tmp_name'] )) {
			$file = $photo ['tmp_name'];
		} else if (is_string ( $photo ) && file_exists ( $photo )) {
			$file = $photo;
		} else {
			throw new Ynauction_Model_Exception ( 'invalid argument passed to setPhoto' );
		}

		$name = basename ( $file );
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array (
				'parent_type' => 'ynfundraising',
				'parent_id' => $this->getIdentity ()
		);

		// Save
		$storage = Engine_Api::_ ()->storage ();

		// Resize image (main)
		$image = Engine_Image::factory ();
		$image->open ( $file )->resize ( 720, 720 )->write ( $path . '/m_' . $name )->destroy ();

		// Resize image (profile)
		$image = Engine_Image::factory ();
		$image->open ( $file )->resize ( 240, 240 )->write ( $path . '/p_' . $name )->destroy ();

		// Resize image (normal)
		$image = Engine_Image::factory ();
		$image->open ( $file )->resize ( 190, 190 )->write ( $path . '/in_' . $name )->destroy ();

		// Resize image (icon)
		$image = Engine_Image::factory ();
		$image->open ( $file );

		$size = min ( $image->height, $image->width );
		$x = ($image->width - $size) / 2;
		$y = ($image->height - $size) / 2;

		$image->resample ( $x, $y, $size, $size, 48, 48 )->write ( $path . '/is_' . $name )->destroy ();

		// Store
		$iMain = $storage->create ( $path . '/m_' . $name, $params );
		$iProfile = $storage->create ( $path . '/p_' . $name, $params );
		$iIconNormal = $storage->create ( $path . '/in_' . $name, $params );
		$iSquare = $storage->create ( $path . '/is_' . $name, $params );

		$iMain->bridge ( $iProfile, 'thumb.profile' );
		$iMain->bridge ( $iIconNormal, 'thumb.normal' );
		$iMain->bridge ( $iSquare, 'thumb.icon' );

		// Remove temp files
		@unlink ( $path . '/p_' . $name );
		@unlink ( $path . '/m_' . $name );
		@unlink ( $path . '/in_' . $name );
		@unlink ( $path . '/is_' . $name );

		// Update row
		$this->modified_date = date ( 'Y-m-d H:i:s' );
		$this->photo_id = $iMain->getIdentity ();
		$this->save ();

		return $this;
	}
	public function getSingletonAlbum() {
		$table = Engine_Api::_ ()->getItemTable ( 'ynfundraising_album' );
		$select = $table->select ()->where ( 'campaign_id = ?', $this->getIdentity () )->order ( 'album_id ASC' )->limit ( 1 );

		$album = $table->fetchRow ( $select );

		if (null === $album) {
			$album = $table->createRow ();
			$album->setFromArray ( array (
					'title' => $this->getTitle (),
					'campaign_id' => $this->getIdentity ()
			) );
			$album->save ();
		}

		return $album;
	}
	public function addPhoto($file_id) {
		$file = Engine_Api::_ ()->getItemTable ( 'storage_file' )->getFile ( $file_id );
		$album = $this->getSingletonAlbum ();
		$params = array (
				// We can set them now since only one album is allowed
				'collection_id' => $album->getIdentity (),
				'album_id' => $album->getIdentity (),
				'campaign_id' => $this->getIdentity (),
				'user_id' => $file->user_id,
				'file_id' => $file_id
		);
		$photo = Engine_Api::_ ()->getDbtable ( 'photos', 'ynfundraising' )->createRow ();
		$photo->setFromArray ( $params );
		$photo->save ();
		return $photo;
	}
	public function getPhoto($photo_id) {
		$photoTable = Engine_Api::_ ()->getItemTable ( 'ynfundraising_photo' );
		$select = $photoTable->select ()->where ( 'file_id = ?', $photo_id )->limit ( 1 );
		$photo = $photoTable->fetchRow ( $select );
		return $photo;
	}
	/**
	 * get all sponsor level
	 */
	public function getSponsorLevels() {
		$sponsorLevelTable = Engine_Api::_ ()->getItemTable ( 'ynfundraising_sponsor_level' );
		$select = $sponsorLevelTable->select ()->where ( 'campaign_id = ?', $this->getIdentity () )->order ( 'amount' );
		$sponsor_levels = $sponsorLevelTable->fetchAll ( $select );
		return $sponsor_levels;
	}
	/**
	 * Gets a proxy object for the comment handler
	 *
	 * @return Engine_ProxyObject
	 *
	 */
	public function comments() {
		return new Engine_ProxyObject ( $this, Engine_Api::_ ()->getDbtable ( 'comments', 'core' ) );
	}

	/**
	 * Gets a proxy object for the like handler
	 *
	 * @return Engine_ProxyObject
	 *
	 */
	public function likes() {
		return new Engine_ProxyObject ( $this, Engine_Api::_ ()->getDbtable ( 'likes', 'core' ) );
	}

	/**
	 * Gets a proxy object for the tags handler
	 *
	 * @return Engine_ProxyObject
	 *
	 */
	/**
	 *
	 * @author trunglt
	 * @return multitype:number string
	 */
	public function getStatus() {
		$status = array ();
		if ($this->published) {
			$status ['type'] = 1;
			$status ['condition'] = 'Published';
		} else {
			$status ['type'] = 0;
			$status ['condition'] = 'Unpublished';
		}
		return $status;
	}
	public function getLimited() {
		$str = "";
		if ($this->expiry_date != '0000-00-00 00:00:00' && $this->expiry_date != '1970-01-01 00:00:00') {
			$time = strtotime ( $this->expiry_date ) - time ();
			$min = floor ( $time / 60 );
			if ($min > 10080) {
				$months = floor ( $min / 10080 );
				$str .= $months . Zend_Registry::get ( 'Zend_Translate' )->_ ( "w " );
				$min = $min - $months * 10080;
			}
			if ($min > 1440) {
				$days = floor ( $min / 1440 );
				$str .= $days . Zend_Registry::get ( 'Zend_Translate' )->_ ( "d " );
				$min = $min - $days * 1440;
			}
			if ($min > 60) {
				$hours = floor ( $min / 60 );
				$str .= $hours . Zend_Registry::get ( 'Zend_Translate' )->_ ( "h " );
			} elseif ($str == "" && $min > 0) {
				$str = $min . Zend_Registry::get ( 'Zend_Translate' )->_ ( "m" );
			}
		}
		return $str;
	}
	public function checkFollow($user_id = 0) {
		$followTable = Engine_Api::_ ()->getItemTable ( 'ynfundraising_follow' );
		$select = $followTable->select ()->where ( 'campaign_id = ?', $this->getIdentity () )->where ( 'user_id = ?', $user_id );
		$follow = $followTable->fetchRow ( $select );
		if ($follow)
			return false;
		else {
			return true;
		}
	}
	public function canEdit() {
		if (in_array ( $this->status, array (
				Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS,
				Ynfundraising_Plugin_Constants::CAMPAIGN_DRAFT_STATUS
		) )) {
			return true;
		}
		return false;
	}
	public function getTotalDonors() {
		$values = array (
				"campaign" => $this->getIdentity ()
		);
		$donors = Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->getDonorPaginator ( $values );
		return $donors->getTotalItemCount ();
	}
	public function checkDonor($user_id = 0) {
		$donationTable = Engine_Api::_ ()->getItemTable ( 'ynfundraising_donation' );
		$select = $donationTable->select ()->where ( 'campaign_id = ?', $this->getIdentity () )->where ( 'user_id = ?', $user_id );
		$donation = $donationTable->fetchRow ( $select );
		if ($donation)
			return true;
		else {
			return false;
		}
	}
}

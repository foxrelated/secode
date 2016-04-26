<?php
class Ynidea_Model_Trophy extends Core_Model_Item_Abstract
{
  
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'ynidea_trophies',
      'reset' => true,
      'action' => 'detail',
      'id' => $this->getIdentity(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  } 
  public function getPrintHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'ynidea_trophies',
      'reset' => true,
      'action' => 'print',
      'id' => $this->getIdentity(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }   
  public function getNominees()
  {
  	$nomineesTable = Engine_Api::_()->getDbtable('nominees', 'ynidea');
    $select = $nomineesTable->select()
    ->where('trophy_id = ?', $this->trophy_id);
    $rows = $nomineesTable->fetchAll($select);
  	return count($rows);
  }
  public function resetNominees()
  {
  	$nomineesTable = Engine_Api::_()->getDbtable('nominees', 'ynidea');
    $select = $nomineesTable->select()
    ->where('trophy_id = ?', $this->trophy_id);
    $rows = $nomineesTable->fetchAll($select);
	foreach($rows as $row)
		$row->delete();
  	return true;
  }
  public function getJudges()
  {
  	 $judgesTable = Engine_Api::_()->getDbtable('judges', 'ynidea');
    $select = $judgesTable->select()
    ->where('trophy_id = ?', $this->trophy_id);
    $rows = $judgesTable->fetchAll($select);
  	return count($rows);
  }
  public function getJudgers()
  {
  	 $judgesTable = Engine_Api::_()->getDbtable('judges', 'ynidea');
    $select = $judgesTable->select()
    ->where('trophy_id = ?', $this->trophy_id);
    $rows = $judgesTable->fetchAll($select);
  	return $rows;
  }
  public function checkJudges($user = null)
  {
  	$judgesTable = Engine_Api::_()->getDbtable('judges', 'ynidea');
    $select = $judgesTable->select()
    ->where('trophy_id = ?', $this->trophy_id)
	->where('user_id = ?', $user->user_id)->limit(1);
    $row = $judgesTable->fetchRow($select);
  	return $row;
  }
  public function checkNominee($idea = null)
  {
  	$nomineesTable = Engine_Api::_()->getDbtable('nominees', 'ynidea');
    $select = $nomineesTable->select()
    ->where('trophy_id = ?', $this->trophy_id)
	->where('idea_id = ?', $idea->idea_id)->limit(1);
    $row = $nomineesTable->fetchRow($select);
  	return $row;
  }
  public function checkFavourite()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $favouriteTable = Engine_Api::_()->getDbtable('trophyfavourites', 'ynidea');
    $select = $favouriteTable->select()
    ->where('trophy_id = ?', $this->trophy_id)
    ->where('user_id = ?', $viewer->getIdentity());
    $row = $favouriteTable->fetchRow($select);
    if($row)
    {
        return false;
    }
    else
    {
        return true;
    }
  }
  public function getFavourite()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $favouriteTable = Engine_Api::_()->getDbtable('trophyfavourites', 'ynidea');
    $select = $favouriteTable->select()
    ->where('trophy_id = ?', $this->trophy_id)
    ->where('user_id = ?', $viewer->getIdentity());
    $row = $favouriteTable->fetchRow($select);
    return $row;
  }
  public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file); 
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => 'ynidea',
      'parent_id' => $this->getIdentity()
    );

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path.'/m_'.$name)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path.'/p_'.$name)
      ->destroy();

    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(100, 100)
      ->write($path.'/in_'.$name)
      ->destroy();

    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path.'/is_'.$name)
      ->destroy();

    // Store
    $iMain = $storage->create($path.'/m_'.$name, $params);
    $iProfile = $storage->create($path.'/p_'.$name, $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iSquare = $storage->create($path.'/is_'.$name, $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    // Remove temp files
    @unlink($path.'/p_'.$name);
    @unlink($path.'/m_'.$name);
    @unlink($path.'/in_'.$name);
    @unlink($path.'/is_'.$name);

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id = $iMain->getIdentity();
    $this->save();

    return $this;
  }
    /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }
  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }
  public function checkGoldAward()
	{
		$awardTable = Engine_Api::_()->getDbtable('awards', 'ynidea');
	    $select = $awardTable->select()
	    ->where('trophy_id = ?', $this->trophy_id)->where("award = 0")->limit(1);	    
	    $row = $awardTable->fetchRow($select);
		return $row;
	}
   public function resetVotes()
   {
   	    $voteTable = Engine_Api::_()->getDbtable('trophyvotes', 'ynidea');
	    $select = $voteTable->select()
	    ->where('trophy_id = ?', $this->trophy_id);	    
	    $rows = $voteTable->fetchAll($select);
		foreach($rows as $row)
		{
			$row->delete();
		}
		return true;
   }
   /**
	 * 
	 * Add function for fundraising campaign
	 */
	 
	 /**
	  * Check exist campaign
	  */
	public function checkExistCampaign()
	{
	    $campaignTable = Engine_Api::_()->getDbtable('campaigns', 'ynfundraising');
	    $select = $campaignTable->select()
	    ->where('parent_id = ?', $this->trophy_id)
	    ->where('parent_type = ?', 'trophy')
		->where("status IN ('draft','ongoing')")
	    ->limit(1);
	    $row = $campaignTable->fetchRow($select);
		return $row;
	}
	public function checkExistRequest()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
	    $requestTable = Engine_Api::_()->getDbtable('requests', 'ynfundraising');
	    $select = $requestTable->select()
	    ->where('parent_id = ?', $this->trophy_id)
		->where('requester_id = ?',$viewer->getIdentity())
	    ->where('parent_type = ?', 'trophy')
		->where('is_completed = ?', 0)
	    ->limit(1);
	    $row = $requestTable->fetchRow($select);
		return $row;
	}
	public function checkExistRequestApproved()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
	    $requestTable = Engine_Api::_()->getDbtable('requests', 'ynfundraising');
	    $select = $requestTable->select()
	    ->where('parent_id = ?', $this->trophy_id)
	    ->where('parent_type = ?', 'trophy')
		->where('is_completed = ?', 0)
		->where('status = ?', 'approved')
	    ->limit(1);
	    $row = $requestTable->fetchRow($select);
		return $row;
	}
}
<?php
class Ynidea_Model_Idea extends Core_Model_Item_Abstract
{
    /*protected $_owner_type = 'user';
    protected $_parent_type = 'user';*/
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'ynidea_specific',
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
      'route' => 'ynidea_specific',
      'action' => 'print-view',
      'reset' => true,
      'id' => $this->idea_id,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  } 
  public function checkFavourite()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $favouriteTable = Engine_Api::_()->getDbtable('favourites', 'ynidea');
    $select = $favouriteTable->select()
    ->where('idea_id = ?', $this->idea_id)
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
    $favouriteTable = Engine_Api::_()->getDbtable('favourites', 'ynidea');
    $select = $favouriteTable->select()
    ->where('idea_id = ?', $this->idea_id)
    ->where('user_id = ?', $viewer->getIdentity());
    $row = $favouriteTable->fetchRow($select);
	return $row;
  }
  public function checkFollow()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $followTable = Engine_Api::_()->getDbtable('follows', 'ynidea');
    $select = $followTable->select()
    ->where('idea_id = ?', $this->idea_id)
    ->where('user_id = ?', $viewer->getIdentity());
    $row = $followTable->fetchRow($select);
    if($row)
    {
        return false;
    }
    else
    {
        return true;
    }
  }
  public function getFollow()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $followTable = Engine_Api::_()->getDbtable('follows', 'ynidea');
    $select = $followTable->select()
    ->where('idea_id = ?', $this->idea_id)
    ->where('user_id = ?', $viewer->getIdentity());
    $row = $followTable->fetchRow($select);
	return $row;
  }
  public function getFollows()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $followTable = Engine_Api::_()->getDbtable('follows', 'ynidea');
    $select = $followTable->select()
    ->where('idea_id = ?', $this->idea_id);
    $rows = $followTable->fetchAll($select);
    return $rows;
  }
  public function checkCoauthors($user = null)
  {
  	$coAuthorTable = Engine_Api::_()->getDbtable('coauthors', 'ynidea');
    $select = $coAuthorTable->select()
    ->where('idea_id = ?', $this->idea_id)
	->where('user_id = ?', $user->user_id)->limit(1);
    $row = $coAuthorTable->fetchRow($select);
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

    $image->resample($x, $y, $size, $size, 48, 35)
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
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   **/
   public function tags()
   {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
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
   
   /**
   * 
   *
   * @return String
   **/
   public function getSlug($str = null) {
        return trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($this -> title))), '-');
    }
    
    public function getRevisions(){
        $revisionTable = Engine_Api::_()->getDbtable('versions', 'ynidea');
        $select = $revisionTable->select()
        ->where('idea_id = ?', $this->idea_id)->order("idea_version DESC");        
        $revisions = Zend_Paginator::factory($select);
        return $revisions;
    }
	public function checkAward($trophy_id = 0)
	{
	    $awardTable = Engine_Api::_()->getDbtable('awards', 'ynidea');
	    $select = $awardTable->select()
	    ->where('idea_id = ?', $this->idea_id)
	    ->where('trophy_id = ?', $trophy_id)->limit(1);
	    $row = $awardTable->fetchRow($select);
		return $row;
	}
	public function getAwards()
	{
	    $awardTable = Engine_Api::_()->getDbtable('awards', 'ynidea');
	    $select = $awardTable->select()
	    ->where('idea_id = ?', $this->idea_id);	    
	    $rows = $awardTable->fetchAll($select);
		return $rows;
	}
	public function getNewVersionCount()
	{
		$versionTable = Engine_Api::_()->getDbtable('versions', 'ynidea');
	    $select = $versionTable->select()
	    ->where('idea_id = ?', $this->idea_id)
	    ->order("idea_version DESC");
	    $rows = $versionTable->fetchAll($select);
		if($rows)
			return $rows[0]->idea_version + 1;
		return 1;
	}
	public function getNewestVersion()
	{
		$versionTable = Engine_Api::_()->getDbtable('versions', 'ynidea');
	    $select = $versionTable->select()
	    ->where('idea_id = ?', $this->idea_id)
	    ->order("idea_version DESC");
	    $rows = $versionTable->fetchRow($select);
		return $rows;
	}
	public function clearVoteCoAuthor($user_id)
	{
		$voteTable = Engine_Api::_()->getDbtable('ideavotes', 'ynidea');
	    $select = $voteTable->select()
	    ->where('idea_id = ?', $this->idea_id)
	    ->where("user_id = ?", $user_id);
		$rows = $voteTable->fetchAll($select);
		foreach($rows as $row)
		{
			$row->delete();
			$this->vote_count = $this->vote_count - 1;
			$this->save();
		}
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
	    ->where('parent_id = ?', $this->idea_id)
	    ->where('parent_type = ?', 'idea')
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
	    ->where('parent_id = ?', $this->idea_id)
		->where('requester_id = ?',$viewer->getIdentity())
	    ->where('parent_type = ?', 'idea')
		->where('is_completed = ?', 0)
	    ->limit(1);
	    $row = $requestTable->fetchRow($select);
		return $row;
	}
	public function checkExistRequestApproved()
	{
	    $requestTable = Engine_Api::_()->getDbtable('requests', 'ynfundraising');
	    $select = $requestTable->select()
	    ->where('parent_id = ?', $this->idea_id)
	    ->where('parent_type = ?', 'idea')
		->where('is_completed = ?', 0)
		->where('status = ?', 'approved')
	    ->limit(1);
	    $row = $requestTable->fetchRow($select);
		return $row;
	}
}
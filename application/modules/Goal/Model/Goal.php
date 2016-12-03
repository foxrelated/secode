<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */

class Goal_Model_Goal extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'user';
  protected $_owner_type = 'user';
  protected $_parent_is_owner = true;

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'goal_profile',
      'reset' => true,
      'id' => $this->getIdentity(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getParent($recurseType = null)
  {
    return $this->getOwner('user');
  }

  public function getCategory()
  {
    return Engine_Api::_()->getDbtable('categories', 'goal')
        ->find($this->category_id)->current();
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
      throw new Group_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => 'goal',
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
      ->resize(140, 160)
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
    $this->photo_id = $iMain->file_id;
    $this->save();

    // Add to album
    $viewer = Engine_Api::_()->user()->getViewer();
    $photoTable = Engine_Api::_()->getItemTable('goal_photo'); 
    //$goalAlbum = $this->getSingletonAlbum();
    $photoItem = $photoTable->createRow();
    $photoItem->setFromArray(array(
      'goal_id' => $this->getIdentity(),
      'user_id' => $viewer->getIdentity(),
      'file_id' => $iMain->getIdentity(),
    ));
    $photoItem->save();

    return $this;
  }

public function totalUncompletedTasks($gid) {
    $taskTable = Engine_Api::_()->getDbtable('tasks','goal');
    $tSel = $taskTable->select()
            ->where('goal_id = ?', $gid)
            ->where('complete = ?', 0)
            ;
    $total_tasks = $taskTable->fetchAll($tSel);
    if(count($total_tasks) > 0){ 
        return count($total_tasks);
    }else {
        return 0;
    }
}

public function totalCompletedTasks($gid) {
    $taskTable = Engine_Api::_()->getDbtable('tasks','goal');
    $tSel = $taskTable->select()
            ->where('goal_id = ?', $gid)
            ->where('complete = ?', 1)
            ;
    $total_tasks = $taskTable->fetchAll($tSel);
    if(count($total_tasks) > 0){ 
        return count($total_tasks);
    }else {
        return 0;
    }
}

public function totalTasks($gid) {
    $taskTable = Engine_Api::_()->getDbtable('tasks','goal');
    $tSel = $taskTable->select()
            ->where('goal_id = ?', $gid)
            ;
    $total_tasks = $taskTable->fetchAll($tSel);
    if(count($total_tasks) > 0){ 
        return count($total_tasks);
    }else {
        return 0;
    }
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
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }  
  
  protected function _delete()
  {
    if( $this->_disableHooks ) return;

    //get all tasks of this goal
    $taskTable = Engine_Api::_()->getDbtable('tasks','goal');
    $task_sel = $taskTable->select()
            ->where('goal_id = ?', $this->getIdentity())
            ;
    $tasks = $taskTable->fetchAll($task_sel);
    
     //delete tasks of this goal
      if(count($tasks) > 0){
          foreach ($tasks as $task){
              $task->delete();
          }
      }
    parent::_delete();
  }
}
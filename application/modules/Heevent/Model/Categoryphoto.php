<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Categoryphoto.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 27.09.13
 * Time: 17:22
 * To change this template use File | Settings | File Templates.
 */
class Heevent_Model_Categoryphoto extends Core_Model_Item_Abstract
{
  protected $categoryphoto_id;

  protected function _postInsert()
  {
    $this->categoryphoto_id = $this->getIdentity();
    parent::_postInsert();
  }

  public function getPhotoUrl($type = null)
  {
    if ($file = Engine_Api::_()->getDbtable('files', 'storage')->getFile($this->file_id)) {
      return $file->getPhotoUrl();
    } else {
      return "";
    }
  }

  public function delete()
  {
    $storageTable = Engine_Api::_()->getDbtable('files', 'storage');
    $storageDb = $storageTable->getAdapter();
    $storageDb->beginTransaction();

    $file = $storageTable->getFile($this->file_id);
    $val = parent::delete();
    if($file) {
      try {
        $file->delete();
        $storageDb->commit();
        return $val;
      } catch (Exception $e) {
        $storageDb->rollBack();
        throw $e;
      }
    }
  }
}


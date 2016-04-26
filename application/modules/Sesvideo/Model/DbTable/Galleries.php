<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Galleries.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesvideo_Model_DbTable_Galleries extends Engine_Db_Table {

  protected $_rowClass = "Sesvideo_Model_Gallery";

  public function getGallery($param = array()) {
    $tableName = $this->info('name');
    $select = $this->select()
            ->from($tableName);
    if (isset($param['fetchAll']))
      return $this->fetchAll($select);
    return Zend_Paginator::factory($select);
  }

}

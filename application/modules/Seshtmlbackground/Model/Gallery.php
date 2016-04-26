<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Seshtmlbackground
 * @package    Seshtmlbackground
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Gallery.php 2015-10-22 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Seshtmlbackground_Model_Gallery extends Core_Model_Item_Abstract {
  protected $_searchTriggers = false;
  public function countSlide() {
    $slideTable = Engine_Api::_()->getItemTable('seshtmlbackground_slide');
    return $slideTable->select()
                    ->from($slideTable, new Zend_Db_Expr('COUNT(slide_id)'))
                    ->where('gallery_id = ?', $this->gallery_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
  }

}

<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Gallery.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesvideo_Model_Gallery extends Core_Model_Item_Abstract {

  public function countSlide() {
    $slideTable = Engine_Api::_()->getItemTable('sesvideo_slide');
    return $slideTable->select()
                    ->from($slideTable, new Zend_Db_Expr('COUNT(slide_id)'))
                    ->where('gallery_id = ?', $this->gallery_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
  }

}

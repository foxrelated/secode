<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Seshtmlbackground
 * @package    Seshtmlbackground
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Core.php 2015-10-22 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Seshtmlbackground_Api_Core extends Core_Api_Abstract {

  public function getRow($params = array()) {

    $table = Engine_Api::_()->getDbtable($params['table_name'], 'seshtmlbackground');
    $tableName = $table->info('name');
    $select = $table->select()->order($params['id'] . " DESC");
    return $table->fetchRow($select);
  }
}
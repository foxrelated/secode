<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: module-delete.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo "Remove Module as Advertisable?" ?></h3>
    <p>
      <?php echo "Are you sure you want to remove this module as advertisable? Users will not be able to directly advertise their content from this module after being removed." ?>
    </p>
    <br />
    <p>
      <button type='submit'><?php echo "Delete" ?></button>
      <?php echo "or" ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
      <?php echo "cancel" ?></a>
    </p>
  </div>
</form>

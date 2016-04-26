<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: delete.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate("Delete Playlist?") ?></h3>
    <p><?php echo $this->translate("Are you sure you want to delete this playlist?") ?></p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->sesmusic_id?>"/>
      <button type='submit'><?php echo $this->translate("Delete Playlist") ?></button>
      <?php echo $this->translate("or") ?>
      <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Cancel") ?></a>
    </p>
  </div>
</form>
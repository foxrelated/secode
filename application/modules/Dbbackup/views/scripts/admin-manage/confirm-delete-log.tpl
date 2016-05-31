<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: delete.tpl 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
?>
<?php $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;?>
  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Delete All Backup files and their Log entries?") ?></h3>
      <p>
        <?php echo $this->translate("Are you sure that you want to delete All Backup files and their Log entries?  These files will not be recoverable after being deleted.") ?>
      </p>
      <br />
      <p>
        <button type='submit' name="clear"><?php echo $this->translate("Delete") ?></button>
        <?php echo $this->translate("or") ?>
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>

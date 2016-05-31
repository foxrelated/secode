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

<div>
  <form method="post" class="global_form_popup">
		<h3><?php echo $this->translate("Delete Backup Destination") ?></h3>
    <p>
      <?php echo $this->translate("Are you sure that you want to delete this destination? It will not be recoverable after being deleted.") ?>
    </p>    
    <br>
    <div class="tip">
    	<span>
	      <?php echo $this->translate("Note: You are using this destination in the Automatic Backup. If this destination is deleted, your automatic backup will become off. You should then re-configure your backup settings for automatic backup and use available backup destinations."); ?>
	    </span>  
    </div>
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->id ?>"/>
      <button type='submit'><?php echo $this->translate("Delete") ?></button>
      <?php echo $this->translate(" or ") ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("cancel") ?></a>
    </p>
  </form>
</div>



<?php if (@$this->closeSmoothbox): ?>
        <script type="text/javascript">
          TB_close();
        </script>
<?php endif; ?>

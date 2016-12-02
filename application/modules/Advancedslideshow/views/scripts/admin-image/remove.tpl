<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: remove.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class='global_form' style="margin:15px 0 0 15px;">
  <form method="post">
    <div>
      <div>
        <h3><?php echo $this->translate('Delete Slide ?');?></h3>
        <p> <?php echo $this->translate('Are you sure you want to delete this slide ?'); ?> </p>
				<br />
        <p>
          <input type="hidden" name="confirm" value="true"/>
          <button type='submit' target="_parent" style="color:#D12F19;"><?php echo $this->translate('Delete');?></button>
          <?php echo $this->translate('or');?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
      </div>
    </div>
  </form>
</div>

<?php if( @$this->closeSmoothbox ): ?>
	<script type="text/javascript">
  		TB_close();
	</script>
<?php endif; ?>
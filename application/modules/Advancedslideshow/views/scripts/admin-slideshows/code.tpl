<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: code.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="advslideshow_code_popup">
	<div>
		<?php echo $this->translate('Please copy the following code and paste on none-widgetized page and enjoy the desirable slideshow !');?>
    <div id="tab1">
			<div class="advslideshow_codetop">
				 <?php echo $this->translate('Code'); ?>
			</div>
	    <textarea class="advslideshow_codemain" onclick="this.focus(); this.select()"><?php if(!empty($this->code)):?><?php echo $this->code; ?><?php endif; ?></textarea>
	  </div>
    <div class="advslideshow_code_popup_btm">
      <a onclick="javascript:parent.Smoothbox.close()" href="javascript:void(0);"><?php echo $this->translate('Close'); ?></a>
    </div>
  </div>
</div>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
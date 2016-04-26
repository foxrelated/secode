<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: publish.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<div class='global_form_popup'>
  <?php if ($this->success): ?>
    <script type="text/javascript">
      parent.$('list-item-<?php echo $this->listing_id ?>').destroy();
      setTimeout(function() {
        parent.Smoothbox.close();
      }, 1000 );
    </script>
    <div class="global_form_popup_message">
      <?php echo $this->translate('Your list has been published.'); ?> 
    </div>
  <?php else: ?>
	  <form method="POST" action="<?php echo $this->url() ?>">
	    <div>
	      <h3><?php echo $this->translate('Publish Listing ?'); ?></h3>
	      <p>
	        <?php echo $this->translate('Are you sure that you want to publish this Listing ?'); ?>
	      </p>
	      <p>&nbsp;
	      </p>
	      <p>
	        <input type="hidden" name="listing_id" value="<?php echo $this->listing_id?>"/>
					<input type="hidden" value="" name="search"><input type="checkbox" checked="checked" value="1" id="search" name="search">
					<?php echo $this->translate("Show this listing in search results"); ?>
					<br />
					<br />
	        <button type='submit'><?php echo $this->translate('Publish'); ?></button>
	        <?php echo $this->translate(' or ')?> <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate('cancel')?></a>
	      </p>
	    </div>
	  </form>
  <?php endif; ?>
</div>

<?php if( @$this->closeSmoothbox ): ?>
	<script type="text/javascript">
  	TB_close();
	</script>
<?php endif; ?>
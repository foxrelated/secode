<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: _formButtonSubmit.tpl 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
?>

<div id="submit-wrapper" class="form-wrapper">
	<div id="submit-label" class="form-label"> </div>
	<div id="submit-element" class="form-element">
		<button type="submit" id="done" name="done" value='submit' onclick="javascript:showlightbox();" >
			<?php echo $this->translate('Restore') ?>
			           


		</button>
        <?php echo $this->translate(" or ") ?> 
        <a href='javascript:void(0);' onclick='redirect_backup();'>
        <?php echo $this->translate("cancel") ?></a>
	</div>
</div>

<script type="text/javascript">
function redirect_backup() {
	window.location.href = 'admin/dbbackup/manage';
}
</script>

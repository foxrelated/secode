<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: delete.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<form method="post" class="global_form global_form_popup">
	<div>
		<div>
			<h3><?php echo $this->translate('Delete Review?'); ?></h3>
			<p>
				<?php echo $this->translate('Are you sure that you want to delete this review? It will not be recoverable after being deleted.'); ?>
			</p>
			<br />
			<p>
				<input type="hidden" name="confirm" value="true"/>
				<button type='submit'><?php echo $this->translate('Delete'); ?></button>
				<?php echo $this->translate(' or '); ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
			</p>
		</div>
	</div>
</form>

<?php if (@$this->closeSmoothbox): ?>
	<script type="text/javascript">
		TB_close();
	</script>
<?php endif; ?>

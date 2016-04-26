<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: tagscloud.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<script type="text/javascript">  
	var tagAllAction = function(tag_id, tag){
		$('tag').value = tag;
		$('tag_id').value = tag_id;
		$('filter_form_tagscloud').submit();
	}
</script>

<?php include_once APPLICATION_PATH . '/application/modules/List/views/scripts/navigation_views.tpl'; ?>
<h3><b><?php echo $this->translate('Popular Listing Tags'); ?></b></h3>
<?php echo $this->translate('Browse the tags created for listings by the various members.'); ?>
<br />

<?php if(!empty($this->tag_array)):?>

	<form id='filter_form_tagscloud' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'list_general', true) ?>' style='display: none;'>
		<input type="hidden" id="tag" name="tag"  value=""/>
		<input type="hidden" id="tag_id" name="tag_id"  value=""/>
	</form>

	<div class="mtop_10">
		<?php foreach($this->tag_array as $key => $frequency):?>
			<?php $string = $this->string()->escapeJavascript($key)?>
			<?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency'])*$this->tag_data['step'] ?>
			<a href='javascript:void(0);' onclick="javascript:tagAllAction('<?php echo $this->tag_id_array[$key]; ?>', '<?php echo $string; ?>');" style="font-size:<?php echo $step ?>px;" title=''><?php echo $key ?><sup><?php echo $frequency ?></sup></a>&nbsp; 
		<?php endforeach;?>
	</div>
	<br /><br /><br /><br /><br />
<?php endif; ?>
<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php
	$baseUrl = $this->layout()->staticBaseUrl;
	$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/List/externals/styles/style_list.css');
?>
<script type="text/javascript">
  var tagCloudAction = function(tag_id, tag){
    if($('filter_form')) {
       var form = document.getElementById('filter_form');
      }else if($('filter_form_tag')){
				var form = document.getElementById('filter_form_tag');
    }

    form.elements['tag'].value = tag;
		form.elements['tag_id'].value = tag_id;
		form.submit();
  }
</script>

<form id='filter_form_tag' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'list_general', true) ?>' style='display: none;'>
	<input type="hidden" id="tag" name="tag"  value=""/>
	<input type="hidden" id="tag_id" name="tag_id" value=""/>
</form>

<h3><?php echo $this->translate('Popular Listing Tags'); ?> (<?php echo $this->count_only ?>)</h3>
<ul class="seaocore_sidebar_list">
	<?php foreach ($this->tag_array as $key => $frequency): ?>
		<?php $string = $this->string()->escapeJavascript($key)?>
		<?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency']) * $this->tag_data['step'] ?>
		<?php ?>
		<a href='javascript:void(0);' onclick="javascript:tagCloudAction('<?php echo $this->tag_id_array[$key]; ?>', '<?php echo $string; ?>');" style="font-size:<?php echo $step ?>px;" title=''><?php echo $key ?><sup><?php echo $frequency ?></sup></a>
	<?php endforeach; ?>
	<br/>
	<b class="floatR"><?php echo $this->htmlLink(array('route' => 'list_general', 'action' => 'tagscloud'), $this->translate('Explore Tags &raquo;')) ?></b>
	<div class="clear"></div>
</ul>
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

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/List/externals/styles/style_list.css');?>

<script type="text/javascript">
  var tagAction =function(tag){
    $('tag').value = tag;
    $('filter_form').submit();
  }
</script>

<?php
	$this->tagstring = "";
	foreach ($this->userTags as $tag) {
		if (!empty($tag->text)) {
			$this->tagstring .= " <a href='javascript:void(0);'onclick='javascript:tagAction({$tag->tag_id})' >#$tag->text</a>";
		}
	}
?>

<?php if($this->tagstring): ?>
	<h3><?php echo $this->translate('%1$s\'s Tags', $this->htmlLink($this->list->getParent(), $this->list->getParent()->getTitle())) ?></h3>
  <ul class="seaocore_sidebar_list">
    <li> 
			<?php echo $this->tagstring; ?>
		</li>	
	</ul>
<?php endif; ?>
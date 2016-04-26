<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js');
	include APPLICATION_PATH . '/application/modules/Sitelike/views/scripts/settings_css.tpl' ; 
?>
<script>
var seaocore_like_url = en4.core.baseUrl + 'sitelike/index/globallikes';
var seaocore_content_type = '<?php echo $this->resource_type ?>';
</script>

<?php if(!empty($this->viewer_id)): ?>
	<div class="sitelike_button" id="<?php echo $this->resource_type ?>_unlikes_<?php echo $this->resource_id;?>" style ='display:<?php echo $this->hasLike ?"block":"none"?>' >
		<a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type ?>');">
			<i class="like_thumbdown_icon"></i>
			<span><?php echo $this->translate('Unlike') ?></span>
		</a>
	</div>

	<div class="sitelike_button" id="<?php echo $this->resource_type ?>_most_likes_<?php echo $this->resource_id;?>" style ='display:<?php echo $this->hasLike ?"none":"block"?>' >
		<a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type ?>');">
			<i class="like_thumbup_icon"></i>
			<span><?php echo $this->translate('Like') ?></span>
		</a>
	</div>
	<input type ="hidden" id = "<?php echo $this->resource_type ?>_like_<?php echo $this->resource_id;?>" value = '<?php echo $this->hasLike ? $this->hasLike[0]['like_id'] :0; ?>' />
<?php endif; ?>
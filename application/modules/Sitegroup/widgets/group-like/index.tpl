<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<ul class="sitegrouplike_users_block">
	<h3><?php echo $this->translate(array('%s Person Likes This', '%s People Like This', $this->num_of_like),$this->locale()->toNumber($this->num_of_like)); ?></h3>
	<?php	
		$container = 1;
		foreach( $this->user_obj as $path_info ){
		if ($container %3 == 1) : ?>
			<li>
		<?php endif;?>
			<div class="likes_member_sitegroup">
				<div class="likes_member_thumb">
					<?php echo $this->htmlLink($path_info->getHref(), $this->itemPhoto($path_info, 'thumb.icon'), array('class' => 'item_photo','title' => $path_info->getTitle(), 'target' => '_parent')); ?>
				</div>
 				<div class="likes_member_name">
					<?php echo $this->htmlLink($path_info->getHref(), Engine_Api::_()->sitegroup()->truncation($path_info->getTitle()), array('title'=> $path_info->getTitle(), 'target' => '_parent'));?>
				</div>
			</div>		
		<?php if ($container %3 == 0) : ?>
			</li>
	 	<?php endif;?>	
 		<?php $container++ ;} ?>
	<li>
		<div class="sitegrouplike_users_block_links">
			<?php if( !empty($this->detail) )	{
				echo '<a class="smoothbox likes_viewall_link" href="' . $this->url(array('action' => 'like-groups', 'resource_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'call_status' => 'public'), 'sitegroup_like', true) . '">' . $this->translate('See All &raquo;') . '</a>';
			}	?>
		</div>	
	</li>
</ul>	
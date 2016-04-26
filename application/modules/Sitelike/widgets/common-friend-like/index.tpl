<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: friend_like.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<h3><?php echo $this->translate(array('%s Friend Likes This', '%s Friends Like This', $this->num_of_like),$this->locale()->toNumber($this->num_of_like)); ?></h3>
<ul class="sitelike_users_block">
	<?php
		$container = 1;
		foreach( $this->friend_likes_obj as $path_info ) {
		if ($container %3 == 1) : ?>
			<li>
		<?php endif;?>
			<div class="likes_member_list">
				<div class="likes_member_thumb">
					<?php echo $this->htmlLink($path_info->getHref(), $this->itemPhoto($path_info, 'thumb.icon'), array('class' => 'item_photo seao_common_add_tooltip_link', 'title' => $path_info->getTitle(), 'target' => '_parent', 'rel'=> 'user'.' '.$path_info->getIdentity())); ?>
				</div>
				<div class="likes_member_name">
					<?php echo $this->htmlLink($path_info->getHref(), Engine_Api::_()->sitelike()->turncation($path_info->getTitle()), array('title'=> $path_info->getTitle(), 'target' => '_parent', 'class' => 'seao_common_add_tooltip_link', 'rel'=> 'user'.' '.$path_info->getIdentity()));?>
				</div>
			</div>
		<?php if ($container %3 == 0) : ?>
			</li>
	 	<?php endif;?>
 		<?php $container++ ;} ?>
	<li>
		<div class="sitelike_users_block_links">
			<?php if( !empty($this->detail) )	{
				echo '<a class="smoothbox fright" href="' . $this->url(array('module' => 'sitelike', 'controller' => 'index', 'action' => 'likelist', 'resource_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'call_status' => 'friend'), 'default', true) . '">' . $this->translate('See all') . '</a>';
			}	?>
		</div>
	</li>
</ul>
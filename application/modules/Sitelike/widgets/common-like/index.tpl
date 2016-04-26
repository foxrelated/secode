<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: people_like.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<h3><?php echo $this->translate(array('%s Person Likes This', '%s People Like This', $this->num_of_like),$this->locale()->toNumber($this->num_of_like)); ?></h3>
<ul class="sitelike_users_block">
	<?php
		$container = 1;
		foreach( $this->user_obj as $path_info ){
		if ($container %3 == 1) : ?>
			<li>
		<?php endif;?>
			<div class="likes_member_list">
				<div class="likes_member_thumb">
					<?php echo $this->htmlLink($path_info->getHref(), $this->itemPhoto($path_info, 'thumb.icon'),array('title'=>$path_info->getTitle(), 'target' => '_parent', 'class' => 'seao_common_add_tooltip_link', 'rel'=> 'user'.' '.$path_info->getIdentity()));?>
				</div>
				<div class="likes_member_name">
					<?php echo $this->htmlLink($path_info->getHref(), $path_info->getTitle(), array('title' => $path_info->getTitle(), 'target' => '_parent', 'class' => 'seao_common_add_tooltip_link', 'rel'=> 'user'.' '.$path_info->getIdentity())); ?>
				</div>
			</div>
		<?php if ($container %3 == 0) : ?>
			</li>
	 	<?php endif;?>
 		<?php $container++ ;} ?>
	<li>
		<div class="sitelike_users_block_links">
			<?php if( ( $this->user_id == $this->resource_id ) && ($this->num_of_like > 1) ) { ?>
			<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitelike', 'controller' => 'index', 'action' => 'compose', 'resource_type' => 'member', 'resource_id' => $this->user_id), $this->translate("Message All"), array('class' => 'smoothbox likes_viewall_link', 'style'=>'float:left;')); }?>
			<?php if ( !empty($this->detail) ){
			echo '<a class="smoothbox fright" href="' . $this->url(array('module' => 'sitelike', 'controller' => 'index', 'action' => 'likelist', 'resource_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'call_status' => 'public'), 'default', true) . '">' . $this->translate('See all') . '</a>';
			} ?>
		</div>
	</li>
</ul>
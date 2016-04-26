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

<div id="common_like" class="sm-content-list">
	<h3><?php echo $this->translate(array('%s Person Likes This', '%s People Like This', $this->num_of_like),$this->locale()->toNumber($this->num_of_like)); ?></h3>
  <ul data-role="listview" data-icon="false">
		<?php foreach ($this->user_obj as $path_info): ?>
			<?php $container = 1;?>
				<?php if ($container %3 == 1) : ?>
					<li data-icon="arrow-r">
				<?php endif;?>
				<a href="<?php echo $path_info->getHref(); ?>">
					<?php echo $this->itemPhoto($path_info, 'thumb.icon'); ?>
					<h3><?php echo $path_info->getTitle() ?></h3>
				</a> 
			<?php if ($container %3 == 0) : ?>
				</li>
			<?php endif;?>
			<?php $container++ ?>
		<?php endforeach; ?>
	</ul>
  <?php if( ( $this->user_id == $this->resource_id ) && ($this->num_of_like > 1) || (!empty($this->detail))) :?> 
		<div class="seaocore_profile_cover_buttons">
			<table cellpadding="2" cellspacing="0">
				<tr>
					<?php if( ( $this->user_id == $this->resource_id ) && ($this->num_of_like > 1) ) { ?>
						<td>
							<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitelike', 'controller' => 'index', 'action' => 'compose', 'resource_type' => 'member', 'resource_id' => $this->user_id), $this->translate("Message All"), array('class' => 'likes_viewall_link', 'data-role'=>"button", 'data-mini' => "true"));?>
						</td>
					<?php  } ?>
					<?php if ( !empty($this->detail) ){ ?>
						<td>
							<?php  echo '<a data-role="button" data-mini="true" href="' . $this->url(array('module' => 'sitelike', 'controller' => 'index', 'action' => 'likelist', 'resource_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'call_status' => 'public'), 'default', true) . '">' . $this->translate('See all') . '</a>'; ?>
						</td>
					<?php } ?>
				</tr> 
			</table>
		</div>
   <?php endif;?>
</div>
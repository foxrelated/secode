<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: friend_mycontent_likelist.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
	var likeMembers = function(call_status) {
		sm4.core.request.send({
			type: "GET", 
			dataType: "html", 
			url : sm4.core.baseUrl + 'sitelike/index/likelist',
			data: {
				'format':'html',
				'call_status' : call_status,
				'is_ajax' : 1,
				'resource_type' : resource_type,
				'resource_id' : resource_id
			}
		},{
			'element' : $.mobile.activePage.find("#like_members"),
			'showLoading': true
		}
		);
	};
</script>



<div id="like_members">
	<?php
		$mixsettingstable = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' );
		$sub_status_select = $mixsettingstable->fetchRow(array('resource_type = ?'=> $this->resource_type));
		$title = $sub_status_select->title_items;
		if($this->call_status == 'public')	{
			$title = $this->translate('People Who Like This %s', $title);
		}	else	{
			$title = $this->translate('Friends Who Like This %s', $title);
		}
	?>
	<div class="top">
		<div class="heading"><strong><?php echo $title; ?></strong></div>
	</div>
	<div class="sm-content-list">
		<select name="auth_view" onchange="likeMembers($(this).val());" >
			<option value="public" <?php if($this->call_status == 'public'):?> selected="selected" <?php endif;?>><?php echo $this->translate("All") ?></option> 
			<option value="friend" <?php if($this->call_status == 'friend'):?> selected="selected" <?php endif;?> ><?php echo $this->translate("Friends") ?></option> 
		</select>
		<?php if($this->user_obj->getTotalItemCount() > 0): ?>
			<ul data-role="listview" data-icon="arrow-r">
				<?php foreach ($this->user_obj as $user): ?>
					<li>
						<a href="<?php echo $user->getHref(); ?>">
							<?php echo $this->itemPhoto($user, 'thumb.icon'); ?>
							<h3><?php echo $user->getTitle() ?></h3>
						</a> 
					</li>
				<?php endforeach; ?>
				<?php if ($this->user_obj->count() > 1): ?>
					<?php
						echo $this->paginationAjaxControl(
									$this->user_obj, 0, "like_members", array('call_status' => $this->call_status, 'url' => $this->url(array('module' => 'sitelike', 'controller' => 'index', 'action' => 'likelist', 'resource_id' => $this->resource_id, 'resource_type' => $this->resource_type), 'default', true)));
					?>
				<?php endif; ?>
			</ul>
		<?php else:?>
			<div class="tip">
				<span>
					<span><?php echo $this->no_result_msg;?></span>
				</span>
			</div>
		<?php endif; ?>
	</div>
</div>
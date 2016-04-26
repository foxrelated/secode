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
			url : sm4.core.baseUrl + 'sitelike/index/myfriendlikes',
			data: {
				'format':'html',
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

<?php if(empty($this->is_ajax)):?>
	<div class="headline">
		<h2><?php echo $this->translate('Likes');?></h2>
		<div class='tabs'>
			<?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
		</div>
	</div>
<?php endif;?>

<div id="like_members">
	<?php
		$mixsettingstable = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' );
		$sub_status_select = $mixsettingstable->fetchRow(array('resource_type = ?'=> $this->resource_type));
	?>
	<div class="top">
		<div class="heading"><strong><?php echo $this->translate("Friends' Who Like This " . $sub_status_select->title_items ); ?></strong></div>
	</div><br />
	<div class="sm-content-list">
		<?php if($this->user_obj->getTotalItemCount() > 0): ?>
			<ul  data-role="listview" data-icon="arrow-r">
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
									$this->user_obj, 0, "like_members", array('url' => $this->url(array('module' => 'sitelike', 'controller' => 'index', 'action' => 'myfriendlikes', 'resource_id' => $this->resource_id, 'resource_type' => $this->resource_type), 'default', true), 'is_ajax' => 1));
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
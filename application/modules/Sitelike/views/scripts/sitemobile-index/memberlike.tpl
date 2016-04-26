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
<?php 
  $this->headScriptSM()->prependFile($this->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/scripts/sitemobile/core.js')
?>
<script type="text/javascript">
	var call_status = '<?php echo $this->call_status; ?>';
	var resource_id = '<?php echo $this->resource_id; ?>';// Resource Id which are send to controller in the 'pagination' & 'searching'.
	var resource_type = '<?php echo $this->resource_type; ?>';
	var likeMembers = function(call_status) {
		sm4.core.request.send({
			type: "GET", 
			dataType: "html", 
			url : sm4.core.baseUrl + 'sitelike/index/memberlike',
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

<div class="sm-content-list" id="like_members">
	<select name="auth_view" onchange="likeMembers($(this).val());" >
		<option value="public" <?php if($this->call_status == 'public'):?> selected="selected" <?php endif;?>><?php echo $this->translate("All") ?></option> 
		<option value="friend" <?php if($this->call_status == 'friend'):?> selected="selected" <?php endif;?> ><?php echo $this->translate("Friends") ?></option> 
	</select>
  <?php if($this->user_obj): ?>
   <ul data-role="listview" data-icon="none">
			<?php foreach ($this->user_obj as $userinfo): ?>
				<?php
					$like_ids = Engine_Api::_()->getApi('like', 'seaocore')->hasLike('user',$userinfo->user_id );
					if (!empty($like_ids[0]['like_id'])) {
						$unlike_show = "display:block";
						$like_show = "display:none"; 
						$like_id = $like_ids[0]['like_id'];
						}
					else {
						$unlike_show = "display:none;";
						$like_show = "display:block;"; 
						$like_id = 0;
					}
				?>
				<?php
				if(!empty( $this->like_setting_button)) {
					$like_setting_button = $unlike_show;
				}
				else {
					$like_setting_button = "display:none;";
				}
			 ?> 
       <li data-inset="true" id="li_member_like_<?php echo $userinfo->user_id;?>" <?php if(($this->like_setting_button || $like_id == 0)) : ?> data-icon="<?php echo $like_id > 0 ? "thumbs-down": "thumbs-up"  ?>" <?php endif;?> >
					<a href="<?php echo $userinfo->getHref(); ?>">	
           <?php echo $this->itemPhoto($userinfo, 'thumb.icon'); ?>
           <h3><strong><?php echo $userinfo->getTitle() ?></strong></h3>
						<p class="sm-ui-lists-action" id="member_num_of_like_<?php echo $userinfo->user_id;?>">
							<?php
								// Members 'Number of likes'.
								$friend_like = Engine_Api::_()->getApi('like', 'seaocore')->likeCount($this->resource_type, $userinfo->user_id );
								echo $this->translate(array('%s like', '%s likes', $friend_like),$this->locale()->toNumber($friend_like)) ?>
						</p>
          </a> 
					<?php if(($this->like_setting_button || $like_id == 0)) :?>
						<a href = "javascript:void(0);" onclick="sm4.sitelike.do_like.createLike('<?php echo $userinfo->user_id; ?>', 'user', 'member');"></a>
						<div class="ui-item-member-action">
							<p class="item-listlike-button">
								<?php if(!empty($this->viewer_id)) { ?>
									<a class="fright" href = "javascript:void(0);" id =  "member_unlikes_<?php echo $userinfo->user_id;?>" style ='<?php echo $like_setting_button;?>'>
										<i class="ui-icon ui-icon-thumbs-down"></i>
									</a>
									<a class="fright" href = "javascript:void(0);" id="member_most_likes_<?php echo $userinfo->user_id;?>" style ='<?php echo $like_show;?>'>
										<i class="ui-icon ui-icon-thumbs-up"></i>
									</a>
								<?php } ?>
							</p>
							<input type ="hidden" id = "member_like_<?php echo $userinfo->user_id;?>" value = '<?php echo $like_id; ?>' /> />
						</div>
					<?php endif;?>
       </li>
    <?php endforeach; ?>
		<?php if ($this->user_obj->count() > 1): ?>
			<?php
				echo $this->paginationAjaxControl(
							$this->user_obj, 0, "like_members", array('call_status' => $this->call_status, 'url' => $this->url(array('module' => 'sitelike', 'controller' => 'index', 'action' => 'memberlike', 'resource_id' => $this->resource_id, 'resource_type' => $this->resource_type, 'is_ajax' => 1), 'default', true)));
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
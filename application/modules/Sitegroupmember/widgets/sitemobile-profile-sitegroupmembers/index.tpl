<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<script type="text/javascript">

	function removeGroupAdmin (group_id, user_id) 
	{ 
		var friendUrl = '<?php echo $this->url(array('action' => 'delete'), 'sitegroup_manageadmins', true) ?>';
		$.ajax({
			url : friendUrl,
      type: "POST",
      dataType: "html",
			data : {
				format: 'html',
				group_id: group_id,
				owner_id: user_id
			},
			'success' : function(responseTree, responseElements, responseHTML, responseJavaScript)
			{
				document.getElementById('remove_group_admin').style.display = 'none';
				parent.window.location.reload();
			}
		});
	};

	function userWidgetRequestSend (group_id, user_id) 
	{ 
		var friendUrl = '<?php echo $this->url(array('action' => 'index'), 'sitegroup_manageadmins', true) ?>';
		$.ajax({
			url : friendUrl,
			type: "POST",
			dataType: "html",
			data : {
				format: 'html',
				group_id: group_id,
				user_id: user_id
			},
			'success' : function(responseTree, responseElements, responseHTML, responseJavaScript)
			{
				document.getElementById('make_group_admin').style.display = 'none';
				parent.window.location.reload();
			}
		});
	}

  function profileTabRequest(visibility, role_id) {

    var urlRequest = sm4.core.baseUrl + 'core/widget/index/content_id/' + <?php echo $this->identity?>;
		if($.mobile.activePage.find("#global_group_sitegroup-index-view")) {
			urlRequest = sm4.core.baseUrl + 'sitegroup/mobile-widget/index/content_id/' + <?php echo $this->identity?>;
		} else if($.mobile.activePage.find("#global_group_sitegroup-index-view")) {
			urlRequest = sm4.core.baseUrl + 'sitegroup/mobile-widget/index/content_id/' + <?php echo $this->identity?>;
		} else if($.mobile.activePage.find("#global_group_sitegroup-index-view")) {
			urlRequest = sm4.core.baseUrl + 'sitegroup/mobile-widget/index/content_id/' + <?php echo $this->identity?>;
		}

		sm4.core.request.send({
      type: "POST", 
      dataType: "html", 
      url : urlRequest,
      data: {
        'subject': sm4.core.subject.guid != '' ? sm4.core.subject.guid : $.mobile.activePage.attr("data-subject"),
        'format':'html',
        'visibility': visibility,
        'role_id':  role_id
      },
    },{
      'element' : $.mobile.activePage.find("#profile_sitegroupmembers_<?php echo $this->identity?>").parent().parent(),
			 showLoading:true
    }
    );
  }

</script>


<div class="sm-content-list">
	<?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($this->viewer_id, $this->group_id, $params = 'Invite'); ?>
	<?php if ($hasMembers && $this->viewer()->getIdentity()): ?>
		<table cellpadding="2" cellspacing="0">
      <tr>
				<?php if (!empty($this->can_edit)) : ?>
					<td>
						<?php
							echo $this->htmlLink(array(
								'route' => 'sitegroup_profilegroupmember',
								'action' => 'invite-members',
								'group_id' => $this->group_id,
										), $this->translate('Add People'), array(
								'class' => 'smoothbox',
								'data-role' => "button", 'data-icon' => "plus", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"
						))
						?>
					</td>
				<?php elseif (empty($this->sitegroup->member_invite)): ?>
					<td>
						<?php
						echo $this->htmlLink(array(
								'route' => 'sitegroup_profilegroupmember',
								'action' => 'invite',
								'group_id' => $this->group_id,
										), $this->translate('Add People'), array(
								'class' => 'smoothbox',
								'data-role' => "button", 'data-icon' => "plus", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"
						))
						?>
					</td>
				<?php endif; ?>
       <?php if($this->viewer()->getIdentity()) :?>
				<td>
					<?php
						echo $this->htmlLink(array(
								'route' => 'default',
								'module' => 'sitegroupmember',
								'controller' => 'index',
								'action' => 'compose',
								'resource_id' => $this->group_id,
										), $this->translate('Message Members'), array(
								'class' => 'smoothbox',
								'data-role' => "button", 'data-icon' => "envelope", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true", 'data-ajax' => 'false'
						));
					?>
				</td>
       <?php endif;?>
      </tr>
		</table><br/>
	<?php endif; ?>

  <strong><?php echo $this->translate('Browse by:'); ?></strong>
	<select name="visibility"  onchange = "profileTabRequest($(this).val(), '<?php echo $this->role_id ?>');">
		<option value="join_date" <?php if ($this->visibility == 'join_date'): ?> selected='selected' <?php endif; ?>><?php echo $this->translate("Members by Joined Date"); ?></option>        
		<option value="featured" <?php if ($this->visibility == 'featured'): ?> selected='selected' <?php endif; ?>><?php echo $this->translate("Featured Members"); ?></option>
		<option value="highlighted" <?php if ($this->visibility == 'highlighted'): ?> selected='selected' <?php endif; ?>><?php echo $this->translate("Highlighted Members"); ?></option>             
		<option value="displayname" <?php if ($this->visibility == 'displayname'): ?> selected='selected' <?php endif; ?>><?php echo $this->translate("Members By Name"); ?></option>
		<option value="groupadmin" <?php if ($this->visibility == 'groupadmin'): ?> selected='selected' <?php endif; ?> ><?php echo $this->translate("Group Admins"); ?></option>
	</select>

	<?php if (count($this->roleParamsArray) > 1): ?> <br />
		<strong><?php echo $this->translate('Roles:'); ?></strong>
		<select name="role_id" onchange = "profileTabRequest('<?php echo $this->visibility;?>', $(this).val());">
			<option value="0" ></option>
			<?php foreach ($this->roleParamsArray as $key => $roleLabel): ?>	
				<option value="<?php echo $key ?>" <?php if ($this->role_id == $key): ?> selected="selected" <?php endif; ?>><?php echo $this->translate($roleLabel); ?></option>
			<?php endforeach; ?>
		</select><br />
	<?php endif; ?>

	<ul data-role="listview" data-inset="false" id="profile_sitegroupmembers_<?php echo $this->identity;?>">
		<?php if ($this->request_count->getTotalItemCount() > 0) : ?>
			<?php if (!empty($this->can_edit)) : ?>
				<div class="sitegroup_member_see_waiting"><?php echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'request-member', 'group_id' => $this->group_id, 'tab' => $this->identity), $this->translate(array('View %s membership request waiting for approval.', 'View %s membership requests waiting for approval.', $this->request_count->getTotalItemCount()), $this->request_count->getTotalItemCount()), array(' class' => 'ui-btn-default smoothbox')); ?></div>	
			<?php endif; ?><br />
		<?php endif; ?>
    <?php if($this->paginator->getTotalItemCount() > 0):?>
			<?php foreach ($this->paginator as $sitegroupmember):?>
				<?php $isGroupAdmin = $this->sitegroup->isGroupAdmin($sitegroupmember->user_id); ?>
				<li <?php if (($sitegroupmember->group_owner_id != $sitegroupmember->user_id && $sitegroupmember->user_id == $this->viewer_id) || !empty($this->can_edit)): ?> data-icon="cog" data-inset="true" <?php else:?> data-icon = "false" <?php endif;?>>
					<a href="<?php echo $sitegroupmember->getHref(); ?>">
						<?php echo $this->itemPhoto($sitegroupmember->getOwner(), 'thumb.profile'); ?>
						<h3><?php echo $sitegroupmember->getTitle(); ?></h3>
                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.title')) : ?>
                    <?php if (!empty($sitegroupmember->title)) : ?>
                      <p>
                        <?php $roleCount = count(json_decode($sitegroupmember->title));
                        $a  = json_decode($sitegroupmember->title);
                        if($roleCount == 1) :
													echo implode(", ", json_decode($sitegroupmember->title));
                        elseif($roleCount == 2):
													echo implode(" and ", json_decode($sitegroupmember->title));
                        endif;
                        ?>
                      </p>
                    <?php endif; ?>
                  <?php endif; ?>
					<p>
						<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.date')): ?>
								<?php if (!empty($sitegroupmember->date)) : ?>
									<?php echo $this->translate('MEMBER_DATE') ?>
									<?php
									      $DATE = explode('-', $sitegroupmember->date);
                        $YY = $DATE['0'];
                        $MM = $DATE['1'];
                        $DD = $DATE['2'];
                        if ($MM == '00' && $DD == '00') {
                          $dateShow = ' in ' . $YY;
                        } elseif ($DD == '00') {
                          $dateShow = ' in ' . date('F', strtotime($MM)) . ', ' . $YY;
                        } elseif ($YY != '00' && $MM != '00' && $DD != '00') {
                         $dateShow = ' on ' . date('F', strtotime($sitegroupmember->date)) . ' ' . $DD . ', ' . $YY;
                        }
                        echo $dateShow;
									?>
								<?php endif; ?>
						<?php endif; ?>
					</p> 
					</a>
					<?php if (($sitegroupmember->group_owner_id != $sitegroupmember->user_id && $sitegroupmember->user_id == $this->viewer_id) || !empty($this->can_edit)): ?>
						<a href="#sitegroupmember_<?php echo $sitegroupmember->member_id;?>" data-rel="popup"></a>
						<div data-role="popup" id="sitegroupmember_<?php echo $sitegroupmember->member_id ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
						<div data-inset="true" style="min-width:150px;" class="sm-options-popup">
							<?php if ($sitegroupmember->group_owner_id != $sitegroupmember->user_id) : ?>
								<?php if ($sitegroupmember->active == 1 && $sitegroupmember->user_approved == 1 && empty($this->can_edit)):
									echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'remove', 'member_id' => $sitegroupmember->member_id, 'tab' => $this->identity, 'params' => 'leave'), $this->translate('Leave Group'), array(' class' => 'ui-btn-default smoothbox')) ?>
								<?php else: ?>
									<?php echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'remove', 'member_id' => $sitegroupmember->member_id, 'tab' => $this->identity), $this->translate('Remove Member'), array(' class' => 'ui-btn-default smoothbox')) ?>
								<?php endif; ?>
								<?php if (!empty($this->can_edit)): ?> 
									<?php if (empty($isGroupAdmin)) : ?>
										<a id="make_group_admin" href="javascript:void(0);" onclick="userWidgetRequestSend('<?php echo $sitegroupmember->group_id ?>', '<?php echo $sitegroupmember->user_id ?>');" class="ui-btn-default"><?php echo $this->translate('Make Group Admin') ?></a>
									<?php else: ?>
										<a id="remove_group_admin" href="javascript:void(0);" onclick="removeGroupAdmin('<?php echo $sitegroupmember->group_id ?>', '<?php echo $sitegroupmember->user_id ?>');" class="ui-btn-default"><?php echo $this->translate('Remove Group Admin') ?></a>
									<?php endif; ?>
								<?php endif; ?>
							<?php endif; ?>
							<?php if ($sitegroupmember->user_id != $this->viewer_id) : ?>
								<?php if (!empty($this->can_edit)): ?> 
									<?php if ($sitegroupmember->active == 0 && $sitegroupmember->user_approved == 0) :
										echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'approve', 'member_id' => $sitegroupmember->member_id, 'group_id' => $sitegroupmember->group_id, 'tab' => $this->identity), $this->translate('Approve Member'), array(' class' => 'ui-btn-default smoothbox')); ?>
										<?php echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'reject', 'group_id' => $sitegroupmember->group_id, 'member_id' => $sitegroupmember->member_id, 'tab' => $this->identity), $this->translate('Reject Request'), array(' class' => 'ui-btn-default smoothbox')); ?>
										<?php endif; ?>
									<?php endif; ?>
								<?php endif; ?>
								<?php if ($sitegroupmember->user_id == $this->viewer_id) : ?>
								<?php echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'notification-settings', 'member_id' => $sitegroupmember->member_id, 'tab' => $this->identity), $this->translate('Notification Settings'), array(' class' => 'ui-btn-default smoothbox')) ?>
								<?php endif; ?>
								<?php if ($sitegroupmember->user_id == $this->viewer_id || !empty($this->can_edit)) : ?>
									<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.date') || Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.title')) : ?>
									<?php echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'edit', 'member_id' => $sitegroupmember->member_id, 'group_id' => $sitegroupmember->resource_id, 'tab' => $this->identity), $this->translate('Edit'), array(' class' => 'ui-btn-default smoothbox')) ?>
									<?php endif; ?>
								<?php endif; ?>
								<a href="#" data-rel="back" class="ui-btn-default">
									<?php echo $this->translate('Cancel'); ?>
								</a>
							</div> 
						</div>
					<?php endif;?>
				</li>
			<?php endforeach;?>
	</ul>
	<?php if ($this->paginator->count() > 1): ?>
		<?php
		echo $this->paginationAjaxControl(
						$this->paginator, $this->identity, "profile_sitegroupmembers_$this->identity", array('visibility' => $this->visibility, 'role_id' => $this->role_id));
		?>
	<?php endif; ?>
	<?php else: ?>
		<div class="tip">
			<span>
				<?php echo $this->translate('No such members have joined this group.'); ?>
			</span>
		</div>
	<?php endif; ?>
</div>
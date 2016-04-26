<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
	seaocore_content_type = 'sitegroup_group';
</script>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>   
<?php $minHeight=120; ?>
<div class="sitegroup_cover_information_wrapper">
  <div class='sitegroup_cover_wrapper' id="sitegroup_cover_photo" style='min-height:<?php echo $minHeight;?>px; height:<?php echo (!empty($this->sitegroup->group_cover)) ? $this->columnHeight:$minHeight; ?>px;'  >
  </div>
  <?php if (!empty($this->showContent) || !empty($this->statistics)) : ?>
  <div class="sitegroup_cover_information b_medium">
    <?php if($this->showContent):?>
			<div class="sp_coverinfo_buttons">
				<?php if (in_array('likeButton', $this->showContent)): ?>
					<div>
						<?php echo $this->content()->renderWidget("seaocore.like-button"); ?>
					</div>	
				<?php endif; ?>
				<?php if (in_array('followButton', $this->showContent)): ?>
					<div>
						<?php echo $this->content()->renderWidget("seaocore.seaocore-follow"); ?>
					</div>	
				<?php endif; ?>
				<?php $joinMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($this->viewer_id, $this->sitegroup->group_id);
				if (empty($joinMembers) && in_array('joinButton', $this->showContent) && $this->viewer_id != $this->sitegroup->owner_id && !empty($this->allowGroup)): ?>
				<div>
					<?php if (!empty($this->viewer_id)) : ?>
						<?php if (!empty($this->sitegroup->member_approval)): ?>
							<a class="sitegroup_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'join', 'group_id' => $this->sitegroup->group_id), 'sitegroup_profilegroupmember', true)); ?>'); return false;" ><span><?php echo $this->translate("Join Group"); ?></span></a>
						<?php else: ?>
							<a class="sitegroup_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'request', 'group_id' => $this->sitegroup->group_id), 'sitegroup_profilegroupmember', true)); ?>'); return false;" ><span><?php echo $this->translate("Join Group"); ?></span></a>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				
				<?php if (in_array('addButton', $this->showContent)): ?>
					<?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($this->viewer_id, $this->sitegroup->group_id, $params = 'Invite'); ?>
					<?php if (!empty($hasMembers) && !empty($this->can_edit)) : ?>
					<div>
						<a class="sitegroup_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'group_id' => $this->sitegroup->group_id), 'sitegroup_profilegroupmember', true)); ?>'); return false;" ><i class="add_people"></i><span><?php echo $this->translate("Add People"); ?></span></a>	
					</div>
					<?php elseif (!empty($hasMembers) && empty($this->sitegroup->member_invite)): ?>
					<div>
						<a class="sitegroup_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'group_id' => $this->sitegroup->group_id), 'sitegroup_profilegroupmember', true)); ?>'); return false;" ><i class="add_people"></i><span><?php echo $this->translate("Add People"); ?></span></a>
					</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
    <?php endif; ?>
    <?php if($this->statistics):?>
			<div class="sp_coverinfo_status">
				<?php if (in_array('title', $this->showContent)): ?>
				<h2><?php echo $this->sitegroup->getTitle() ?></h2>
				<?php endif; ?>
				<div class="sp_coverinfo_stats seaocore_txt_light">
					<?php if (in_array('likeCount', $this->statistics) && isset($this->sitegroup->like_count)): ?>
						<a id= "sitegroup_group_num_of_like_<?php echo $this->sitegroup->group_id;?>" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => 'sitegroup_group', 'resource_id' => $this->sitegroup->group_id, 'call_status' => 'public'), 'default', true)); ?>'); return false;" ><?php echo $this->translate(array('%s like', '%s likes', $this->sitegroup->like_count),$this->locale()->toNumber($this->sitegroup->like_count)); ?></a>
					<?php endif; ?>
					
					<?php if (in_array('followCount', $this->statistics) && isset($this->sitegroup->follow_count)): ?>
						<?php if (in_array('likeCount', $this->statistics) && isset($this->sitegroup->like_count)): ?>
							&middot; 
						<?php endif; ?>
						<a id= "sitegroup_group_num_of_follows_<?php echo $this->sitegroup->group_id;?>" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'follow', 'action'=>'get-followers', 'resource_type'	=> 'sitegroup_group', 'resource_id' => $this->sitegroup->group_id, 'format' => 'smoothbox', 'call_status' => 'public'), 'default'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s follower', '%s followers', $this->sitegroup->follow_count),$this->locale()->toNumber($this->sitegroup->follow_count)); ?></a>	
					<?php endif; ?>
					
					<?php if (in_array('memberCount', $this->statistics) && isset($this->sitegroup->member_count)): ?>
						<?php //if (in_array('likeCount', $this->statistics) && isset($this->sitegroup->like_count)): ?>
							&middot; 
						<?php //endif; ?>
						<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1);
						if ($this->sitegroup->member_title && $memberTitle) {
							if ($this->sitegroup->member_count == 1) : ?>
							<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('action'=>'member-join', 'group_id' => $this->sitegroup->group_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitegroupmember_approve'	, true)); ?>'); return false;" ><?php echo $this->sitegroup->member_count . ' member'; ?></a>
						<?php	else: ?>
						<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('action'=>'member-join', 'group_id' => $this->sitegroup->group_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitegroupmember_approve'	, true)); ?>'); return false;" ><?php echo $this->sitegroup->member_count . ' ' .  $this->sitegroup->member_title;?></a>
						<?php 	endif; ?>
						<?php } else { ?>
						<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('action'=>'member-join', 'group_id' => $this->sitegroup->group_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitegroupmember_approve'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s member', '%s members', $this->sitegroup->member_count),$this->locale()->toNumber($this->sitegroup->member_count)); ?></a>
					  <?php 	} ?>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
  </div>
  <?php endif; ?>
<div class="clr"></div>
</div>
<script type="text/javascript">
    document.seaoCoverPhoto= new SitegroupCoverPhoto({
      block :$('sitegroup_cover_photo'),
      photoUrl:'<?php echo $this->url(array('action' => 'get-cover-photo', 'group_id' => $this->sitegroup->group_id,'show_member'=>1, 'memberCount'=>$this->memberCount, 'onlyMemberWithPhoto' => $this->onlyMemberWithPhoto), 'sitegroup_profilegroup', true); ?>',
      buttons:'seao_cover_options',
      columnHeight:<?php echo $this->columnHeight ?>,
      positionUrl :'<?php echo $this->url(array('action' => 'reset-position-cover-photo', 'group_id' => $this->sitegroup->group_id), 'sitegroup_dashboard', true); ?>',
      position :<?php  echo $this->cover_params ? json_encode($this->cover_params): json_encode(array('top' => 0, 'left' => 0)); ?>
    });
</script>
<script type="text/javascript">
	function showSmoothBox(url) {
		Smoothbox.open(url);
		parent.Smoothbox.close;
	}
</script>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $minHeight=110; 
if($this->sitepage->sponsored)
  $minHeight =$minHeight +20;
if($this->sitepage->featured)
  $minHeight =$minHeight +20;
?>
<div class="sitepage_cover_information_wrapper">

  <div class='sitepage_cover_wrapper' id="sitepage_cover_photo" style='min-height:<?php echo $minHeight;?>px; height:<?php echo (!empty($this->sitepage->page_cover) || !empty($this->can_edit)) ? $this->columnHeight:$minHeight; ?>px;'  >
  </div>
  <?php if($this->showContent):?>
  <div class="sitepage_cover_information b_medium">
    <?php if (in_array('mainPhoto', $this->showContent)): ?>
      <div class="sp_coverinfo_profile_photo_wrapper">
        <div class="sp_coverinfo_profile_photo b_dark">
          <?php if (!empty($this->sitepage->sponsored)): ?>
            <?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.image', 1);
            if (!empty($sponsored)) { ?>
              <div class="sitepage_profile_sponsorfeatured" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
                <?php echo $this->translate('SPONSORED'); ?>
              </div>
            <?php } ?>
          <?php endif; ?>
          <div class='sitepage_photo <?php if ($this->can_edit) : ?>sitepage_photo_edit_wrapper<?php endif; ?>'>
            <?php if (!empty($this->can_edit)) : ?>
              <a href="<?php echo $this->url(array('action' => 'profile-picture', 'page_id' => $this->sitepage->page_id), 'sitepage_dashboard', true) ?>" class="sitepage_photo_edit">  	  
                <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/edit_pencil.png', '') ?>
                <?php echo $this->translate('Change Picture'); ?>
              </a>
            <?php endif; ?>
            <table>
              <tr valign="middle">
                <td>
                  <?php echo $this->itemPhoto($this->sitepage, 'thumb.profile', '', array('align' => 'left')); ?>
                </td>
              </tr>
            </table>
          </div>
          <?php if (!empty($this->sitepage->featured)): ?>
            <?php $feature = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.image', 1);
            if (!empty($feature)) { ?>
              <div class="sitepage_profile_sponsorfeatured"  style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.featured.color', '#0cf523'); ?>;'>
                <?php echo $this->translate('FEATURED'); ?>
              </div>
            <?php } ?>
          <?php endif; ?>
        </div>	
      </div>
    <?php endif; ?>

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
      <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) : ?>
				<?php $joinMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $this->sitepage->page_id);
				if (empty($joinMembers) && in_array('joinButton', $this->showContent) && $this->viewer_id != $this->sitepage->owner_id && !empty($this->allowPage)): ?>
					<div>
					<?php if (!empty($this->viewer_id)) : ?>
						<?php if (!empty($this->sitepage->member_approval)): ?>
							<a class="sitepage_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'join', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'); return false;" ><i class="plus_icon"></i><span><?php echo $this->translate("Join Page"); ?></span></a>
						<?php else: ?>
							<a class="sitepage_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'request', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'); return false;" ><i class="plus_icon"></i><span><?php echo $this->translate("Join Page"); ?></span></a>
						<?php endif; ?>
					<?php endif; ?>
					</div>
				<?php endif; ?>

        <?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $this->sitepage->page_id, $params = "Leave");
        if (!empty($hasMembers) && in_array('leaveButton', $this->showContent) && $this->viewer_id != $this->sitepage->owner_id && !empty($this->allowPage)): ?>
					<div>
            <?php if ($this->viewer_id) : ?>
							<a  class="sitepage_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'leave', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'); return false;" ><i class="plus_icon"></i><span><?php echo $this->translate("Leave Page"); ?></span></a>
            <?php endif; ?>
					</div>
				<?php endif; ?>
      
				<?php if (in_array('addButton', $this->showContent)): ?>
					<?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $this->sitepage->page_id, $params = 'Invite'); ?>
					<?php if (!empty($hasMembers) && !empty($this->can_edit)) : ?>
					<div>
						<a class="sitepage_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'); return false;" ><i class="add_people"></i><span><?php echo $this->translate("Add People"); ?></span></a>	
					</div>
					<?php elseif (!empty($hasMembers) && empty($this->sitepage->member_invite)): ?>
					<div>
						<a class="sitepage_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'); return false;" ><i class="add_people"></i><span><?php echo $this->translate("Add People"); ?></span></a>
					</div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
			
    </div>
    <div class="sp_coverinfo_status">
      <?php if (in_array('title', $this->showContent)): ?>
        <h2><?php echo $this->sitepage->getTitle() ?></h2>
      <?php endif; ?>
      <div class="sp_coverinfo_stats seaocore_txt_light">
        <?php if (in_array('likeCount', $this->showContent) && isset($this->sitepage->like_count)): ?>
          <a id= "sitepage_page_num_of_like_<?php echo $this->sitepage->page_id;?>" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => 'sitepage_page', 'resource_id' => $this->sitepage->page_id, 'call_status' => 'public'), 'default', true)); ?>'); return false;" ><?php echo $this->translate(array('%s like', '%s likes', $this->sitepage->like_count),$this->locale()->toNumber($this->sitepage->like_count)); ?></a>
        <?php endif; ?>
        
        <?php if (in_array('followCount', $this->showContent) && isset($this->sitepage->follow_count)): ?>
					<?php if (in_array('likeCount', $this->showContent) && isset($this->sitepage->like_count)): ?>
						&middot; 
					<?php endif; ?>
						<a id= "sitepage_page_num_of_follows_<?php echo $this->sitepage->page_id;?>" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'follow', 'action'=>'get-followers', 'resource_type'	=> 'sitepage_page', 'resource_id' => $this->sitepage->page_id, 'format' => 'smoothbox', 'call_status' => 'public'), 'default'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s follower', '%s followers', $this->sitepage->follow_count),$this->locale()->toNumber($this->sitepage->follow_count)); ?></a>
        <?php endif; ?>

				<?php if (in_array('memberCount', $this->showContent) && isset($this->sitepage->member_count) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')): ?>
					<?php //if (in_array('likeCount', $this->statistics) && isset($this->sitepage->like_count)): ?>
					&middot; 
					<?php //endif; ?>
						<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
						if ($this->sitepage->member_title && $memberTitle) { ?>
						<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('action'=>'member-join', 'page_id' => $this->sitepage->page_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitepagemember_approve'	, true)); ?>'); return false;" ><?php echo $this->sitepage->member_count . ' ' .  $this->sitepage->member_title;?></a>
						<?php } else { ?>
						<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('action'=>'member-join', 'page_id' => $this->sitepage->page_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitepagemember_approve'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s member', '%s members', $this->sitepage->member_count),$this->locale()->toNumber($this->sitepage->member_count)); ?></a>
					  <?php 	} ?>
				<?php endif; ?>
				
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
<div class="clr"></div>
<script type="text/javascript">
    document.seaoCoverPhoto= new SitepageCoverPhoto({
      block :$('sitepage_cover_photo'),
      photoUrl:'<?php echo $this->url(array('action' => 'get-cover-photo', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepage', true); ?>',
      buttons:'seao_cover_options',
      positionUrl :'<?php echo $this->url(array('action' => 'reset-position-cover-photo', 'page_id' => $this->sitepage->page_id), 'sitepage_dashboard', true); ?>',
      position :<?php  echo $this->cover_params ? json_encode($this->cover_params): json_encode(array('top' => 0, 'left' => 0)); ?>
    });
  </script>

<script type="text/javascript">
	function showSmoothBox(url) {
		Smoothbox.open(url);
	}
</script>
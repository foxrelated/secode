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
<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<ul class="sitegroup_sidebar_list">
	<?php foreach ($this->sitegroups as $sitegroup): ?>
		<li>
			<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()), $this->itemPhoto($sitegroup, 'thumb.icon'), array('title' => $sitegroup->getTitle())) ?>
			<div class='sitegroup_sidebar_list_info'>
				<div class='sitegroup_sidebar_list_title'>
					<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()), Engine_Api::_()->sitegroup()->truncation($sitegroup->getTitle()), array('title' => $sitegroup->getTitle())) ?>
				</div>
				<?php if ($this->statistics): ?>
					<?php if(in_array('likeCount', $this->statistics) || in_array('followCount', $this->statistics)) : ?>
						<div class="seaocore_browse_list_info_date">
							<?php if(in_array('likeCount', $this->statistics)): ?>
								<?php echo $this->translate(array('%s like', '%s likes', $sitegroup->like_count), $this->locale()->toNumber($sitegroup->like_count)) ?><?php endif; ?><?php if(in_array('likeCount', $this->statistics) && in_array('followCount', $this->statistics)) : ?>, <?php endif; ?><?php if(in_array('followCount', $this->statistics)): ?><?php echo $this->translate(array('%s follower', '%s followers', $sitegroup->follow_count), $this->locale()->toNumber($sitegroup->follow_count)) ?>	
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if(in_array('viewCount', $this->statistics) || in_array('memberCount', $this->statistics)) : ?>
						<div class="seaocore_browse_list_info_date">
							<?php if(in_array('viewCount', $this->statistics)): ?>
								<?php echo $this->translate(array('%s view', '%s views', $sitegroup->view_count), $this->locale()->toNumber($sitegroup->view_count)) ?><?php endif; ?><?php if(in_array('viewCount', $this->statistics) && in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) : ?>, <?php endif; ?><?php if(in_array('memberCount', $this->statistics)): ?><?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')): ?>
									<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1);
									if ($sitegroup->member_title && $memberTitle) : ?>
									<?php echo $sitegroup->member_count . ' ' .  $sitegroup->member_title; ?>
									<?php else : ?>
									<?php echo $this->translate(array('%s member', '%s members', $sitegroup->member_count), $this->locale()->toNumber($sitegroup->member_count)) ?>
									<?php endif; ?>
								<?php endif; ?>		
							<?php endif; ?>		
						</div>
					<?php endif; ?>	
					<?php if(in_array('commentCount', $this->statistics) || in_array('reviewCount', $this->statistics)) : ?>
						<div class="seaocore_browse_list_info_date">
							<?php if(in_array('commentCount', $this->statistics)): ?>
								<?php echo $this->translate(array('%s comment', '%s comments', $sitegroup->comment_count), $this->locale()->toNumber($sitegroup->comment_count)) ?><?php endif;?><?php if(in_array('commentCount', $this->statistics) && in_array('reviewCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')):?>, <?php endif; ?>			<?php if(in_array('reviewCount', $this->statistics)): ?>
								<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')): ?>
									<?php echo $this->translate(array('%s review', '%s reviews', $sitegroup->review_count), $this->locale()->toNumber($sitegroup->review_count)) ?>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</li>
	<?php endforeach; ?>
</ul>

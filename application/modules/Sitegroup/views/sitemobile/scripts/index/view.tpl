<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl'; ?>
<?php if (empty($this->viewPrivacy) && !empty($this->sitegroupMemberEnabled) && empty($this->hasMembers)) : ?>
<div>
	<div  class="o_hidden t_center">
		<?php //echo $this->sitemain; ?>
		<?php if (!empty($this->sitegroup->sponsored)): ?>
		  <?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.image', 1);
		  if (!empty($sponsored)) { ?>
		    <div class="sitegroup_profile_sponsorfeatured" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.color', '#fc0505'); ?>;'>
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/star-img.png', '') ?>
		      <?php echo $this->translate('SPONSORED'); ?>
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/star-img.png', '') ?>
		    </div>
		  <?php } ?>
		<?php endif; ?>
		<div class='sitegroup_photo <?php if ($this->can_edit) : ?>sitegroup_photo_edit_wrapper<?php endif; ?>'>
		  <?php if (!empty($this->can_edit)) : ?>
		    <a href="<?php echo $this->url(array('action' => 'profile-picture', 'group_id' => $this->sitegroup->group_id), 'sitegroup_dashboard', true) ?>" class="sitegroup_photo_edit">  	  
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/edit_pencil.png', '') ?>
		      <?php echo $this->translate('Change Picture'); ?>
		    </a>
		  <?php endif; ?>
		
		  <div class="t_center" style="float: none; display: inline-block;">
                      <?php echo $this->itemPhoto($this->sitegroup, 'thumb.profile', '', array('align' => 'left')); ?>
                  </div>
		</div>
		<?php if (!empty($this->sitegroup->featured)): ?>
		  <?php $feature = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.feature.image', 1);
		  if (!empty($feature)) { ?>
		    <div class="sitegroup_profile_sponsorfeatured"  style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.featured.color', '#0cf523'); ?>;'>
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/star-img.png', '') ?>
		      <?php echo $this->translate('FEATURED'); ?>
		    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/star-img.png', '') ?>
		    </div>
		  <?php } ?>
		<?php endif; ?>
		<?php if (!empty($this->viewer_id)): ?>
			<div class="generic_layout_container layout_sitegroup_options_sitegroup clr">
				<?php if (!empty($this->member_approval)) : ?>
					<br /><?php echo $this->htmlLink(array('route' => 'sitegroup_profilegroupmember', 'action' => 'join', 'group_id' => $this->sitegroup->group_id), $this->translate('Join Group'), array(  ' class' => 'buttonlink smoothbox icon_sitegroup_join')); ?>
					<?php else : ?>
						<?php if (empty($this->hasMembers)) : ?>
							<br /><?php echo $this->htmlLink(array('route' => 'sitegroup_profilegroupmember', 'action' => 'request', 'group_id' => $this->sitegroup->group_id), $this->translate('Join Group'), array(  ' class' => 'buttonlink smoothbox icon_sitegroup_join')); ?>
						<?php else : ?>
							<br /><?php echo $this->htmlLink(array('route' => 'sitegroup_profilegroupmember', 'action' => 'cancel', 'group_id' => $this->sitegroup->group_id), $this->translate('Cancel Member Request for Group'), array(  ' class' => 'buttonlink smoothbox icon_sitegroup_join')); ?>
						<?php endif ;?>
				<?php endif;  ?>
			</div>
		<?php endif;  ?>
	</div>
	
	<div class="clr">
		<div id='profile_status'>
		  <h2>
		    <?php echo $this->sitegroup->getTitle() ?>
		  </h2>
		</div>
		
		<div class="generic_layout_container layout_core_container_tabs">
			<div class='tabs_alt tabs_parent'>
				<ul class="main_tabs">
					<li id="info" class="active">
						<a href="javascript:void(0);" onclick="showtab('info')"><?php echo $this->translate('Info') ?></a>
					</li>
					<?php if ($this->sitegroup->overview && !empty($this->can_edit_overview)) :?>
					<li id="overview" class="active">
						<a href="javascript:void(0);" onclick="showtab('overview')"><?php echo $this->translate('Overview') ?></a>
					</li>
					<?php endif; ?>
				</ul>
			</div>
			<div id="infocontentshow">
				 <?php 	echo $this->content()->renderWidget("sitegroup.info-sitegroup", array()); ?>
			</div>
			<?php if ($this->sitegroup->overview && !empty($this->can_edit_overview)) :?>
				<div id="overviewcontentshow" >
					<?php echo $this->content()->renderWidget("sitegroup.overview-sitegroup", array()); ?>
				</div>
			<?php endif; ?>
	  </div>
	</div>

</div>
<?php elseif (empty($this->viewPrivacy) && !empty($this->sitegroupMemberEnabled) && !empty($this->hasMembers)) : ?>
<div class="layout_middle">
	<div class="layout_left">
		<?php //echo $this->sitemain; ?>
		<?php if (!empty($this->sitegroup->sponsored)): ?>
		  <?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.image', 1);
		  if (!empty($sponsored)) { ?>
		    <div class="sitegroup_profile_sponsorfeatured" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.color', '#fc0505'); ?>;'>
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/star-img.png', '') ?>
		      <?php echo $this->translate('SPONSORED'); ?>
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/star-img.png', '') ?>
		    </div>
		  <?php } ?>
		<?php endif; ?>
		<div class='sitegroup_photo <?php if ($this->can_edit) : ?>sitegroup_photo_edit_wrapper<?php endif; ?>'>
		  <?php if (!empty($this->can_edit)) : ?>
		    <a href="<?php echo $this->url(array('action' => 'profile-picture', 'group_id' => $this->sitegroup->group_id), 'sitegroup_dashboard', true) ?>" class="sitegroup_photo_edit">  	  
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/edit_pencil.png', '') ?>
		      <?php echo $this->translate('Change Picture'); ?>
		    </a>
		  <?php endif; ?>
		
		  <?php echo $this->itemPhoto($this->sitegroup, 'thumb.profile', '', array('align' => 'left')); ?>
		</div>
		<?php if (!empty($this->sitegroup->featured)): ?>
		  <?php $feature = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.feature.image', 1);
		  if (!empty($feature)) { ?>
		    <div class="sitegroup_profile_sponsorfeatured"  style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.featured.color', '#0cf523'); ?>;'>
		      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/star-img.png', '') ?>
		      <?php echo $this->translate('FEATURED'); ?>
		    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/star-img.png', '') ?>
		    </div>
		  <?php } ?>
		<?php endif; ?>
		<?php if (!empty($this->viewer_id)): ?>
			<div class="generic_layout_container layout_sitegroup_options_sitegroup">
				<?php if (!empty($this->member_approval)) : ?>
					<br /><?php echo $this->htmlLink(array('route' => 'sitegroup_profilegroupmember', 'action' => 'join', 'group_id' => $this->sitegroup->group_id), $this->translate('Join Group'), array(  ' class' => 'buttonlink smoothbox icon_sitegroup_join')); ?>
					<?php else : ?>
						<?php if (empty($this->hasMembers)) : ?>
							<br /><?php echo $this->htmlLink(array('route' => 'sitegroup_profilegroupmember', 'action' => 'request', 'group_id' => $this->sitegroup->group_id), $this->translate('Request Member for Group'), array(  ' class' => 'buttonlink smoothbox icon_sitegroup_join')); ?>
						<?php else : ?>
							<br /><?php echo $this->htmlLink(array('route' => 'sitegroup_profilegroupmember', 'action' => 'cancel', 'group_id' => $this->sitegroup->group_id), $this->translate('Cancel Member Request for Group'), array(  ' class' => 'buttonlink smoothbox icon_sitegroup_join')); ?>
						<?php endif ;?>
				<?php endif;  ?>
			</div>
		<?php endif;  ?>
	</div>
	
	<div class="layout_middle">
		<div id='profile_status'>
		  <h2>
		    <?php echo $this->sitegroup->getTitle() ?>
		  </h2>
		</div>
		
		<div class="generic_layout_container layout_core_container_tabs">
			<div class='tabs_alt tabs_parent'>
				<ul class="main_tabs">
					<li id="info" class="active">
						<a href="javascript:void(0);" onclick="showtab('info')"><?php echo $this->translate('Info') ?></a>
					</li>
					<?php if ($this->sitegroup->overview && !empty($this->can_edit_overview)) :?>
					<li id="overview" class="active">
						<a href="javascript:void(0);" onclick="showtab('overview')"><?php echo $this->translate('Overview') ?></a>
					</li>
					<?php endif; ?>
				</ul>
			</div>
			<div id="infocontentshow">
				 <?php echo $this->content()->renderWidget("sitegroup.info-sitegroup", array()); ?>
			</div>
			<?php if ($this->sitegroup->overview && !empty($this->can_edit_overview)) :?>
				<div id="overviewcontentshow">
					<?php echo $this->content()->renderWidget("sitegroup.overview-sitegroup", array()); ?>
				</div>
			<?php endif; ?>
	  </div>
	</div>

</div>
<?php else : ?>
<?php echo $this->sitemain; ?>
<?php endif; ?>
<?php if ($this->sitegroup->overview) : ?>
<script type="text/javascript">

	sm4.core.runonce.add(function() {
		$('overviewcontentshow').style.display = 'none';
		$('overview').removeClass('active');
	});
	</script>
	<?php endif; ?>
	<script type="text/javascript">
	function showtab(showtabcontent) {
		if (showtabcontent == 'info') {
			$('info').addClass('active');
			$('overview').removeClass('active');
			$('infocontentshow').style.display = 'block';
			$('overviewcontentshow').style.display = 'none';
		} else {	
			$('overview').addClass('active');
			$('info').removeClass('active');
			$('global_content').getElement('.layout_sitegroup_overview_sitegroup').style.display = 'block';
			$('overviewcontentshow').style.display = 'block';
			$('infocontentshow').style.display = 'none';
		}
	}
</script>
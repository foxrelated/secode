<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: featuredowners.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href;
		Smoothbox.open(Obj_Url);
	}
</script>

<?php if (empty($this->is_ajax)) : ?>
<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
	<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>
<div class="layout_middle">
	<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/edit_tabs.tpl'; ?>
	<div class="sitegroup_edit_content">
		<div class="sitegroup_edit_header">
			<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->sitegroup->group_id, $this->sitegroup->owner_id, $this->sitegroup->getSlug()),$this->translate('VIEW_GROUP')) ?>
			<h3><?php echo $this->translate('Dashboard: ').$this->sitegroup->title; ?></h3>
		</div>
 <div id="show_tab_content">
<?php endif; ?>

		<div class="global_form">
			<div>
				<div>
					<h3><?php echo $this->translate('Featured Group Admins'); ?></h3>
					<p class="form-description">
						<?php echo $this->translate('Below you can see all the featured admins of this group. Featured admins are shown on the group profile.') ?>
					</p>
					<?php $featuredhistories_array = $this->featuredhistories->toarray();
						if(!empty($featuredhistories_array)) :
							$count = count($featuredhistories_array);
							echo '<div class="sitegroup_featuredadmins_count">' . $this->translate(array('%s featured group admin', '%s featured group admins', $count), $this->locale()->toNumber($count)); ?></div>

						<div class='sitegroup_featuredadmins_list'>
							<?php foreach ($this->featuredhistories as $item):?>
								<div class='sitegroup_featuredadmins_thumb' id='<?php echo $item->manageadmin_id ?>_groupthumb'>
									<?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
									<?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="sitegroup_featuredadmins_add">
							<?php echo $this->htmlLink(array('route' => 'sitegroup_manageadmins', 'action' => 'list','group_id' => $this->sitegroup->group_id), $this->translate('Manage Featured Group Admins'), array('onclick' => 'owner(this);return false',)) ?>
						</div>
					<?php else : ?>
						<div class="tip">
							<span>
								<?php echo $this->translate("No featured admins have been added for this group yet."); ?>
							</span>
						</div>
						<div class="sitegroup_featuredadmins_add">
							<?php echo $this->htmlLink(array('route' => 'sitegroup_manageadmins', 'action' => 'list', 'group_id' => $this->sitegroup->group_id), $this->translate('Add Featured Group Admins'), array('onclick' => 'owner(this);return false',)) ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		
		<?php if (empty($this->is_ajax)) : ?>
		</div>
    </div>
  </div>
    </div>
  </div>
<?php endif; ?>
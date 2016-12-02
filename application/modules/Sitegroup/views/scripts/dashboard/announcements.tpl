<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: foursquare.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript" >
  var submitformajax = 1;
 // var manage_admin_formsubmit = 1;
</script>
<script type="text/javascript">
  var viewer_id = '<?php echo  $this->viewer_id; ?>';
  var url = '<?php  echo $this->url(array(), 'sitegroup_general', true) ?>';
  
  var manageinfo = function(announcement_id, url,group_id) {
		var childnode =  $(announcement_id + '_group_main');
		childnode.destroy();
		en4.core.request.send(new Request.JSON({
			url : url,
			data : {
				announcement_id : announcement_id,
				group_id : group_id
			},
			onSuccess : function(responseJSON) {
			}
		}))
	};
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
		<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl.'application/modules/Sitegroup/externals/scripts/core.js'); ?>
		<div class="sitegroup_form">
			<div>
				<div>
					<div class="sitegroup_manage_announcements">
						<h3> <?php echo $this->translate('Manage Announcements'); ?> </h3>
						<p class="form-description"><?php echo $this->translate("Below, you can manage the announcements for your group. Announcements are shown on the group profile.") ?></p>
						<br />
						<div class="">
							<a href='<?php echo $this->url(array('action' => 'create-announcement', 'group_id' => $this->group_id ),'sitegroupmember_approve', true) ?>' class="buttonlink seaocore_icon_add"><?php echo $this->translate("Post New Announcement");?></a>
						</div>
						<?php if (count($this->announcements) > 0) : ?>
						<?php foreach ($this->announcements as $item): ?>
							<div id='<?php echo $item->announcement_id ?>_group_main'  class='sitegroup_manage_announcements_list'>
								<div id='<?php echo $item->announcement_id ?>_group'>
                  	<div class="sitegroup_manage_announcements_title">
                    <div class="sitegroup_manage_announcements_option">
                    
											<?php if($item->status == 1):?>
												<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_approved1.gif', '', array('title'=> $this->translate('Enabled'))); ?>
											<?php else: ?>
												<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_approved0.gif', '', array('title'=> $this->translate('Disabled'))); ?>
											<?php endif; ?>
                   
                     <?php $url = $this->url(array('action' => 'delete-announcement'),'sitegroupmember_approve', true); ?>
                      <a href='<?php echo $this->url(array('action' => 'edit-announcement', 'announcement_id' => $item->announcement_id , 'group_id' => $this->group_id ),'sitegroupmember_approve', true) ?>' class="buttonlink seaocore_icon_edit"><?php echo $this->translate("Edit")." ";?></a>
                      <?php //if ( $this->owner_id != $item->user_id ) :?>
                        <a href="javascript:void(0);" onclick="manageinfo('<?php echo $item->announcement_id ?>', '<?php echo $url;?>', '<?php echo $this->group_id ?>')"; class="buttonlink seaocore_icon_delete" ><?php echo $this->translate('Remove');?></a>
                      <?php //endif;?>
                    </div>
										<span><?php echo $item->title; ?></span>
                 	</div> 
                 	<div class="sitegroup_manage_announcements_dates seaocore_txt_light">
										<b><?php echo $this->translate("Start Date: ")?></b> <?php echo $this->translate( gmdate('M d, Y', strtotime($item->startdate))); ?>&nbsp;&nbsp;&nbsp;
										<b><?php echo $this->translate("End Date: ") ?></b><?php echo $this->translate( gmdate('M d, Y', strtotime($item->expirydate))); ?>
                 	</div>
                 		<div class="sitegroup_manage_announcements_body show_content_body"> 
										<?php echo $item->body ?>
                 	</div> 
								</div>
							</div>
						<?php endforeach; ?>
						<?php else: ?>
            	<br />
							<div class="tip">
							<span><?php echo $this->translate('No announcements have been posted for this group yet.'); ?></span>
							</div>
						<?php endif; ?>
					</div>
					<?php  $item = count($this->paginator) ?>
					<input type="hidden" id='count_div' value='<?php echo $item ?>' />
				</div>
			</div>
		</div>
		<br />	
		<div id="show_tab_content_child">
		</div>
<?php if (empty($this->is_ajax)) : ?>
		  </div>
	  </div>
  </div>
</div>
  </div>
<?php endif; ?>
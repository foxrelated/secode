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
<?php 
  include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/Adintegration.tpl';
?>

<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/styles/style_sitegroup_profile.css')
?>

<?php 
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/hideWidgets.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/hideTabs.js');
?>
<?php //if (empty($this->isajax)) : ?>
	<div id="id_<?php echo $this->content_id; ?>">
<?php //endif;?>

<?php //if (!empty($this->show_content)) : ?>
	<?php if($this->showtoptitle == 1):?>
		<div class="layout_simple_head" id="layout_overview">
			<?php echo $this->translate($this->sitegroup->getTitle(). "'s ");?><?php echo $this->translate("Overview");?>
		</div>
	<?php endif;?>	
	
		<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adoverviewwidget', 3) && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)) : ?>
			<div class="layout_right" id="communityad_overview">
				<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adoverviewwidget', 3),"loaded_by_ajax"=>0,'widgetId'=>'group_overview'))?>
			</div>
			<div class="layout_middle">
		<?php endif;?>
		<?php if ($this->can_edit && $this->can_edit_overview):?>
			<?php if(!empty($this->sitegroup->overview)):?>
				<div class="seaocore_add">
					<a href='<?php echo $this->url(array('group_id' => $this->sitegroup->group_id, 'action' => 'overview'), 'sitegroup_dashboard', true) ?>'  class="icon_sitegroups_overview buttonlink"><?php echo $this->translate('Edit Overview'); ?></a>
				</div>
			<?php endif;?>
		<?php endif;?>
	<div>
	<?php if(!empty($this->sitegroup->overview)):?>
		<?php echo $this->sitegroup->overview ?>
	<?php else:?>
		<div class="tip">
			<span>
				<?php   echo $this->translate("No overview has been composed for this Group yet.");?>
				<?php if($this->can_edit && $this->can_edit_overview):?>
					<?php   echo $this->translate("Click ").$this->htmlLink(
										array('route' => 'sitegroup_dashboard', 'action' => 'overview','group_id' => $this->sitegroup->group_id),
										$this->translate('here')

									).  $this->translate(" to compose it.");?>
				<?php endif; ?>
			</span>
		</div>
	<?php endif;?>
</div>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adoverviewwidget', 3) && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)) : ?>
</div>
<?php endif;?>
<?php //endif;?>
<?php //if (empty($this->isajax)) : ?>
	</div>
<?php //endif;?>
<script type="text/javascript">
    var group_communityad_integration = '<?php echo $group_communityad_integration; ?>';
    var show_widgets = '<?php echo $this->widgets ?>';
    var adwithoutpackage = '<?php echo Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup) ?>';
		var group_communityads;
    var contentinformtion;
    var group_showtitle;
    var overview_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adoverviewwidget', 3);?>';

    $$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function(event) {
    	$('global_content').getElement('.layout_sitegroup_overview_sitegroup').style.display = 'block';
     
   	  if(group_showtitle != 0) {
   	  	if($('profile_status') && show_widgets == 1) {
	  	    $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitegroup->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Overview');?></h2>";	
   	  	}	
   	  }
      hideWidgetsForModule('sitegroupoverview');
      $('id_' + <?php echo $this->content_id ?>).style.display = "block";
	    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
	      $$('.'+ prev_tab_class).setStyle('display', 'none');      
	    }

	    prev_tab_id = '<?php echo $this->content_id; ?>';	
	  	prev_tab_class = 'layout_sitegroup_overview_sitegroup';

	    if(group_showtitle == 1 && group_communityads == 1 && overview_ads_display != 0 && group_communityad_integration != 0 && adwithoutpackage != 0) {
				setLeftLayoutForGroup();    	
	    } else if(group_showtitle == 0 && group_communityads == 1 && overview_ads_display != 0 && group_communityad_integration != 0 && adwithoutpackage != 0) {
				setLeftLayoutForGroup(1);
	    }

	    if(group_communityads == 1 && overview_ads_display == 0 ) {
				setLeftLayoutForGroup();   	   	
	    }
	     if($(event.target).get('tag') !='div' && ($(event.target).getParent('.layout_sitegroup_overview_sitegroup')==null)){
         scrollToTopForGroup($("global_content").getElement(".layout_sitegroup_overview_sitegroup"));
       }	        
   });
 
</script>
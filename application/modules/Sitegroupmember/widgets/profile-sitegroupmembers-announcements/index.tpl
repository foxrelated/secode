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
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl.'application/modules/Sitegroup/externals/scripts/core.js'); ?>
<?php if($this->loaded_by_ajax):?>
  <script type="text/javascript">
    var params = {
      requestParams :<?php echo json_encode($this->params) ?>,
      responseContainer :$$('.layout_sitegroupmember_profile_sitegroupmembers_announcements'),
      requestUrl: en4.core.baseUrl+'<?php echo ($this->user_layout) ? 'sitegroup/widget' :'widget';?>'
    }
    en4.sitegroup.ajaxTab.attachEvent('<?php echo $this->identity ?>',params);
  </script>
<?php endif;?>
 
+<?php if($this->showContent): ?> 
<div id='id_<?php echo $this->content_id; ?>'>
	<?php if (count($this->announcements) > 0): ?>
		<ul class="sitegroup_profile_list sitegroup_profile_announcements sitegroup_list_highlight" >
			<?php foreach ($this->announcements as $item): ?>
				<li>
					<b><?php echo $item->title; ?></b>
					<?php if (!empty($item->body)): ?>
						<div class="sitegroup_profile_list_info_des show_content_body">
							<?php echo $item->body; ?>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<div class="tip">
			<span>
				<?php echo $this->translate('No announcements have been created yet.'); ?>
			</span>
		</div>
	<?php endif; ?>
</div>


<script type="text/javascript">

  $$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function(event) 
  {	
  	$('id_' + <?php echo $this->content_id ?>).style.display = "block";
    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {    	
      $$('.'+ prev_tab_class).setStyle('display', 'none');
    }

    prev_tab_id = '<?php echo $this->content_id; ?>';	
    prev_tab_class = 'layout_sitegroupmember_profile_sitegroupmembers_announcements';
		if($(event.target).get('tag') !='div' && ($(event.target).getParent('.layout_sitegroupmember_profile_sitegroupmembers_announcements')==null)){
      scrollToTopForGroup($("global_content").getElement(".layout_sitegroupmember_profile_sitegroupmembers_announcements"));
    }	        
  });
</script>
<?php endif; ?>
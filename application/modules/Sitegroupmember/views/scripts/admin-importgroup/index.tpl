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
<?php if( !empty($this->isImportData) ): ?>
<script type="text/javascript">
	var assigned_previous_id = '<?php echo $this->assigned_previous_id; ?>';
	function startImport() 
	{
    var import_confirmation =  confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to start importing Groups ?")) ?>');
    
    var activity_group = 0;
    if($('activity_group').checked == true) {
      activity_group = 1;
    }    

		if(import_confirmation) {

			Smoothbox.open("<div><center><b>" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing Groups...")) ?>' + "</b><br /><img src='application/modules/Sitegroup/externals/images/loader.gif' alt='' /></center></div>");

			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl+'admin/sitegroupmember/importgroup',
				method: 'get',
				data : {
					'start_import' : 1,
					'assigned_previous_id' : assigned_previous_id,
					'select_package_id' : $('select_package_id').value,
          'activity_group' : activity_group,
					'format' : 'json',
				},
				onSuccess : function(responseJSON) {
					
					$('import_button').style.display = 'none';
					$('importlisting_elements').style.display = 'none';
					if($('packageselect_id'))
					$('packageselect_id').style.display = 'none';
					
					if (responseJSON.assigned_previous_id < responseJSON.last_listing_id) {
						$('import_again_button').style.display = 'block';
						assigned_previous_id = responseJSON.assigned_previous_id;
						
						$('unsuccess_message').innerHTML = "<span style='background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/cross.png);'>"+'<?php echo $this->string()->escapeJavascript($this->translate("Sorry for this inconvenience !!")) ?>' + "<br />"+'<?php echo $this->string()->escapeJavascript($this->translate("Importing is interrupted due to some reason. Please click on 'Import Again' button to start the importing from the same point again.")) ?>'+"</span><br />";
					}
					else {
						$('import_again_button').style.display = 'none';
						$('unsuccess_message').style.display = 'none';
						$('success_message').innerHTML = "<span style='background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/notice.png);'>"+'<?php echo $this->string()->escapeJavascript($this->translate("Importing is done succesfully.")) ?>'+"</span><br />";
					}
					Smoothbox.close();
				}
			}))
		}
	}
</script>
<?php endif; ?>

<h2 class="fleft"><?php echo $this->translate('Groups / Communities Plugin'); ?></h2>

<?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/manageExtensions.tpl'; ?>

<?php if (count($this->navigationGroup)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigationGroup)->render()
  ?>
  </div>
<?php endif; ?>

<div>
  <?php echo $this->htmlLink(array('module' => 'sitegroup', 'controller' => 'importlisting', 'action' => 'index', 'route' => 'admin_default'), $this->translate('Back to Import from CSV file Section'), array('class' => 'icon_sitegroup_admin_back buttonlink')) ?>
</div><br/>


<?php if($this->first_listing_id): ?>
	<div class="importlisting_form">
		<div>
			<h3><?php echo $this->translate('Import official SocialEngine Groups into Groups');?></h3>
			<p>
			<?php echo $this->translate("This Importing tool is designed to migrate content directly from official SocialEngine Group to a Group. Using this, you can convert all the groups on your site into Groups. Please note that we try to import all the data corresponding to a Group but there is a possibility of some data losses too. Before starting to use this tool, please read the following points carefully.<br /><br />1. Below are the conditions which are required to be true for this import. Please check the points carefully and if some condition is yet to be fulfilled then do that first and then start importing your Groups.<br /><br />2. Select a Package into which you want to import your Groups. (Note: If you select a paid package below, then the imported Groups will automatically be  approved and will display on your site without making payment.)<br /><br />3. Group category is a required field. Thus, un-categorized Groups will be imported into ‘Others’ category on your site. (If there is no ‘Others’ 
category 
on your site, then this category will be automatically created during the Import process.)<br /><br />4. Once the import gets started, it is recommended not to close the lightbox, otherwise it will not be completed successfully and some data losses may occur.<br /><br />5. To import Groups, you also require following \"<a href='http://www.socialengineaddons.com/catalog/directory-groups-extensions' target='_blank'>extensions for Groups / Communities Plugin</a>\" to be installed and enabled on your site:<br />&nbsp;&nbsp;&nbsp;&nbsp;a. <a href='http://www.socialengineaddons.com/groupextensions/socialengine-directory-groups-discussions' target='_blank'>Groups / Communities - Discussion Extension</a><br />&nbsp;&nbsp;&nbsp;&nbsp;b. <a href='http://www.socialengineaddons.com/groupextensions/socialengine-directory-groups-events' 
target='_blank'>Groups / Communities - Events Extension</a><br />&nbsp;&nbsp;&nbsp;&nbsp;c. <a href='http://www.socialengineaddons.com/groupextensions/socialengine-directory-groups-polls' target='_blank'>Groups / Communities - Polls Extension</a><br />&nbsp;&nbsp;&nbsp;&nbsp;d. <a href='http://www.socialengineaddons.com/groupextensions/socialengine-directory-groups-documents' target='_blank'>Groups / Communities - Documents Extension</a><br /><br /><b>Note</b>: If you have any other Group installed on your site and want to import that, then please file a support ticket by logging into your SocialEngineAddOns client area.”");?>
			</p>
	
			<br />
			<div id="success_message" class='success-message'></div>
			<div id="unsuccess_message" class="error-message"></div>

			<div class="importlisting_elements" id="importlisting_elements" >
				<?php 
					$is_error = 0;
			
					if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('group') || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advgroup')) {
						$error_msg1 = $this->translate("Group Plugin is installed and enabled!");
						echo "<span class='green'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/tick.png'/><b>$error_msg1</b></span>";
					}
					else {
						$is_error = 1;
						$error_msg1 = $this->translate("Group Plugin is not installed or disabled!");
						echo "<span class='red'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/cross.png'/><b>$error_msg1</b></span>";
					}
			
					if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.isActivate')) {
						$error_msg1 = $this->translate("Groups/Communities Plugin is installed and enabled!");
						echo "<span class='green'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/tick.png'/><b>$error_msg1</b></span>";
					}
					else {
						$is_error = 1;
						$error_msg1 = $this->translate("Groups/Communities Plugin is not installed or disabled!");
						echo "<span class='red'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/cross.png'/><b>$error_msg1</b></span>";
					}
			
					//if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.isActivate')) {
						//$error_msg1 = $this->translate("Groups/Communities - Photo Albums Extension Plugin is installed and enabled!");
						//echo "<span class='green'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/tick.png'/><b>$error_msg1</b></span>";
					//}
					//else {
					//	$is_error = 1;
					//	$error_msg1 = $this->translate("Groups/Communities - Photo Albums Extension Plugin is not installed or disabled!");
					//	echo "<span class='red'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/cross.png'/><b>$error_msg1</b></span>";
					//}
			
					if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupdiscussion.isActivate')) {
						$error_msg1 = $this->translate("Groups/Communities - Discussion Extension Plugin is installed and enabled!");
						echo "<span class='green'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/tick.png'/><b>$error_msg1</b></span>";
					}
					else {
						$is_error = 1;
						$error_msg1 = $this->translate("Groups/Communities - Discussion Extension Plugin is not installed or disabled!");
						echo "<span class='red'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/cross.png'/><b>$error_msg1</b></span>";
					}
			
					if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupevent.isActivate')) {
						$error_msg1 = $this->translate("Groups/Communities - Events Extension Plugin is installed and enabled!");
						echo "<span class='green'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/tick.png'/><b>$error_msg1</b></span>";
					}
					else {
						$is_error = 1;
						$error_msg1 = $this->translate("Groups/Communities - Events Extension Plugin is not installed or disabled!");
						echo "<span class='red'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/cross.png'/><b>$error_msg1</b></span>";
					}
					
					if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('grouppoll') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegrouppoll.isActivate')) {
						$error_msg1 = $this->translate("Groups/Communities - Polls Extension Plugin is installed and enabled!");
						echo "<span class='green'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/tick.png'/><b>$error_msg1</b></span>";
					}
					elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('grouppoll')) {
						$is_error = 1;
						$error_msg1 = $this->translate("Groups/Communities - Polls Extension Plugin is not installed or disabled!");
						echo "<span class='red'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/cross.png'/><b>$error_msg1</b></span>";
						?>
	          <?php
					}
	
					if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('groupdocument') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupdocument.isActivate')) {
						$error_msg1 = $this->translate("Groups/Communities - Documents Extension Plugin is installed and enabled!");
						echo "<span class='green'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/tick.png'/><b>$error_msg1</b></span>";
					}
					elseif(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('groupdocument')) {
						$is_error = 1;
						$error_msg1 = $this->translate("Groups/Communities - Documents Extension Plugin is not installed or disabled!");
						echo "<span class='red'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/cross.png'/><b>$error_msg1</b></span>";
						?>
             <?php 
					}
				?>
        
      <div id="activity_group-wrapper" class="form-wrapper">
        <div class="form-label" id="activity_group-label">&nbsp;</div>
        <div id="activity_group-element" class="form-element">
          <input type="hidden" name="activity_group" value="" /><input type="checkbox" name="activity_group" id="activity_group"/>
          <label for="activity_group" class="optional"><?php echo $this->translate("Import activity feeds also."); ?></label>
        </div>
      </div><br/>
			</div>
		  <?php
				$data = array();
				foreach($this->packages as $category ) {
					$data[$category['package_id']] = $category['title'];
				}
		  ?>
			<?php if(!empty($data)): ?>
				<div id="packageselect_id" class="clr">
					<label>
						<b><?php echo $this->translate("Packages:") ?></b>
					</label>
					<select onchange="getPackageId($(this).value)" class="sitereview_cat_select" name="package_id">     
					<option value="0"><?php echo $this->translate("");?></option>
						<?php foreach ($this->packages as $listingType): ?>
							<option value="<?php echo $listingType->package_id ?>"><?php echo $this->translate($listingType->title);?>
							</option>
						<?php endforeach; ?>
					</select>
				</div><br />
			<?php endif; ?>
			<input type="hidden" id="select_package_id" name="select_package_id" ></input>

			<div id="import_button" class="import_button" style="display:none;">
				<?php if($is_error == 0): ?>
					<button type="button" id="continue" name="continue" onclick='startImport();'>
						<?php echo $this->translate('Start Import');?>
					</button>
				<?php endif;?>
			</div>
	
			<div id="import_again_button" style="display:none;" class="import_button">
				<?php if($is_error == 0): ?>
					<button type="button" id="continue" name="continue" onclick='startImport();'>
						<?php echo $this->translate('Import Again');?>
					</button>
				<?php endif;?>
			</div>
		</div>
	</div>
<?php else : ?>
	<div class="importlisting_form">
		<div>
			<h3><?php echo $this->translate('Import official SocialEngine Groups into Groups');?></h3>
			<p>
			<?php echo $this->translate("This Importing tool is designed to migrate content directly from official SocialEngine Group to a Group. Using this, you can convert all the groups on your site into Groups. Please note that we try to import all the data corresponding to a Group but there is a possibility of some data losses too. Before starting to use this tool, please read the following points carefully.<br /><br />1. Below are the conditions which are required to be true for this import. Please check the points carefully and if some condition is yet to be fulfilled then do that first and then start importing your Groups.<br /><br />2. Select a Package into which you want to import your Groups. (Note: If you select a paid package below, then the imported Groups will automatically be  approved and will display on your site without making payment.)<br /><br />3. Group category is a required field. Thus, un-categorized Groups will be imported into ‘Others’ category on your site. (If there is no ‘Others’ 
category on your site, then this category will be automatically created during the Import process.)<br /><br />4. Once the import gets started, it is recommended not to close the lightbox, otherwise it will not be completed successfully and some data losses may occur.<br /><br />5. To import Groups, you also require following \"<a href='http://www.socialengineaddons.com/catalog/directory-groups-extensions' target='_blank'>extensions for Groups / Communities Plugin</a>\" to be installed and enabled on your site:<br />&nbsp;&nbsp;&nbsp;&nbsp;a. <a href='http://www.socialengineaddons.com/groupextensions/socialengine-directory-groups-discussions' target='_blank'>Groups / Communities - Discussion Extension</a><br />&nbsp;&nbsp;&nbsp;&nbsp;b. <a href='http://www.socialengineaddons.com/groupextensions/socialengine-directory-groups-events' target='_blank'>Groups / Communities - Events Extension</a><br />&nbsp;&nbsp;&nbsp;&nbsp;c. <a href='http://www.socialengineaddons.com/groupextensions/socialengine-directory-
groups-polls ' target='_blank'>Groups / Communities - Polls Extension</a><br />&nbsp;&nbsp;&nbsp;&nbsp;d. <a href='http://www.socialengineaddons.com/groupextensions/socialengine-directory-groups-documents' target='_blank'>Groups / Communities - Documents Extension</a><br /><br /><b>Note</b>: If you have any other Group installed on your site and want to import that, then please file a support ticket by logging into your SocialEngineAddOns client area.”");?>
			</p>
	
			<br />
			<div class="tip">
				<span>
					<?php  echo $this->translate("Currently there are no groups on your site to import.") ?><br />
					<?php if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('group')): ?>
					<?php echo "Group Plugin is not installed or disabled!"; ?><?php endif; ?>
				</span>
			</div>
		</div>
	</div>
<?php endif;?>
<script>
  function getPackageId(package_id) {
  if (package_id == 0) {
   $('import_button').style.display = "none";
  } else {
  $('import_button').style.display = "block";
  }
  $('select_package_id').value = package_id;
  }
</script>

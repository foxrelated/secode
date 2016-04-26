<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: lndex.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
function multiDelete() {
  return confirm("<?php echo $this->translate("Are you sure you want to remove the selected modules as advertisable? Users will not be able to directly advertise their content from these modules after being removed.") ?>");
}

function selectAll() {
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>


<h2><?php echo $this->translate('Advanced Facebook Integration / Likes, Social Plugins and Open Graph') ?></h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>


<h3 style="margin-bottom:6px;"><?php echo $this->translate("Manage Modules for Advanced Facebook Integration"); ?></h3>
<?php
	// Show Success message.
	if(isset($this->success_message))	{
		echo '<ul class="form-notices" style="margin:0px;"><li style="float:left;">' . $this->translate('Successfully create') . ' ' . ucfirst($this->success_message) . ' ' . $this->translate('Communityad.') . '</li></ul>';
	}
?>
  <p><?php echo $this->translate('Here, you can manage and configure ANY content module for integration with Facebook\'s Social Plugins for Likes and Commenting: "Like Button" and "Comments". Such content modules should be using SocialEngine\'s core Likes and Comments systems. Here, you can also add a new module to enable Likes and Comments on its content using Facebook\'s Social Plugins.<br />You can also configure Open Graph protocol implementation settings for any content module on your site which uses SocialEngine\'s in-built Likes and Comments systems.<br />Thus, this interface enables you to extend this plugin to ANY CONTENT MODULE of your site which extends SE\'s Likes and Comments systems. For more tips on this section, visit the FAQ page.<br />If you have the <a href="http://www.socialengineaddons.com/socialengine-facebook-feed-stories-publisher" target="_blanck">Facebook Feed Stories Publisher Plugin</a>, then this interface will also enable you to choose the modules for which users will be able to 
publish Activity Feed Stories on Facebook for their actions performed on the respective modules of your community (Example: creating a new Album, a new Page, etc.). You can choose ANY CONTENT MODULE for this.' ); ?></p>
  <br style="clear:both;" />

  <?php
	// Show link for "Create Featured Content".
		echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'facebookse', 'controller' => 'manage', 'action' => 'module-create'), $this->translate("Add New Module for Advanced Facebook Integration"), array('class'=>'buttonlink seaocore_icon_add'));
	?>
	<br /><br />

<?php
	if( count($this->paginator) ):
?>
  <div class="admin_table_form">
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete();">
  <table class='admin_table seaocore_admin_table' width= "100%" >
    <thead>
      <tr>
<!--        <th class='admin_table_short'>
        	<input onclick='selectAll();' type='checkbox' class='checkbox' />
        </th>-->
        <th class='admin_table_short' align="center">
					<?php	echo $this->translate("ID"); ?>
        </th>
         <th align="left">
        	<?php echo $this->translate("Module Name"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Module Title"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Resource Type"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Title Field"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Body Field"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Owner Field"); ?>
        </th>
        <th class="center">
        	<?php echo $this->translate("Adv. FB Integ."); ?>
        </th>
      <?php   
      $enable_fbfeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
		
		  if ($enable_fbfeedmodule) { ?>
        <th class="center">
        	<?php echo $this->translate("Feed Publishing"); ?>
        </th>
    <?php } ?>    
        <th align="left">
        	<?php echo $this->translate("Options"); ?>
        </th>
      </tr>
    </thead>
    <tbody>
			<?php $is_module_flag = 0; ?>
    	<?php foreach ($this->paginator as $item):?>
				<?php $module_name = $item->module; $modules_array = $this->enabled_modules_array; 
				      $modules_array[] = 'home';
				?>
				<?php if( in_array( $module_name, $modules_array )) { ?>
					<tr>
						<!--<td><input type='checkbox' name='delete_<?php //echo $item->mixsetting_id;?>' value='<?php //echo $item->mixsetting_id ?>' class='checkbox' value="<?php //echo $item->mixsetting_id ?>" <?php //if( !empty($item->is_delete) ) { echo 'DISABLED'; } ?>/></td>-->
						<td class="admin_table_centered"><?php echo $item->mixsetting_id; ?></td>
						<td ><?php if( !empty($item->module) ){ echo $item->module; }else { echo '-'; } ?></td>
            <td ><?php echo !empty($item->module_name) ? $item->module_name : $item->module ?></td>
						<td ><?php if( !empty($item->resource_type) ){ echo $item->resource_type; }else { echo '-'; } ?></td>
						<td ><?php if( !empty($item->module_title) ){ echo $item->module_title; }else { echo '-'; } ?></td>
						<td ><?php if( !empty($item->module_description) ){ echo $item->module_description; }else { echo '-'; } ?></td>
						<td ><?php if( !empty($item->owner_field) ){ echo $item->owner_field; }else { echo '-'; } ?></td>
						<td class="admin_table_centered"><?php echo ( $item->module_enable ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'facebookse', 'controller' => 'manage', 'action' => 'enabled-content-tab', 'mixsetting_id' => $item->mixsetting_id. '_advfb'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => $this->translate('Disable Module for Advanced Facebook Integration'))), array())  : $this->htmlLink(array('route' => 'admin_default', 'module' => 'facebookse', 'controller' => 'manage', 'action' => 'enabled-content-tab', 'mixsetting_id' => $item->mixsetting_id. '_advfb'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Enable Module for Advanced Facebook Integration')))) ) ?></td >
						<?php if ($enable_fbfeedmodule && !empty($item->activityfeed_type)) { ?>
						  <td class="admin_table_centered"><?php echo ( $item->streampublishenable ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'facebookse', 'controller' => 'manage', 'action' => 'enabled-content-tab', 'mixsetting_id' => $item->mixsetting_id . '_fbfeed'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => $this->translate('Disable Module for Feed Publishing'))), array())  : $this->htmlLink(array('route' => 'admin_default', 'module' => 'facebookse', 'controller' => 'manage', 'action' => 'enabled-content-tab', 'mixsetting_id' => $item->mixsetting_id. '_fbfeed'), $this->htmlImage($this->layout()->staticBaseUrl .'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Enable Module for Feed Publishing')))) ) ?></td >
						
						<?php } else if ($enable_fbfeedmodule){ ?>
					           <td class="admin_table_centered">-</td >
						<?php } ?>
						
						<!--<td ><?php //if( !empty($item->resource_id) ){ echo $item->resource_id; }else { echo '-'; } ?></td>-->
						<td><?php	echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'facebookse', 'controller' => 'manage', 'action' => 'module-edit', 'mixsetting_id' => $item->mixsetting_id), $this->translate("edit")) ;	?>
						<?php if(empty($item->default)):?>
							| <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'facebookse', 'controller' => 'manage', 'action' => 'delete-module', 'resource_type' => $item->resource_type), $this->translate("delete"), array('class' => 'smoothbox')) ;	 ?>
							
							<?php endif; ?>
						</td>
					</tr>
				<?php $is_module_flag = 1; } ?>
      <?php  endforeach; ?>
    </tbody>
  </table>
  <br />
	<?php //if( !empty($is_module_flag) ) { ?>
<!--  <div class='buttons'>
  	<button type='submit'><?php //echo $this->translate("Delete Selected") ?></button>
  </div>-->
	<?php //} ?>
</form>
  </div>
<br />
<div>
	<?php echo $this->paginationControl($this->paginator); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no modules available.") ?>
    </span>
  </div>
<?php endif; ?>

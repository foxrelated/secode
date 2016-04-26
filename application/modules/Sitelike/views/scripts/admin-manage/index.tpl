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

<h2>
  <?php echo $this->translate('Likes Plugin & Widgets') ?>
</h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<h3 style="margin-bottom:6px;"><?php echo $this->translate("Manage Modules for Likes"); ?></h3>
<?php
	// Show Success message.
	if(isset($this->success_message))	{
		echo '<ul class="form-notices" style="margin:0px;"><li style="float:left;">' . $this->translate('Successfully create') . ' ' . ucfirst($this->success_message) . ' ' . $this->translate('Communityad.') . '</li></ul>';
	}
?>
  <p><?php echo $this->translate("Here, you can manage modules which are using SocialEngine's in-built Likes system to enable users to Like their content. From this page, you can choose whether content from such a module should be available in the various widgets and pages of this plugin (like on Liked Items, Friends' Likes, Most Liked Items widget, etc). Here, you can also add a new module to enable content from it to be displayed on such pages and widgets. Such a module should be using SocialEngine's in-built Likes system. Thus, this interface enables you to extend this plugin to ANY CONTENT MODULE of your site that extends SE's Likes system. For more tips on this section, visit the FAQ page."); ?></p>
  <br style="clear:both;" />

  <?php
	// Show link for "Create Featured Content".
		echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitelike', 'controller' => 'manage', 'action' => 'module-create'), $this->translate("Add New Module for Likes"), array('class'=>'buttonlink seaocore_icon_add'));
	?>
	<br /><br />

<?php
	if( count($this->paginator) ):
?>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete();">
  <table class='admin_table' width= "100%" >
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
        	<?php echo $this->translate("Plural Title"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Singular Title"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Resource Type"); ?>
        </th>
        <th class="center">
        	<?php echo $this->translate("Enabled"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Options"); ?>
        </th>
      </tr>
    </thead>
    <tbody>
			<?php $is_module_flag = 0; ?>
    	<?php foreach ($this->paginator as $item):?>
				<?php $module_name = $item->module; $modules_array = $this->enabled_modules_array;  ?>
				<?php if( in_array( $module_name, $modules_array )) { ?>
					<tr>
						<!--<td><input type='checkbox' name='delete_<?php //echo $item->mixsetting_id;?>' value='<?php //echo $item->mixsetting_id ?>' class='checkbox' value="<?php //echo $item->mixsetting_id ?>" <?php //if( !empty($item->is_delete) ) { echo 'DISABLED'; } ?>/></td>-->
						<td class="admin_table_centered"><?php echo $item->mixsetting_id; ?></td>
						<td ><?php if( !empty($item->module) ){ echo $item->module; }else { echo '-'; } ?></td>
						<td ><?php if( !empty($item->item_title) ){ echo $item->item_title; }else { echo '-'; } ?></td>
						<td ><?php if( !empty($item->title_items) ){ echo $item->title_items; }else { echo '-'; } ?></td>
						<td ><?php if( !empty($item->resource_type) ){ echo $item->resource_type; }else { echo '-'; } ?></td>
						<td class="admin_table_centered"><?php echo ( $item->enabled ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitelike', 'controller' => 'manage', 'action' => 'enabled-content-tab', 'mixsetting_id' => $item->mixsetting_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/images/enabled1.gif', '', array('title' => $this->translate('Disable Module for Likes'))), array())  : $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitelike', 'controller' => 'manage', 'action' => 'enabled-content-tab', 'mixsetting_id' => $item->mixsetting_id), $this->htmlImage('application/modules/Sitelike/externals/images/enabled0.gif', '', array('title' => $this->translate('Enable Module for Likes')))) ) ?></td >
						<!--<td ><?php //if( !empty($item->resource_id) ){ echo $item->resource_id; }else { echo '-'; } ?></td>-->
						<td><?php	echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitelike', 'controller' => 'manage', 'action' => 'module-edit', 'mixsetting_id' => $item->mixsetting_id), $this->translate("edit")) ;	?>
						<?php if(empty($item->default)):?>
							| <a href='<?php echo $this->url(array('action' => 'delete-module','resource_type' => $item->resource_type)) ?>' class="smoothbox">
								<?php echo $this->translate("delete") ?>
							</a>
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

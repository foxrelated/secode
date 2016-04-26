<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage-module.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  function multiDelete() {
	return confirm("<?php echo $this->translate("Are you sure you want to remove the selected modules as suggestion?") ?>");
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
  <?php echo $this->translate('Suggestions / Recommendations Plugin') ?>
</h2>
<?php if (count($this->navigation)): ?>
	<div class='tabs'>
  <?php
	// Render the menu
	echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
  </div>
<?php endif; ?>

	<div class='seaocore_settings_form'>
	  <a href="<?php echo $this->url(array('module' => 'suggestion', 'controller' => 'settings', 'action' => 'guidelines'), 'admin_default', true) ?>" class="buttonlink" style="background-image:url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Seaocore/externals/images/admin/help.gif);padding-left:23px;"><?php echo $this->translate("Guidelines to display 'Suggest to Friend' link on the content view pages of 3rd party plugin"); ?></a>
	</div>
	<br />

	<h3 style="margin-bottom:6px;"><?php echo $this->translate("Manage Modules for Suggestions / Recommendations"); ?></h3>
<?php
	// Show Success message.
	if (isset($this->success_message)) {
	  echo '<ul class="form-notices" style="margin:0px;"><li style="float:left;">' . $this->translate('Successfully create') . ' ' . ucfirst($this->success_message) . ' ' . $this->translate('Suggestion.') . '</li></ul>';
	}
?>
	<p><?php echo $this->translate("Here, you can manage various modules for which you want recommendations to be sent to users. You can add new modules to enable contents from it to be suggested to other members on your site using “Suggest to Friend” link available on the ‘content view pages’. Thus, this interface enables you to extend this plugin to ANY CONTENT MODULE of your site that enables recommendations to be sent to users on your site about any module of your site. If you do not want the content from a module to be suggested to other members, then simply disable that module from here. For more tips on this section, visit the FAQ page."); ?></p>
	<br style="clear:both;" />

<?php
	// Show link for "Create Featured Content".
	echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'suggestion', 'controller' => 'settings', 'action' => 'module-create'), $this->translate("Add New Module for Suggestions"), array('class' => 'buttonlink seaocore_icon_add'));
?>
	<br /><br />

<?php
	if (count($this->paginator)):
?>
	  <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete();">
	    <table class='admin_table' width= "100%" >
	      <thead>
	        <tr>
	          <th class='admin_table_short' align="center">
		  <?php echo $this->translate("ID"); ?>
        </th>
		<th align="left">
		  <?php echo $this->translate("Module Name"); ?>
        </th>
        <th align="left">
		  <?php echo $this->translate("Item Type"); ?>
        </th>
        <th align="left">
		  <?php echo $this->translate("Item Title"); ?>
        </th>
        <th align="left">
		  <?php echo $this->translate("Button Title"); ?>
        </th>


        <th class="center">
		  <?php echo $this->translate("Quality"); ?>
        </th>        <th class="center">
		  <?php echo $this->translate("Link"); ?>
        </th>        <th class="center">
		  <?php echo $this->translate("Popup"); ?>
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
	  <?php foreach ($this->paginator as $item): ?>
	  <?php $module_name = $item->module;
			$modules_array = $this->enabled_modules_array; ?>
<?php if (in_array($module_name, $modules_array)) { ?>
			  <tr>
				<td class="admin_table_centered"><?php echo $item->modinfo_id; ?></td>
				<td ><?php if (!empty($item->module)) {
          if( strstr($item->module, 'sitereview') ) {
            if( !empty($item->settings) ) {
              $tempSettings = @unserialize($item->settings);
              if( !empty($tempSettings) && !empty($tempSettings['listing_id']) ) {
                $tempTitle = Engine_Api::_()->getItem('sitereview_listingtype', $tempSettings['listing_id']);
                echo 'sitereview: ' . $tempTitle->title_singular;
              }
            }
          }else {
            echo $item->module;
          }
			  } else {
				echo '-';
			  } ?></td>
				<td ><?php if (!empty($item->item_type)) {
				echo $item->item_type;
			  } else {
				echo '-';
			  } ?></td>
				<td ><?php if (!empty($item->item_title)) {
				echo $item->item_title;
			  } else {
				echo '-';
			  } ?></td>
			<td ><?php if (!empty($item->button_title)) {
				echo $item->button_title;
			  } else {
				echo '-';
			  } ?></td>

			<td><?php if ($item->quality == 0) {
				echo 'Average';
			  } else if ($item->quality == 1) {
				echo 'Good';
			  } else if ($item->quality == 2) {
				echo 'High';
			  } else {
				echo '-';
			  } ?></td>

					<td class="admin_table_centered"><?php echo ( $item->link ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'suggestion', 'controller' => 'settings', 'action' => 'enabled-content-tab', 'modinfo_id' => $item->modinfo_id, 'type' => 'link'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/enabled1.gif', '', array('title' => $this->translate('Disable Suggest to Friend link'))), array()) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'suggestion', 'controller' => 'settings', 'action' => 'enabled-content-tab', 'modinfo_id' => $item->modinfo_id, 'type' => 'link'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/enabled0.gif', '', array('title' => $this->translate('Enable Suggest to Friend link')))) ) ?></td >

					<td class="admin_table_centered"><?php if (!empty($item->default)) {
				echo ( $item->popup ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'suggestion', 'controller' => 'settings', 'action' => 'enabled-content-tab', 'modinfo_id' => $item->modinfo_id, 'type' => 'popup'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/enabled1.gif', '', array('title' => $this->translate('Disable popup after creating content'))), array()) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'suggestion', 'controller' => 'settings', 'action' => 'enabled-content-tab', 'modinfo_id' => $item->modinfo_id, 'type' => 'popup'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/enabled0.gif', '', array('title' => $this->translate('Enable popup after creating content')))) );
			  } else {
				echo '-';
			  } ?></td >

			  		<td class="admin_table_centered"><?php echo ( $item->enabled ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'suggestion', 'controller' => 'settings', 'action' => 'enabled-content-tab', 'modinfo_id' => $item->modinfo_id, 'type' => 'enabled'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/enabled1.gif', '', array('title' => $this->translate('Disable Module for Suggestions'))), array()) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'suggestion', 'controller' => 'settings', 'action' => 'enabled-content-tab', 'modinfo_id' => $item->modinfo_id, 'type' => 'enabled'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/enabled0.gif', '', array('title' => $this->translate('Enable Module for Suggestions')))) ) ?></td >
		  		<td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'suggestion', 'controller' => 'settings', 'action' => 'module-edit', 'modinfo_id' => $item->modinfo_id), $this->translate("edit")); ?>
<?php if (empty($item->default)): ?>
						  | <a href='<?php echo $this->url(array('action' => 'delete-module', 'item_type' => $item->item_type)) ?>' class="smoothbox"><?php echo $this->translate("delete") ?></a>

						  | <a href='<?php echo $this->url(array('action' => 'icon', 'getModule' => $item->module)) ?>' class="smoothbox">
<?php echo $this->translate("icon") ?>
						  </a>
<?php endif; ?>

						</td>
					  </tr>
<?php $is_module_flag = 1;
			  } ?>
<?php endforeach; ?>
			      </tbody>
			    </table>
			    <br />
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
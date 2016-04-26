<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
  <?php echo $this->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin') ?>
</h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3 style="margin-bottom:6px;"><?php echo $this->translate("Manage Modules for Check-ins"); ?></h3>
<?php
  // Show Success message.
  if (isset($this->success_message)) {
    echo '<ul class="form-notices" style="margin:0px;"><li style="float:left;">' . $this->translate('Successfully create') . ' ' . ucfirst($this->success_message) . ' ' . $this->translate('Check-ins.') . '</li></ul>';
  }
?>

<p><?php echo $this->translate('Here you can manage various modules in which you want users to be able to check-in. Here, you can add new modules to enable users to check-in into them from their view pages. Thus, this interface enables you to extend this plugin to ANY CONTENT MODULE of your site that enables users on your site to check-in into any module of your site. You will also need to place the widget: "Content Check-in button & stats" on the content profile page. You can then choose the settings for that widget according to your requirements. If you do not want users to check-in into a content, then simply disable that module from here. For more tips on this section, visit the FAQ page.'); ?></p>
<br style="clear:both;" />

<?php
  // Show link for "Create Featured Content".
  echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitetagcheckin', 'controller' => 'content', 'action' => 'add'), $this->translate("Add New Module for Check-ins"), array('class' => 'buttonlink seaocore_icon_add'));
?>
<br /><br />

<?php if (count($this->paginator)): ?>
  <form method="post" action="<?php echo $this->url(); ?>">
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
            <?php echo $this->translate("Database Table Item"); ?>
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
              <td class="admin_table_centered">
                <?php echo $item->content_id; ?>
              </td>
              <td>
                <?php if (!empty($item->module)) {
                  echo $item->module;
                } else {
                  echo '-';
                } ?>
              </td>
              <td>
                <?php if (!empty($item->resource_type)) {
                  echo $item->resource_type;
                } else {
                  echo '-';
                } ?>
              </td>
              <td class="admin_table_centered">
                <?php echo ( $item->enabled ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitetagcheckin', 'controller' => 'content', 'action' => 'enabled', 'content_id' => $item->content_id), $this->htmlImage('application/modules/Sitetagcheckin/externals/images/enabled1.gif', '', array('title' => $this->translate('Disable Module for Check-ins'))), array()) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitetagcheckin', 'controller' => 'content', 'action' => 'enabled', 'content_id' => $item->content_id), $this->htmlImage('application/modules/Sitetagcheckin/externals/images/enabled0.gif', '', array('title' => $this->translate('Enable Module for Check-ins'))))) ?>
							</td>
							<td>
								<?php if($item->module == 'sitepagealbum'):?>
									<?php $module = 'sitepage';?>
								<?php elseif($item->module == 'sitebusinessalbum'):?>
									<?php $module = 'sitebusiness';?>
								<?php elseif($item->module == 'sitegroupalbum'):?>
                  <?php $module = 'sitegroup';?>
								<?php elseif($item->module == 'sitestorealbum'):?>
                  <?php $module = 'sitestore';?>
                <?php else:?>
                  <?php $module = $item->module;?>
                <?php endif;?>
                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitetagcheckin', 'controller' => 'content', 'action' => 'edit', 'content_id' => $item->content_id, 'module_name' => $module), $this->translate("edit")); ?>
                <?php if (empty($item->default)): ?> | 
                  <a href='<?php echo $this->url(array('route' => 'admin_default', 'module' => 'sitetagcheckin', 'controller' => 'content', 'action' => 'delete', 'resource_type' => $item->resource_type, 'module_name' => $item->module)); ?>' class="smoothbox">
                    <?php echo $this->translate("delete") ?>
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
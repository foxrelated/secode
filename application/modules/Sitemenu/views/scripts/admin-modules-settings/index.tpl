<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<script type="text/javascript">

    function multiDelete()
    {
        return confirm("<?php echo "Are you sure you want to remove the selected modules as advertisable? Users will not be able to directly advertise their content from these modules after being removed." ?>");
    }

    function selectAll()
    {
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
    <?php echo 'Advanced Menus Plugin - Interactive and Attractive Navigation' ?>
</h2>

<div class='tabs'>
    <?php
    echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
</div>

<h3 style="margin-bottom:6px;"><?php echo "Manage Modules for Menus"; ?></h3>
<p>
    <?php echo "Here, you can manage modules. These are the modules that can be used to advertise or show content in Main Menu. Here you can add, edit and delete a module. As well as you can set status for that module, if you want to show the module content in main menu enable it otherwise you can disable it. You can also see the details of the module."; ?>
</p>
<br style="clear:both;" />

<?php
// Show link for "Create Featured Content".
echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemenu', 'controller' => 'modules-settings', 'action' => 'module-create'), "Add New Module", array('class' => 'buttonlink sitemenu_icon_create'));
?>
<br /><br />

<?php
if (count($this->paginator)):
    ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete();">
        <table class='admin_table'>
            <thead>
                <tr>
                    <th class='admin_table_short'>
                        <input onclick='selectAll();' type='checkbox' class='checkbox' />
                    </th>
                    <th class='admin_table_short' align="center">
                        <?php
                        echo "ID";
                        ?>
                    </th>
                    <th align="left">
                        <?php echo "Content Module"; ?>
                    </th>
                    <th align="left">
                        <?php echo "Content Title"; ?>
                    </th>
                    <th class="center">
                        <?php echo "Status"; ?>
                    </th>
                    <th align="left">
                        <?php echo "Options"; ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $is_module_flag = 0; ?>
                <?php foreach ($this->paginator as $item): ?>
                    <?php $module_name = $item->module_name;
                    $modules_array = $this->enabled_modules_array; ?>
                    <?php if (in_array($module_name, $modules_array)) : ?>
                        <tr>
                            <td>
                                <input type='checkbox' name='delete_<?php echo $item->module_id; ?>' value='<?php echo $item->module_id ?>' class='checkbox'  <?php
            if (!empty($item->is_delete)) :
                echo 'DISABLED';
            endif;
                        ?>/>
                            </td>
                            <td class="admin_table_centered">
                                <?php echo $item->module_id; ?>
                            </td>
                            <td >
                                <?php
                                if (!empty($item->module_name)) :
                                    echo $item->module_name;
                                else :
                                    echo '-';
                                endif;
                                ?>
                            </td>
                            <td ><?php
                    if (!empty($item->module_title)) :
                        echo $item->module_title;
                    else :
                        echo '-';
                    endif;
                                ?></td>
                            <?php if (!empty($item->status)): ?>
                                <td class="center"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemenu', 'controller' => 'modules-settings', 'action' => 'module-status', 'module_id' => $item->module_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitemenu/externals/images/region_enable1.gif', '', array('title' => 'Disable'))) ?></td>
                            <?php else: ?>
                                <td class="center"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemenu', 'controller' => 'modules-settings', 'action' => 'module-status', 'module_id' => $item->module_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitemenu/externals/images/region_enable0.gif', '', array('title' => 'Enable'))) ?></td>
                            <?php endif; ?>
                            <td >
                                <?php
                                
                                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemenu', 'controller' => 'modules-settings', 'action' => 'module-detail', 'module_id' => $item->module_id), "details", array('class' => 'smoothbox'));
                                
                                if($item->module_name != 'sitestore'):
                                echo ' | '.$this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemenu', 'controller' => 'modules-settings', 'action' => 'create-categories-menu', 'module_id' => $item->module_id), "create categories menu", array('class' => 'smoothbox'));
                                else:
                                  echo ' | N/A';
                                endif;
                                if (empty($item->is_delete)) :
                                echo ' | ' . $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemenu', 'controller' => 'modules-settings', 'action' => 'module-create', 'module_id' => $item->module_id), "edit");
                                
                                    echo ' | ' . $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemenu', 'controller' => 'modules-settings', 'action' => 'module-delete', 'module_id' => $item->module_id), "delete", array('class' => 'smoothbox'));
                                endif;
                                ?>
                            </td>
                        </tr><?php $is_module_flag = 1;
                endif; ?>
                <?php endforeach; ?>
            </tbody> 
        </table>
        <br />
        <?php if (!empty($is_module_flag)) : ?>
            <div class='buttons'>
                <button type='submit'><?php echo "Delete Selected" ?></button>
            </div>
        <?php endif; ?>
    </form>
    <br />
    <div>
        <?php echo $this->paginationControl($this->paginator); ?>
    </div>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo "There are no modules available." ?>
        </span>
    </div>
<?php endif;
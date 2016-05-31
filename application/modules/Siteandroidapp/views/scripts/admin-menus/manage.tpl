<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    manage.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">

    var SortablesInstance;
    window.addEvent('load', function () {
        SortablesInstance = new Sortables('menu_list', {
            clone: true,
            constrain: false,
            handle: '.item_label',
            onComplete: function (e) {
                reorder(e);
            }
        });
    });

    var reorder = function (e) {
        var menuitems = e.parentNode.childNodes;
        var ordering = {};
        var i = 1;
        for (var menuitem in menuitems)
        {
            var child_id = menuitems[menuitem].id;

            if ((child_id != undefined))
            {
                ordering[child_id] = i;
                i++;
            }
        }
        ordering['format'] = 'json';

        // Send request
        var url = '<?php echo $this->url(array('action' => 'order')) ?>';
        var request = new Request.JSON({
            'url': url,
            'method': 'POST',
            'data': ordering,
            onSuccess: function (responseJSON) {
            }
        });

        request.send();
    }
</script>

<h2>
    <?php echo $this->translate('Android Mobile Application'); ?>
</h2>

<div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>


<h3><?php echo $this->translate('Manage App Dashboard Menus'); ?></h3>
<p class="form-description">
    <?php echo $this->translate('Below, you will be able to manage your app\'s dashboard, by managing its Menus and Menu Categories. You can create a new menu / category and set their order by simply dragging and dropping them in desired order. For each menu, you can enable / disable / delete it, change its properties, etc.<br/><br/>A very useful feature of this Android App product is that it enables you to add any new content to your app in real time. This means that this API powered app not only shows content from the SocialEngine core and app integrated modules, it also enables you to have content in your app from external web pages.<br />Thus, you can even show content from other 3rd-party modules that are not integrated with the app: Add a new dashboard menu for it, URL, Title, Icon, and you are done. With the Single-Sign-On (SSO) capability of the API, users get auto-logged-in if they are already logged-in in the app.<br /><br />[<span style="font-weight: bold;">Note</span>: All changes that you do here to Dashboard Menus will reflect to all the Android App users.]') ?>
</p>

<br />
<p>
    <a href='<?php echo $this->url(array("module" => "siteandroidapp", "controller" => "menus", "action" => 'add-menu'), "admin_default", true) ?>' class="smoothbox buttonlink seaocore_icon_add"><?php echo $this->translate("Add New Menu / Category"); ?></a>
</p>

<br />
<?php if (COUNT($this->paginator)): ?>

    <div class="seaocore_admin_order_list">
        <div class="list_head">
            <div style="width:10%">
                <?php echo "ID"; ?>
            </div>

            <div style="width:20%">
                <?php echo "Label"; ?>
            </div>

            <div style="width:10%">
                <?php echo "Module"; ?>
            </div>

            <div style="width:10%">
                <?php echo "Type"; ?>
            </div>

            <div style="width:15%">
                <?php echo "Visible To"; ?>
            </div>
            
            <div style="width:5%">
                <?php echo "Status"; ?>
            </div>

            <div style="width:10%">
                <?php echo "Options"; ?>
            </div>
        </div>
        <ul id='menu_list'>
            <?php foreach ($this->paginator as $item): ?>
                <?php if ($item->type == 'category'): ?>
                    <li id="content_<?php echo $item->getIdentity(); ?>" style="background-color: #b8e5f2">
                    <?php else: ?>
                    <li id="content_<?php echo $item->getIdentity(); ?>">
                    <?php endif; ?>                
                    <input type='hidden'  name='order[]' value='<?php echo $item->getIdentity(); ?>'>

                    <div style="width:10%;" class=''>
                        <?php echo $item->menu_id ?>
                    </div>

                    <div style="width:20%;" class=''>
                        <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->dashboard_label, 20) ?>
                    </div>

                    <div style="width:10%;" class=''>
                        <i><?php echo!empty($item->module) ? $item->module : '-'; ?></i>
                    </div>

                    <div style="width:10%;" class=''>
                        <i><?php echo ($item->type == 'menu') ? 'Menu' : 'Category'; ?></i>
                    </div>
                    
                    <div style="width:15%;" class=''>
                        <?php
                            if($item->show == 'both')
                                $showLabel = 'Both Logged-in & Logged-out Users';
                            else if($item->show == 'login') 
                                $showLabel = 'Only Logged-in Users';
                            else if($item->show == 'logout') 
                                $showLabel = 'Only Logged-out Users';
                            
                            echo !empty($showLabel) ? $showLabel : '-'; 
                        ?>
                    </div>

                    <div style="width:5%;" class=''>
                        <?php echo ( $item->status ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteandroidapp', 'controller' => 'menus', 'action' => 'status', 'id' => $item->getIdentity()), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => $this->translate('Disable it'))), array('class' => 'smoothbox')) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteandroidapp', 'controller' => 'menus', 'action' => 'status', 'id' => $item->getIdentity()), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Enable it'))), array('class' => 'smoothbox')) ) ?>
                    </div>

                    <div style="width:10%;" class=''>
                        <?php
                        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteandroidapp', 'controller' => 'menus', 'action' => 'info', 'id' => $item->getIdentity()), $this->translate('info'), array('class' => 'smoothbox'));

                        echo ' | ' . $this->htmlLink(
                                array('route' => 'admin_default', 'module' => 'siteandroidapp', 'controller' => 'menus', 'action' => 'edit-menu', 'id' => $item->getIdentity()), $this->translate("edit"), array('class' => 'smoothbox'));

                        if (empty($item->default))
                            echo ' | ' . $this->htmlLink(
                                    array('route' => 'admin_default', 'module' => 'siteandroidapp', 'controller' => 'menus', 'action' => 'delete', 'id' => $item->getIdentity()), $this->translate("delete"), array('class' => 'smoothbox'));
                        ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <div class="tip" style="width: 100%">
        <span style="width: 100%"><?php echo $this->translate('There are no menu available.') ?></span>
    </div>
<?php endif; ?>

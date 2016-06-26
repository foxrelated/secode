<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editor.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<h2><?php echo 'Advanced Menus Plugin - Interactive and Attractive Navigation'; ?></h2>

<div class='tabs'>
    <?php
    echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
</div>

<h3 style="margin-bottom:6px;"><?php echo "Menu Editor"; ?></h3>

<p>
    <?php echo 'Use this area to manage the various navigation menus that appear in your community. When you select the menu you wish to edit, a list of the menu items it contains will be shown. You can drag these items up and down to change their order. You can create sub menus and 3rd level menus. You can edit a menu and can select content to be shown in respective menu.'; ?>
</p>
<br/>
<div class="tip">
  <span>
    <?php echo "If you find the ordering of the menus inappropriate, reorder the Main Menus, Sub Menus and the 3rd Level Menu by dragging them up or down."; ?>
  </span>
</div>

<br/>
<div class="admin_menus_filter">
    <form action="<?php echo $this->url() ?>" method="get">
        <b><?php echo "Editing:"; ?></b>
        <?php echo $this->formSelect('name', $this->selectedMenu, array('onchange' => '$(this).getParent(\'form\').submit();'), $this->menuList) ?>
    </form>
</div>

<br />

<div class="admin_menus_options">
    <?php echo $this->htmlLink(array('reset' => false, 'action' => 'create', 'addType' => 'Item', 'name' => $this->selectedMenu), 'Add Item', array('class' => 'buttonlink admin_menus_additem smoothbox')) ?>
  <?php echo $this->htmlLink(array('reset' => false, 'module' => 'core', 'controller' => 'menus', 'action' => 'create-menu'), 'Add Menu', array('class' => 'buttonlink admin_menus_addmenu smoothbox')) ?>
</div>

<br />
<?php $info_string = null; $info_array = array(); ?>
<ul class="admin_menus_items sm_admin_menus_items" id='menu_list_1'>
  <?php
    $tempSubSubMenuCount = $tempSubMenuCount = 1;
    foreach ($this->menuItems as $menuItemArray):

      $menuItem = $menuItemArray['info'];
      $menuItem = $menuItem['menuObj'];
      $temp_root_depth = 1;
      unset($menuItemArray['info']);
  ?>
  <li class="admin_menus_item<?php if (isset($menuItem->enabled) && !$menuItem->enabled): echo ' disabled';endif; ?>" id="admin_menus_item_<?php echo $menuItem->name ?>"  >
    <span class="item_wrapper">
      <span class="item_options">
        <?php
          $tempURL = $this->url(array('action' => 'edit', 'addType' => 'Item', 'name' => $menuItem->name, 'mainMenu' => $this->selectedMenu), "admin_default", false);
            echo "<a href='javascript:void(0);' onClick='openSmoothbox(\"" . $tempURL . "\", $menuItem->id)'>" . 'edit' . "</a>";
              if ($menuItem->custom): ?>
                | <?php $tempDeleteURL = $this->url(array('action' => 'delete', 'addType' => 'Item', 'name' => $menuItem->name, 'mainMenu' => $this->selectedMenu), "admin_default", false);
                echo "<a href='javascript:void(0);' onClick='openSmoothbox(\"" . $tempDeleteURL . "\", $menuItem->id)'>" . 'delete' . "</a>";?>
        <?php endif; ?>
      </span>
      <span class="item_label1">
        <?php echo $menuItem->label; ?>
      </span>
      <span class="item_url">
        <?php
            echo '<a>(variable)</a>';
        ?>
      </span>
    </span>
  <?php  // For Sub Menus
    if (COUNT($menuItemArray)) :
      $temp_root_depth = 2;
  ?>
  <ul class="admin_menus_item" id='sub_menu_list_<?php echo $tempSubMenuCount++; ?>' style="padding-left: 20px;">
  <?php
    foreach ($menuItemArray as $subMenuItemArray) :
      $subMenuItem = $subMenuItemArray['info'];
      $subMenuItem = $subMenuItem['menuObj'];
      $temp_parent_depth = 1;
      unset($subMenuItemArray['info']);
  ?>
  <li class="admin_menus_item<?php if (isset($subMenuItem->enabled) && !$subMenuItem->enabled): echo ' disabled'; endif; ?>" id="admin_menus_item_<?php echo $subMenuItem->name ?>" style="padding-top: 35px" >
    <span class="item_wrapper">
      
      <span class="item_options">
        <?php if (isset($menuItem->enabled) && $menuItem->enabled): ?>
        <?php
          $childCount = COUNT($subMenuItemArray);
          $tempURL = $this->url(array('action' => 'edit', 'addType' => 'Item', 'name' => $subMenuItem->name, 'mainMenu' => $this->selectedMenu, 'childCount' => $childCount), "admin_default", false);
          echo "<a href='javascript:void(0);' onClick='openSmoothbox(\"" . $tempURL . "\", $subMenuItem->id)'>" . 'edit' . "</a>";
        ?>
        <?php if ($subMenuItem->custom): ?>
        | <?php $tempDeleteURL = $this->url(array('action' => 'delete', 'addType' => 'Item', 'name' => $subMenuItem->name, 'mainMenu' => $this->selectedMenu), "admin_default", false);
              echo "<a href='javascript:void(0);' onClick='openSmoothbox(\"" . $tempDeleteURL . "\", $subMenuItem->id)'>" . 'delete' . "</a>";?>
        <?php endif; ?>
        <?php else:?>
        <?php echo 'parent disabled';?>
        <?php endif;?>
      </span>
      <span class="item_label2">
        <?php echo $subMenuItem->label; ?>
      </span>
      <span class="item_url">
      <?php
          echo '<a>(variable)</a>';
      ?>
      </span>
    </span>
    <?php // For Sub Sub Menus
      if (COUNT($subMenuItemArray)) :
        $temp_root_depth = 3;
    ?>
    <ul class="admin_menus_item" id="sub_sub_menu_list_<?php echo $tempSubSubMenuCount++; ?>" style="padding-left: 20px;">
    <?php
      foreach ($subMenuItemArray as $subSubMenuItemArray) :
        $subSubMenuItem = $subSubMenuItemArray['info'];
        $subSubMenuItem = $subSubMenuItem['menuObj'];
        $temp_parent_depth = 2;
        $temp_child_depth = 1;
        if(isset($subSubMenuItem['info']))
          unset($subSubMenuItem['info']);
    ?>
      <li class="admin_menus_item<?php if (isset($subSubMenuItem->enabled) && !$subSubMenuItem->enabled): echo ' disabled'; endif; ?>" id="admin_menus_item_<?php echo $subSubMenuItem->name ?>" style="padding-top: 35px">
      <span class="item_wrapper">
        <span class="item_options">    
          <?php if (isset($subMenuItem->enabled) && $subMenuItem->enabled): ?>
          <?php $tempURL = $this->url(array('action' => 'edit', 'addType' => 'Item', 'name' => $subSubMenuItem->name, 'mainMenu' => $this->selectedMenu, 'childCount' => $childCount), "admin_default", false);
                echo "<a href='javascript:void(0);' onClick='openSmoothbox(\"" . $tempURL . "\",$subSubMenuItem->id)'>" . 'edit' . "</a>"; ?>
          <?php if ($subSubMenuItem->custom): ?>
          | <?php $tempDeleteURL = $this->url(array('action' => 'delete', 'addType' => 'Item', 'name' => $subSubMenuItem->name, 'mainMenu' => $this->selectedMenu), "admin_default", false);
                    echo "<a href='javascript:void(0);' onClick='openSmoothbox(\"" . $tempDeleteURL . "\",$subSubMenuItem->id)'>" . 'delete' . "</a>";?>
          <?php endif; ?>
          <?php else:?>
        <?php echo 'parent disabled';?>
        <?php endif;?>
        </span>
      <span class="item_label3">
        <?php echo $subSubMenuItem->label; ?>
      </span>
      <span class="item_url">
      <?php
          echo '<a>(variable)</a>';
      ?>
      </span>
    </span>
  </li>
  <?php
    $info_array[$subSubMenuItem->id] = $temp_child_depth;
    endforeach;
  ?>
  </ul>
  <?php endif; ?>
</li>
<?php
  $info_array[$subMenuItem->id] = $temp_parent_depth;
  endforeach;
?>
</ul>
<?php
  endif;
?>
</li>
<?php
  $info_array[$menuItem->id] = $temp_root_depth;
endforeach;
?>
</ul>

<script type="text/javascript">
  
    new Sortables('menu_list_1', {
        clone: true,
        constrain: false,
        handle: '.item_label1',
        onComplete: function(e) {
                reorder(e);
        }
    });
    
<?php for ($tempSubVar = 1; $tempSubVar <= $tempSubMenuCount; $tempSubVar++): ?>
          new Sortables('sub_menu_list_<?php echo $tempSubVar; ?>', {
              clone: true,
              constrain: false,
              handle: '.item_label2',
              onComplete: function(e) {
                  reorder(e);
              }
          });
<?php endfor; ?>

<?php for ($tempSubSubVar = 1; $tempSubSubVar <= $tempSubSubMenuCount; $tempSubSubVar++): ?>
          new Sortables('sub_sub_menu_list_<?php echo $tempSubSubVar; ?>', {
              clone: true,
              constrain: false,
              handle: '.item_label3',
              onComplete: function(e) {
                  reorder(e);
              }
          });
<?php endfor; ?>
  
    var SortablesInstance;
    window.addEvent('domready', function() {
        $$('.item_label1').addEvents({
            mouseover: showPreview,
            mouseout: showPreview
        });
        
        $$('.item_label2').addEvents({
            mouseover: showPreview,
            mouseout: showPreview
        });
        
        $$('.item_label3').addEvents({
            mouseover: showPreview,
            mouseout: showPreview
        });
    });

    var showPreview = function(event) {
        try {
            element = $(event.target);
            element = element.getParent('.admin_menus_item').getElement('.item_url');
            if( event.type == 'mouseover' ) {
                element.setStyle('display', 'block');
            } else if( event.type == 'mouseout' ) {
                element.setStyle('display', 'none');
            }
        } catch( e ) {
        }
    }

    var reorder = function(e) {
        var menuitems = e.parentNode.childNodes;
        var ordering = {};
        var i = 1;
        for (var menuitem in menuitems)
        {
            var child_id = menuitems[menuitem].id;
            if ((child_id != undefined) && (child_id.substr(0, 5) == 'admin'))
            {
                ordering[child_id] = i;
                i++;
            }
        }
        ordering['menu'] = '<?php echo $this->selectedMenu; ?>';

        ordering['format'] = 'json';
        // Send request
        var url = '<?php echo $this->url(array('module' => 'sitemenu', 'controller' => 'menu-settings', 'action' => 'order')) ?>';
        var request = new Request.JSON({
            'url' : url,
            'method' : 'POST',
            'data' : ordering,
            onSuccess : function(responseJSON) {
            }
        });
        
        request.send();
        
    }
    function ignoreDrag()
    {
        event.stopPropagation();
        return false;
    }
  
    function openSmoothbox(url, id){
      var menu_info_array= <?php echo json_encode($info_array); ?>;
      url = url + '/info_string/' +  menu_info_array[id];
      Smoothbox.open(url);
    }
</script>
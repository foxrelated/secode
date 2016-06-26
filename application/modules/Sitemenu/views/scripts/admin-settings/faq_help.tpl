<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<script type="text/javascript">  
  function faq_show(id) {
    if($(id)){
      if($(id).style.display == 'block') {
        $(id).style.display = 'none';
      } else {
        $(id).style.display = 'block';
      }
    }
  }
<?php if ($this->faq_id): ?>
    window.addEvent('domready',function(){
      faq_show('<?php echo $this->faq_id; ?>');
    });
<?php endif; ?>
</script>

<?php $i = 1; ?>
<div class="admin_seaocore_files_wrapper">
  <ul class="admin_seaocore_files seaocore_faq">
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo 'I want to create sub-menus for my main menus. How can I do so? '; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo "Ans: There are two ways to do so,<br/>
First Way:<br/>
1. Go to the menu editor and click on 'Add Item' link.<br/>
2. Now fill the required details.<br/>
3. Enable \"Do you want to use this menu item as a sub menu item?\" setting and choose the parent menu from the dropdown, for which you want to add sub-menu.<br/><br/>

Configure various settings for your sub-menu and click on create menu item. You can now see this sub-menu added, just after the menu you had selected from the parent menu dropdown. <br/><br/>

Second Way: <br/>
1. Go to the menu editor and edit the menu you want to add as a sub menu for the given main menus.<br/>
2. Enable \"Do you want to use this menu item as a sub menu item?\" setting and choose the parent menu from the dropdown, for which you want to add sub-menu.<br/><br/>

You can now see this sub-menu added, just after the menu you had selected from the parent menu dropdown."; ?>
        </div>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo 'I want to show content for my sub-menus but I am not getting an option for this, If I am adding a new menu from "Add Item" I am able to select content for this but not for my sub-menus. What might be the reason?'; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo "Ans: It is happening because the layout selected for main menu (Parent Menu) is followed by its sub-menu so, to show content for sub-menus you have to select content based layout for the main menu."; ?>				
        </div>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo 'I want to place sign in and sign up pop up shown in the mini menu, on various buttons and links for the non logged in users, Is it possible? If so, how can I do so?'; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo "Ans: Yes, you can do so by pasting the below code:<br/><br/>
For Sign In Link:<br/>
&lt;a onclick=\"advancedMenuUserLoginOrSignUp('login');\" href='javascript:void(0)'&gt;Sign In&lt;/a&gt;
<br/><br/>
For Sign In Button:<br/>
&lt;button onclick=\"advancedMenuUserLoginOrSignUp('login');\"&gt;Sign In&lt;/button&gt;
<br/><br/>
For Sign Up Link:<br/>
&lt;a onclick=\"advancedMenuUserLoginOrSignUp('signup');\" href='javascript:void(0)'&gt;Sign Up&lt;/a&gt;
<br/><br/>
For Sign Up Button:<br/>
&lt;button onclick=\"advancedMenuUserLoginOrSignUp('signup');\"&gt;Sign Up&lt;/button&gt;
<br/><br/>
The non logged in users on your site will now be able to see 'sign in and sign up pop' up for those buttons and links. <br/>[Note: It will only work if you have placed our Advanced Mini Menu Widget on your site.]"; ?>				
        </div>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo 'I have entered icon URLs for sub-menus from menu editor. But I am not able to see any icon on my site. What might be the reason?'; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo 'Ans: It is happening because you have not selected "Labels with Icons" option for "Show in menu tabs" setting from menu editor. Please select the above option for the menus you have entered icon URL,  you will now be able to see icons for your sub-menus.'; ?>				
        </div>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo 'I have enter icon URLs for sub menus but they are not coming proper at user side. What might be the reason?'; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo "Ans: The recommended size for uploading icon is 16x16 px. Please enter icon URLs of this size, your icons will be well aligned with proper visibility."; ?>				
        </div>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo 'I want to enter icon URLs for the default standard navigation menus, but I do not know how to proceed. What should I do?'; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo "Ans: Default standard navigation menus do not appear as menus in the menu editor so you will not be able to enter icon URLs for those. But, If you want icons for these menus, you can create custom menus for these navigation menus from the menu editor and upload icons for the same."; ?>				
        </div>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo 'Height of the menus are not coming properly on my site. What might be the reason?'; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo "Ans: Please go to the 'Menu Editor' and edit the main menus regarding which the content height is not coming properly on your site. Now configure the height of these menus from 'Content Height' setting.<br/>[Note: You will be able to set height only for the content based menus.]"; ?>				
        </div>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo 'I have configured various menu color settings from Advanced Main Menu widget. Now I am not able to see any changes on user side. What might be the reason?'; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo "Ans: It is happening because you have selected default instead of custom for 'Menu Style' setting, in 'Advanced Main Menu' widget. Please select custom menu style to reflect your various menu color and style changes."; ?>				
        </div>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo 'I am not able to see arrows (placed at the right side of the menus having sub menus) properly for the Main Menus on my site. What might be the reason?'; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo "Ans: It is happening because the recommended number of menus you can place in the Main Menu is 7 and you might have increased this value. Increasing main menus will decrease the space between the menus, by which arrows will get shifted below the menu and you will not be able to see the arrows properly."; ?>				
        </div>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo 'Is it compatible with other themes and plugins?'; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo "Ans: This plugin has been developed by following complete SocialEngine standards. Hence, it will work well with all themes and plugins that have also been developed by following SocialEngine's plugin standards."; ?>				
        </div>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo "Floating menu's height and width is not coming properly on my site. What might be the reason?"; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo 'Ans: Please add the below code in theme.css:<br/>
#global_header.fixed .sitemenu_main_menu_wrapper{<br/>
	width: $theme_topbar_width;<br/>
}<br/>
Your floating menu will now view properly on your site.'; ?>				
        </div>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo 'I want to change view type for the main menus on my site, but I do not know how to proceed. How can I do so?'; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo "Ans: Please follow the below steps to do so:<br/>
1. Go to Menu Editor section available in the admin panel of this plugin.<br/>
2. Select the Main Navigation Menu from the editing drop down.<br/>
3. Now click on the edit link of the main menu for which you want to change the view type.<br/>
4. Choose view type for your menu amongst the available view type from the 'Main Menu View Type' option. <br/>
5. Save Your changes using 'Edit Menu Item' button.<br/>
You will now be able to see this view type for your main menu on your site."; ?>				
        </div>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo 'I want to change ordering of my main menus and the sub menus. How can I do so?'; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo "Ans: Please go to the Menu Editor >> Main Navigation Menu, now drag and drop the menus to change the sequence with your desired ones. To reorder the main menus and the sub menus, click on their names and drag them up or down.<br/><br/>[Note: You can only drag and drop the sub menus within their main menus.]"; ?>				
        </div>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo "I can see 'My Cart' only while scrolling, but I want it to be visible always? Is it possible with this plugin?"; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo "Ans: Yes, Please go to the:<br/>
1. Layout Editor >> Site Header >> Advanced Main Menu widget. <br/>
2. Now, select \"<b>Always</b>\" for \"When do you want to show 'Cart Icon' and 'Language Bar' / 'Global Search' / ('Product Search' also if you have our <a href='http://www.socialengineaddons.com/socialengine-stores-marketplace-ecommerce-plugin' target='_blank'>Stores / Marketplace - Ecommerce Plugin</a>)\" setting.<br/>
Now you will be able to see 'My Cart' option always."; ?>				
        </div>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo 'For which type of Main and Mini menus I will be able to upload icons?'; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo "Ans: You can upload icon only for those menus which are created by you from “Menu Editor” page (Custom Menus). (Note: This case is applicable for both Main Menus and Mini Menus.)"; ?>				
        </div>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo 'The CSS of this plugin is not coming on my site. What should I do ?'; ?></a>
      <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
        <div>
          <?php echo "Ans: Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."; ?>				
        </div>
      </div>
    </li>
    
  </ul>
</div>
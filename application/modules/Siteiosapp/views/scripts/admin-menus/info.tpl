<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    info.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup admin_member_stats">
    <h3>Menu / Category Information</h3>
    <ul>
        <li>
            <?php echo $this->translate('Dashboard Label:'); ?>
            <span><?php echo $this->menu->dashboard_label; ?></span>
        </li>

        <?php if ($this->menu->type != 'category'): ?>
            <li>
                <?php echo $this->translate('Header Label:'); ?>
                <span><?php echo!empty($this->menu->header_label) ? $this->menu->header_label : '-'; ?></span>
            </li>
        <?php endif; ?>

        <li>
            <?php echo $this->translate('Type:'); ?>
            <span><?php echo ucfirst($this->menu->type); ?></span>
        </li> 
        
        <li>
            <?php echo $this->translate('Visible To:'); ?>
            <span><?php 
                            if($this->menu->show == 'both')
                                $showLabel = 'Both Logged-in and Logged-out Users';
                            else if($this->menu->show == 'login') 
                                $showLabel = 'Only Logged-in Users';
                            else if($this->menu->show == 'logout') 
                                $showLabel = 'Only Logged-out Users';
                            
                            echo !empty($showLabel) ? $showLabel : '-'; 
            ?></span>
        </li> 

        <?php if ($this->menu->type != 'category'): ?>
            <li>
                <?php echo $this->translate('Menu Name:'); ?>
                <span><?php echo!empty($this->menu->name) ? $this->menu->name : '-'; ?></span>
            </li> 

            <li>
                <?php echo $this->translate('Module:'); ?>
                <span><?php echo!empty($this->menu->module) ? $this->menu->module : '-'; ?></span>
            </li> 

            <li>
                <?php echo $this->translate('Icon:'); ?>
                <span><?php echo!empty($this->menu->icon) ? $this->menu->icon : '-'; ?></span>
            </li> 

            <li>
                <?php echo $this->translate('URL:'); ?>
                <span><?php echo!empty($this->menu->url) ? $this->menu->url : '-'; ?></span>
            </li> 
        <?php endif; ?>

        <li>
            <?php echo $this->translate('Status:'); ?>
            <span><?php echo!empty($this->menu->status) ? 'Enabled' : 'Disabled'; ?></span>
        </li> 
    </ul>
    <br/>
    <button type="submit" onclick="parent.Smoothbox.close();
          return false;" name="close_button" value="Close">Close</button>
</div>

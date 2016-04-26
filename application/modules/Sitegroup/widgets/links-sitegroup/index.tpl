<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Sitegroup
* @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php $manageAdminEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1);?>
<?php $groupilike = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.mylike.show', 1);?>
<?php $claimEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.claimlink', 1);?>
<div class="quicklinks sitegroup_manage_admin_link">
    <ul>
        <?php if($this->showGroupAdmin && !empty($manageAdminEnabled) && !empty($this->manageadmin_count)): ?>
        <li>
            <a href='<?php echo $this->url(array('action' => 'my-groups'), 'sitegroup_manageadmins', true) ?>' class="icon_sitegroups_group-owner buttonlink"> <?php echo $this->translate("Groups I Admin") ?></a>
        </li>
        <?php endif; ?>
        <?php if($this->showGroupClaimed && !empty($claimEnabled) && !empty($this->showClaimLink)): ?>			
        <li>
            <a href='<?php echo $this->url(array('action' => 'my-groups'), 'sitegroup_claimgroups', true) ?>' class="icon_sitegroups_claim buttonlink"> <?php echo $this->translate("Groups I've Claimed") ?></a>
        </li>				
        <?php endif; ?>
        <?php if($this->showGroupLiked && !empty($groupilike)): ?>
        <li>
            <a href='<?php echo $this->url(array('action' => 'mylikes'), 'sitegroup_like', true) ?>' class="icon_sitegroups_like buttonlink"> <?php echo $this->translate("Groups I Like") ?></a>
        </li>
        <?php endif; ?>
        <?php if($this->showGroupJoined && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')): ?>
        <li>
            <a href='<?php echo $this->url(array('action' => 'my-joined'), 'sitegroup_like', true) ?>' class="icon_sitegroup_join buttonlink"> <?php echo $this->translate("Groups I've Joined") ?></a>
        </li>
        <?php endif; ?>
    </ul>		
</div>	
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: remove-photo.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>

<div class="sitegroup_viewgroups_head">
  <?php echo $this->htmlLink($this->sitegroup->getHref(), $this->itemPhoto($this->sitegroup, 'thumb.icon', '', array('align' => 'left'))) ?>
  <h2>	
    <?php echo $this->sitegroup->__toString() ?>
  </h2>
</div>

<div class='global_form'>
  <form method="post" action="<?php echo $this->url(array('group_id' => $this->sitegroup->group_id, 'action' => 'remove-photo'), 'sitegroup_dashboard', true) ?>" class="global_form" enctype="application/x-www-form-urlencoded">
    <div>
      <div>
        <h3><?php echo $this->translate("Remove Photo"); ?></h3>
        <p class="form-description"><?php echo $this->translate("Do you want to remove profile photo of your Group? Doing so will set your Group's profile photo back to the default photo.") ?></p>
        <div class="form-elements">
          <div id="buttons-wrapper" class="form-wrapper"><fieldset id="fieldset-buttons">		
              <button type="submit" id="submit" name="submit" value="submit"><?php echo $this->translate("Remove Photo"); ?></button>			
              <?php echo $this->translate("or"); ?> <a onclick="parent.Smoothbox.close();" href="<?php echo $this->url(array('group_id' => $this->sitegroup->group_id, 'action' => 'profile-picture'), 'sitegroup_dashboard', true) ?>" type="button" id="cancel" name="cancel"><?php echo $this->translate("cancel"); ?></a></fieldset></div>
          <input type="hidden" id="token" value="ec82d65d6feea1b453c59302a8dbdfba" name="token">
        </div>
      </div>
    </div>
  </form>
</div>
</div>
</div>
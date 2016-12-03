<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php echo $this->render('_jsAdmin.tpl') ?>

<?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/admin-review/_navigationAdmin.tpl'; ?>

<h3><?php echo $this->translate('Profile Fields for Reviews'); ?></h3>

<p>
  <?php echo $this->translate("Profile information will enable review owner to add additional information about their review. This non-generic additional information will help others know more specific details about the review. Below, you can create Profile Types for the Reviews on your site, and then create Profile Information Fields for those profile types. Multiple profile types enable you to have different profile information fields for different type of reviews. You can set the sequence of the profile fields by drag-and-drop.<br/><br/>You can also map the Categories for reviews with Profile Types from the 'Category-Review Profile Fields Mapping' section such that if a review belongs to a product associated with that category, it will automatically have the corresponding profile fields.<br /><br />An example use case of this feature would be creating different review information fields for business and education oriented products."); ?>
</p>

<br />

<div class="admin_fields_type">
  <h3><?php echo $this->translate("Editing Profile Information Fields for Review Profile Type:") ?></h3>
  <?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions) ?>
</div>

<br />

<div class="admin_fields_options">
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion">    <?php echo $this->translate('Add Question'); ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addheading"><?php echo $this->translate('Add Heading'); ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_renametype"><?php echo $this->translate('Rename Profile Type'); ?></a>
  <?php if (count($this->topLevelOptions) > 1): ?>
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_deletetype"><?php echo $this->translate('Delete Profile Type'); ?></a>
  <?php endif; ?>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addtype"><?php echo $this->translate('Create New Profile Type'); ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;"><?php echo $this->translate('Save Order'); ?></a>
</div>

<br />

<ul class="admin_fields">
  <?php foreach ($this->secondLevelMaps as $map): ?>
    <?php echo $this->adminFieldMeta($map) ?>
  <?php endforeach; ?>
</ul>

<br />
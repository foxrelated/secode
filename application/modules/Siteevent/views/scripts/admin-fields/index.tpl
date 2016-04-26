<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php echo $this->render('_jsAdmin.tpl') ?>

<h2>
    <?php echo $this->translate('Advanced Events Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate('Profile Fields for Events'); ?></h3>

<p>
    <?php echo $this->translate('Profile information will enable event owner to add additional information about their event. This non-generic additional information will help others know more specific details about the event. Below, you can create Profile Types for the Events on your site, and then create Profile Information Fields for those profile types. Multiple profile types enable you to have different profile information fields for different type of events. You can also map the Categories for events with Profile Types from the "Category-Event Profile Mapping" section such that if an event belongs to a category, it will automatically have the corresponding profile fields.<br /><br />You can set the sequence of the profile fields by drag-and-drop. You can also enable the "Profile Information Fields" to be displayed on event profile pages by choosing the appropriate value for "SHOW IN QUICK INFORMATION WIDGET?" field while adding / editing the questions.<br /><br />An example use case of this feature would be creating different profile information fields for business and education oriented events.'); ?>
</p>

<br />

<div class="admin_fields_type">
    <h3><?php echo $this->translate("Editing Profile Information Fields for Event Profile Type:") ?></h3>
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

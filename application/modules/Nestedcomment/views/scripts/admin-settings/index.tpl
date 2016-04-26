<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	if( !empty($this->isModsSupport) ):
		foreach( $this->isModsSupport as $modName ) {
			echo "<div class='tip'><span>" . $this->translate("Note: You do not have the latest version of the '%s'. Please upgrade it to the latest version to enable its integration with Advanced Comments Plugin - Nested Comments, Replies, Voting & Attachments.", ucfirst($modName)) . "</span></div>";
		}
	endif;
?>
<h2><?php echo $this->translate('Advanced Comments Plugin - Nested Comments, Replies, Voting & Attachments'); ?></h2>

<?php if (count($this->navigation)): ?>
<div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>
<?php endif; ?>

<?php
$row = Engine_Api::_()->seaocore()->checkEnabledNestedComment('advancedactivity', array('checkModuleExist' => true));
    if($row && Engine_Api::_()->seaocore()->checkEnabledNestedComment('advancedactivity')) :?>
    <div class="tip">
        <span> 
         <?php echo $this->translate("Below settings will not apply on the Advanced Activity Feeds Plugin, you need to go to the ‘Manage Modules’ section of this plugin to configure the respective settings from its ‘edit’ link."); ?>
        </span>
    </div>
<?php endif;?> 

<div class='seaocore_settings_form'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>
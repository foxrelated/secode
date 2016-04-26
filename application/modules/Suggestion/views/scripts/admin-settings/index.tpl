<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Suggestions / Recommendations Plugin') ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
  <?php
  // Render the menu
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

  <div class='tip'>
    <span>
	<?php echo $this->translate("To make the task of managing widgets easy for you, we have moved these settings to “Manage Modules” and “Layout Editor”. You can now configure desired settings from there for the respective plugin."); ?>
  </span>
</div>
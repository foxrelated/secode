<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');
?>

<h2>
  <?php echo $this->translate("Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin") ?>
</h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.google.map.key')):  ?>
		<?php 
		 $URL_MAP = $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'map'), 'admin_default', true);
		echo $this->translate('<div class="tip"><span>Note: You have not entered Google Places API Key for your website. Please <a href="%s" target="_blank"> Click here </a></span></div>', $URL_MAP); ?>
<?php endif;  ?>

<div class='seaocore_settings_form'>
  <div class='settings' style="margin-top:15px;">
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<script type="text/javascript">
	window.addEvent('domready', function() {
		<?php
     $userSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.usersettings');
     //$fieldIdValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.field.id');
		?>
		
		showUserSettingsOption('<?php echo $userSettings ?>');
	});

  function showUserSettingsOption(options) {
    if(options == 0) {
			if ($('sitetagcheckin_levelsettings-wrapper'))
				$('sitetagcheckin_levelsettings-wrapper').style.display='none';
			if ($('sitetagcheckin_networksettings-wrapper'))
					$('sitetagcheckin_networksettings-wrapper').style.display='none';
			if ($('sitetagcheckinprofile_mapping-wrapper'))
					$('sitetagcheckinprofile_mapping-wrapper').style.display='none';
			if ($('sitetagcheckin_userstatus-wrapper'))
					$('sitetagcheckin_userstatus-wrapper').style.display='none';
			if ($('sitetagcheckin_mapshow-wrapper'))
				$('sitetagcheckin_mapshow-wrapper').style.display='none';
			if ($('sitetagcheckin_layouts_oder-wrapper'))
				$('sitetagcheckin_layouts_oder-wrapper').style.display='none';
		  if ($('sitetagcheckin_memberlimit-wrapper'))
				$('sitetagcheckin_memberlimit-wrapper').style.display='none';
		}
		else {
			if ($('sitetagcheckin_levelsettings-wrapper'))
				$('sitetagcheckin_levelsettings-wrapper').style.display='block';
			if ($('sitetagcheckin_networksettings-wrapper'))
				$('sitetagcheckin_networksettings-wrapper').style.display='block';
			if ($('sitetagcheckinprofile_mapping-wrapper'))
				$('sitetagcheckinprofile_mapping-wrapper').style.display='block';
			if ($('sitetagcheckin_userstatus-wrapper'))
				$('sitetagcheckin_userstatus-wrapper').style.display='block';
			if ($('sitetagcheckin_mapshow-wrapper'))
				$('sitetagcheckin_mapshow-wrapper').style.display='block';
			if ($('sitetagcheckin_layouts_oder-wrapper'))
				$('sitetagcheckin_layouts_oder-wrapper').style.display='block';
			if ($('sitetagcheckin_memberlimit-wrapper'))
				$('sitetagcheckin_memberlimit-wrapper').style.display='block';
		}
  }
  
</script>
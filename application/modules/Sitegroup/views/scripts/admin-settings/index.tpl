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

$coreSettings = Engine_Api::_()->getApi('settings', 'core');

if (!empty($this->isModulesActivated)):
  foreach ($this->isModulesActivated as $modKey => $modValue) {
    $url = $this->url(array('module' => $modKey, 'controller' => 'settings', 'action' => 'readme'), 'admin_default', true);
    if ($modKey == 'sitegroupinvite') {
      $url = $this->url(array('module' => $modKey, 'controller' => 'global', 'action' => 'readme'), 'admin_default', true);
    }
		$showMessage = Zend_Registry::get('Zend_Translate')->_('<div class="tip"><span>Note: You have installed the "%s" plugin but have not activated it on your site. Please activate it first. <a href=%s>Click here</a> to activate the "%s" plugin.</span></div>');
		$showMessage = sprintf($showMessage, $modValue, $url, $modValue );
		echo $showMessage;
  }
endif;

if( !empty($this->isModsSupport) ):
	foreach( $this->isModsSupport as $modName ) {
		echo $this->translate('<div class="tip"><span>Note: You do not have the latest version of the "%s". Please upgrade it to the latest version to enable its integration with Groups / Communities Plugin.</span></div>', ucfirst($modName));
	}
endif;
?>
 <?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<script type="text/javascript">
  if(document.getElementById('sitegroup_map_city')) {
    window.addEvent('domready', function() {
      new google.maps.places.Autocomplete(document.getElementById('sitegroup_map_city'));
    });
  }
</script>

<h2 class="fleft"><?php echo $this->translate('Groups / Communities Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/manageExtensions.tpl'; ?>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<?php if (count($this->subnavigation) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.isActivate', 0)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->subnavigation)->render() ?>
    </div>
<?php endif; ?>

<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; ?>

<?php
$moduleName = 'sitevideointegration';
if (!isset($_COOKIE[$moduleName . '_dismiss'])):
    ?>
    <?php if (!Engine_Api::_()->hasModuleBootstrap('sitevideointegration')): ?>
        <div id="dismissintegration_modules">
            <div class="seaocore-notice">
                <div class="seaocore-notice-icon">
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                </div>
                <div style="float:right;">
                    <button onclick="dismissintegration('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                </div>
                <div class="seaocore-notice-text ">
                    <?php echo 'To set up a robust Videos System with <a href="https://www.socialengineaddons.com/socialengine-directory-pages-plugin">"Groups / Communities plugin"</a>, you can purchase our awesome <a  target="_blank" href="https://www.socialengineaddons.com/socialengine-videos-product-kit">"Advanced Videos - Product Kit"</a>.'; ?>
                </div>	
            </div>
        </div>
    <?php else: ?>
<?php if(Engine_Api::_()->hasModuleBootstrap('sitevideo') && !Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup'))):?>
        <div id="dismissintegration_modules">
            <div class="seaocore-notice">
                <div class="seaocore-notice-icon">
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                </div>
                <div style="float:right;">
                    <button onclick="dismissintegration('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                </div>
                <div class="seaocore-notice-text ">
                    <?php echo 'You have installed <a href="https://www.socialengineaddons.com/videoextensions/socialengine-advanced-videos-pages-businesses-groups-listings-events-stores-extension" target="_blank">Advanced Videos - Pages, Businesses, Groups, Multiple Listing Types, Events, Stores, etc Extension</a> installed on your website. If you want to display videos using the Advanced Videos Plugin on your website so that all videos can be place all together then please <a  target="_blank" href="admin/sitevideointegration/modules">click here</a> to integrate it.'; ?>
                </div>	
            </div>
        </div>
 <?php endif; ?>
    <?php endif; ?>

<?php endif; ?>

<?php
$moduleName = 'documentintegration';
if (!isset($_COOKIE[$moduleName . '_dismiss'])):
    ?>
    <?php if (!Engine_Api::_()->hasModuleBootstrap('documentintegration')): ?>
        <div id="dismissintegration_modules">
            <div class="seaocore-notice">
                <div class="seaocore-notice-icon">
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                </div>
                <div style="float:right;">
                    <button onclick="dismissintegration('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                </div>
                <div class="seaocore-notice-text ">
                    <?php echo 'To set up a robust Documents System with <a href="https://www.socialengineaddons.com/socialengine-directory-pages-plugin">"Groups / Communities plugin"</a>, you can purchase our awesome <a  target="_blank" href="https://www.socialengineaddons.com/socialengine-videos-product-kit">"Documents Sharing - Product Kit"</a>.'; ?>
                </div>	
            </div>
        </div>
    <?php else: ?>
<?php if(Engine_Api::_()->hasModuleBootstrap('document') && !Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup'))):?>
        <div id="dismissintegration_modules">
            <div class="seaocore-notice">
                <div class="seaocore-notice-icon">
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                </div>
                <div style="float:right;">
                    <button onclick="dismissintegration('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                </div>
                <div class="seaocore-notice-text ">
                    <?php echo 'You have installed <a href="https://www.socialengineaddons.com/documentextensions/socialengine-documents-sharing-pages-businesses-groups-listings-events-stores-extension" target="_blank">Documents Sharing - Pages, Businesses, Groups, Multiple Listing Types, Events, Stores, etc Extension</a> installed on your website. If you want to display documemts using the Documents Plugin on your website so that all documents can be place all together then please <a  target="_blank" href="admin/documentintegration/modules">click here</a> to integrate it.'; ?>
                </div>	
            </div>
        </div>
 <?php endif; ?>
    <?php endif; ?>

<?php endif; ?>

<div>

<?php if (!$coreSettings->getSetting('seaocore.google.map.key')):  ?>
		<?php 
		 $URL_MAP = $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'map'), 'admin_default', true);
		echo $this->translate('<div class="tip"><span>Note: You have not entered Google Places API Key for your website. Please <a href="%s" target="_blank"> Click here </a></span></div>', $URL_MAP); ?>
<?php endif;  ?>

<?php if(!$this->hasLanguageDirectoryPermissions):?>
<div class="seaocore_tip">
  <span>
    <?php echo "Please log in over FTP and set CHMOD 0777 (recursive) on the application/languages/ directory for  change the pharse groups and group." ?>
  </span>
</div>
<?php endif; ?>

<div class='clear sitegroup_settings_form'>
  <div class='settings'>
<?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="text/javascript">
  var display_msg=0;
  window.addEvent('domready', function() {
   // $('translation_file').checked=false;
  //  toogleLaguagePhase('none');
    showlocationKM('<?php echo $coreSettings->getSetting('sitegroup.proximitysearch', 1); ?>');	  showcategoryblock	('<?php echo $coreSettings->getSetting('sitegroup.category.edit', 0); ?>');
    showclaim('<?php echo $coreSettings->getSetting('sitegroup.claimlink', 1); ?>');
    showlocationOption('<?php echo $coreSettings->getSetting('sitegroup.locationfield', 1); ?>');
    showpackageOption('<?php echo $coreSettings->getSetting('sitegroup.package.enable', 1); ?>');
    showDefaultNetwork('<?php echo $coreSettings->getSetting('sitegroup.network', 0) ?>');
    showMapOptions('<?php echo $coreSettings->getSetting('sitegroup.location', 1) ?>');
    display_msg=1;
  });

  //  HERE WE CREATE A FUNCTION FOR SHOWING THE LOCATION IN KM OR MILES
  function showpackageOption(option) {
    if($('sitegroup_package_enable-wrapper')) {
      if(option == 1) {
        if($('sitegroup_currency-wrapper'))
          $('sitegroup_currency-wrapper').style.display='block';
        if($('sitegroup_payment_benefit-wrapper'))
          $('sitegroup_payment_benefit-wrapper').style.display='block';
      if($('sitegroup_package_view-wrapper'))
          $('sitegroup_package_view-wrapper').style.display='block';
       if($('sitegroup_package_information-wrapper'))
          $('sitegroup_package_information-wrapper').style.display='block';
      }
      else{
        if($('sitegroup_currency-wrapper'))
          $('sitegroup_currency-wrapper').style.display='none';
        if($('sitegroup_payment_benefit-wrapper'))
          $('sitegroup_payment_benefit-wrapper').style.display='none';
      if($('sitegroup_package_view-wrapper'))
          $('sitegroup_package_view-wrapper').style.display='none';
      if($('sitegroup_package_information-wrapper'))
          $('sitegroup_package_information-wrapper').style.display='none';
      }
    }
  }

  function showlocationOption(option) {

		
    if(option == 1) {
      if($('sitegroup_location-wrapper'))
        $('sitegroup_location-wrapper').style.display='block';
      if($('sitegroup_proximitysearch-wrapper'))
        $('sitegroup_proximitysearch-wrapper').style.display='block';
      if($('sitegroup_proximitysearch-1'))
        if($('sitegroup_proximitysearch-1').checked)
          showlocationKM(1);
      else
        showlocationKM(0);
      if($('sitegroup_location-1'))
        if($('sitegroup_location-1').checked)
          showMapOptions(1);
      else
        showMapOptions(0);
              if($('sitegroup_multiple_location-wrapper'))
        $('sitegroup_multiple_location-wrapper').style.display='block';
    }
    else{
      if($('sitegroup_location-wrapper'))
        $('sitegroup_location-wrapper').style.display='none';
      if($('sitegroup_proximitysearch-wrapper'))
        $('sitegroup_proximitysearch-wrapper').style.display='none';
      if($('sitegroup_proximity_search_kilometer-wrapper'))
        $('sitegroup_proximity_search_kilometer-wrapper').style.display='none';
              if($('sitegroup_multiple_location-wrapper'))
        $('sitegroup_multiple_location-wrapper').style.display='none';
      showMapOptions(0);
    }
		
  }
  //  HERE WE CREATE A FUNCTION FOR SHOWING THE LOCATION IN KM OR MILES
  function showlocationKM(option) {
    if($('sitegroup_proximity_search_kilometer-wrapper')) {
      if(option == 1) {
        if($('sitegroup_proximity_search_kilometer-wrapper'))
          $('sitegroup_proximity_search_kilometer-wrapper').style.display='block';	
      }
      else{
        if($('sitegroup_proximity_search_kilometer-wrapper'))
          $('sitegroup_proximity_search_kilometer-wrapper').style.display='none';
      }
    }
  }

  //  HERE WE CREATE A FUNCTION FOR SHOWING BOUNCING
  function showMapOptions(option) {
    if($('sitegroup_location-wrapper')) {
      if(option == 1) {
        if($('sitegroup_map_sponsored-wrapper'))
          $('sitegroup_map_sponsored-wrapper').style.display='block';
          if($('sitegroup_map_zoom-wrapper'))
						$('sitegroup_map_zoom-wrapper').style.display='block';
           if($('sitegroup_map_city-wrapper'))
						$('sitegroup_map_city-wrapper').style.display='block';
      }
      else{
        if($('sitegroup_map_sponsored-wrapper'))
          $('sitegroup_map_sponsored-wrapper').style.display='none';
         if($('sitegroup_map_zoom-wrapper'))
					$('sitegroup_map_zoom-wrapper').style.display='none';
         if($('sitegroup_map_city-wrapper'))
					$('sitegroup_map_city-wrapper').style.display='none';
      }
    }
  }

  function showclaim(option) 
  {
    if($('sitegroup_claim_show_menu-wrapper')) {
      if(option == 1) { 
        $('sitegroup_claim_show_menu-wrapper').style.display='block';	
      }
      else{
        $('sitegroup_claim_show_menu-wrapper').style.display='none';
      }		
    }
    if($('sitegroup_claim_email-wrapper')) {
      if(option == 1) { 
        $('sitegroup_claim_email-wrapper').style.display='block';	
      }
      else{
        $('sitegroup_claim_email-wrapper').style.display='none';
      }		
    }
  }

  function showcategoryblock(option)
  {
    if(option == 1 && display_msg == 1) {
      alert("<?php echo $this->string()->escapeJavascript($this->translate('After giving this permission members can edit categories but it can cause content loss like reviews rating data, profile type details etc.')) ?>");
    }
  }
  function showDefaultNetwork(option) {
    if($('sitegroup_default_show-wrapper')) {
      if(option == 0) {
        $('sitegroup_default_show-wrapper').style.display='block';        
        showDefaultNetworkType($('sitegroup_default_show-1').checked);
        $('sitegroup_privacybase-wrapper').style.display='block';
         $('sitegroup_networkprofile_privacy-wrapper').style.display='none';
      }else{
        showDefaultNetworkType(1);
        $('sitegroup_default_show-wrapper').style.display='none';
        $('sitegroup_privacybase-wrapper').style.display='none';
        $('sitegroup_networkprofile_privacy-wrapper').style.display='block';
      }
    }
  }

  function showDefaultNetworkType(option) {
    if($('sitegroup_networks_type-wrapper')) {
      if(option == 1) {
        $('sitegroup_networks_type-wrapper').style.display='block';
      }else{
        $('sitegroup_networks_type-wrapper').style.display='none';
      }
    }
  }
</script>

<script type="text/javascript">
    function dismissintegration(modName) {
        var d = new Date();
        // Expire after 1 Year.
        d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toGMTString();
        document.cookie = modName + "_dismiss" + "=" + 1 + "; " + expires;
        $('dismissintegration_modules').style.display = 'none';
    }

</script>

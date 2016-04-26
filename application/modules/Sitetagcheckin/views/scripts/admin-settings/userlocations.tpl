<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: userlocations.tpl 2012-01-12 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate("Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<?php 
$locationMappingCount = count($this->option_id);
if (!empty($locationMappingCount))  :
$eventCount = count($this->row);
if (!empty($this->userSettings)) : ?>
<div class="seaocore_settings_form">
    <div class="tip">
  <span>
      <?php
      echo 'To enable Proximity and Geo-location search for Members on your site, you need to sync the locations of all the members on your site with Google Places. Thus,<a href="' . $this->url(array('action' => 'usersink-location')) . '" class="smoothbox"> click here</a> to sync '.$eventCount.' members on your site with Google Places. This search is similar to the location based searching and browsing of Directory Items / Pages from “<a href="http://www.socialengineaddons.com/socialengine-directory-pages-plugin" target="_blank">Directory / Pages Plugin</a>”.'; ?>
      </span>
</div>
</div>
<?php else : ?>
<div class="tip">
  <span>  
<?php echo $this->translate("To enable Proximity and Geo-location search for Members on your site, please choose ‘Yes’ option for ‘Members Location & Proximity Search’ field from the ‘Global Settings’ section of this plugin."); ?>
  </span>
</div>
<?php endif; ?>

<?php else : ?>
<div class="tip">
  <span>  
<?php echo $this->translate('You have currently not mapped “Location” type fields for any Profile Type on your site. Please map “Location” type fields by using the ‘Profile Type - Location Field Mapping’ field in the ‘Global Settings’ section of this plugin. This mapping will sync member locations which they enter from their ‘Edit Profile’ page and their ‘Edit My Location’ page, after they click on ‘Save’ button on these pages.'); ?>
  </span>
</div>
<?php endif; ?>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: albumlocations.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate("Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) : ?>
<div class="seaocore_settings_form">
    <div class="tip">
  <span>  
      <?php $albumCount = $this->row;
      echo 'To enable Proximity and Geo-location search for Albums on your site, you need to sync the locations of all the albums on your site with Google Places. Thus,<a href="' . $this->url(array('action' => 'sinkalbums-location')) . '" class="smoothbox"> click here</a> to sync '.$albumCount.' albums on your site with Google Places. This search is similar to the location based searching and browsing of Directory Items / Pages from “<a href="http://www.socialengineaddons.com/socialengine-directory-pages-plugin" target="_blank">Directory / Pages Plugin</a>”.'; ?>
      </span>
</div>
</div>
<?php else : ?>
<div class="tip">
  <span>  
<?php echo $this->translate('Your site does not have the SE "Albums Plugin" installed and enabled on it. Please install and enable that plugin on your website to enable the Proximity and Geo-location search for Albums on your site. This search is similar to the location based searching and browsing of Directory Items / Pages from “<a href="http://www.socialengineaddons.com/socialengine-directory-pages-plugin" target="_blank">Directory / Pages Plugin</a>”.'); ?>
  </span>
</div>
<?php endif; ?>
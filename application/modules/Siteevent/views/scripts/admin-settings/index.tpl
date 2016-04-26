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
 <?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<script type="text/javascript">
if(document.getElementById('siteevent_map_city')) {
	window.addEvent('domready', function() {
		new google.maps.places.Autocomplete(document.getElementById('siteevent_map_city'));
	});
}
</script>

<?php
if (!empty($this->isModsSupport)):
    foreach ($this->isModsSupport as $modName) {
        echo "<div class='tip'><span>" . $this->translate("Note: You do not have the latest version of the '%s'. Please upgrade it to the latest version to enable its integration with Advanced Events plugin.", ucfirst($modName)) . "</span></div>";
    }
endif;
?>
<?php $url = $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'upgrade'), 'admin_default', true); ?>

<h2>
    <?php echo $this->translate('Advanced Events Plugin'); ?>
</h2>


<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<?php if (count($this->navigationGeneral)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
    </div>
<?php endif; ?>


<?php if (!empty($this->getHostTypeArray)): ?>
    <div id="dismiss_modules">
        <div class="seaocore-notice">
            <div class="seaocore-notice-icon">
                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
            </div>
            <div style="float:right;">
                <button onclick="dismissNote();"><?php echo $this->translate('Dismiss'); ?></button>
            </div>
            <div class="seaocore-notice-text">
                <?php echo $this->translate("Note: It seems that this plugin has been used at multiple domains, because of which this plugin may not work properly on domain configures to use this plugin. Please find the list of other domains below :"); ?></br>
                <ul>
                    <?php
                    foreach ($this->getHostTypeArray as $getHostName):
                        if ($this->viewAttapt != $getHostName && !empty($getHostName)):
                            echo '<li><b>' . $getHostName . '</b></li>';
                        endif;
                    endforeach;
                    ?>
                </ul>
                <?php echo $this->translate("1) If you do not want to use this plugin on Multiple Domains, then please click on 'Dismiss' button.<br/> 2) If above is not the case and you want to use this plugin on multiple domains, then please file a support ticket from your SocialEngineAddOns <a href='http://www.socialengineaddons.com/user/login' target='_blank'>client area</a>."); ?>
            </div>
        </div>
    </div>
    <?php
endif;
?>

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
                    <?php echo 'To set up a robust Videos System with <a href="https://www.socialengineaddons.com/socialengine-advanced-events-plugin">"Advanced Events Plugin"</a>, you can purchase our awesome <a  target="_blank" href="https://www.socialengineaddons.com/socialengine-videos-product-kit">"Advanced Videos - Product Kit"</a>.'; ?>
                </div>	
            </div>
        </div>
    <?php else: ?>
<?php if(Engine_Api::_()->hasModuleBootstrap('sitevideo') && !Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'siteevent_event', 'item_module' => 'siteevent'))):?>
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

<div class='seaocore_settings_form siteevent_global_settings'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>

<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>

<script type="text/javascript">

    function dismissNote() {
        $('is_remove_note').value = 1;
        $('review_global').submit();
    }

    window.addEvent('domready', function() {
        showDefaultNetwork('<?php echo $settings->getSetting('siteevent.network', 0) ?>');
        
        showTimezoneSetting('<?php echo $settings->getSetting('siteevent.datetime.format', 'medium') ?>');

        showLocationSettings('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1) ?>');
    });
    
    function showTimezoneSetting(value) {
      if( value == 'full' || value == 'long' ) {
        $('siteevent_timezone-wrapper').style.display = 'block';
      } else {
        $('siteevent_timezone-wrapper').style.display = 'none';
      }
    }

    function showDefaultNetwork(option) {
        if ($('siteevent_default_show-wrapper')) {
            if (option == 0) {
                $('siteevent_default_show-wrapper').style.display = 'block';
                showDefaultNetworkType($('siteevent_default_show-1').checked);
                $('siteevent_networkprofile_privacy-wrapper').style.display = 'none';
            }
            else {
                showDefaultNetworkType(1);
                $('siteevent_default_show-wrapper').style.display = 'none';
                $('siteevent_networkprofile_privacy-wrapper').style.display = 'block';
            }
        }
    }

    function showDefaultNetworkType(option) {
        if ($('siteevent_networks_type-wrapper')) {
            if (option == 1) {
                $('siteevent_networks_type-wrapper').style.display = 'block';
            } else {
                $('siteevent_networks_type-wrapper').style.display = 'none';
            }
        }
    }

    function showLocationSettings(option) {

        if ($('siteevent_veneuname-wrapper')) {
            if (option == 1) {
                $('siteevent_veneuname-wrapper').style.display = 'block';
            } else {
                $('siteevent_veneuname-wrapper').style.display = 'none';
            }
        }

        if ($('siteevent_map_sponsored-wrapper')) {
            if (option == 1) {
                $('siteevent_map_sponsored-wrapper').style.display = 'block';
            } else {
                $('siteevent_map_sponsored-wrapper').style.display = 'none';
            }
        }

        if ($('siteevent_proximity_search_kilometer-wrapper')) {
            if (option == 1) {
                $('siteevent_proximity_search_kilometer-wrapper').style.display = 'block';
            } else {
                $('siteevent_proximity_search_kilometer-wrapper').style.display = 'none';
            }
        }

        if ($('seaocore_locationdefaultmiles-wrapper')) {
            if (option == 1) {
                $('seaocore_locationdefaultmiles-wrapper').style.display = 'block';
            } else {
                $('seaocore_locationdefaultmiles-wrapper').style.display = 'none';
            }
        }

        if ($('seaocore_locationdefault-wrapper')) {
            if (option == 1) {
                $('seaocore_locationdefault-wrapper').style.display = 'block';
            } else {
                $('seaocore_locationdefault-wrapper').style.display = 'none';
            }
        }

        if ($('siteevent_map_city-wrapper')) {
            if (option == 1) {
                $('siteevent_map_city-wrapper').style.display = 'block';
            } else {
                $('siteevent_map_city-wrapper').style.display = 'none';
            }
        }

        if ($('siteevent_map_zoom-wrapper')) {
            if (option == 1) {
                $('siteevent_map_zoom-wrapper').style.display = 'block';
            } else {
                $('siteevent_map_zoom-wrapper').style.display = 'none';
            }
        }
    }

</script>

<style type="text/css">
    .seaocore-notice-text ul {
        list-style: disc outside none;
        margin: 3px 0 0 18px;
    }
    .seaocore-notice-text ul li{
        margin: 2px 0 2px 0px;
    }
</style>

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
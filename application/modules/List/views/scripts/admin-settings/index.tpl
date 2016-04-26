<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
 <?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");
?>

<script type="text/javascript">
	window.addEvent('domready', function() {
		new google.maps.places.Autocomplete(document.getElementById('list_map_city'));
	});
</script>
<?php
	if( !empty($this->isModsSupport) ):
		foreach( $this->isModsSupport as $modName ) {
			echo "<div class='tip'><span>" . $this->translate("Note: You do not have the latest version of the '%s'. Please upgrade it to the latest version to enable its integration with Listing / Catalog Showcase plugin.", ucfirst($modName)) . "</span></div>";
		}
	endif;
?>

<h2><?php echo $this->translate('Listings / Catalog Showcase Plugin'); ?></h2>
<?php if (count($this->navigation)): ?>
	<div class='seaocore_admin_tabs'>
		<?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
	</div>
<?php endif; ?>

<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; ?>
<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/Opengraph_message.tpl'; ?>
<div class='seaocore_settings_form'>
	<div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>

<script type="text/javascript">

	window.addEvent('domready', function() {
		showlocationKM('<?php echo $settings->getSetting('list.proximitysearch', 1); ?>');
		showDefaultNetwork('<?php echo $settings->getSetting('list.network', 0) ?>');
		showMapOptions('<?php echo $settings->getSetting('list.location', 1) ?>');
		showlocationOption('<?php echo $settings->getSetting('list.locationfield', 1); ?>');
    showExpiryDuration('<?php echo Engine_Api::_()->list()->expirySettings()?>');
    showDescription('<?php echo $settings->getSetting('list.description.allow', 1) ?>');
	});

  function showDescription(option) {
    if($('list_requried_description-wrapper')) {
      if(option == 1) {
        $('list_requried_description-wrapper').style.display='block';
      } else{
        $('list_requried_description-wrapper').style.display='none';
      }
    }
  }

  function showlocationOption(option) {

    if(option == 1) {
      if($('list_location-wrapper'))
        $('list_location-wrapper').style.display='block';
      if($('list_proximitysearch-wrapper'))
        $('list_proximitysearch-wrapper').style.display='block';
      if($('list_proximitysearch-1'))
        if($('list_proximitysearch-1').checked)
          showlocationKM(1);
      else
        showlocationKM(0);
      if($('list_location-1'))
        if($('list_location-1').checked)
          showMapOptions(1);
      else
        showMapOptions(0);
    }
    else{
      if($('list_location-wrapper'))
        $('list_location-wrapper').style.display='none';
      if($('list_proximitysearch-wrapper'))
        $('list_proximitysearch-wrapper').style.display='none';
      if($('list_proximity_search_kilometer-wrapper'))
        $('list_proximity_search_kilometer-wrapper').style.display='none';

      showMapOptions(0);
    }
  }
  
	//HERE WE CREATE A FUNCTION FOR SHOWING THE LOCATION IN KM OR MILES
	function showlocationKM(option) {
		if($('list_proximity_search_kilometer-wrapper')) {
			if(option == 1) { 
						$('list_proximity_search_kilometer-wrapper').style.display='block';	
			}else{
					$('list_proximity_search_kilometer-wrapper').style.display='none';
			}
		}
	}

	//HERE WE CREATE A FUNCTION FOR SHOWING BOUNCING
	function showMapOptions(option) {
		if($('list_location-wrapper')) {
			if(option == 1) {
           if($('list_map_sponsored-wrapper'))
						$('list_map_sponsored-wrapper').style.display='block';
           if($('list_map_zoom-wrapper'))
						$('list_map_zoom-wrapper').style.display='block';
           if($('list_map_city-wrapper'))
						$('list_map_city-wrapper').style.display='block';
			}
			else{
         if($('list_map_sponsored-wrapper'))
					$('list_map_sponsored-wrapper').style.display='none';
         if($('list_map_zoom-wrapper'))
					$('list_map_zoom-wrapper').style.display='none';
         if($('list_map_city-wrapper'))
					$('list_map_city-wrapper').style.display='none';
			}
		}
	}

	function showDefaultNetwork(option) {
		if($('list_default_show-wrapper')) {
			if(option == 0) {
				$('list_default_show-wrapper').style.display='block';
			}
			else{
				$('list_default_show-wrapper').style.display='none';
			}
		}
	}

  function showExpiryDuration(option) {
    if($('list_expirydate_duration-wrapper')) {
      if(option == 2) {
        $('list_expirydate_duration-wrapper').style.display='block';
      }else{
        $('list_expirydate_duration-wrapper').style.display='none';
      }
    }
  }
</script>
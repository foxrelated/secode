<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _composerCheckin.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headTranslate(array('Share Location', 'Where are you?','in','at'));
 $sitetagcheckin_status_update = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.status.update', '1');
 if ((empty($this->isAFFWIDGET) &&  empty($this->isAAFWIDGETMobile)) || empty($sitetagcheckin_status_update)):
  return;
endif;

//GET API KEY
$language = $_COOKIE['en4_language'];
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')        
				->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooVerticalScroll.js')
        ->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/scripts/composer.js');
    if (empty($this->isAAFWIDGETMobile)):
    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/scripts/composer_checkin.js');
  else:
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/scripts/mobile_composer_checkin.js');
  endif;
 
?>
<script type="text/javascript">
  en4.core.runonce.add(function() {
   //new Asset.javascript('');
    composeInstance.addPlugin(new ComposerCheckin.Plugin.STCheckin({
      title : '<?php echo $this->string()->escapeJavascript($this->translate('Share Location')) ?>',
      enabled: true,
      allowEmpty : <?php echo (!$this->subject() || $this->viewer()->isSelf($this->subject()))? 1 : 0; ?>,
//      chekin_url:en4.core.baseUrl+'sitetagcheckin/checkin/search/subject_guid/'+'//<?php //echo $this->subject() ? $this->subject()->getGuid() : '0'; ?>',     
      lang : {
        'Share location' : '<?php echo $this->string()->escapeJavascript($this->translate('Share location')) ?>',
        'Where are you?' : '<?php echo $this->string()->escapeJavascript($this->translate('Where are you?')) ?>',
        'in' : '<?php echo $this->string()->escapeJavascript($this->translate('in')) ?>',
        'at' : '<?php echo $this->string()->escapeJavascript($this->translate('at')) ?>',
        'Enter the location:':'<?php echo $this->string()->escapeJavascript($this->translate('Enter the location:')) ?>'
      },
      suggestOptions : {
        'url' : en4.core.baseUrl+'sitetagcheckin/checkin/suggest',
        'data' : {
          'format' : 'json'
        }
      }
    }));
  });
  function initCheckinSitetag() {}
</script>
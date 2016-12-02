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
<script type="text/javascript">
var ADD_LOCATION_TRANSLATE="<?php echo $this->string()->escapeJavascript($this->translate("Add Location")); ?>";
var EDIT_LOCATION_TRANSLATE="<?php echo $this->string()->escapeJavascript($this->translate("Edit Location")); ?>";
var WHERE_WAS_THIS_PHOTO_TAKEN="<?php echo $this->string()->escapeJavascript($this->translate("Where was this photo taken?")); ?>";
var SAVE="<?php echo $this->string()->escapeJavascript($this->translate("Save")); ?>";
var CANCEL="<?php echo $this->string()->escapeJavascript($this->translate("Cancel")); ?>";
</script>

<?php  
	$this->headTranslate(array('ADD_LOCATION_TRANSLATE', 'EDIT_LOCATION_TRANSLATE', 'WHERE_WAS_THIS_PHOTO_TAKEN', 'SAVE', 'CANCEL'));
?>

<?php $subject = $this->subject->getGuid();?>

<?php
   $this->headLink()
   ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css');
	 $this->headScript()
         ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooVerticalScroll.js');
?>

<?php
   $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
   $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<?php if($this->resource_type == 'user') :?>
	<?php 
	$this->headScript()
					->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/scripts/core_status_update.js');

	?>

	<script type="text/javascript">
		function tagAutosuggestSTMap() {
			var tagAutoSuggestMap = new TagAutoSuggestionMap({
				checkInOptions : {
					'previousLocation' : '',
					'tagParams' : 0,
					'linkDisplay' : '',
					'locationDiv' : '<?php echo $this->locationDiv; ?>',
					'subject': '<?php echo $this->subject; ?>',
					'showSuggest' : '<?php echo $this->showSuggest; ?>',
					'displayLocation' : '',
					'content_page_id' : 0,
					'content_business_id' : 0,
          'content_group_id' : 0,
          'content_store_id' : 0,
					'content_event_id' : 0
				},
		  });
		}

		if(typeof photoLightbox != 'undefined' || (typeof is_location_ajax != 'undefined' && is_location_ajax == 1)) {
			en4.core.runonce.add(function()
			{
				var cssUrl = "<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css' ?>";
				new Asset.css(cssUrl);
				if(!(typeof TagAutoSuggestionMap == 'function')){
					new Asset.javascript( en4.core.staticBaseUrl+'application/modules/Sitetagcheckin/externals/scripts/core_status_update.js',{
						onLoad :tagAutosuggestSTMap
					});
				} else {
					tagAutosuggestSTMap();
				}
			});
		} else {
			tagAutosuggestSTMap();
		}

  function initSitetagging() {}

	</script>
<?php else:?>

	<?php
		$this->headScript()
					->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/scripts/core.js');
  
    $is_mobile = Engine_Api::_()->seaocore()->isMobile();
		if($this->displayLocation == 1):
			if($this->checkin['vicinity']) {
				if(isset($this->checkin['name']) && $this->checkin['name'] && $this->checkin['name'] != $this->checkin['vicinity']) {
					$this->checkin['label'] = $this->checkin['name'] . ', ' . $this->checkin['vicinity'];
				} else {
					$this->checkin['label'] = $this->checkin['vicinity'];
				}
			}
      if(!$is_mobile) {
				$this->displayLocation = $this->addprefix . " " . $this->htmlLink($this->url(array('guid' => "activity_action_$this->action_id",'format'=>'smoothbox'), 'sitetagcheckin_viewmap', true), $this->checkin['label'], array( 'onclick' => 'showCheckinSmoothbox(this);return false;'));
			} else {
				$this->displayLocation = $this->addprefix . " " . $this->htmlLink($this->url(array('guid' => "activity_action_$this->action_id"), 'sitetagcheckin_viewmap', true), $this->checkin['label']);
			}
			endif;
	
	?>

	<script type="text/javascript">
		function showCheckinSmoothbox(element) {
			Smoothbox.open(element.href);
			return false;
		}

		function tagAutosuggestST() {
			var tagAutoSuggest = new TagAutoSuggestion({
				checkInOptions : {
					'previousLocation' : '<?php echo !empty($this->getLocation) ? $this->getLocation : '' ?>',
					'tagParams' : <?php echo!empty($this->params['checkin']) ? json_encode($this->params['checkin']) : 0 ?>,
					'linkDisplay' : '<?php echo $this->linkDisplay; ?>',
					'locationDiv' : '<?php echo $this->locationDiv; ?>',
					'subject': '<?php echo $subject; ?>',
					'displayLocation' : '<?php echo $this->displayLocation; ?>',
					'content_page_id' : '<?php echo $this->content_page_id;?>',
					'content_business_id' : '<?php echo $this->content_business_id;?>',
          'content_group_id' : '<?php echo $this->content_group_id;?>',
          'content_store_id' : '<?php echo $this->content_store_id;?>',
					'content_event_id' : '<?php echo $this->content_event_id;?>'
				},
			});
		}

		if(typeof photoLightbox != 'undefined' || (typeof is_location_ajax != 'undefined' && is_location_ajax == 1)) {
			en4.core.runonce.add(function()
			{
				var cssUrl = "<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css' ?>";
				new Asset.css(cssUrl);
				if(!(typeof TagAutoSuggestion == 'function')){
					new Asset.javascript( en4.core.staticBaseUrl+'application/modules/Sitetagcheckin/externals/scripts/core.js',{
						onLoad :tagAutosuggestST
					});
				} else {
					tagAutosuggestST();
				}
			});
		} else {
			tagAutosuggestST();
		}

		function initSitetagging() {}

	</script>
<?php endif;?>
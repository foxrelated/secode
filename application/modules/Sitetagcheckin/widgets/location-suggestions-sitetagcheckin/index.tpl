<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
   $this->headLink()
   ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css');
?>
<?php
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

<div class="stcheckin_suggest_location" id="stcheckin_suggest_location">
  <div id="album-no-location">
		<?php $album_id = $this->albumrow->album_id; ?>
		<div class="stcheckin_suggest_location_title">
			<?php echo $this->htmlLink($this->albumrow->getHref(), $this->string()->truncate($this->albumrow->getTitle(), 25), array( 'title' => $this->albumrow->getTitle())) ?>
		</div>
		<div class="stcheckin_suggest_location_photo">
			<?php echo $this->htmlLink($this->albumrow->getHref(), $this->itemPhoto($this->albumrow, 'thumb.normal'), array('class' => 'thumb', 'title' => $this->albumrow->getTitle())) ?>
		</div>
		<div class="stcheckin_suggest_location_cont">
			<div id="show-non-photo" style="display:block;">
				<?php if(empty($this->photorow)):?>
					<?php echo $this->translate('Where were photos in this album taken?');?>
				<?php else:?>
					<?php echo $this->translate("Were photos in this album taken $this->displayLocation.");?>
				<?php endif;?>
			</div>
	    <?php if(!empty($this->photorow)):?>
	      <div id="show_album_suggest_location"></div>
				<div id="button-yes-no">
					<button onclick="showLocationSuggestion(1);"><?php echo $this->translate("Yes");?></button>
					<button onclick="showLocationSuggestion(0);"><?php echo $this->translate("No");?></button>
				</div>
	    <?php endif;?>
	  </div>  
		<div class="seaotagalbumsuggestlocation" id="seaocore_suggest_location" style="display;none;">
			<?php
				//RENDER LOCATION WIDGET
				echo $this->content()->renderWidget("sitetagcheckin.location-sitetagcheckin", array('showSuggest'=> 1, 'album_suggest_id' => $this->albumrow->album_id)); 
			?>
		</div>
	</div>
  <div id="showbackground-image" style="display:none;" class="clr"></div>
</div>


<script type="text/javascript">

window.addEvent('domready', function() {
  <?php if(empty($this->photorow)):?>
    $('seaocore_suggest_location').style.display = "block";
  <?php else:?>
    $('seaocore_suggest_location').style.display = "none";
  <?php endif;?>
});

function showLocationSuggestion(option) {
  if(option == 1) {
    $('showbackground-image').style.display = "block";
    $('showbackground-image').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitetagcheckin/externals/images/loading.gif" alt="Loading" /></center>';
    $('seaocore_suggest_location').style.display = "none";
 		  var request = new Request.JSON({
				url:  en4.core.baseUrl + 'sitetagcheckin/index/save-location/',
        method: 'post',
				'data' : {
					'subject': '<?php echo "album_$album_id"; ?>',
          'checkin' : <?php echo Zend_Json::encode($this->params['checkin']) ?>,
          'location' : '<?php echo $this->getLocation ?>',
          'content_page_id': 0,
          'content_business_id':0,
          'content_group_id':0,
          'content_store_id' : 0,
					'content_event_id' : 0,
					'format' : 'html',
          'isajax' : 1
				},
				//responseTree, responseElements, responseHTML, responseJavaScript
				onSuccess :  function(responseJSON) {
           if($('button-yes-no'))
           $('button-yes-no').style.display="none";
           if($('show_album_suggest_location'))
           $('show_album_suggest_location').innerHTML = responseJSON.displayLocationWithUrl;
           $('showbackground-image').innerHTML = '';
           $('showbackground-image').style.display = "none";
           $('show-non-photo').style.display = "none";
				  Smoothbox.bind($("stcheckin_suggest_location"));
					en4.core.runonce.trigger();
				}
			});
		request.send();   
  } else {
    $('seaocore_suggest_location').style.display = "block";
    $('show-non-photo').style.display = "block";
    $('show-non-photo').innerHTML = "Where were photos in this album taken?";
		if($('button-yes-no'))
			$('button-yes-no').style.display="none";
  }
}

</script>


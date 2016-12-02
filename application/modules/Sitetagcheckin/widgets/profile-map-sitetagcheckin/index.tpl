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
   $this->headLink()
   ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css');
?>
<?php $advancedActivity = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
	if($advancedActivity):
			$this->headLink()
			->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Advancedactivity/externals/styles/style_advancedactivity.css');
	endif;
?>

<?php
	//GET API KEY
	$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
  $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/scripts/activity_core.js');
?>

<script type="text/javascript">
  var is_active_req_map = false;
	function showFeeds() {

    if($('mapsitetagcheckin_map'))
		$('mapsitetagcheckin_map').style.display = "none";
    if($('seaocore_map_info'))
		$('seaocore_map_info').style.display = "none";
    if($('seaocore_profile_fields'))
		$('seaocore_profile_fields').style.display = "none";

		$('feed_items').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/loading.gif" alt="" style="margin:50px 0;" /></center>';
		var url = '<?php echo $this->url(array('action' => 'get-feed-items'), 'sitetagcheckin_general', true);?>';

    is_active_req_map = true;
		en4.core.request.send(new Request.HTML({
			url : url,
			data : {
				format : 'html',
				subject:  en4.core.subject.guid,
				show_map: 0,
				is_ajax: 0,
        content_feeds: 1
			},
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if($('mapsitetagcheckin_map'))	
				$('mapsitetagcheckin_map').style.display = "none";
				$('feed_items').innerHTML = responseHTML;
        is_active_req_map = false;
				Smoothbox.bind($("feed_items"));
				en4.core.runonce.trigger();
			}
		}), {"force":true});

	}

	function showMap() {
    if(is_active_req_map) return;
    if($('mapsitetagcheckin_map'))	
		$('mapsitetagcheckin_map').style.display = "block";
    if($('seaocore_map_info'))
		$('seaocore_map_info').style.display = "block";
    if($('seaocore_profile_fields'))
		$('seaocore_profile_fields').style.display = "block";
		$('feed_items').empty();
	}

</script>

<?php if($this->checkin_show_options == 2):?>
	<div class="stcheckin_profile_header">
		<div class="stcheckin_profile_header_right">
			<div onclick="showFeeds();" id="display_feedlinks" class="stcheckin_profile_buttons stcheckin_tip">
				<div class="stcheckin_tip_content"><?php echo $this->translate("View Feeds");?></div>
				<!--<img src="./application/modules/Sitetagcheckin/externals/images/list-view.png" alt="" />-->
                <span class="seaocore_tab_icon tab_icon_list_view" onclick="switchview(0);" ></span>
			</div>
			<div onclick="showMap();" id="display_maplinks" class="stcheckin_profile_buttons stcheckin_tip">
				<div class="stcheckin_tip_content"><?php echo $this->translate("View Map");?></div>
				<!--<img src="./application/modules/Sitetagcheckin/externals/images/map-view.png" alt="" />-->
                <span class="seaocore_tab_icon tab_icon_map_view" onclick="switchview(2);" ></span>
			</div>
		</div>
	</div>
<?php endif;?>

<div id="feed_items"></div>

<?php if($this->checkin_show_options == 1  || $this->checkin_show_options == 2):?>
	<div id="mapsitetagcheckin_map" class="seaocheckinmaparea">
   <div class="seaocore_map">
		<div class="seaocheckinmap" id="mapsitetagcheckin_profile_map_canvas" style="height:<?php echo $this->checkin_map_height;?>px;"></div>
		<?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
		<?php if (!empty($siteTitle)) : ?>
			<div class="seaocore_map_info" id="seaocore_map_info" style="display:none;">
        <?php echo 'Locations on '; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a>
      </div>
		<?php endif; ?>
  </div> 
 </div>

<?php if(!empty($this->locationInformation['location'])) :?>
	<div class='profile_fields' id="seaocore_profile_fields" style="display:none;">
		<h4>
			<span><?php echo $this->translate('Location Information') ?></span>
		</h4>
		<ul>
			<li>
				<span><?php echo $this->translate('Location:'); ?> </span>
				<span>
 					<b><?php echo $this->locationInformation['location']; ?></b> 
					<?php $subjectType = $this->subject->getType();?>
          <?php if($subjectType == 'event' || $subjectType == 'group') :?>
  				- <b><?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->locationInformation['location_id'], 'resouce_type' => $this->subject->getType()), $this->translate("Get Directions"), array('onclick' => 'showlightbox(this);return false')) ; ?></b>
          <?php elseif(in_array($subjectType, array('sitepage_page', 'sitebusiness_business', 'list_listing', 'recipe', 'sitegroup_group', 'sitestore_store', 'siteevent_event'))) :?>
					- <b><?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->subject->getIdentity(), 'resouce_type' => $this->subject->getType()), $this->translate("Get Directions"), array('onclick' => 'showlightbox(this);return false')) ; ?></b>
					<?php endif;?>
			  </span>
			</li>
			<?php if(!empty($this->locationInformation['formatted_address'])):?>
				<li>
					<span><?php echo $this->translate('Formatted Address:'); ?> </span>
					<span><?php echo $this->locationInformation['formatted_address']; ?> </span>
				</li>
			<?php endif; ?>
			<?php if(!empty($this->locationInformation['address'])):?>
				<li>
					<span><?php echo $this->translate('Street Address:'); ?> </span>
					<span><?php echo $this->locationInformation['address'];  ?> </span>
				</li>
			<?php endif; ?>
			<?php if(!empty($this->locationInformation['city'])):?>
				<li>
					<span><?php echo $this->translate('City:'); ?></span>
					<span><?php echo $this->locationInformation['city']; ?> </span>
				</li>
			<?php endif; ?>
			<?php if(!empty($this->locationInformation['zipcode'])):?>
				<li>
					<span><?php echo $this->translate('Zipcode:'); ?></span>
					<span><?php echo $this->locationInformation['zipcode']; ?> </span>
				</li>
			<?php endif; ?>
			<?php if(!empty($this->locationInformation['state'])):?>
				<li>
					<span><?php echo $this->translate('State:'); ?></span>
					<span><?php echo $this->locationInformation['state']; ?></span>
				</li>
			<?php endif; ?>
			<?php if(!empty($this->locationInformation['country'])):?>
				<li>
					<span><?php echo $this->translate('Country:'); ?></span>
					<span><?php echo $this->locationInformation['country']; ?></span>
				</li>
			<?php endif; ?>
		</ul>
	</div>
	<?php endif;?>
<?php endif;?>

<?php $subject = $this->subject; ?>
<?php if($this->checkin_show_options == 1 || $this->checkin_show_options == 2):?>
<script type="text/javascript">

  //GLOBAL "MAP" VARIABLE
  var mapCheckin = null;

  function initializeProfileMap() { 
    var latlng = new google.maps.LatLng('<?php echo $this->locationInformation["latitude"];?>', '<?php echo $this->locationInformation["longitude"];?>');

    //CREATE THE MAP
    var myOptions = {
      zoom: <?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('sitetagcheckin.map.zoom', 2)?>,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    mapCheckin = new google.maps.Map(document.getElementById("mapsitetagcheckin_profile_map_canvas"),
    myOptions);

    var contentString = '<div id="content">'+
      '<div id="siteNotice">'+
      '</div>'+'<div class="stcheckin_map_location">'+
			'<div class="stcheckin_map_location_title">'+
	    	"<?php echo $this->string()->escapeJavascript($subject->getTitle())?>"+
        '<div class="clr"></div>'+
      '</div>'+
      '<div class="stcheckin_map_location_photo" >'+
    		'<?php echo  $this->itemPhoto($subject, 'thumb.normal') ?>'+
	    '</div>'+ 
      '<div class="stcheckin_map_location_info">'+
        <?php if(0):?>
          '<div class="stcheckin_map_location_stat">'+
            '<?php echo $this->timestamp(strtotime($subject->creation_date)) ?> - <?php echo $this->string()->escapeJavascript($this->translate('posted by')); ?> '+
            '<?php echo $this->htmlLink($subject->getOwner()->getHref(), $this->string()->escapeJavascript($subject->getOwner()->getTitle())) ?>'+
          '</div>'+
        <?php endif; ?>
        '<div class="stcheckin_map_location_stat ">'
           <?php if(isset($subject->comment_count)):?>
	      	+'<?php echo $this->string()->escapeJavascript($this->translate(array('%s comment', '%s comments', $subject->comment_count), $this->locale()->toNumber($subject->comment_count))) ?>,&nbsp;'
          <?php endif;?>  
          
           <?php if(isset($subject->view_count)):?>+
	        '<?php echo $this->string()->escapeJavascript($this->translate(array('%s view', '%s views', $subject->view_count), $this->locale()->toNumber($subject->view_count))) ?>'+<?php endif;?>
		    '</div>'+
 		  '</div>'+
 		  '<div class="clr"></div>'+
	 ' </li></ul>'+
      '</div>';

    var infowindow = new google.maps.InfoWindow({
      content: contentString,
      size: new google.maps.Size(250,50)
    });

		var markerCheckin = new google.maps.Marker({
			position: latlng,
			map: mapCheckin,
			title: '<?php echo $this->locationInformation["location"]?>'
		});

    google.maps.event.addListener(markerCheckin, 'click', function() {
      infowindow.open(mapCheckin,markerCheckin);
    });

    google.maps.event.addListener(mapCheckin, 'click', function() {
      infowindow.close();
			google.maps.event.trigger(mapCheckin, 'resize');
      mapCheckin.setZoom(<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('sitetagcheckin.map.zoom', 2)?>);
      mapCheckin.setCenter(latlng);
    });
    
    if($$('.tab_layout_sitetagcheckin_profile_map_sitetagcheckin'))
		$$('.tab_layout_sitetagcheckin_profile_map_sitetagcheckin').addEvent('click', function() {
      showMap();
			google.maps.event.trigger(mapCheckin, 'resize');
			mapCheckin.setZoom(<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('sitetagcheckin.map.zoom', 2)?>);
			mapCheckin.setCenter(latlng);
		});
   
    if($('seaocore_map_info'))
    $('seaocore_map_info').style.display ="block";

    if($('seaocore_profile_fields'))
    $('seaocore_profile_fields').style.display ="block";
  }

function showlightbox(thisobj) {
	var Obj_Url = thisobj.href;
	Smoothbox.open(Obj_Url);
}
</script>

</script>

<?php endif;?>

<script type="text/javascript">

  window.addEvent('load',function() {
    <?php if($this->checkin_show_options == 1 || $this->checkin_show_options == 2):?>
      initializeProfileMap();
    <?php elseif($this->checkin_show_options == 0):?>
       showFeeds();
    <?php endif;?>
  });

</script>
<style type="text/css">
.profile_fields > ul > li > span + span{width:auto !important;}
</style>
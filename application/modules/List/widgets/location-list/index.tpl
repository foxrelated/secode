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
  $this->headLink()
  	->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/List/externals/styles/list_tooltip.css');
?>

<?php $this->headScript()->appendFile("https://maps.google.com/maps/api/js?sensor=false"); ?>

<script type="text/javascript">
  var myLatlng;
  function initialize() {
    var myLatlng = new google.maps.LatLng(<?php echo $this->location->latitude; ?>,<?php echo $this->location->longitude; ?>);
    var myOptions = {
      zoom: <?php echo $this->location->zoom; ?> ,
      center: myLatlng,
      navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    var map = new google.maps.Map(document.getElementById("list_view_map_canvas"), myOptions);

    var contentString = '<div id="content">'+
      '<div id="siteNotice">'+
      '</div>'+'<ul class="lists_locationdetails"><li>'+
			'<div class="lists_locationdetails_info_title">'+
	    	"<?php echo $this->string()->escapeJavascript($this->list->getTitle())?>"+
        '<div class="floatR">'+
          '<span >'+
            <?php if ($this->list->featured == 1): ?>
	            '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/list_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Featured')))) ?>'+	            <?php endif; ?>
          '</span>'+
          '<span>'+
            <?php if ($this->list->sponsored == 1): ?>
	            '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>'+
          	<?php endif; ?>
          '</span>'+
        '</div>'+
        '<div class="clear"></div>'+
      '</div>'+
      '<div class="lists_locationdetails_photo" >'+
    		'<?php echo  $this->itemPhoto($this->list, 'thumb.normal') ?>'+
	    '</div>'+ 
      '<div class="lists_locationdetails_info">'+
        <?php if (($this->list->rating > 0) && $this->ratngShow): ?>
        	'<span style="clear:both;" title="<?php echo $this->list->rating.$this->translate(' rating'); ?>">'+
          	<?php for ($x = 1; $x <= $this->list->rating; $x++): ?>
         			'<span class="rating_star_generic rating_star" ></span>'+
          	<?php endfor; ?>
        		<?php if ((round($this->list->rating) - $this->list->rating) > 0): ?>
        			'<span class="rating_star_generic rating_star_half"></span>'+
        		<?php endif; ?>
	        '</span>'+
	      <?php endif; ?>

        '<div class="lists_locationdetails_info_date">'+
          '<?php echo $this->timestamp(strtotime($this->list->creation_date)) ?> - <?php echo $this->string()->escapeJavascript($this->translate('posted by')); ?> '+
          '<?php echo $this->htmlLink($this->list->getOwner()->getHref(), $this->string()->escapeJavascript($this->list->getOwner()->getTitle())) ?>'+
        '</div>'+

        '<div class="lists_locationdetails_info_date">'+
	      	'<?php echo $this->string()->escapeJavascript($this->translate(array('%s comment', '%s comments', $this->list->comment_count), $this->locale()->toNumber($this->list->comment_count))) ?>,&nbsp;'+
	        '<?php echo $this->string()->escapeJavascript($this->translate(array('%s view', '%s views', $this->list->view_count), $this->locale()->toNumber($this->list->view_count))) ?>'+
		    '</div>'+
			'<div class="lists_locationdetails_info_date">'+
				"<i><b>"+"<?php echo $this->string()->escapeJavascript( $this->location->location); ?>"+ "</b></i>"+
	      '</div>'+
        
 		  '</div>'+
 		  '<div class="clear"></div>'+
	 ' </li></ul>'+

      '</div>';
   
    var infowindow = new google.maps.InfoWindow({
      content: contentString ,
      size: new google.maps.Size(250,50)

    });

    var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
			title: "<?php echo str_replace('"', ' ',$this->list->getTitle())?>"
   
    });

    google.maps.event.addListener(marker, 'click', function() {
      infowindow.open(map,marker);
    });

    $$('.tab_layout_list_location_list').addEvent('click', function() {
			google.maps.event.trigger(map, 'resize');
			map.setZoom(<?php echo $this->location->zoom; ?> );
			map.setCenter(myLatlng);
    });

    google.maps.event.addListener(map, 'click', function() {
      infowindow.close();
			google.maps.event.trigger(map, 'resize');
      map.setZoom(<?php  echo $this->location->zoom; ?> );
      map.setCenter(myLatlng);
    });
  }
</script>

<div class='profile_fields'>
	<ul class="advlist_profile_location">
		<li>
			<div id="list_view_map_canvas"></div>
		</li>
	</ul>
	<h4>
		<span><?php echo$this->translate('Location Information') ?></span>
	</h4>
  <ul>
		<li>
			<span><?php echo $this->translate('Location:'); ?> </span>
			<span><b><?php echo  $this->location->location; ?> </b></span>
		</li>

		<?php if(!empty($this->location->formatted_address)):?>
			<li>
				<span><?php echo $this->translate('Formatted Address:'); ?> </span>
				<span><?php echo $this->location->formatted_address; ?> </span>
			</li>
		<?php endif; ?>

		<?php if(!empty($this->location->address)):?>
			<li>
				<span><?php echo $this->translate('Street Address:'); ?> </span>
				<span><?php echo $this->location->address; ?> </span>
			</li>
		<?php endif; ?>

			<?php if(!empty($this->location->city)):?>
			<li>
				<span><?php echo $this->translate('City:'); ?></span>
				<span><?php echo $this->location->city; ?> </span>
			</li>
		<?php endif; ?>

			<?php if(!empty($this->location->zipcode)):?>
			<li>
				<span><?php echo $this->translate('Zipcode:'); ?></span>
				<span><?php echo $this->location->zipcode; ?> </span>
			</li>
		<?php endif; ?>

			<?php if(!empty($this->location->state)):?>
			<li>
				<span><?php echo $this->translate('State:'); ?></span>
				<span><?php echo $this->location->state; ?></span>
			</li>
			<?php endif; ?>

			<?php if(!empty($this->location->country)):?>
			<li>
				<span><?php echo $this->translate('Country:'); ?></span>
				<span><?php echo $this->location->country; ?></span>
			</li>
		<?php endif; ?>
	</ul>
</div>

<script type="text/javascript">
  window.addEvent('domready',function(){
    initialize();
  });
</script>
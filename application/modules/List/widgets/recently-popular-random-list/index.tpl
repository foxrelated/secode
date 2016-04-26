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
  	
	$baseUrl = $this->layout()->staticBaseUrl;
	$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/List/externals/styles/style_list.css');
?>
<?php $enableBouce=$this->settings->getSetting('list.map.sponsored', 1); ?>
<script type="text/javascript">
var url = '<?php echo $this->url(array('action' => 'ajaxhomelist'), 'list_general', true) ?>';
</script>

<div class="layout_core_container_tabs">
<div class="tabs_alt tabs_parent">
  <ul id="main_tabs">
    <?php if ($this->tab1_show == 1): ?>
      <?php if ($this->active_tab1 == 1): ?>
        <li class = 'active' id = 'list_home_page_tab1' >
      <?php else : ?>
        <li class = '' id = 'list_home_page_tab1' >
      <?php endif; ?>
	      <?php
	      if ($this->tab2_show == 1 || $this->tab3_show == 1 || $this->tab4_show == 1 || $this->tab5_show == 1): ?>
	        <a href='javascript:void(0);'  onclick="showListListing('Recently Posted')"><?php echo $this->translate("Recent") ?></a>
	     <?php elseif ($this->tab2_show != 1 && $this->tab3_show != 1 && $this->tab4_show != 1 && $this->tab5_show != 1):
	        echo $this->translate("Recent");
	      endif;
	      ?>
      </li>
    <?php endif; ?>
    <?php if ($this->tab2_show == 1): ?>
      <?php if ($this->active_tab2 == 1): ?>
      <li class = 'active' id = 'list_home_page_tab2' >
      <?php else : ?>
      <li class = '' id = 'list_home_page_tab2' >
      <?php endif; ?>
        <?php
        if (( $this->tab1_show == 1 || $this->tab3_show == 1 ||$this->tab4_show == 1) || $this->tab5_show == 1): ?>
         <a href='javascript:void(0);' onclick="showListListing('Most Viewed')"> <?php echo $this->translate("Most Popular"); ?></a>
      <?php  elseif ($this->tab1_show != 1 && $this->tab3_show != 1 && $this->tab4_show != 1 && $this->tab5_show != 1):
          echo $this->translate("Most Popular");
        endif;
        ?>
      </li>
    <?php endif; ?>
    <?php if ($this->tab3_show == 1): ?>
      <?php if ($this->active_tab3 == 1) : ?>
      <li class = 'active' id = 'list_home_page_tab3' >
      <?php else : ?>
      <li class = '' id = 'list_home_page_tab3' >
      <?php endif; ?>
        <?php
        if ($this->tab2_show == 1 || $this->tab1_show == 1 || $this->tab4_show == 1 || $this->tab5_show == 1): ?>
          <a href='javascript:void(0);' onclick="showListListing('Random')"> <?php echo  $this->translate("Random"); ?></a>
       <?php elseif ($this->tab2_show != 1 && $this->tab1_show != 1 && $this->tab4_show != 1 && $this->tab5_show != 1):
          echo $this->translate("Random");
        endif;
        ?>
      </li>
    <?php endif; ?>
      <?php if ($this->tab4_show == 1): ?>
      <?php if ($this->active_tab4 == 1) : ?>
      <li class = 'active' id = 'list_home_page_tab4' >
      <?php else : ?>
      <li class = '' id = 'list_home_page_tab4' >
      <?php endif; ?>
        <?php
        if ($this->tab2_show == 1 || $this->tab1_show == 1 || $this->tab3_show == 1 || $this->tab5_show == 1): ?>
          <a href='javascript:void(0);' onclick="showListListing('Featured')"> <?php echo  $this->translate("Featured"); ?></a>
       <?php elseif ($this->tab2_show != 1 && $this->tab1_show != 1 && $this->tab3_show != 1 &&  $this->tab5_show != 1 ):
          echo $this->translate("Featured");
        endif;
        ?>
      </li>
    <?php endif; ?>
      <?php if ($this->tab5_show == 1): ?>
      <?php if ($this->active_tab5 == 1) : ?>
      <li class = 'active' id = 'list_home_page_tab5' >
      <?php else : ?>
      <li class = '' id = 'list_home_page_tab5' >
      <?php endif; ?>
        <?php
        if ($this->tab2_show == 1 || $this->tab1_show == 1 || $this->tab3_show == 1 || $this->tab4_show == 1): ?>
          <a href='javascript:void(0);' onclick="showListListing('Sponosred')"> <?php echo  $this->translate("Sponsored"); ?></a>
       <?php elseif ($this->tab2_show != 1 && $this->tab1_show != 1 && $this->tab3_show != 1 && $this->tab4_show != 1):
          echo $this->translate("Sponsored");
        endif;
        ?>
      </li>
    <?php endif; ?>
    <?php if (($this->list_view && $this->grid_view) || ($this->map_view && $this->grid_view) || ($this->list_view && $this->map_view)): ?>
    <?php  if( $this->enableLocation && $this->map_view): ?>
        <?php  $latitude = $this->settings->getSetting('list.map.latitude', 0); ?>
        <?php  $longitude = $this->settings->getSetting('list.map.longitude', 0); ?>
        <?php  $defaultZoom = $this->settings->getSetting('list.map.zoom', 1); ?>
	    <li class="list_show_tooltip_wrapper floatR">
				<div class="list_show_tooltip"><?php echo $this->translate("Map View") ?></div>
      	<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/List/externals/images/map_icon.png' onclick="rswitchview(2)" align="left" alt="" class="select_view" />
	    </li>
    <?php endif;?>
    <?php  if( $this->grid_view): ?>
    <li class="list_show_tooltip_wrapper floatR">
			<div class="list_show_tooltip"><?php echo $this->translate("Grid View") ?></div>
      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/List/externals/images/grid.png' onclick="rswitchview(1)" align="left" alt="" class="select_view" />
    </li>
    <?php endif;?>
    <?php  if( $this->list_view): ?>
    <li class="list_show_tooltip_wrapper floatR">
      <div class="list_show_tooltip"><?php echo $this->translate("List View") ?></div>
      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/List/externals/images/list.png' onclick="rswitchview(0)" align="left" alt="" class="select_view" />
    </li>
    <?php endif;?>
   <?php endif;?>
  </ul>
</div>
<div id="dynamic_app_info">
<?php
  include_once APPLICATION_PATH . '/application/modules/List/views/scripts/_recently_popular_random_list.tpl';
?>
 </div>
</div>



<script type="text/javascript" >
	function rswitchview(flage){
	if(flage==2){
      if($('rmap_canvas_view')){
			$('rmap_canvas_view').style.display='block';
			google.maps.event.trigger(rmap, 'resize');
			rmap.setZoom( <?php echo $defaultZoom?>);
			rmap.setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude?>));
      }
			if($('rgrid_view'))
      $('rgrid_view').style.display='none';
     if($('rimage_view'))
		$('rimage_view').style.display='none';
		}else if(flage==1){
     if($('rmap_canvas_view'))
		$('rmap_canvas_view').style.display='none';
    if($('rgrid_view'))
		$('rgrid_view').style.display='none';
    if($('rimage_view'))
		$('rimage_view').style.display='block';
		}else{
    if($('rmap_canvas_view'))
		$('rmap_canvas_view').style.display='none';
    if($('rgrid_view'))
		$('rgrid_view').style.display='block';
    if($('rimage_view'))
		$('rimage_view').style.display='none';
		}
	}
</script>
<script type="text/javascript">

/* moo style */
window.addEvent('domready',function() {
	if($('rimage_view')){
  showtooltip();
  }
  
   <?php if( $this->enableLocation && $this->map_view): ?>  
    rinitialize();
    <?php endif; ?>

 rswitchview(<?php echo $this->defaultView ?>);

    $$('.tab_layout_list_recently_popular_random_list').addEvent('click', function() {
      
    	google.maps.event.trigger(rmap, 'resize');
    	rmap.setZoom( <?php echo $defaultZoom ?>);
			rmap.setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));    
    });

});

var showtooltip = function (){
 
  //opacity / display fix
	$$('.list_tooltip').setStyles({
		opacity: 0,
		display: 'block'
	});
	//put the effect in place
	$$('.jq-list_tooltip li').each(function(el,i) {
		el.addEvents({
			'mouseenter': function() {
				el.getElement('div').fade('in');
			},
			'mouseleave': function() {
				el.getElement('div').fade('out');
			}
		});
	});
  
}
</script>

<script type="text/javascript">

   var showListListing = function (tabshow) {

    if (tabshow == "Recently Posted") {
      if($('list_home_page_tab2'))
        $('list_home_page_tab2').erase('class');
      if($('list_home_page_tab3'))
        $('list_home_page_tab3').erase('class');
       if($('list_home_page_tab4'))
        $('list_home_page_tab4').erase('class');
        if($('list_home_page_tab5'))
        $('list_home_page_tab5').erase('class');
      if($('list_home_page_tab1'))
        $('list_home_page_tab1').set('class', 'active');

    }
    else if (tabshow == "Most Viewed") {
      if($('list_home_page_tab1'))
        $('list_home_page_tab1').erase('class');
      if($('list_home_page_tab3'))
        $('list_home_page_tab3').erase('class');
       if($('list_home_page_tab4'))
        $('list_home_page_tab4').erase('class');
        if($('list_home_page_tab5'))
        $('list_home_page_tab5').erase('class');
      if($('list_home_page_tab2'))
        $('list_home_page_tab2').set('class', 'active');

    }

    else if(tabshow == "Random") {
      if($('list_home_page_tab1'))
        $('list_home_page_tab1').erase('class');
      if($('list_home_page_tab2'))
        $('list_home_page_tab2').erase('class');
       if($('list_home_page_tab4'))
        $('list_home_page_tab4').erase('class');
        if($('list_home_page_tab5'))
        $('list_home_page_tab5').erase('class');
      if($('list_home_page_tab3'))
        $('list_home_page_tab3').set('class', 'active');


    }else if(tabshow == "Featured") {
      if($('list_home_page_tab1'))
        $('list_home_page_tab1').erase('class');
      if($('list_home_page_tab2'))
        $('list_home_page_tab2').erase('class');
       if($('list_home_page_tab3'))
        $('list_home_page_tab3').erase('class');
        if($('list_home_page_tab5'))
        $('list_home_page_tab5').erase('class');
      if($('list_home_page_tab4'))
        $('list_home_page_tab4').set('class', 'active');

    }else if(tabshow == "Sponosred") {
      if($('list_home_page_tab1'))
        $('list_home_page_tab1').erase('class');
      if($('list_home_page_tab2'))
        $('list_home_page_tab2').erase('class');
       if($('list_home_page_tab3'))
        $('list_home_page_tab3').erase('class');
        if($('list_home_page_tab4'))
        $('list_home_page_tab4').erase('class');
      if($('list_home_page_tab5'))
        $('list_home_page_tab5').set('class', 'active');

    }


   if($('dynamic_app_info') != null) {
      $('dynamic_app_info').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/List/externals/images/loader.gif" class="list_tabs_loader_img" /></center>';
    }

    var request = new Request.HTML({
      'url' : '<?php echo $this->url(array('action' => 'ajaxhomelist'), 'list_general', true) ?>',
      'data' : {
        'format' : 'html',
        'task' : 'ajax',
        'tab_show' : tabshow

      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('dynamic_app_info').innerHTML = responseHTML;

        showtooltip();
       <?php if( $this->enableLocation && $this->map_view): ?>
        rinitialize();
        <?php endif; ?>
         rswitchview(<?php echo $this->defaultView ?>);
      }
    });

    request.send();
  }
</script>
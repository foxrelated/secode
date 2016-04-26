<h3>
	<?php echo $this->translate("Result Search");
	if(!empty($this->formValues['start_date']))
	{
		echo ": ".$this->translate("From")." ";
		echo date("F j, Y", strtotime($this->formValues['start_date']));
	}
	if(!empty($this->formValues['end_date']))
	{
		echo " ".$this->translate("To")." ";
		echo date("F j, Y", strtotime($this->formValues['end_date']));
	}
	?>
</h3>
<div class="ynevent-search-result-page">
<?php $repeateType = array(
			'0' => '', 
			'1' => $this->translate('daily'), 
			'7' => $this->translate('weekly'), 
			'30' => $this->translate('monthly'));
?>
<?php if (!empty($this->message)) : ?>
	<div class="tip">
		<span><?php echo $this->translate($this->message)?></span>
	</div>	
<?php endif;?>
<?php if( count($this->paginator) > 0 ): ?>

<div id="ynevent_list_time" class="ynevent_list-view">

    <div class="ynevent-action-view-method ynclearfix"> 	
		<div class="ynevent_home_page_list_content" rel="map_view">
			<div class="ynevent_home_page_list_content_tooltip"><?php echo $this->translate('Map View')?></div>
		    <span id="map_view_<?php echo $this->identity;?>" class="ynevent_home_page_list_content_icon tab_icon_map_view" onclick="ynevent_view_map_time();">
				<i class="fa fa-map-marker"></i>
			</span>
		</div>

		<div class="ynevent_home_page_list_content" rel="grid_view">
			<div class="ynevent_home_page_list_content_tooltip"><?php echo $this->translate('Grid View')?></div>
		    <span id="grid_view_<?php echo $this->identity;?>" class="ynevent_home_page_list_content_icon tab_icon_grid_view" onclick="ynevent_view_grid_time();">
				<i class="fa fa-th"></i></span>
			</span>
		</div>
		
		<div class="ynevent_home_page_list_content" rel="list_view">
			<div class="ynevent_home_page_list_content_tooltip"><?php echo $this->translate('List View')?></div>
			<span id="list_view_<?php echo $this->identity;?>" class="ynevent_home_page_list_content_icon tab_icon_list_view" onclick="ynevent_view_list_time();">
				<i class="fa fa-th-list"></i>
			</span>
		</div>
    </div>
	
    <div class="ynevent-tabs-content ynclearfix">   
        <div class="tabcontent" style="display:block">     
    	  	<?php
    			echo $this->partial('_list_most_item.tpl', 'ynevent', array('events' => $this->paginator));
    		?>
        </div>
	   <iframe id='list-most-time-iframe'style="max-height: 500px; display: none;" > </iframe>
    </div>
</div>
  <?php if( $this->paginator->count() > 1 ): ?>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues,
    )); ?>
  <?php endif; ?>

<?php else: ?>
	<?php if (empty($this->message)) : ?>
	  <div class="tip">
		    <span>
		    <?php if ($this->is_search): ?>
		    	<?php echo $this->translate('There were no events found matching your search criteria.') ?>
		    <?php else: ?>
			    <?php if( $this->filter != "past" ): ?>
					      <?php echo $this->translate('Nobody has created an event yet.') ?>
					      <?php if( $this->canCreate ): ?>
					        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action'=>'create'), 'event_general').'">', '</a>'); ?>
					      <?php endif; ?>
			    <?php else: ?>
			      		<?php echo $this->translate('There are no past events yet.') ?>
			    <?php endif; ?>
			<?php endif; ?>
		    </span>
	  </div>
  	<?php endif;?>

<?php endif; ?>
<script type="text/javascript">
       var ynevent_view_map_time = function()
       {
       		document.getElementById('ynevent_list_time').set('class','ynevent_map-view');
       		var html =  '<?php echo $this->url(array('action'=>'display-map-view', 'ids' => $this->eventIds), 'event_general') ?>';
       		
       		document.getElementById('list-most-time-iframe').dispose();
       		
       		var iframe = new IFrame({
       			id : 'list-most-time-iframe',
       			src: html,
			    styles: {			       
			        'height': '500px',
			        'width' : '100%'
			    },
			});
       		iframe.inject($('ynevent_list_time'));
       		document.getElementById('list-most-time-iframe').style.display = 'block';
       }
       var ynevent_view_grid_time =  function()
       {
       		document.getElementById('ynevent_list_time').set('class','ynevent_grid-view');
       }  
       
        var ynevent_view_list_time = function()
       {
       		document.getElementById('ynevent_list_time').set('class','ynevent_list-view');
       }     
</script>

<script type="text/javascript">
en4.core.runonce.add(function()
{
    function setCookie(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toUTCString();
	    document.cookie = cname + "=" + cvalue + "; " + expires;
	}

	function getCookie(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0; i<ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0)==' ') c = c.substring(1);
	        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
	    }
	    return "";
	}
    
 	// Get cookie
	var myCookieViewMode = getCookie('ynevent-viewmode-cookie');
	if ( myCookieViewMode == '') 
	{
		myCookieViewMode = 'list_view';
	}
	switch(myCookieViewMode) {
	    case 'map_view':
	        ynevent_view_map_time();
	        break;
	    case 'grid_view':
	        ynevent_view_grid_time();
	        break;
	 	case 'list_view':
	        ynevent_view_list_time();
	        break;
    }
	
	// Set click viewMode
	$$('.ynevent_home_page_list_content').addEvent('click', function(){
		var viewmode = this.get('rel');
		setCookie('ynevent-viewmode-cookie', viewmode, 1);
	});
});

</script>

</div>
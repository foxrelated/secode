<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 8905 2011-04-20 01:20:01Z jung $
 * @author     John
 */
?>

<div id="ynevent_list_item_<?php echo $this -> identity;?>" class="<?php echo $this -> class_mode;?>">
  <div id="ynevent_list_item_content_<?php echo $this -> identity;?>" class="ynevent-tabs-content ynclearfix">
    <div class="ynevent-action-view-method clearfix">
      <?php if(in_array('map', $this -> mode_enabled)):?>
      <div class="ynevent_home_page_list_content_<?php echo $this -> identity;?>" rel="map_view">
	                	<span id="map_view_<?php echo $this->identity;?>" class="ynevent_home_page_list_content_icon" onclick="ynevent_view_map(<?php echo $this -> identity;?>);">
							<i class="fa fa-map-marker"></i>
	                	</span>
      </div>
      <?php endif;?>
      <?php if(in_array('grid', $this -> mode_enabled)):?>
      <div class="ynevent_home_page_list_content_<?php echo $this -> identity;?>" rel="grid_view">
	                	<span id="grid_view_<?php echo $this->identity;?>" class="ynevent_home_page_list_content_icon" onclick="ynevent_view_grid(<?php echo $this -> identity;?>);">
							<i class="fa fa-th"></i></span>
        </span>
      </div>
      <?php endif;?>
      <?php if(in_array('list', $this -> mode_enabled)):?>
      <div class="ynevent_home_page_list_content_<?php echo $this -> identity;?>" rel="list_view">
	                	<span id="list_view_<?php echo $this->identity;?>" class="ynevent_home_page_list_content_icon" onclick="ynevent_view_list(<?php echo $this -> identity;?>);">
							<i class="fa fa-th-list"></i>
	                	</span>
      </div>
      <?php endif;?>
    </div>
    <div id="tab_events_popular_<?php echo $this -> identity;?>" class="tabcontent">
      <?php
          echo $this->partial('_list_most_item.tpl', 'ynevent', array('events' => $this->paginator));
      ?>
    </div>
    <iframe id='list-most-items-iframe_<?php echo $this -> identity;?>' style="max-height: 500px;"> </iframe>
  </div>

  <script type="text/javascript">
    var ynevent_view_map = function(id)
    {
      var eventIds = null;
      $$('#ynevent_list_item_' + id + ' .tabcontent').each(function (el){
        if(el.get('style') == "display: block;")
        {
          var idElement = el.get('id');
          eventIds =  '<?php echo $this -> eventIds ?>';
        }
      });

      document.getElementById('ynevent_list_item_'+id).set('class','ynevent_map-view');
      var html =  '<?php echo $this->url(array('action'=>'display-map-view'), 'event_general') ?>' + '/ids/' + eventIds;
      if(document.getElementById('list-most-items-iframe_'+id))
        document.getElementById('list-most-items-iframe_'+id).dispose();
      var iframe = new IFrame({
        id : 'list-most-items-iframe_'+id,
        src: html,
        styles: {
          'height': 500,
        },
      });
      iframe.inject($$('#ynevent_list_item_content_'+id)[0]);
      document.getElementById('list-most-items-iframe_'+id).style.display = 'block';
    }

    var ynevent_view_grid =  function(id)
    {
      if(document.getElementById('list-most-items-iframe_'+id))
        document.getElementById('list-most-items-iframe_'+id).dispose();
      document.getElementById('ynevent_list_item_'+id).set('class','ynevent_grid-view');
    }

    var ynevent_view_list = function(id)
    {
      if(document.getElementById('list-most-items-iframe_'+id))
        document.getElementById('list-most-items-iframe_'+id).dispose();
      document.getElementById('ynevent_list_item_'+id).set('class','ynevent_list-view');
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
      var myCookieViewMode = getCookie('ynevent-viewmode-cookie_<?php echo $this -> identity;?>');
      if ( myCookieViewMode == '')
      {
        myCookieViewMode = '<?php echo $this -> view_mode;?>_view';
      }
      if ( myCookieViewMode == '')
      {
        myCookieViewMode = 'list_view';
      }
      switch(myCookieViewMode) {
        case 'map_view':
          ynevent_view_map(<?php echo $this -> identity;?>);
        break;
        case 'grid_view':
        ynevent_view_grid(<?php echo $this -> identity;?>);
        break;
        case 'list_view':
        ynevent_view_list(<?php echo $this -> identity;?>);
        break;
        }

        // Set click viewMode
        $$('.ynevent_home_page_list_content_<?php echo $this -> identity;?>').addEvent('click', function(){
				var viewmode = this.get('rel');
				setCookie('ynevent-viewmode-cookie_<?php echo $this -> identity;?>', viewmode, 1);
        });
        });

  </script>
</div>
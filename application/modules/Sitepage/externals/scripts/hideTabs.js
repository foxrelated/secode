var hideWidgetsForModule = function(widgetname) {
  if(widgetname == 'sitepageactivityfeed') {
    if($('global_content').getElement('.layout_activity_feed')) {
      $('global_content').getElement('.layout_activity_feed').style.display = 'block';
    }
  }
  else {
    if($('global_content').getElement('.layout_activity_feed')) {
      $('global_content').getElement('.layout_activity_feed').style.display = 'none';
    }
  }
  if(widgetname == 'sitepageseaocoreactivityfeed') {
		if($('global_content').getElement('.layout_seaocore_feed')) {
			$('global_content').getElement('.layout_seaocore_feed').style.display = 'block';
    }
  } else {
		if($('global_content').getElement('.layout_seaocore_feed')) {
			$('global_content').getElement('.layout_seaocore_feed').style.display = 'none';
    }
  }
  if(widgetname == 'sitepageadvancedactivityactivityfeed') {
    if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
      $('global_content').getElement('.layout_advancedactivity_home_feeds').style.display = 'block';
    }
  } else {
    if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
      $('global_content').getElement('.layout_advancedactivity_home_feeds').style.display = 'none';
    }
  }
  if(widgetname == 'sitepageinfo') {
    if($('global_content').getElement('.layout_sitepage_info_sitepage')) {
      $('global_content').getElement('.layout_sitepage_info_sitepage').style.display = 'block';
    }
  }
  else {
    if($('global_content').getElement('.layout_sitepage_info_sitepage')) {
      $('global_content').getElement('.layout_sitepage_info_sitepage').style.display = 'none';
    }
  }
  if(widgetname == 'sitepageoverview') {
    if($('global_content').getElement('.layout_sitepage_overview_sitepage')) {
      $('global_content').getElement('.layout_sitepage_overview_sitepage').style.display = 'block';
    }
  }
  else {
    if($('global_content').getElement('.layout_sitepage_overview_sitepage')) {
      $('global_content').getElement('.layout_sitepage_overview_sitepage').style.display = 'none';
    }
  }
  if(widgetname == 'sitepagelocation') {
    if($('global_content').getElement('.layout_sitepage_location_sitepage')) {
      $('global_content').getElement('.layout_sitepage_location_sitepage').style.display = 'block';
    }
  }
  else {
    if($('global_content').getElement('.layout_sitepage_location_sitepage')) {
      $('global_content').getElement('.layout_sitepage_location_sitepage').style.display = 'none';
    }
  }
  if(widgetname == 'sitepagelink') {
    if($('global_content').getElement('.layout_core_profile_links')) {
      $('global_content').getElement('.layout_core_profile_links').style.display = 'block';
    }
  }
  else {
    if($('global_content').getElement('.layout_core_profile_links')) {
      $('global_content').getElement('.layout_core_profile_links').style.display = 'none';
    }
  }

}
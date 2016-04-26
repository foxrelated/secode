var hideWidgetsForModule = function(widgetname) {
	
	if(widgetname == 'sitegroupactivityfeed') {
		if($('global_content').getElement('.layout_activity_feed')) {
			$('global_content').getElement('.layout_activity_feed').style.display = 'block';
		}
	}
	else {
		if($('global_content').getElement('.layout_activity_feed')) {
			$('global_content').getElement('.layout_activity_feed').style.display = 'none';
		}
	}
	if(widgetname == 'sitegroupseaocoreactivityfeed') {
		if($('global_content').getElement('.layout_seaocore_feed')) {
			$('global_content').getElement('.layout_seaocore_feed').style.display = 'block';
		}
	} else {
		if($('global_content').getElement('.layout_seaocore_feed')) {
			$('global_content').getElement('.layout_seaocore_feed').style.display = 'none';
		}
	}
	if(widgetname == 'sitegroupadvancedactivityactivityfeed') {
		if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
			$('global_content').getElement('.layout_advancedactivity_home_feeds').style.display = 'block';
		}
	} else {
		if($('global_content').getElement('.layout_advancedactivity_home_feeds')) {
			$('global_content').getElement('.layout_advancedactivity_home_feeds').style.display = 'none';
		}
	}
	if(widgetname == 'sitegroupinfo') {
		if($('global_content').getElement('.layout_sitegroup_info_sitegroup')) {
			$('global_content').getElement('.layout_sitegroup_info_sitegroup').style.display = 'block';
		}
	}
	else {
		if($('global_content').getElement('.layout_sitegroup_info_sitegroup')) {
			$('global_content').getElement('.layout_sitegroup_info_sitegroup').style.display = 'none';
		}
	}
	if(widgetname == 'sitegroupoverview') {
		if($('global_content').getElement('.layout_sitegroup_overview_sitegroup')) {
			$('global_content').getElement('.layout_sitegroup_overview_sitegroup').style.display = 'block';
		}
	}
	else {
		if($('global_content').getElement('.layout_sitegroup_overview_sitegroup')) {
			$('global_content').getElement('.layout_sitegroup_overview_sitegroup').style.display = 'none';
		}
	}
	if(widgetname == 'sitegrouplocation') {
		if($('global_content').getElement('.layout_sitegroup_location_sitegroup')) {
			$('global_content').getElement('.layout_sitegroup_location_sitegroup').style.display = 'block';
		}
	}
	else {
		if($('global_content').getElement('.layout_sitegroup_location_sitegroup')) {
			$('global_content').getElement('.layout_sitegroup_location_sitegroup').style.display = 'none';
		}
	}
	if(widgetname == 'sitegrouplink') {
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
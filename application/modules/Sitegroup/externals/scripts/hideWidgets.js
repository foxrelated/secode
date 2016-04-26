function hideWidgets() {

  if($('global_content').getElement('.layout_activity_feed')) {
		$('global_content').getElement('.layout_activity_feed').style.display = 'none';
	}
	if($('global_content').getElement('.layout_sitegroup_info_sitegroup')) {
		$('global_content').getElement('.layout_sitegroup_info_sitegroup').style.display = 'none';
	}
	if($('global_content').getElement('.layout_sitegroup_location_sitegroup')) {
		$('global_content').getElement('.layout_sitegroup_location_sitegroup').style.display = 'none';
	}
	if($('global_content').getElement('.layout_core_profile_links')) {
		$('global_content').getElement('.layout_core_profile_links').style.display = 'none';
	}
	if($('global_content').getElement('.layout_sitegroup_overview_sitegroup')) {
		$('global_content').getElement('.layout_sitegroup_overview_sitegroup').style.display = 'none';
	}
}
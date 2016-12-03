function hideWidgets() {

  if($('global_content').getElement('.layout_activity_feed')) {
		$('global_content').getElement('.layout_activity_feed').style.display = 'none';
	}
	if($('global_content').getElement('.layout_sitepage_info_sitepage')) {
		$('global_content').getElement('.layout_sitepage_info_sitepage').style.display = 'none';
	}	
	if($('global_content').getElement('.layout_sitepage_location_sitepage')) {
		$('global_content').getElement('.layout_sitepage_location_sitepage').style.display = 'none';
	}		
	if($('global_content').getElement('.layout_core_profile_links')) {
		$('global_content').getElement('.layout_core_profile_links').style.display = 'none';
	}
	if($('global_content').getElement('.layout_sitepage_overview_sitepage')) {
		$('global_content').getElement('.layout_sitepage_overview_sitepage').style.display = 'none';
	}
}

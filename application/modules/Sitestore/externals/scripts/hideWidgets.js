/* $Id: hideWidgets.js 2013-09-02 00:00:00Z SocialEngineAddOns Copyright 2012-2013 BigStep Technologies Pvt. Ltd. $ */

function hideWidgets() {

  if($('global_content').getElement('.layout_activity_feed')) {
		$('global_content').getElement('.layout_activity_feed').style.display = 'none';
	}
	if($('global_content').getElement('.layout_sitestore_info_sitestore')) {
		$('global_content').getElement('.layout_sitestore_info_sitestore').style.display = 'none';
	}
	if($('global_content').getElement('.layout_sitestore_location_sitestore')) {
		$('global_content').getElement('.layout_sitestore_location_sitestore').style.display = 'none';
	}
	if($('global_content').getElement('.layout_core_profile_links')) {
		$('global_content').getElement('.layout_core_profile_links').style.display = 'none';
	}
	if($('global_content').getElement('.layout_sitestore_overview_sitestore')) {
		$('global_content').getElement('.layout_sitestore_overview_sitestore').style.display = 'none';
	}
}
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: widgetsettings.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<style type="text/css">
.settings #submit-label{
	display:block;
	margin-bottom:25px;
}
#TB_window {
	background:#fff !important;
}
</style>

<h2><?php echo $this->translate('Advanced Facebook Integration / Likes, Social Plugins and Open Graph') ?></h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<div class='seaocore_settings_form'>
  <div class='settings facebookse_fbsp_form'>
<?php echo $this->form->render($this) ?>
 </div>

</div>
 <?php $siteurl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/';?>
<script type="text/javascript">
//<![CDATA[
var fetchWidgetSettings =function(pagelevel_id) {
	if (pagelevel_id != 0) {
	  window.location.href= en4.core.baseUrl+'admin/facebookse/settings/widgetsettings/'+pagelevel_id;
	}
	else {
	  window.location.href= en4.core.baseUrl+'admin/facebookse/settings/widgetsettings/';
	}
   
}

var show_preview = function () {
  var plugin_type = '';
	var fb_width = '250';
	var fb_height = '250';
	var show_header = true;
	var widget_color_scheme = 'light';
	var widget_border_color = '';
	var recommend = true;
	var connection = 1;
	var show_stream = true;
  var fbpageurl = ''; 
	if ($('widget_type')) {
		plugin_type = $('widget_type').value;
	}
	
  if ($('fb_width')) {
     fb_width = $('fb_width').value;
  }

	if ($('fb_height')) {
     fb_height = $('fb_height').value;
  }

	if ($('show_header') && $('show_header').checked == false) {
     show_header = false;
  }
	
	if ($('widget_color_scheme')) {
    widget_color_scheme = $('widget_color_scheme').value;
  }

	if ($('widget_font')) {
     var widget_font = $('widget_font').value;
  }

	if ($('widget_border_color')) {
    widget_border_color = $('widget_border_color').value;
    
  }

	if ($('recommend') && $('recommend').checked == false) {
    recommend = false;
  }

	if ($('connection')) {
    connection = $('connection').value;
  }

  if ($('fbpageurl')) {
    fbpageurl = $('fbpageurl').value;
  }

  if ($('show_stream') && $('show_stream').checked == false) {
    show_stream = false;
  }
	
  if (plugin_type != '') {
		var url =   en4.core.baseUrl+'admin/facebookse/settings/showfbsocialpluginpreview';
		url += '?site="<?php echo $siteurl;?>"&recommend=' + recommend + '&show_header=' + show_header +'&widget_font=' + widget_font +'&widget_color_scheme=' + widget_color_scheme +'&plugin_type=' + plugin_type +'&fb_width=' + fb_width +'&fb_height=' + fb_height +'&widget_border_color=' + escape(widget_border_color) +'&connection=' + connection +'&show_stream=' + show_stream +'&fbpageurl=' + fbpageurl;
		Smoothbox.open(url);
  }
}
 //]]>
</script>
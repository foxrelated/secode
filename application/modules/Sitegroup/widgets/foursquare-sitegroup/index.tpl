<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<center>
	<a href="https://foursquare.com/intent/venue.html" class="fourSq-widget" data-variant="wide"><?php echo $this->translate("Save to foursquare");?></a>
	<script type='text/javascript'>
		(function() {
			window.___fourSq = {};
			var s = document.createElement('script');
			s.type = 'text/javascript';
			s.src = 'http://platform.foursquare.com/js/widgets.js';
			s.async = true;
			var ph = document.getElementsByTagName('script')[0];
			ph.parentNode.insertBefore(s, ph);
		})();
	</script>
</center>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: middle3.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style type="text/css">
.slideshow-thumbnailsmiddle3{
	width:<?php echo $this->width ?>px;
}
.slideshow-thumbnailsmiddle3 ul {
	background: <?php echo $this->thumb_back_color ?>;
}
.slideshow-thumbnailsmiddle3-active {
	background-color: <?php echo $this->thumb_bord_color ?>;
	opacity: 1;
}
.slideshow-thumbnailsmiddle3-inactive {
	background-color: 	<?php echo $this->thumb_bord_active ?>;
	opacity: .5;
}
.slideshow-thumbnailsmiddle3 img {
	width: <?php  $temp_width_s = ($this->thumb_width > 90) ?  $this->thumb_width : 91; echo $temp_width_s - 10;  ?>px;
}
</style>
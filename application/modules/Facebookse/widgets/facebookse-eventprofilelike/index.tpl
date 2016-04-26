<?php
  /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php $this->headScript()
				  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Facebookse/externals/scripts/core.js');?>
<div id="eventprofile_like">
	<?php if(empty($this->event_type)){ echo $this->translate($this->fb_event_type); } ?>
</div>

<script type="text/javascript">
var fblike_moduletype = 'event';
var fblike_moduletype_id = '<?php echo $this->fblike_moduletype_id;?>';
var call_advfbjs = '1';
$('eventprofile_like').innerHTML = '<?php echo $this->like_button;?>';
en4.facebookse.loadFbLike(<?php echo $this->LikeSettings;?>);
window.addEvent('domready', function()
  {
  	
  	
  });
</script>
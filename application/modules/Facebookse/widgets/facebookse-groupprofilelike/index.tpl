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
<div id="groupprofile_like">
</div>

<script type="text/javascript">
var fblike_moduletype = 'group';
var fblike_moduletype_id = '<?php echo $this->fblike_moduletype_id;?>'; 
var call_advfbjs = '1';
$('groupprofile_like').innerHTML = '<?php echo $this->like_button;?>';
window.addEvent('domready', function()
  {
  	en4.facebookse.loadFbLike(<?php echo $this->LikeSettings;?>);
    
  });
</script>
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
<div id="userprofile_like">
	<?php if(empty($this->user_type)){ echo $this->translate($this->fb_user_type); } ?>
</div>

<script type="text/javascript">
var fblike_moduletype = 'member';
var fblike_moduletype_id = '<?php echo Engine_Api::_()->core()->getSubject()->getIdentity();?>';
var call_advfbjs = '1'; 
 $('userprofile_like').innerHTML = '<?php echo $this->like_button;?>';
 en4.core.runonce.add(function() { 
    en4.facebookse.loadFbLike(<?php echo $this->LikeSettings;?>);
 });
</script>
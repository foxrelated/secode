<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sharesettings.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<style type="text/css">
.settings #submit-label{
	display:block;
	margin-bottom:25px;
}
</style>
<h2>Facebook for Social Engine</h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs facebookse_tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>

</div>

<script type="text/javascript">
//<![CDATA[
var fetchShareSettings =function(pagelevel_id) {
	if (pagelevel_id != 0) {
	  window.location.href= en4.core.baseUrl+'admin/facebookse/settings/shares/'+pagelevel_id;
	}
	else {
	  window.location.href= en4.core.baseUrl+'admin/facebookse/settings/shares/';
	}
   
  }

function get_code () {
	
	if ($('share_url')) {
	  var share_url = $('share_url').value;
	}

	if ($('share_type')) {
	  var share_type = $('share_type').value;
	}
	
	var url =   en4.core.baseUrl+'admin/facebookse/settings/getsharecode';
	url += '?share_url=' + share_url + '&share_type=' + share_type ;
	Smoothbox.open(url);
  }	
//]]>
</script>
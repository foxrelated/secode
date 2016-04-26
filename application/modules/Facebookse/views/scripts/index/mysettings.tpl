<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: mysettings.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<div class="headline">
  <h2>
	  <?php echo $this->translate('Facebook');  ?>
	</h2>
   <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>

</div>

<script type="text/javascript">

var openchildwindow = 0;
showfeeddialogchildwindow ();

function show_fbpermissionbox () {
	openchildwindow = 1;
	showfeeddialogchildwindow (); 
}

function showfeeddialogchildwindow () {
	if (openchildwindow == 1) {
	  var url = '<?php echo $this->fb_url;?>'; 
		var child_window = window.open (url ,'mywindow','width=800,height=700');
	}
	if (window.opener!= null) {
	  
		if (openchildwindow == 0) { 
      submitform () ;
			close();
		
		}
	}
}

function submitform () {
	window.opener.document.getElementById('form-setting-fbfeed').submit();
}

</script>
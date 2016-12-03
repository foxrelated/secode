<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate("Email Templates Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitemailtemplates/externals/images/back.png" class="icon" />
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemailtemplates', 'controller' => 'settings', 'action' => 'manage'), $this->translate('Back to Manage Templates'), array('class'=> 'buttonlink', 'style'=> 'padding-left:0px;')) ?>
<br /><br />

<div class='clear  seaocore_settings_form'>
	<div class='settings'>
		<?php echo $this->form->render($this); ?>
	</div>
</div>

<style type="text/css">
.settings form .form-notices{display:none;}
.defaultSkin iframe {
	height:300px !important;
	width: 650px !important;
}
</style>

<script type="text/javascript">

  window.addEvent('domready', function() {
  var testEmailDemo = "<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('testemail.demo',1);?>";
  var checkboxValue = $('testemail_demo').checked;   
  showOption(checkboxValue);
  var titleoption = 1;   
  showSiteTitle(titleoption);
	showlogoOptions('1');
  showtaglineptions('1');
  var optionlogo = 1;
		if(optionlogo == 1) {
			$('img_path-wrapper').style.display='block';
			$('logo_photo_preview-wrapper').style.display='block';
		}	else {
			$('img_path-wrapper').style.display='none';
			$('logo_photo_preview-wrapper').style.display='none';

		}

	});

  function showOption(option) {
		if(option == true) {
			$('testemail_admin-wrapper').style.display = 'block';
		} else {
			$('testemail_admin-wrapper').style.display = 'none';
		}
  }

  function showSiteTitle(titleoption) {
		if(titleoption == true) {
			$('site_title-wrapper').style.display = 'block';
      $('sitetitle_fontsize-wrapper').style.display = 'block';
      $('sitetitle_fontfamily-wrapper').style.display = 'block';
      $('sitetitle_location-wrapper').style.display = 'block';
      $('sitetitle_position-wrapper').style.display = 'block';
		} else {
			$('site_title-wrapper').style.display = 'none';
      $('sitetitle_fontsize-wrapper').style.display = 'none';
      $('sitetitle_fontfamily-wrapper').style.display = 'none';
      $('sitetitle_location-wrapper').style.display = 'none';
      $('sitetitle_position-wrapper').style.display = 'none';
		}
  }

  function updateTextFields(option) {
		if($('logo_photo_preview')){
			$('logo_photo_preview').value = option;
		}
		if($('logo_photo_preview-element')) {
			$('logo_photo_preview-element').innerHTML = "<img src='" + option + "' style='max-height:100px;' >" ;
		}
  }

	function showlogoOptions(option) { 
		if($('show_icon-wrapper')) {
			if(option == 1) {
						$('img_path-wrapper').style.display='block';
            $('sitelogo_location-wrapper').style.display='block';
            $('sitelogo_position-wrapper').style.display='block';
						$('logo_photo_preview-wrapper').style.display='block';
			}else{
					$('img_path-wrapper').style.display='none';
          $('sitelogo_location-wrapper').style.display='none';
          $('sitelogo_position-wrapper').style.display='none';
					$('logo_photo_preview-wrapper').style.display='none';
			}
		}
	}

  function showtaglineptions(taglineoption) {
		if(taglineoption == true) {
			$('tagline_title-wrapper').style.display = 'block';
      $('tagline_fontsize-wrapper').style.display = 'block';
      $('tagline_fontfamily-wrapper').style.display = 'block';
      $('tagline_location-wrapper').style.display = 'block';
      $('tagline_position-wrapper').style.display = 'block';
		} else {
			$('tagline_title-wrapper').style.display = 'none';
      $('tagline_fontsize-wrapper').style.display = 'none';
      $('tagline_fontfamily-wrapper').style.display = 'none';
      $('tagline_location-wrapper').style.display = 'none';
      $('tagline_position-wrapper').style.display = 'none';
		}
  }
</script>
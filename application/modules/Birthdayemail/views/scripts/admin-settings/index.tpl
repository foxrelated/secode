<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthdayemail
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<script type="text/javascript">
	var sitemailtemplates = '<?php echo $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');?>';
	window.addEvent('domready', function() { 
	  var e1 = $('birthdayemail_reminder-1');
	  var e2 = $('birthdayemail_wish-1');
	  var e3 = $('birthdayemail_reminder-0');
	  var e4 = $('birthdayemail_wish-0');
	  var e5 = $('birthdayemail_demo');
	  $('birthdayemail_reminder_options-wrapper').setStyle('display', (e1.checked ?'block':'none'));
    if(sitemailtemplates == 0) {
			$('birthdayemail_color-wrapper').setStyle('display', (e1.checked || e2.checked ?'block':'none'));
			$('birthdayemail_title_color-wrapper').setStyle('display', (e1.checked || e2.checked ?'block':'none'));
			$('birthdayemail_site_title-wrapper').setStyle('display', (e1.checked || e2.checked ?'block':'none'));
    }
	  $('birthdayemail_demo-wrapper').setStyle('display', (e1.checked || e2.checked ?'block':'none'));
	  //$('birthdayemail_admin-wrapper').setStyle('display', (e1.checked || e2.checked ?'block':'none'));
	  $('birthdayemail_wish_image-wrapper').setStyle('display', (e2.checked ?'block':'none'));
	  $('birthdayemail_wish_image_display-wrapper').setStyle('display', (e2.checked ?'block':'none'));
	  $('birthdayemail_admin-wrapper').setStyle('display', (e5.checked && (e1.checked || e2.checked) ?'block':'none'));


	  $('birthdayemail_wish-1').addEvent('click', function(){
      if(sitemailtemplates == 0) {
				$('birthdayemail_color-wrapper').setStyle('display', ($(this).checked || e1.checked ?'block':'none'));
				$('birthdayemail_title_color-wrapper').setStyle('display', ($(this).checked || e1.checked ?'block':'none'));
				$('birthdayemail_site_title-wrapper').setStyle('display', ($(this).checked || e1.checked ?'block':'none'));
      }
	    $('birthdayemail_demo-wrapper').setStyle('display', ($(this).checked || e1.checked ?'block':'none'));
	    $('birthdayemail_admin-wrapper').setStyle('display', (e5.checked && $(this).checked || e1.checked ?'block':'none'));
	    $('birthdayemail_wish_image-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
	    $('birthdayemail_wish_image_display-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
	  });

	  $('birthdayemail_wish-0').addEvent('click', function(){
      if(sitemailtemplates == 0) {
				$('birthdayemail_color-wrapper').setStyle('display', ($(this).checked && e3.checked ?'none':'block'));
				$('birthdayemail_title_color-wrapper').setStyle('display', ($(this).checked && e3.checked ?'none':'block'));
				$('birthdayemail_site_title-wrapper').setStyle('display', ($(this).checked && e3.checked ?'none':'block'));
      }
	    $('birthdayemail_demo-wrapper').setStyle('display', ($(this).checked && e3.checked ?'none':'block'));
	    if (e5.checked == true) {
	      $('birthdayemail_admin-wrapper').setStyle('display', ($(this).checked && e3.checked ?'none':'block'));
            }
            else {
	      $('birthdayemail_admin-wrapper').setStyle('display', 'none');
            }
	    
	    $('birthdayemail_wish_image-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
	    $('birthdayemail_wish_image_display-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
	  });
	  
	  $('birthdayemail_reminder-1').addEvent('click', function(){
	    $('birthdayemail_reminder_options-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
      if(sitemailtemplates == 0) {
				$('birthdayemail_color-wrapper').setStyle('display', ($(this).checked || e2.checked ?'block':'none'));
				$('birthdayemail_title_color-wrapper').setStyle('display', ($(this).checked || e2.checked ?'block':'none'));
				$('birthdayemail_site_title-wrapper').setStyle('display', ($(this).checked || e2.checked ?'block':'none'));
      }
	    $('birthdayemail_demo-wrapper').setStyle('display', ($(this).checked || e2.checked ?'block':'none'));
	    $('birthdayemail_admin-wrapper').setStyle('display', (e5.checked && ($(this).checked || e2.checked) ?'block':'none'));
	  });

	  $('birthdayemail_reminder-0').addEvent('click', function(){
	    $('birthdayemail_reminder_options-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
      if(sitemailtemplates == 0) {
				$('birthdayemail_color-wrapper').setStyle('display', ($(this).checked && e4.checked ?'none':'block'));
				$('birthdayemail_title_color-wrapper').setStyle('display', ($(this).checked && e4.checked ?'none':'block'));
				$('birthdayemail_site_title-wrapper').setStyle('display', ($(this).checked && e4.checked ?'none':'block'));
      }
	    $('birthdayemail_demo-wrapper').setStyle('display', ($(this).checked && e4.checked ?'none':'block'));
            if (e5.checked == true) {
	      $('birthdayemail_admin-wrapper').setStyle('display', ($(this).checked && e4.checked ?'none':'block'));
            }
            else {
	      $('birthdayemail_admin-wrapper').setStyle('display', 'none');
            }
	  });
      
	  $('birthdayemail_demo').addEvent('click', function(){
	    $('birthdayemail_admin-wrapper').setStyle('display', ($(this).checked && (e1.checked || e2.checked) ?'block':'none'));
	  });

	});
	  

</script>
<h2>
  <?php echo $this->translate('Birthdays Plugin') ?>
</h2>

<?php $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');?>
<?php if($sitemailtemplates):?>
	<div class="tip">
		<span>
			<?php echo $this->translate('The settings for the colors of the email template have been moved to the Email Templates Plugin. Please %1$svisit here%2$s to see and configure these settings.', '<a href="'.$this->url(array("module" => "sitemailtemplates","controller" => "settings"),"admin_default", true).'">', '</a>');?>
		</span>
	</div>
<?php endif;?>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>

    <?php echo $this->form->render($this); ?>
  </div>
</div>

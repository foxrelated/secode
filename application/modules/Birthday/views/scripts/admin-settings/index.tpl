<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<script type="text/javascript">
	
	window.addEvent('domready', function() { 
	  var e1 = $('Widget-3');
		if($('birthday_entries-wrapper')) {
			$('birthday_entries-wrapper').setStyle('display', (e1.checked ?'none':'block'));
			$('birthday_daystart-wrapper').setStyle('display', (e1.checked ?'block':'none'));
			
			$('Widget-3').addEvent('click', function(){
				$('birthday_entries-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
				$('birthday_daystart-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
			});
			
			$('Widget-0').addEvent('click', function(){
				$('birthday_entries-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
				$('birthday_daystart-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
			});

			$('Widget-1').addEvent('click', function(){
				$('birthday_entries-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
				$('birthday_daystart-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
			});
				
			$('Widget-2').addEvent('click', function(){
				$('birthday_entries-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
				$('birthday_daystart-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
			});
		}
	});

</script>
<h2>
  <?php echo $this->translate('Birthdays Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; ?>

<div class='seaocore_settings_form'>
  <div class='settings'>

    <?php echo $this->form->render($this); ?>
  </div>
</div>

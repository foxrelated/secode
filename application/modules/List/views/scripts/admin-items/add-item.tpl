<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: add-item.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php 
  $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');

	$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
?>

<style type="text/css">
.global_form_popup dt label{font-weight:normal;}
</style>

<script type="text/javascript">

	en4.core.runonce.add(function()
	{
		var contentAutocomplete = new Autocompleter.Request.JSON('title', '<?php echo $this->url(array('module' => 'list', 'controller' => 'items', 'action' => 'get-listings'), 'admin_default', true) ?>', {
			'postVar' : 'text',
			'minLength': 0,
			'selectMode': 'pick',
			'autocompleteType': 'tag',
			'className': 'seaocore-autosuggest',
			'customChoices' : true,
			'filterSubset' : true,
			'multiple' : false,
			'injectChoice': function(token){
					var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id':token.label});
					new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice1'}).inject(choice);
					this.addChoiceEvents(choice).inject(this.choices);
					choice.store('autocompleteChoice', token);
				}
		});

		contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
			$('listing_id').value = selected.retrieve('autocompleteChoice').id;
		});
	});
</script>

<script type="text/javascript">
	en4.core.runonce.add(function()
	{
			en4.core.runonce.add(function init()
			{
				monthList = [];
				myCal = new Calendar({ 'start_cal[date]': 'M d Y', 'end_cal[date]' : 'M d Y' }, {
					classes: ['event_calendar'],
					pad: 0,
					direction: 0
				});
			});
	});

	en4.core.runonce.add(function(){

		// check end date and make it the same date if it's too
		cal_starttime.calendars[0].start = new Date( $('starttime-date').value );
		// redraw calendar
		cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
		cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);

	});

	window.addEvent('domready', function() {
		if($('starttime-minute')) {
			$('starttime-minute').style.display= 'none';
		}
		if($('starttime-ampm')) {
			$('starttime-ampm').style.display= 'none';
		}
		if($('starttime-hour')) {
			$('starttime-hour').style.display= 'none';
		}

		if($('endtime-minute')) {
			$('endtime-minute').style.display= 'none';
		}
		if($('endtime-ampm')) {
			$('endtime-ampm').style.display= 'none';
		}
		if($('endtime-hour')) {
			$('endtime-hour').style.display= 'none';
		}
	});
</script>

<div class="settings global_form_popup">
	<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
</div>
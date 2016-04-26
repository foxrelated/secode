<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
	echo $this->content()->renderWidget('yncontest.main-menu') ?>

<div class = "contestcreate_midle">
	<?php if($this->checkGateway == 0):?>
		<div class="tip">
			<span>				
				<?php echo $this->translate("The payment account is not set. Please contact admin for more details!"); ?>
			</span>
		</div>
	<?php elseif(!$this->plugin):?>
		<div class="tip">
			<span>
				<?php echo $this->translate("There are no plug-in %s","Blogs, Videos, Albums or Music") ?>
			</span>
			</div>
	<?php else:?>
		<?php echo $this->form->render($this);?>	
	<?php endif;?>
</div>

<script type="text/javascript">
  var cal_start_date_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_end_date.calendars[0].start = new Date( $('start_date-date').value );
    // redraw calendar
    cal_end_date.navigate(cal_end_date.calendars[0], 'm', 1);
    cal_end_date.navigate(cal_end_date.calendars[0], 'm', -1);
  }
  var cal_end_date_onHideStart = function(){
	    // check start date and make it the same date if it's too
	    cal_start_date.calendars[0].end = new Date( $('end_date-date').value );
	    // redraw calendar
	    cal_start_date.navigate(cal_start_date.calendars[0], 'm', 1);
	    cal_start_date.navigate(cal_start_date.calendars[0], 'm', -1);
  }
  
  var cal_start_date_submit_entries_onHideStart = function(){	   
		// check end date and make it the same date if it's too
	    cal_end_date_submit_entries.calendars[0].start = new Date( $('start_date_submit_entries-date').value );
	    // redraw calendar
	    cal_end_date_submit_entries.navigate(cal_end_date_submit_entries.calendars[0], 'm', 1);
	    cal_end_date_submit_entries.navigate(cal_end_date_submit_entries.calendars[0], 'm', -1);
  }
  var cal_end_date_submit_entries_onHideStart = function(){ 
	    // check start date and make it the same date if it's too
	    cal_start_date_submit_entries.calendars[0].end = new Date( $('end_date_submit_entries-date').value );
	    // redraw calendar
	    cal_start_date_submit_entries.navigate(cal_start_date_submit_entries.calendars[0], 'm', 1);
	    cal_start_date_submit_entries.navigate(cal_start_date_submit_entries.calendars[0], 'm', -1);
  }
  
  var cal_start_date_vote_entries_onHideStart = function(){	   
	// check end date and make it the same date if it's too
	    cal_end_date_vote_entries.calendars[0].start = new Date( $('start_date_vote_entries-date').value );
	    // redraw calendar
	    cal_end_date_vote_entries.navigate(cal_end_date_vote_entries.calendars[0], 'm', 1);
	    cal_end_date_vote_entries.navigate(cal_end_date_vote_entries.calendars[0], 'm', -1);
  }
  var cal_end_date_vote_entries_onHideStart = function(){ 
	   // check start date and make it the same date if it's too
	    cal_start_date_vote_entries.calendars[0].end = new Date( $('end_date_vote_entries-date').value );
	    // redraw calendar
	    cal_start_date_vote_entries.navigate(cal_start_date_vote_entries.calendars[0], 'm', 1);
	    cal_start_date_vote_entries.navigate(cal_start_date_vote_entries.calendars[0], 'm', -1);
  }
 

	

  
</script>
<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
      'postVar' : 'text',
      'customChoices' : true,
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
  });
  </script>
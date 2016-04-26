<?php
$menu = $this->partial('_menu.tpl', array());
echo $menu;
?>
<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
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
<div class="layout_left ynfundraising_create_right_menu ">
<?php
$menu_create = $this->partial('_menu_create.tpl', array('active_menu'=>'step01','campaign_id'=>$this->campaign_id));
echo $menu_create;
?>
</div>
<div class="layout_middle">
<?php echo $this->form->render($this);?>
</div>
<script type="text/javascript">
	function actionSubmit()
	{
	   var list_donation_amount = $$('.amount_input');
	   var minimum_value = parseFloat($('minimum_donate').value);
	   var flag = true;
	   if(minimum_value > 0)
	   {
		   for(var i = 0; i < list_donation_amount.length; i ++)
		   {
		   	  var value = parseFloat(list_donation_amount[i].value);
		   	  if(value < minimum_value && value > 0 && value != "")
		   	  {
		   	  	 $('error_' + i).style.display = 'block';
		   	  	 flag = false;
		   	  }
		   }
	   }
	   for(var i = 0; i < list_donation_amount.length; i ++)
	   {
	   	  var value = list_donation_amount[i].value;
	   	  if((isNaN(value) && value != "") || (value < 0 && value != ""))
	   	  {
	   	  	 $('error_invalid_' + i).style.display = 'block';
	   	  	 flag = false;
	   	  }
	   }

	   if(flag == false)
	   		return false;
	   $('ynfundraising_create_step1').submit();
	   $('buttons-wrapper').hide();
	}
</script>
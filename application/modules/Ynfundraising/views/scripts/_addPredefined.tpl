<div id="predefined-wrapper" class="form-wrapper">
	<div id="predefined-label" class="form-label">
		<label for="predefined" class="optional"><?php echo $this->translate("Predefined List")?></label>
	</div>
	<div id="predefined-element" class="form-element">
		<div id="predefined">
			<?php echo $this->element->getAttrib('contentHtml');?>
		</div>
		<a href="javascript: void(0);" onclick="return addInput();" id="addOptionLink"><?php echo $this->translate("Add More Donation Amount") ?></a>
		<p class="description"><?php echo $this->translate("(Enter up to predefined 5 donation amount to available on the page)")?></p>
	</div>
</div>
<script type="text/javascript">
	var donations = $$('.amount_input').length;
	var optionParent = $('predefined');
	var addInput = function()
	{
		if (donations != 5)
		{
			var inputElement = new Element('input', {
		          'type': 'text',
		          'id' : 'input_' + donations,
		          'name': 'predefined[]',
		          'class': 'amount_input',
		          'value': '',
		          'onchange': 'checkValue(this, '+ donations +')',
		          'events': {
		            'keydown': function(event) {
		              if (event.key == 'enter') {
		                if (this.get('value').trim().length > 0) {
		                  addInput();
		                  return false;
		                } else
		                  return true;
		              } else
		                return true;
		            } // end keypress event
		          } // end events
		        });
		    inputElement.inject(optionParent);

		    var removeElement = new Element('a', {
		          'id': 'remove_' + donations,
		          'class': 'buttonlink icon_ynfundraising_delete',
		          'link': true,
		          'href': 'javascript:;',
		          'html': '<?php echo $this->translate("Remove");?>',
		          'onclick': 'removeInput(' + donations + ')'
		        });
		    removeElement.inject(optionParent);

		    var errorElement = new Element('label', {
		          'id': 'error_' + donations,
		          'class': '',
		          'style':'display:none; color: red',
		          'text': '<?php echo $this->translate("The donation amount should be greater than minimum amount.");?>'
		        });
		    errorElement.inject(optionParent);

		    var errorInvalidElement = new Element('label', {
		          'id': 'error_invalid_' + donations,
		          'class': '',
		          'style':'display:none; color: red',
		          'text': '<?php echo $this->translate("The donation amount invalid.");?>'
		        });
		    errorInvalidElement.inject(optionParent);
			donations += 1;
		} else {
			$$('.addOptionLink').set('onclick','javascript:;');
		}
	}
	var removeInput = function(id)
	{
		if(donations > 0)
		{
			var element_input = $('input_' + id);
			var element_remove = $('remove_' + id);
			var error_remove = $('error_' + id);
			var error_invalid_remove = $('error_invalid_' + id);

			optionParent.removeChild(element_input);
			optionParent.removeChild(element_remove);
			optionParent.removeChild(error_remove);
			optionParent.removeChild(error_invalid_remove);
			donations -= 1;
			$$('.addOptionLink').set('onclick','return addInput()');
		}
	}
	var checkValue = function(obj, id)
	{
		var minimum_value = parseFloat($('minimum_donate').value);
		var value = parseFloat(obj.value);
		if((minimum_value > 0 && minimum_value > value && value >= 0))
		{
			$('error_' + id).style.display = 'block';
		}
		else
		{
			$('error_' + id).style.display = 'none';
		}
		if((isNaN(obj.value) && obj.value != "") || (obj.value <= 0 && obj.value != "" ))
		{
			$('error_invalid_' + id).style.display = 'block';
		}
		else
		{
			$('error_invalid_' + id).style.display = 'none';
		}
	}
</script>
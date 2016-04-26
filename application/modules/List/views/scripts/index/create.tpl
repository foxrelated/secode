<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: create.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
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

      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'customChoices' : true,
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

	window.addEvent('domready', function() { 
		checkDraft();
	});

	function checkDraft(){
		if($('draft')){
			if($('draft').value==0) {
				$("search-wrapper").style.display="none";
				$("search").checked= false;
			} else{
				$("search-wrapper").style.display="block";
				$("search").checked= true;
			}
		}
	}
 
  en4.core.runonce.add(function(){
     if('<?php echo $this->expiry_setting; ?>' !=1){
       document.getElementById("end_date_enable-wrapper").style.display = "none";
     }
    if($('end_date-date')){
      // check end date and make it the same date if it's too
      cal_end_date.calendars[0].start = new Date( $('end_date-date').value );
      // redraw calendar
      cal_end_date.navigate(cal_end_date.calendars[0], 'm', 1);
      cal_end_date.navigate(cal_end_date.calendars[0], 'm', -1);
    }

  });
  var updateTextFields = function(endsettings)
  {
    var endtime_element = document.getElementById("end_date-wrapper");
    endtime_element.style.display = "none";

    if (endsettings.value == 0)
    {
      endtime_element.style.display = "none";
      return;
    }

    if (endsettings.value == 1)
    {
      endtime_element.style.display = "block";
      return;
    }
  }
  en4.core.runonce.add(updateTextFields);
</script>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
    //'topLevelId' => (int) @$this->topLevelId,
    //'topLevelValue' => (int) @$this->topLevelValue
  ))
?>
<?php
	$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/List/externals/styles/style_list.css');
?>  
<?php include_once APPLICATION_PATH . '/application/modules/List/views/scripts/navigation_views.tpl'; ?>

<div class='layout_middle'>
	<?php if ($this->current_count >= $this->quota  && !empty($this->quota)): ?>
		<div class="tip"> 
			<span>
				<?php echo $this->translate('You have already created the maximum number of listings allowed.'); ?>
			</span>
		</div>
		<br/>
	<?php else: ?>
		<?php if($this->list_render == 'list_form') { echo $this->form->render($this); } else { echo $this->translate($this->list_formrender); } ?>
	<?php endif; ?>
</div>

<script type="text/javascript">
	if($('subcategory_id'))
   $('subcategory_id').style.display = 'none';
</script>

<script type="text/javascript">

	var getProfileType = function(category_id) {
		var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('profilemaps', 'list')->getMapping()); ?>;
		for(i = 0; i < mapping.length; i++) {
			if(mapping[i].category_id == category_id)
				return mapping[i].profile_type;
		}
		return 0;
	}

	var defaultProfileId = '<?php echo '0_0_'.$this->defaultProfileId ?>'+'-wrapper';
	if($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
		$(defaultProfileId).setStyle('display', 'none');
	}
  
  <?php if($this->getCategoriesCount <= 0): ?>  
    window.addEvent('domready', function() {
      var defaultProfileIdElement = '<?php echo '0_0_'.$this->defaultProfileId ?>';
      var getCategoriesCount = <?php echo $this->getCategoriesCount ?>;
      if(getCategoriesCount == 0) {
        $(defaultProfileIdElement).value = <?php echo $this->getDefaultProfileType; ?>;
        changeFields($(defaultProfileIdElement));
      }  
    });  
  <?php endif;?>
</script>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl  17.09.11 11:55 TeaJay $
 * @author     Taalay
 */
?>




<div class="layout_left">
<?php if (!$this->isPublic): ?>
<div id='panel_options'>
<?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
echo $this->navigation()
->menu()
->setContainer($this->navigation)
->setPartial(array('_navIcons.tpl', 'core'))
->render()
?>
</div>
<?php endif; ?>
</div>



<div class="layout_middle">

    <h3>
      <?php echo $this->translate('Create New Item'); ?>
    </h3>

  <p class="flying-text-imp">
    <?php echo $this->translate("STORE_VIEWS_SCRIPTS_CREATE_PRODUCT_DESCRIPTION") ?>
  </p>

<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Store/externals/scripts/page.js');
?>

<script type="text/javascript">
  var switchType = function()
 	{
     if ($('price_type').value == 'simple') {
       $('list_price-wrapper').style.display='none';
       $('discount_expiry_date-wrapper').style.display='none';
     } else {
       $('list_price-wrapper').style.display='block';
       $('discount_expiry_date-wrapper').style.display='block';
     }
 	}

  var switchAmount = function()
  {
    if ($('type').value == 'digital') {
      $('quantity-wrapper').style.display='none';
      $('additional_params-wrapper').style.display='none';
    } else {
      $('quantity-wrapper').style.display='block';
      $('additional_params-wrapper').style.display='block';
    }
  }

  en4.core.runonce.add(function()
  {
    cal_discount_expiry_date.calendars[0].start = new Date();
    cal_discount_expiry_date.navigate(cal_discount_expiry_date.calendars[0], 'm', 1);
    cal_discount_expiry_date.navigate(cal_discount_expiry_date.calendars[0], 'm', -1);

    switchAmount();
    switchType();

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

  var resetValue = function() {
    $('discount_expiry_date-date').value = '';
    $('discount_expiry_date-hour').value = '';
    $('discount_expiry_date-minute').value = '';
    $('discount_expiry_date-ampm').value = '';
    $('calendar_output_span_discount_expiry_date-date').innerHTML = '<?php echo $this->translate('STORE_Select a date')?>';
  }
</script>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
    //'topLevelId' => (int) @$this->topLevelId,
    //'topLevelValue' => (int) @$this->topLevelValue
  ))
?>
<?php echo $this->form->render($this); ?>

</div>


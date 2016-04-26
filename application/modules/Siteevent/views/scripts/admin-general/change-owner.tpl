<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: change-owner.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
    function check_submit()
    {
        if (document.getElementById('user_id').value == '')
        {
            return false;
        }
        else
        {
            return true;
        }
    }
</script>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<div class="siteevent_admin_popup">
    <div>
        <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
    </div>
</div>

<script type="text/javascript">
    //en4.core.runonce.add(function()
    //{
    var contentAutocomplete = new Autocompleter.Request.JSON('title', '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'general', 'action' => 'get-owner', 'event_id' => $this->event_id), 'admin_default', true) ?>', {
        'postVar': 'text',
        'minLength': 1,
        'selectMode': 'pick',
        'autocompleteType': 'tag',
        'className': 'siteevent_categories-autosuggest',
        'customChoices': true,
        'filterSubset': true,
        'multiple': false,
        'injectChoice': function(token) {
            var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id': token.label});
            new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice1'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);

        }
    });
    contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
        document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;
    });
    //});

</script>

<?php
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . '/externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . '/externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . '/externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . '/externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">

    // Populate data
    var maxRecipients = 1;

    var to = {
        id:false,
        type:false,
        guid:false,
        title:false
    };


    function removeFromToValue(id) {
        // code to change the values in the hidden field to have updated values
        // when recipients are removed.
        var toValues = $('toValues').value;
        var toValueArray = toValues.split(",");
        var toValueIndex = "";

        var checkMulti = id.search(/,/);

        // check if we are removing multiple recipients
        if (checkMulti != -1) {
            var recipientsArray = id.split(",");
            for (var i = 0; i < recipientsArray.length; i++) {
                removeToValue(recipientsArray[i], toValueArray);
            }
        }
        else {
            removeToValue(id, toValueArray);
        }

        // hide the wrapper for usernames if it is empty
        if ($('toValues').value == "") {
            $('toValues-wrapper').setStyle('height', '0');
        }

        $('to').disabled = false;
    }

    function removeToValue(id, toValueArray) {
        for (var i = 0; i < toValueArray.length; i++) {
            if (toValueArray[i] == id) toValueIndex = i;
        }

        toValueArray.splice(toValueIndex, 1);
        $('toValues').value = toValueArray.join();
    }

    en4.core.runonce.add(function () {
            new Autocompleter.Request.JSON('to', '<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest'), 'default', true) ?>', {
                'minLength':1,
                'delay':250,
                'selectMode':'pick',
                'autocompleteType':'message',
                'multiple':false,
                'className':'message-autosuggest',
                'filterSubset':true,
                'tokenFormat':'object',
                'tokenValueKey':'label',

                'injectChoice':function (token) {
                    if (token.type == 'user') {
                        var choice = new Element('li', {
                            'class':'autocompleter-choices',
                            'html':token.photo,
                            'id':token.label
                        });
                        new Element('div', {
                            'html':this.markQueryValue(token.label),
                            'class':'autocompleter-choice'
                        }).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }
                    else {
                        var choice = new Element('li', {
                            'class':'autocompleter-choices friendlist',
                            'id':token.label
                        });
                        new Element('div', {
                            'html':this.markQueryValue(token.label),
                            'class':'autocompleter-choice'
                        }).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }

                },
                onPush:function () {
                    if ($('toValues').value.split(',').length >= maxRecipients) {
                        $('to').disabled = true;
                    }
                }
            });
    });
</script>

<div class="headline">
    <h2>
        <?php echo $this->translate('Money'); ?>
    </h2>

    <div class="tabs">
        <?php
        // Render the menu
        echo $this->navigation()
            ->menu()
            ->setContainer($this->navigation)
            ->render();
        ?>
    </div>
</div>
<?php if ($this->money > 1): ?>
<?php echo $this->form->render($this) ?>
<?php else: ?>
<div class="tip">
    <span><?php echo $this->translate('You do not have the balance. ')?><?php echo $this->htmlLink(array('route' => 'money_subscription', 'action' => 'choose'), $this->translate('Replenish the balance'));?></span>
</div>
<?php endif; ?>


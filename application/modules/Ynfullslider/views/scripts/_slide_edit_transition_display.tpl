<?php
$transitions = array(
    '' => $this->translate("No Animation"),
    'sft' => $this->translate("Short from Top"),
    'sfb' => $this->translate("Short from Bottom"),
    'sfr' => $this->translate("Short from Right"),
    'sfl' => $this->translate("Short from Left"),
    'lft' => $this->translate("Long from Top"),
    'lfb' => $this->translate("Long from Bottom"),
    'lfr' => $this->translate("Long from Right"),
    'lfl' => $this->translate("Long from Left"),
    'skewfromleft' => $this->translate("Skew from Left"),
    'skewfromright' => $this->translate("Skew from Right"),
    'skewfromleftshort' => $this->translate("Skew Short from Left"),
    'skewfromrightshort' => $this->translate("Skew Short from Right"),
    'fade' => $this->translate("Fading"),
    'randomrotate' => $this->translate("Fade in and Random Rotate")
);
?>

<fieldset class="ynfullslider_form_transitionoptions">
    <label><input type="checkbox" name="random_transition" value="1"><?php echo $this->translate("Random transition") ?></label>
    <select name="transition_id">
        <?php foreach($transitions as $key => $value): ?>
        <option value="<?php echo $key ?>"><?php echo $value ?></option>
        <?php endforeach; ?>
    </select>
    <br>
    <label><?php echo $this->translate("Duration") ?><input type="number" min="0" name="transition_duration"></label>
    <p><?php echo $this->translate("The time a transition effect takes to complete in milliseconds") ?></p>
    <br>
    <br>
    <label><?php echo $this->translate("Delay time") ?><input type="number" min="0" name="transition_delay"></label>
    <p><?php echo $this->translate("Play the animation after a certain number of milliseconds (counting from the time slide background appearing)") ?></p>
</fieldset>

<fieldset class="ynfullslider_form_displayoptions" id="show_options">
    <span><?php echo $this->translate("Display options") ?></span>
    <label><input type="checkbox" name="show_all" value="1" class="ynfullslider_form_displayoptions_checkbox"><?php echo $this->translate("All") ?></label>
    <label><input type="checkbox" name="show_desktop" value="1" class="ynfullslider_form_displayoptions_checkbox"><?php echo $this->translate("Desktop") ?></label>
    <label><input type="checkbox" name="show_mobile" value="1" class="ynfullslider_form_displayoptions_checkbox"><?php echo $this->translate("Mobile") ?></label>
    <label><input type="checkbox" name="show_tablet" value="1" class="ynfullslider_form_displayoptions_checkbox"><?php echo $this->translate("Tablet") ?></label>
</fieldset>

<script>
    en4.core.runonce.add(function() {
        var parent, ele;
        $$('.ynfullslider_form_displayoptions_checkbox').removeEvents('click').addEvent('click', function(el){
            ele = el.target;
            if (ele.name == "show_all") {
                selectAll(ele);
            } else {
                parent = ele.getParent('[class=ynfullslider_form_displayoptions]');
                if (!ele.checked) {
                    parent.getFirst('label').getFirst('input').checked = false;
                }
            }
        });
    });

    function selectAll(el)
    {
        var parent = el.getParent('[id=show_options]');
        var checkBoxes = parent.getChildren('label');
        var i, len = checkBoxes.length;
        for (i = 1; i < len; i++) {
            checkBoxes[i].getChildren('input')[0].checked = checkBoxes[0].getChildren('input')[0].checked;
        }
    }
</script>
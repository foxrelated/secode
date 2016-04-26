<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css'); ?>

<script type="text/javascript">
    var locationAction = function(cityValue)
    {
        if ($("tag"))
            $("tag").value = '';
        var form;
        if ($('filter_form')) {
            form = document.getElementById('filter_form');
        } else if ($('filter_form_location')) {
            form = $('filter_form_location');
        }
        form.elements['location'].value = cityValue;

        form.submit();
    }
</script>

<ul class="siteevent_popular_locations">
    <form id='filter_form_location' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), "siteevent_general", true) ?>' style='display: none;'>
        <input type="hidden" id="location" name="location"  value=""/>
    </form>
    <?php foreach ($this->siteeventLocation as $siteeventLocation): ?>
        <?php if (!empty($siteeventLocation->city) || !empty($siteeventLocation->state)): ?>
            <li <?php if (!empty($this->searchLocation) && ( $this->searchLocation == $siteeventLocation->city || $this->searchLocation == $siteeventLocation->state )): ?>style="font-weight: bold;" <?php endif; ?>>
                <span class="fright mleft5">
                    <?php
                    if (!empty($siteeventLocation->city)): echo "" . $siteeventLocation->count_location . "";
                    else: echo "" . $siteeventLocation->count_location_state . "";
                    endif;
                    ?>
                </span>
                <span class="o_hidden">
                    <a href="javascript:void(0);" onclick="locationAction('<?php if (!empty($siteeventLocation->city))
                        echo $siteeventLocation->city;
                    else
                        echo $siteeventLocation->state;
                    ?>')" ><?php echo ucfirst($siteeventLocation->city) ?><?php
                    $state = null;
                    if (!empty($siteeventLocation->city) && !empty($siteeventLocation->state))
                        $state.=" [";$state.=ucfirst($siteeventLocation->state);
                    if (!empty($siteeventLocation->city) && !empty($siteeventLocation->state))
                        $state.="] ";echo $state;
                    ?></a>
                </span>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
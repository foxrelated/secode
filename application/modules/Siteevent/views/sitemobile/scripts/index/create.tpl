<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$defaultProfileFieldId = Engine_Api::_()->getDbTable('metas', 'siteevent')->defaultProfileId();
$defaultProfileFieldId = "0_0_$defaultProfileFieldId";
?>
<?php $hasPackageEnable = Engine_Api::_()->siteevent()->hasPackageEnable(); 
if ($hasPackageEnable): ?>
    <?php $this->PackageCount = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getPackageCount(); ?>
<?php endif; ?>
<?php
$this->headTranslate(array('edit', 'Date & Time', 'on the following days', 'Specific dates and times are set for this event.'));
?>

<?php if ($this->parentTypeItem->getType() != 'user'): ?>
    <div class="siteevent_viewevents_head">
        <?php echo $this->htmlLink($this->parentTypeItem->getHref(), $this->itemPhoto($this->parentTypeItem, 'thumb.icon', '', array('align' => 'left'))) ?>
        <h2>	
            <?php echo $this->parentTypeItem->__toString() ?>	
            <?php echo $this->translate('&raquo; '); ?>
            <?php echo $this->translate('Events'); ?>
        </h2>
    </div><br />
<?php endif; ?>

<?php $coreSettings = Engine_Api::_()->getApi('settings', 'core'); ?>
<script type="text/javascript">

    sm4.core.runonce.add(function() {
        checkDraft(); 
<?php if ($coreSettings->getSetting('siteevent.onlineevent.allow', 1) == 1) : ?>
            //ADD A LINK WITH VENUE NAME FIELD:
            var language = sm4.core.language.translate("online event");
            if ($('#venue_name-element').length > 0) {
                $("#venue_name-element").append("<div id='myDiv'>Running an <a href='javascript:void(0);'  name='online_event' onclick='siteeventCreateIsOnline(true);return false;'>" + language + "</a> </div>");
                siteeventCreateIsOnline(false);
    <?php if (!empty($_POST) && !empty($_POST['is_online'])) : ?>
                    siteeventCreateIsOnline(true);
    <?php endif; ?>
            }
<?php endif; ?>


        var locationEl = $.mobile.activePage.find('#location');
        
        if (locationEl) {
            var autocomplete = new google.maps.places.Autocomplete(locationEl.get(0));
            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }
                $.mobile.activePage.find('#latitude').val(place.geometry.location.lat());
                $.mobile.activePage.find('#longitude').val(place.geometry.location.lng());
            });

        }
    });

    function checkDraft() {
        if ($('#draft') && $('#search-wrapper')) {
            if ($('#draft').val() == 1) {
                $('#search-wrapper').css("display", "none");
                $("#search").checked = false;
            } else {
                $('#search-wrapper').css("display", "block");
                $("#search").checked = true;
            }
        }
    }
    sm4.core.runonce.add(function() {
        sm4.core.Module.autoCompleter.attach("tags", '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest'), 'default', true) ?>', {'singletextbox': true, 'limit': 10, 'minLength': 1, 'showPhoto': false, 'search': 'text'}, 'toValues');

    });
</script>
<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
))
?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<div class='siteevent_event_form'>
    <?php if ($this->current_count >= $this->quota && !empty($this->quota)): ?>
        <div class="tip"> 
            <span>
                <?php echo $this->translate("You have already created the maximum number of events allowed."); ?>
            </span>
        </div>
        <br/>
    <?php elseif ($this->category_count > 0): ?>
        <?php if ($this->siteevent_render == 'siteevent_form'): ?>
            <?php if ($hasPackageEnable && $this->PackageCount > 0): ?>
                <h3><?php echo $this->translate("Create New Event") ?></h3>
<!--                <p><?php echo $this->translate("Create an event using these quick, easy steps and get going."); ?></p>	-->
                <h4 class="siteevent_create_step"><?php echo $this->translate("2. Configure your event based on the package you have chosen."); ?></h4>
                <div class='siteeventpage_layout_right'>      
                    <div class="siteevent_package_page p5">          
                        <ul class="siteevent_package_list">
                            <li class="p5">
                                <div class="siteevent_package_list_title">
                                    <h3><?php echo $this->translate('Package Details'); ?>: <?php echo $this->translate(ucfirst($this->package->title)); ?></h3>
                                </div>           
                                <div class="siteevent_package_stat"> 
                                    <?php if (in_array('price', $this->packageInfoArray)): ?>
                                        <span>
                                            <b><?php echo $this->translate("Price") . ": "; ?> </b>
                                            <?php if (isset($this->package->price)): ?>
                                                <?php
                                                if ($this->package->price > 0):echo $this->locale()->toCurrency($this->package->price, $currency);
                                                else: echo $this->translate('FREE');
                                                endif;
                                                ?>
                                            <?php endif; ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('ticket_type', $this->packageInfoArray)): ?>
                                        <span>
                                            <b><?php echo $this->translate("Price") . ": "; ?> </b>
                                            <?php
                                            if ($this->package->ticket_type):echo $this->translate("PAID & FREE");
                                            else: echo $this->translate('FREE');
                                            endif;
                                            ?>
                                        </span>
                                    <?php endif; ?>                                    
                                    <?php if (in_array('billing_cycle', $this->packageInfoArray)): ?>
                                        <span>
                                            <b><?php echo $this->translate("Billing Cycle") . ": "; ?> </b>
                                            <?php echo $this->package->getBillingCycle() ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('duration', $this->packageInfoArray)): ?>
                                        <span style="width: auto;">
                                            <b><?php echo ($this->package->price > 0 && $this->package->recurrence > 0 && $this->package->recurrence_type != 'forever' ) ? $this->translate("Billing Duration") . ": " : $this->translate("Duration") . ": "; ?> </b>
                                            <?php echo $this->package->getPackageQuantity(); ?>
                                        </span>
                                    <?php endif; ?>
                                    <br />
                                    <?php if (in_array('featured', $this->packageInfoArray)): ?>
                                        <span>
                                            <b><?php echo $this->translate("Featured") . ": "; ?> </b>
                                            <?php
                                            if ($this->package->featured == 1)
                                                echo $this->translate("Yes");
                                            else
                                                echo $this->translate("No");
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('Sponsored', $this->packageInfoArray)): ?>
                                        <span>
                                            <b><?php echo $this->translate("Sponsored") . ": "; ?> </b>
                                            <?php
                                            if ($this->package->sponsored == 1)
                                                echo $this->translate("Yes");
                                            else
                                                echo $this->translate("No");
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('rich_overview', $this->packageInfoArray) && ($this->overview && Engine_Api::_()->authorization()->getPermission($this->viewer->level_id, 'siteevent_event', "overview"))): ?>
                                        <span>
                                            <b><?php echo $this->translate("Rich Overview") . ": "; ?> </b>
                                            <?php
                                            if ($this->package->overview == 1)
                                                echo $this->translate("Yes");
                                            else
                                                echo $this->translate("No");
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('videos', $this->packageInfoArray) && Engine_Api::_()->authorization()->getPermission($this->viewer->level_id, 'siteevent_event', "video")): ?>
                                        <span>
                                            <b><?php echo $this->translate("Videos") . ": "; ?> </b>
                                            <?php
                                            if ($this->package->video == 1)
                                                if ($this->package->video_count)
                                                    echo $this->package->video_count;
                                                else
                                                    echo $this->translate("Unlimited");
                                            else
                                                echo $this->translate("No");
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('photos', $this->packageInfoArray) && Engine_Api::_()->authorization()->getPermission($this->viewer->level_id, 'siteevent_event', "photo")): ?>
                                        <span>
                                            <b><?php echo $this->translate("Photos") . ": "; ?> </b>
                                            <?php
                                            if ($this->package->photo == 1)
                                                if ($this->package->photo_count)
                                                    echo $this->package->photo_count;
                                                else
                                                    echo $this->translate("Unlimited");
                                            else
                                                echo $this->translate("No");
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if (in_array('description', $this->packageInfoArray)): ?>
                                    <div class="siteevent_list_details">
                                        <?php echo $this->translate($this->package->description); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($this->PackageCount > 1): ?>
                                    <div class="siteevent_create_link mtop10 clr">
                                        <a href="<?php echo $this->url(array('action' => 'index'), "siteevent_package", true) ?>">&laquo; <?php echo $this->translate("Choose a different package"); ?></a>
                                    </div>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="siteevent_layout_left">
                <?php endif; ?>
                <?php echo $this->form->setAttrib('class', 'global_form siteevent_create_list_form')->render($this); ?>
                <?php if ($hasPackageEnable && $this->PackageCount > 0): ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <?php echo $this->translate($this->siteevent_formrender); ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script type="text/javascript">
    function smSiteeventRepeatEvent(repeatEvent) {
        if (repeatEvent.val() == "daily") {
            $("#dailyrepeat_interval-wrapper").css("display", "block");
            $("#daily_repeat_time-wrapper").css("display", "block");
            $("#id_weeklyrepeat_interval_select-wrapper").css("display", "none");
            $("#weekly_repeat_time-wrapper").css("display", "none");
            $("#id_monthlyabsolute_day_select-wrapper").css("display", "none");
            $("#id_monthlyrepeat_interval_select-wrapper").css("display", "none");
            $("#monthly_repeat_time-wrapper").css("display", "none");

        } else if (repeatEvent.val() == "weekly") {
            $("#dailyrepeat_interval-wrapper").css("display", "none");
            $("#daily_repeat_time-wrapper").css("display", "none");
            $("#id_weeklyrepeat_interval_select-wrapper").css("display", "block");
            $("#weekly_repeat_time-wrapper").css("display", "block");
            $("#id_monthlyabsolute_day_select-wrapper").css("display", "none");
            $("#id_monthlyrepeat_interval_select-wrapper").css("display", "none");
            $("#monthly_repeat_time-wrapper").css("display", "none");
        } else if (repeatEvent.val() == "monthly") {
            $("#dailyrepeat_interval-wrapper").css("display", "none");
            $("#daily_repeat_time-wrapper").css("display", "none");
            $("#id_weeklyrepeat_interval_select-wrapper").css("display", "none");
            $("#weekly_repeat_time-wrapper").css("display", "none");
            $("#id_monthlyabsolute_day_select-wrapper").css("display", "block");
            $("#id_monthlyrepeat_interval_select-wrapper").css("display", "block");
            $("#monthly_repeat_time-wrapper").css("display", "block");
        } else if (repeatEvent.val() == "never") {
            $("#dailyrepeat_interval-wrapper").css("display", "none");
            $("#daily_repeat_time-wrapper").css("display", "none");
            $("#id_weeklyrepeat_interval_select-wrapper").css("display", "none");
            $("#weekly_repeat_time-wrapper").css("display", "none");
            $("#id_monthlyabsolute_day_select-wrapper").css("display", "none");
            $("#id_monthlyrepeat_interval_select-wrapper").css("display", "none");
            $("#monthly_repeat_time-wrapper").css("display", "none");
        }
    }
    sm4.core.runonce.add(function() {
        var defaultProfileId = '<?php echo '0_0_' . $this->defaultProfileId ?>' + '-wrapper';
    });


    var getProfileType = function(category_id) {
        var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'siteevent')->getMapping('profile_type')); ?>;
        for (i = 0; i < mapping.length; i++) {
            if (mapping[i].category_id == category_id)
                return mapping[i].profile_type;
        }
        return 0;
    }


    sm4.core.runonce.add(function()
    {

        $("#dailyrepeat_interval-wrapper").css("display", "none");
        $("#daily_repeat_time-wrapper").css("display", "none");
        $("#id_weeklyrepeat_interval_select-wrapper").css("display", "none");
        $("#weekly_repeat_time-wrapper").css("display", "none");
        $("#id_monthlyabsolute_day_select-wrapper").css("display", "none");
        $("#id_monthlyrepeat_interval_select-wrapper").css("display", "none");
        $("#monthly_repeat_time-wrapper").css("display", "none");

        var defaultProfileId = '<?php echo $defaultProfileFieldId ?>' + '-wrapper';
        if ($.type($.mobile.activePage.find('#' + defaultProfileId)) && typeof $.mobile.activePage.find('#' + defaultProfileId) != 'undefined') {
            $.mobile.activePage.find('#' + defaultProfileId).css('display', 'none');
        }
    });

</script>

<?php if (0): ?>
    <div style="display:none;" id="expertTips">
        <div class="global_form_popup" style="width:450px;">
            <div class="show_content_body">
                <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.experttips'); ?>
            </div>
            <div class="clr mtop10">
                <button onclick="SmoothboxSEAO.close();"><?php echo $this->translate('Close'); ?></button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function expertTips() {
            SmoothboxSEAO.open('<div>' + $('expertTips').innerHTML + '</div>');
        }
    </script>  
<?php endif; ?>

<style type="text/css">

    .se_create_more{
        display: block !important;
        margin-bottom: 10px;
    }
</style>
<script type="text/javascript">

    if ($('#guest_lists-wrapper')) {
        $('#guest_lists-wrapper').css("display", "none");
    }
    function showGuestLists(option) {

        if ($('#guest_lists-wrapper')) {
            if (option == 0) {
                $('#guest_lists-wrapper').css("display", "block");
            }
            else {
                $('#guest_lists-wrapper').css("display", "none");
            }
        }
    }

</script>
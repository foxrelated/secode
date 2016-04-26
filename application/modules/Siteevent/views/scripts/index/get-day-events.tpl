<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-day-events.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>

<div>
    <b><?php echo date("l", $this->current_date) . ', ' . date("F", $this->current_date) . ' ' . date("j", $this->current_date) . ', ' . date("Y", $this->current_date); ?></b> 
</div>
<div onclick="Smoothbox.close()" class="popup_close fright"><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/cross.png' alt='close'/></div>
<div>
    <?php
    $currentDate = date('Y-m-d', $this->current_date);
    $previous_date = strtotime($currentDate . ' -1 day');
    $next_date = strtotime($currentDate . ' +1 day');
    //GET THE PREVIOUS DATE TEXT
    ?>
    <span>
        <a href="javascript:void(0);" onclick="getDayEvents(<?php echo $previous_date; ?>);"><?php echo (strtotime(' -1 day') == $previous_date) ? $this->translate('yesterday') : ($previous_date == time()) ? $this->translate('today') : date("F", $previous_date) . ' ' . date("j", $previous_date) ; ?> </a> 

    </span>

    <span>
        <a href="javascript:void(0);" onclick="getDayEvents(<?php echo $next_date; ?>);"><?php echo (strtotime(' +1 day') == $next_date) ? $this->translate('tomorrow') : ($next_date == time()) ? $this->translate('today') : ($next_date == strtotime(' -1 day')) ? $this->translate('yesterday') : date("F", $next_date) . ' ' . date("j", $next_date) ; ?>  </a> 
    </span>
</div>

<div id="days_eventlisting">
    <ul class="siteevent_browse_list">
        <?php if (count($this->dayEvents) > 0) : ?>
            <?php foreach ($this->dayEvents as $siteevent): ?>

                <?php
                // Convert the dates for the viewer
                $startDateObject = new Zend_Date(strtotime($siteevent->starttime));
                $endDateObject = new Zend_Date(strtotime($siteevent->endtime));
                if ($this->viewer() && $this->viewer()->getIdentity()) {
                    $tz = $this->viewer()->timezone;
                    $startDateObject->setTimezone($tz);
                    $endDateObject->setTimezone($tz);
                }
                ?>

                <li>
                    <div class='siteevent_browse_list_photo b_medium'>
                        <?php echo $this->htmlLink($siteevent->getHref(), $this->itemPhoto($siteevent, 'thumb.main', '', array('align' => 'center'))); ?>
                    </div>
                    <div class='siteevent_browse_list_info'>

                        <div class="siteevent_browse_list_info_header">
                            <div class="siteevent_list_title_small">
                                <?php echo $this->htmlLink($siteevent->getHref(), $thislocale()->toEventTime($startDateObject, array('size' => $datetimeFormat)), array('title' => $thislocale()->toEventTime($startDateObject, array('size' => $datetimeFormat)))); ?>
                            </div>
                        </div> 


                        <div class="siteevent_browse_list_info_header">
                            <div class="siteevent_list_title_small">
                                <?php echo $this->htmlLink($siteevent->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), 600), array('title' => $siteevent->getTitle())); ?>
                            </div>
                        </div> 

                        <div class='siteevent_browse_list_info_stat seaocore_txt_light'>
                            <?php
                            if (!empty($siteevent->location) && $siteevent->canView() && !empty($this->showContent) && in_array('location', $this->showContent)):
                                echo $siteevent->location;
                                ?>

                            <?php endif; ?>

                        </div>

                        <div class='siteevent_browse_list_info_stat seaocore_txt_light'>

                            <?php if ($this->postedby): ?> - <?php echo $this->translate('led by'); ?>
                                <?php echo $siteevent->getLedBys(); ?><?php endif ?>
                        </div>
                    </div>
                </li>

            <?php endforeach; ?>
        <?php else: ?>

            <?php echo $this->translate('Nothing was scheduled for this day.'); ?>

        <?php endif; ?>
    </ul>
</div>
<div class="seaocore_view_more mtop10 calendarlist_viewmore">
    <?php
    echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
        'id' => '',
        'class' => 'buttonlink icon_viewmore'
    ))
    ?>
</div>
<div class="seaocore_loading" id="" style="display: none;">
    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
    <?php echo $this->translate("Loading...") ?>
</div>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        var view_more_content = $('siteevent_dayevents').getElements('.calendarlist_viewmore');
        view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');

        view_more_content.removeEvents('click');
        view_more_content.addEvent('click', function() {
            if (en4.core.request.isRequestActive())
                return;
            var params = {
                requestParams:<?php echo json_encode($this->params) ?>,
                responseContainer: $('siteevent_dayevents')
            }

            params.requestParams.page =<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>;
            view_more_content.setStyle('display', 'none');
            params.responseContainer.getElements('.seaocore_loading').setStyle('display', '');

            sendAjaxRequestCalendar(params);
        });
    });

    function sendAjaxRequestCalendar(params) {

        var url = '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'index', 'action' =>
        'get-day-events'), 'default', true)?>';

        if (params.requestUrl)
            url = params.requestUrl;

        var request = new Request.HTML({
            url: url,
            data: $merge(params.requestParams, {
                format: 'html',
                is_ajax: true
            }),
            evalScripts: true,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                if (params.requestParams.page == 1) {
                    params.responseContainer.empty();
                    Elements.from(responseHTML).inject(params.responseContainer);
                    <?php if ($this->enableLocation): ?>
                        srInitializeMap(params.requestParams.content_id);
                    <?php endif; ?>
                } else {
                    var element = new Element('div', {
                        'html': responseHTML
                    });
                    params.responseContainer.getElements('.seaocore_loading').setStyle('display', 'none');
                }
                en4.core.runonce.trigger();
                // Smoothbox.bind(params.responseContainer);                                      
            }
        });
        en4.core.request.send(request);
    }
</script>

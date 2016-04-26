<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _calendarEventList.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
$datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');
$isValidOccurrencesExist = Engine_Api::_()->siteevent()->isValidOccurrencesExist();
if (($this->params['page'] == 1) && empty($isValidOccurrencesExist)) :
    ?>
    <div class="siteevent_events_listing_popup" style="min-height: 200px; margin: 5px;">
        <div class="o_hidden siteevent_events_listing_popup_head b_medium">
            <span class="bold mtop5 fleft">
                <?php
                if ($datetimeFormat != 'full')
                    echo $this->locale()->toDate($this->current_date, array('format' => 'EEEE')) . ', ' . $this->locale()->toDate($this->current_date, array('size' => $datetimeFormat));
                else
                    echo $this->locale()->toDate($this->current_date, array('size' => $datetimeFormat));
                ?>
            </span>
            <a href="javascript:void(0);" onclick="toggleCalender();" class="fright" >
                <?php echo $this->translate("View Calendar");?>
            </a>
        </div>

        <div class="siteevent_myevents_top_links o_hidden b_medium">
            <?php

            $currentDateOriginal = date('m/d/Y', strtotime($this->current_date));
            $currentDate = $this->locale()->toDate($this->current_date, array('format' => 'M/d/yyyy'));
            $todaysDate = $this->locale()->toDate($this->todaysDate, array('format' => 'M/d/yyyy'));
            $previous_date = strtotime($currentDate . ' -1 day');
            $next_date = strtotime($currentDate . ' +1 day');

            ?>

            <div data-role="controlgroup"  data-type="horizontal" data-mini="true">
                    <a href="javascript:void(0);" data-icon="arrow-l" data-role="button" onclick="getDayEvents(<?php echo $previous_date; ?>, '<?php echo $this->category_id ?>');">
                            <?php echo (strtotime($todaysDate . ' -1 day') == $previous_date) ? $this->translate('yesterday') : (($previous_date == strtotime($todaysDate)) ? $this->translate('today') : $this->locale()->toDate(strtotime($currentDateOriginal . ' -1 day'), array('format' => 'MMMM')) . ' ' . date("j", $previous_date)) ; ?>                        
                    </a> 
                    <a href="javascript:void(0);" data-icon="arrow-r" data-iconpos="right" data-role="button" onclick="getDayEvents(<?php echo $next_date; ?>, '<?php echo $this->category_id ?>');">
                            <?php                             
                            echo (strtotime($todaysDate . ' +1 day') == $next_date) ? $this->translate('tomorrow') : ( ($next_date == strtotime($todaysDate)) ? $this->translate('today') : (($next_date == strtotime($todaysDate . ' -1 day')) ? $this->translate('yesterday') : ($this->locale()->toDate(strtotime($currentDateOriginal . ' +1 day'), array('format' => 'MMMM')) . ' ' . date("j", $next_date)))) ; ?>
                    </a>
            </div>
            <?php if (!empty($this->categoryIds)) : ?>
                    <select class="mright5" id="category_id" onchange="getDayEvents(<?php echo strtotime($currentDate); ?>, this.value);" value="<?php if (!empty($this->category_id)) : echo $this->category_id;
        endif;
                ?>">
                        <option id="0" value="0"><?php echo $this->translate("All Categories") ?></option>
                        <?php foreach ($this->categoryIds as $category) : ?>
                            <option id="<?php echo $category->category_id ?>" value="<?php echo $category->category_id ?>" <?php if ($this->category_id == $category->category_id) : echo 'selected';
                        endif;?>><?php echo $category->category_name ?></option>
                        <?php endforeach; ?>
                    </select>
            <?php endif; ?>
        </div>
        <div  id="list_view" class="sm-content-list siteevent_events_listing_popup_cont">
            <ul data-role="listview" data-inset="false" class="siteevent_browse_list" id="days_eventlisting">
            <?php endif; ?>  
            <?php if ($this->totalCount) : ?>
                <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
                <?php foreach ($this->paginator as $siteevent): ?>

                    <?php
                    // Convert the dates for the viewer
                    $startDateObject = new Zend_Date(strtotime($siteevent->starttime));
                    ?>

                    <li data-icon="arrow-r" class="b_medium">
                        <a href="<?php echo $siteevent->getHref(); ?>">
                         <?php echo $this->itemPhoto($siteevent, 'thumb.icon'); ?>
                          <h3><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), 30); ?></h3>                           
                            <?php
                            $hostDisplayName = $siteevent->getHostName(true);
                            if (!empty($hostDisplayName)) {
                                if (is_array($hostDisplayName)) {
                                    echo '<div class="siteevent_listings_stats">';
                                    //echo $hostDisplayName['displayImage'];
                                    echo '<i class="siteevent_icon_strip siteevent_icon siteevent_icon_host" title="' . $this->translate('Host') . '"></i>';
                                    echo '<div class="o_hidden">
                      <b>' . $hostDisplayName['displayName'] . '</b>
                  </div>
                </div>';
                                } else {
                                    echo '<div class="siteevent_listings_stats siteevent_listings_host event_host ' . $className . ' ">';

                                    echo '<i class="siteevent_icon_strip siteevent_icon siteevent_icon_host" title="' . $this->translate('Host') . '"></i>';
                                    echo '<div class="o_hidden">
                    <b>' . $hostDisplayName . '</b>
                  </div>
                </div>';
                                }
                            }
                            ?>

                            <div class="siteevent_listings_stats">
                                <i class="siteevent_icon_strip siteevent_icon siteevent_icon_time" title="<?php echo $this->translate('Start Time') ?>"></i>
                                <div class="o_hidden">
                            <?php echo $this->locale()->toEventTime($startDateObject, array('size' => $datetimeFormat)); ?>
                                </div>
                            </div>

<!--                            <div class="siteevent_listings_stats">
                                <i class="siteevent_icon_strip siteevent_icon siteevent_icon_tag" title="<?php echo $this->translate('Category') ?>"></i>
                                <div class="o_hidden">
        <?php echo $siteevent->getCategory()->getTitle(true) ?>
                                </div>
                            </div>-->

                            <?php $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1); ?>
                            <?php if ($locationEnabled && !empty($siteevent->location)): ?>
                                <div class="siteevent_listings_stats">
                                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_location" title="<?php echo $this->translate('Location') ?>"></i>
                                    <div class="o_hidden">
                                <?php echo $siteevent->location; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                          </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                    <div class="tip txt_center">
                <?php 
                
                     if(isset($this->params['ismanage']) && $this->params['ismanage'])
                        echo $this->translate('You do not have any event matching this criteria.'); 
                     else
                       echo $this->translate('Nobody has created an event with this criteria.');
                     ?>
                    </div>
            <?php endif; ?>
            <?php if ($this->params['page'] == 1) : ?>
            </ul>
            <div class="feed_viewmore clr"  id="calendarlist_viewmore" style="margin-bottom: 5px;">
                <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
                    'id' => 'feed_viewmore_link',
                    'class' => 'ui-btn-default icon_viewmore'                   
                ))
                ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<script type="text/javascript">
    sm4.core.runonce.add(function() {
        var view_more_content = $.mobile.activePage.find('#calendarlist_viewmore');
        view_more_content.css('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');

        view_more_content.unbind('click');
        view_more_content.bind('click', function() {
            if (sm4.core.request.isRequestActive())
                return;
            var params = {
                requestParams:<?php echo json_encode($this->params) ?>,
                responseContainer: $.mobile.activePage.find('#days_eventlisting')
            }

            params.requestParams.page =<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>;
            view_more_content.css('display', 'none');
            //$.mobile.showPageLoadingMsg();

            sendAjaxRequestCalendar(params);
        });

    });

    function sendAjaxRequestCalendar(params) {

        var url = sm4.core.baseUrl + 'widget/index/mod/siteevent/name/calendarview-siteevent';

        if (params.requestUrl)
            url = params.requestUrl;

       $.ajax({
            url: url,
            data: $.extend(params.requestParams, {
                format: 'html',
                is_ajax: true
            }),
            evalScripts: true,
            success: function(responseHTML) {
               // $.mobile.hidePageLoadingMsg();
                $.mobile.activePage.find('.siteevent_events_listing_popup_cont').find('ul').append(responseHTML);
                $.mobile.activePage.find('.siteevent_events_listing_popup_cont').find('ul').listview('refresh');
                sm4.core.dloader.refreshPage();
                sm4.core.runonce.trigger();                                     
            }
        });

    }
    
    function toggleCalender(){
      $.mobile.activePage.find('#dyanamic_code').css('display','block');
      $.mobile.activePage.find('#event_list_content').css('display','none');
    }
</script>
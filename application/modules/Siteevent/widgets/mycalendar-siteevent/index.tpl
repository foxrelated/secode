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
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js'); ?>
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<?php if ($this->viewtype == 'calendar') : ?>
    <?php if (!$this->isajax): ?>
        <?php include APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_managequicklinks.tpl'; ?>
        <script type="text/javascript">
            var calendar_params = <?php echo Zend_Json_Encoder::encode($this->params); ?>;

            // function to switch to next and last months through ajax request
            function another_month(date_current, prev_next) {
                if (prev_next == 'prev')
                    $('prev_month').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/loading.gif" />';
                else {
                    $('calendar_viewmore').style.display = 'none';
                    //$('feed_viewmore').style.display = 'none';
                    $('calendar_loading').style.display = '';
                }
                var data_another_month = {
                    'date_current': date_current,
                    'format': 'html',
                    'is_ajax': true,
                    'prev_next': prev_next,
                    calendar_params: <?php echo Zend_Json_Encoder::encode($this->params); ?>
                };
                en4.core.request.send(new Request.HTML({
                    'url': en4.core.baseUrl + 'widget/index/mod/siteevent/name/mycalendar-siteevent',
                    'data': data_another_month,
                    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

                        if (prev_next == 'prev') {
                            $('prev_month').destroy();
                            Elements.from(responseHTML).reverse().inject($('dyanamic_code'), 'top');
                        }
                        else {
                            Elements.from(responseHTML).reverse().inject($('dyanamic_code'));

                        }
                        en4.core.runonce.trigger();
                    }
                }));
            }


            /* moo style */

        <?php if ($this->invite_count > 0 && !$this->isajax) : ?>

                window.addEvent('domready', function() {

                    getInvitedList('calendar', <?php echo $this->invite_count; ?>);

                });

        <?php endif; ?>
        </script>
        <div class='siteevent_manage_event'>
            <div class="siteevent_myevents_top_links o_hidden b_medium">
                <div class="fleft siteevent_myevents_view_links">
                    <span class="seaocore_button fleft">
                        <a href='<?php echo $this->url(array('action' => 'manage', 'ref' => 'list'), "siteevent_general", true); ?>' >
                            <span><?php echo $this->translate('List'); ?></span>
                        </a>
                    </span>
                    <span class="seaocore_button fleft seaocore_button_selected">
                        <a href='<?php echo $this->url(array('action' => 'manage', 'ref' => 'calendar'), "siteevent_general", true); ?>' >
                            <span><?php echo $this->translate('Calendar'); ?></span>
                        </a>
                    </span>     
                </div>
            </div> 

            <div class="siteevent_main_calendar_wrapper" id="dyanamic_code">
            <?php endif; ?>

            <?php if ($this->prev_next == 'prev') : ?>
                <div id='prev_month' class="siteevent_main_calendar_prev_month txt_center mbot10">
                    <a class="prev_month_l" href="javascript:void(0);" onclick='another_month(<?php echo $this->date_last ?>, "prev")'><?php echo $this->locale()->toDate($this->date_last, array('format' => 'MMMM')) . ' ' . date("Y", $this->date_last); ?></a>
                </div>
            <?php endif; ?>
            <?php if (!$this->isajax): ?>
                <div id="calendar_invited_list">

                </div>
            <?php endif; ?>

            <div id="calender" class="siteevent_main_calendar siteevent_calendar b_medium br_body_bg">
                <div class="siteevent_main_calendar_mname b_medium bold f_small">

                    <?php echo $this->translate($this->current_month_text); ?><?php echo " \t" . $this->current_year; ?>
                </div>
                <table cellpadding="0" cellspacing="0" class="siteevent_main_calendar_table">
                    <?php $day_start = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.calendar.daystart', 1);
                    ?>
                    <tr class="siteevent_main_calendar_row siteevent_main_calendar_row_head b_medium">
                        <?php if ($day_start == 1) : ?>
                            <td class='b_medium' title="<?php echo $this->translate('SITEEVENT_DAY_SUNDAY') ?>"><div class="day seaocore_txt_light"><?php
                                    echo
                                    $this->translate("SITEEVENT_DAY_SUNDAY")
                                    ?></div></td>

                            <?php $calendarday_array = array(1 => 7, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5, 7 => 6); ?>
                            <?php
                        elseif ($day_start == 3):

                            $calendarday_array = array(1 => 6, 2 => 7, 3 => 1, 4 => 2, 5 => 3, 6 => 4, 7 => 5);
                            ?>

                            <td class='b_medium' title="<?php echo $this->translate('SITEEVENT_DAY_SATURDAY') ?>"><div class="day seaocore_txt_light "><?php
                                    echo
                                    $this->translate("SITEEVENT_DAY_SATURDAY")
                                    ?></div></td>
                            <td class='b_medium' title="<?php echo $this->translate('SITEEVENT_DAY_SUNDAY') ?>"><div class="day seaocore_txt_light"><?php
                                    echo
                                    $this->translate("SITEEVENT_DAY_SUNDAY")
                                    ?></div></td>
                            <?php
                        else :

                            $calendarday_array = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7);
                            ?>
                                <?php endif; ?>
                        <td class='b_medium' title="<?php echo $this->translate('SITEEVENT_DAY_MONDAY') ?>"><div class="day seaocore_txt_light"><?php
                                echo
                                $this->translate("SITEEVENT_DAY_MONDAY");
                                ?></div></td>
                        <td class='b_medium' title="<?php echo $this->translate('SITEEVENT_DAY_TUESDAY') ?>"><div class="day seaocore_txt_light"><?php
                                echo
                                $this->translate("SITEEVENT_DAY_TUESDAY")
                                ?></div></td>
                        <td class='b_medium' title="<?php echo $this->translate('SITEEVENT_DAY_WEDNESDAY') ?>"><div class="day seaocore_txt_light"><?php
                                echo
                                $this->translate("SITEEVENT_DAY_WEDNESDAY")
                                ?></div></td>
                        <td class='b_medium' title="<?php echo $this->translate('SITEEVENT_DAY_THURSDAY') ?>"><div class="day seaocore_txt_light"><?php
                                echo
                                $this->translate("SITEEVENT_DAY_THURSDAY")
                                ?></div></td>
                        <td class='b_medium' title="<?php echo $this->translate('SITEEVENT_DAY_FRIDAY') ?>"><div class="day seaocore_txt_light"><?php
                                echo
                                $this->translate("SITEEVENT_DAY_FRIDAY")
                                ?></div></td>

                                <?php if ($day_start != 3) : ?>
                            <td class='b_medium' title="<?php echo $this->translate('SITEEVENT_DAY_SATURDAY') ?>"><div class="day seaocore_txt_light"><?php
                                    echo
                                    $this->translate("SITEEVENT_DAY_SATURDAY")
                                    ?></div></td>
                                <?php endif; ?>
                                <?php if ($day_start == 2) : ?>
                            <td class='b_medium' title="<?php echo $this->translate('SITEEVENT_DAY_SUNDAY') ?>"><div class="day seaocore_txt_light"><?php
                                    echo
                                    $this->translate("SITEEVENT_DAY_SUNDAY")
                                    ?></div></td>
                    <?php endif; ?>
                    </tr>
                    <?php
                    $i = 1;
                    $date = 1;
                    $empty = 1;
                    $flag = 0;
                    $next_month = false;
                    $prev_month = false;
                    $before_startday = 0;
                    $calendarStartWeekday = $calendarday_array[1];
                    ?>
                    <?php
                    $total_cells = $this->total_cells;
                    $ceil = 1;
                    ?>


                    <?php $total_month_birthcount = 0; ?>
                    <?php while ($total_cells > 0) : ?>

                        <?php if (($i - 1) % 7 == 0) {
                            $ceil = 1
                            ?>
                            <tr class="siteevent_main_calendar_row siteevent_main_calendar_row_list b_medium">
                            <?php } ?>
                            <?php
                            $currentdate = mktime(0, 0, 0, $this->current_month, $date, $this->current_year);

                            $week_day_of_month = date("w", $currentdate);
                            if ($week_day_of_month == 0)
                                $week_day_of_month = 7;
                            ?>
                            <td <?php if ($calendarday_array[$ceil] == $week_day_of_month && $date <= $this->noOfDays) : ?> class="b_medium <?php echo date("Ymd", $currentdate); ?>" <?php else : ?> class="b_medium" <?php endif; ?> >

                                <?php
                                if ($calendarday_array[$ceil] == $week_day_of_month && $date <= $this->noOfDays) {
                                    //NOW WE WILL SHOW EVENT LISTING ON A PARTICULAR DAY IF EXIST
                                    if (count($this->monthEventResults) > 0 && isset($this->monthEventResults[$date])):
                                        ?>
                                        <?php
                                        $class = 'active day';
                                        if ($date == date('d', time()) && $this->current_month == date('m', time()))
                                            $class = 'day current_date';
                                        ?>
                                        <div class="<?php echo $class; ?>">

                                            <?php echo '<a href="javascript:void(0);" class="bold">' . $date . '</a>'; ?>
                                            <div id="view_<?php echo $date; ?>">
                                                <ul class="eventsList">
                                                    <?php
                                                    foreach ($this->monthEventResults[$date] as $key => $dayEvents):

                                                        if ($key <= 2) :
                                                            ?>
                                                            <li class="f_small mtop5 c_event">
                                                            <?php $event_href = Engine_Api::_()->getItem('siteevent_event', $dayEvents['event_id'])->getHref() . '/' . $dayEvents['occurrence_id']; ?>
                                                                <a href='<?php echo $event_href; ?>' title= '<?php echo $dayEvents['title']; ?>' target= '_parent' class= 'seao_common_add_tooltip_link' rel= 'siteevent_event <?php echo $dayEvents['event_id']; ?> <?php echo $dayEvents['occurrence_id']; ?>'><span class="f_small bold"><?php echo $this->locale()->toTime($dayEvents['starttime_database']); ?></span> <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($dayEvents['title'], 15); ?></a>
                                                            </li>   
                                                            <?php
                                                        else:
                                                            $currentdate = mktime(0, 0, 0, $this->current_month, $date, $this->current_year);
                                                            echo '<li class="f_small"><a href="javascript:void(0);" onclick="getDayEvents(' . $currentdate . ')">' . (count($this->monthEventResults[$date]) - 3) . ' ' . $this->translate('more') . '...</a></li>';
                                                            break;
                                                        endif;
                                                        ?>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <?php
                                    else :

                                        if ($date == date('d', time()) && $this->current_month == date('m', time()))
                                            echo '<div class="day current_date"><a href="javascript:void(0)" class="bold">' . $date . '</a></div>';
                                        else
                                            echo '<div class="day"><a href="javascript:void(0)" class="bold">' . $date . '</a></div>';
                                    endif;

                                    $date = $date + 1;
                                    ?>
                                    <script>
                                        en4.core.runonce.add(function() {
                                            $$('.<?php echo date("Ymd", $currentdate); ?>').addEvent('click', function() {

                                                getDayEvents(<?php echo $currentdate; ?>);
                                            })
                                        });
                                    </script>

                                    <?php
                                } else {
                                    $currentdate_temp = mktime(23, 59, 59, $this->current_month, 1, $this->current_year);
                                    if ($i < $this->noOfDays) {
                                        $before_startday++;
                                        if ($this->first_day_of_month > $calendarStartWeekday) {
                                            $days_diff = ($this->first_day_of_month - $calendarStartWeekday) * (24 * 3600);
                                            $calendarStartWeekday++;
                                        } else {
                                            $days_diff = (7 - $calendarStartWeekday + $this->first_day_of_month) * (24 * 3600);
                                            $calendarStartWeekday++;
                                        }

                                        $currentdate_temp = $currentdate_temp - $days_diff;
                                        if (!$prev_month) {
                                            echo '<div class="day npm_day"><a href="javascript:void(0);" class="' . date("Ymd", ($currentdate_temp)) . '">' . date("j", ($currentdate_temp)) . ' ' . $this->locale()->toDate($currentdate_temp, array('format' => 'MMMM')) . '</div>';
                                            $prev_month = true;
                                        } else {
                                            echo '<div class="day npm_day"><a href="javascript:void(0);" class="' . date("Ymd", ($currentdate_temp)) . '">' . date("j", ($currentdate_temp)) . '</div>';
                                        }
                                    } elseif ($i > ($this->noOfDays + $before_startday)) {
                                        $currentdate_temp = mktime(23, 59, 59, $this->current_month, $this->noOfDays, $this->current_year);
                                        $days_diff = ($i - $this->noOfDays - $before_startday) * (24 * 3600);
                                        $currentdate_temp = $currentdate_temp + $days_diff;
                                        if (!$next_month) {
                                            echo'<div class="day npm_day"><a href="javascript:void(0);" class="' . date("Ymd", ($currentdate_temp)) . '">' . date("j", ($currentdate_temp)) . ' ' . $this->locale()->toDate($currentdate_temp, array('format' => 'MMMM')) . '</div>';
                                            $next_month = true;
                                        }
                                        else
                                            echo '<div class="day npm_day"><a href="javascript:void(0);" class="' . date("Ymd", ($currentdate_temp)) . '">' . date("j", ($currentdate_temp)) . '</a></div>';
                                    }
                                    ?>
                                    <script>
                                        en4.core.runonce.add(function() {
                                            $$('.<?php echo date("Ymd", $currentdate_temp); ?>').addEvent('click', function() {

                                                getDayEvents(<?php echo $currentdate_temp; ?>);
                                            })
                                        });
                                    </script>

                                    <?php
                                }

                                $ceil = $ceil + 1;
                                ?>

                            </td>

                        <?php if ($i % 7 == 0) { ?>
                            </tr>
                        <?php } //}  ?>
                        <?php $i = $i + 1; ?>
                        <?php
                        $total_cells = $total_cells - 1;
                        if ($date <= $this->noOfDays && !$total_cells)
                            $total_cells = 7;
                        ?>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php endif; ?>
        <?php if (!$this->isajax): ?>   
        </div>

        <div class="seaocore_view_more" id="calendar_viewmore" style="display: none;">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->locale()->toDate($this->date_next, array('format' => 'MMMM')). ' ' . date("Y", $this->date_next), array(
                'id' => 'calendar_viewmore_link',
                'class' => 'buttonlink icon_viewmore'
            ))
            ?>
        </div>

        <div id="calendar_loading" style="display: none;" class="seaocore_view_more">
            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="Loading" />
    <?php echo $this->translate("Loading...") ?>
        </div>
    </div>
<?php endif; ?>
        
<?php if ($this->prev_next == 'next' || !$this->isajax) : ?>
    <script type="text/javascript">

        en4.core.runonce.add(function() {
            window.onscroll = doOnScrollLoadActivity;
            $('calendar_viewmore').style.display = '';
            //$('feed_viewmore').style.display = 'none';
            $('calendar_loading').style.display = 'none';

            $('calendar_viewmore_link').removeEvents('click').addEvent('click', function(event) {
                event.stop();
                another_month(<?php echo $this->date_next; ?>, 'next');
            });
            function doOnScrollLoadActivity()
            {
                if ($('calendar_viewmore')) {
                    if (typeof($('calendar_viewmore').offsetParent) != 'undefined') {
                        var elementPostionY = $('calendar_viewmore').offsetTop;
                    } else {
                        var elementPostionY = $('calendar_viewmore').y;
                    }
                    if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {
                        another_month(<?php echo $this->date_next; ?>, 'next');
                    }
                }
            }
        });

    </script>
<?php endif;?>

<div id="join_form_options" style="display:none;">
    <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this); ?>
</div>
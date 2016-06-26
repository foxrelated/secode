<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<?php
$TodayNumber = date('N');
$today = date('d');
$plusDay = 0;
?>

<div class="heevent-block">
    <h3 class="heevent-widget"><?php echo $this->translate('Upcoming Events') ?></h3>
    <ul class="event_cal">
        <li class="eweek this-week-btn week-btn"><?php echo $this->translate('HEEVENT_This week') ?></li>
    </ul>
    <div class="week-cal-cont">
        <ul class="event_cal this-week-cal week-cal active">
            <?php while ($TodayNumber++ < 8) { ?>
                <li <?php if (!$plusDay) echo 'class="today-events"'; ?>>
                    <div class="eevent_cont">
                        <?php while ($event = $this->allevents[$today][0]) { ?>
                            <div class="eevent">
                                <span><?php echo ($event->view_count === 'Going') ? $this->translate('Ongoing') : $this->locale()->toTime($event->starttime) ?></span>
                                <a href="<?php echo $event->getHref() ?>"><?php echo $event->getTitle() ?></a>
                            </div>
                            <?php array_shift($this->allevents[$today]);
                        } ?>
                    </div>
                    <div class="edate">
                        <label><?php echo $today; ?></label>
                        <span><?php echo !$plusDay? $this->translate('HEVENT_Today'):$this->translate('HEVENT_' . date('l', strtotime("+ " . $plusDay . " day"))); ?></span>
                    </div>
                </li>
                <?php $today = date('d', strtotime("+ " . ++$plusDay . " day"));
            } ?>
        </ul>
    </div>
    <ul class="event_cal">
        <li class="eweek next-week-btn week-btn"><?php echo $this->translate('HEEVENT_Next week') ?></li>
    </ul>
    <div class="week-cal-cont">
        <ul class="event_cal next-week-cal week-cal">
            <?php $j = 1;
            while ($j++ < 8) {
                ?>
                <li>
                    <div class="eevent_cont">
                        <?php while ($event = $this->allevents[$today][0]) { ?>
                            <div class="eevent">
                                <span><?php echo $this->locale()->toTime($event->starttime) ?></span> <a
                                    href="<?php echo $event->getHref() ?>"><?php echo $event->getTitle() ?></a>
                            </div>
                            <?php array_shift($this->allevents[$today]);
                        } ?>
                    </div>
                    <div class="edate">
                        <label><?php echo $today; ?></label>
                        <span><?php echo $this->translate('HEVENT_' . date('l', strtotime("+ " . $plusDay . " day"))); ?></span>
                    </div>
                </li>
                <?php $today = date('d', strtotime("+ " . ++$plusDay . " day"));
            } ?>
        </ul>
    </div>
</div>

<script type="text/javascript">
    window.addEvent('domready', function () {
        var slideEvents = function (toSlide) {
            var sLen = toSlide.length;
            var i;
            for (i = 0; i < sLen; i++) {
                var li = toSlide[i];
                var eventCount = li.getElements('.eevent').length;
                if (eventCount < 2) continue;
                if (li._nterv)
                    window.clearInterval(li._nterv);
                li._evIndex = 0;
                li._fCh = li.getElement('.eevent');
                li._evCount = eventCount;

                li._nterv = setInterval((function (li) {
                    return function () {
                        var index = li._evIndex % li._evCount;
                        var ml = '-' + (100.5 * index) + '%';
                        li._fCh.setStyle('margin-left', ml);
                        li._evIndex = ++index;
                        li.addClass('sliding');
                        setTimeout(function () {
                            li.removeClass('sliding');
                        }, 500);
                    };
                })(li), 4000 + Math.round(Math.random() * 1000 - 1000));
            }
        };
        var init = function () {
            var tW = $$('.this-week-cal')[0];
            var nW = $$('.next-week-cal')[0];
            var tWBtn = $$('.this-week-btn')[0];
            var nWBtn = $$('.next-week-btn')[0];

            var tWMarginTop = '-' + tW.getHeight() + 'px';
            var nWMarginTop = '-' + nW.getHeight() + 'px';
            tW.setStyle('margin-top', tWMarginTop);
            nW.setStyle('margin-top', nWMarginTop);
            tWBtn.addEvent('click', function (e) {
                nW.removeClass('active');
                tW.addClass('active');
            });
            nWBtn.addEvent('click', function (e) {
                tW.removeClass('active');
                nW.addClass('active');
            });
        };
        init();
        slideEvents($$('.week-cal li'));
    });
</script>
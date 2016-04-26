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
            <!--    <a class="fright" href="javascript:void(0);" onclick="Smoothbox.close()">-->
            <a class="fright seao_smoothbox_lightbox_close" href="javascript:void(0);" >
                <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/closebox.png' alt='close'/>
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

            <div class="fleft siteevent_myevents_view_links">
                <span class="seaocore_button fleft">
                    <a href="javascript:void(0);" onclick="getDayEvents(<?php echo $previous_date; ?>, '<?php echo $this->category_id ?>');">
                        <span>
                            &laquo;
                            <?php echo (strtotime($todaysDate . ' -1 day') == $previous_date) ? $this->translate('yesterday') : (($previous_date == strtotime($todaysDate)) ? $this->translate('today') : $this->locale()->toDate(strtotime($currentDateOriginal), array('format' => 'MMMM')) . ' ' . date("j", $previous_date)) ; ?>
                        </span>
                    </a> 
                </span>
                <span class="seaocore_button fleft">
                    <a href="javascript:void(0);" onclick="getDayEvents(<?php echo $next_date; ?>, '<?php echo $this->category_id ?>');">
                        <span>
                            <?php 
                            
                            echo (strtotime($todaysDate . ' +1 day') == $next_date) ? $this->translate('tomorrow') : ( ($next_date == strtotime($todaysDate)) ? $this->translate('today') : (($next_date == strtotime($todaysDate . ' -1 day')) ? $this->translate('yesterday') : ($this->locale()->toDate(strtotime($currentDateOriginal . ' +2 day'), array('format' => 'MMMM')) . ' ' . date("j", $next_date)))) ; ?>
                            &raquo;
                        </span>
                    </a> 
                </span>
            </div>
            <?php
            // SHOW EVENT CREATE LINK IF THE VIEWER IS ALLOWED 
            $is_createallowed = Siteevent_Plugin_Menus::canCreateSiteevents('');
            if ($is_createallowed) {
                ?>
                <span class="seaocore_button fright">
                  <?php if (Engine_Api::_()->siteevent()->hasPackageEnable()):?>
                        <a href='<?php echo $this->url(array('action' => 'index'), "siteevent_package", true) ?>'>
                          <span>+ <?php echo $this->translate('Create an event'); ?></span>
                        </a>
                    <?php else:?>
                        <a href='<?php echo $this->url(array('action' => 'create'), "siteevent_general", true) ?>'>
                          <span>+ <?php echo $this->translate('Create an event'); ?></span>
                        </a>
                        <?php endif; ?>          
                </span>

            <?php } ?>
            <?php if (!empty($this->categoryIds)) : ?>
                <div class="fright mright5">
                    <select class="mright5" id="category_id" onchange="getDayEvents(<?php echo strtotime($currentDate); ?>, this.value);" value="<?php if (!empty($this->category_id)) : echo $this->category_id;
        endif;
                ?>">
                        <option id="0" value="0"><?php echo $this->translate("All Categories") ?></option>
                        <?php foreach ($this->categoryIds as $category) : ?>
                            <option id="<?php echo $category->category_id ?>" value="<?php echo $category->category_id ?>" <?php if ($this->category_id == $category->category_id) : echo 'selected';
                        endif;?>><?php echo $category->category_name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>
        <div  class="siteevent_events_listing_popup_cont">
            <ul class="siteevent_browse_list" id="days_eventlisting">
            <?php endif; ?>  
            <?php if ($this->totalCount) : ?>
                <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
                <?php foreach ($this->paginator as $siteevent): ?>

                    <?php
                    // Convert the dates for the viewer
                    $startDateObject = new Zend_Date(strtotime($siteevent->starttime));
                    ?>

                    <li class="b_medium">
                        <div class='siteevent_browse_list_photo b_medium'>
                            <?php echo $this->htmlLink($siteevent->getHref(), $this->itemPhoto($siteevent, 'thumb.main', '', array('align' => 'center'))); ?>
                        </div>
                        <div class='siteevent_browse_list_info'>
                            <div class="siteevent_browse_list_info_header">
                                <div class="siteevent_list_title_small">
        <?php echo $this->htmlLink($siteevent->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), 600), array('title' => $siteevent->getTitle())); ?>
                                </div>
                            </div>

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
														<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) : ?>
                               <?php if(!empty($siteevent->price) && $siteevent->price > 0) :?>
																	<div class="siteevent_listings_stats">
																			<i class="siteevent_icon_strip siteevent_icon siteevent_icon_price" title="<?php echo $this->translate('Price') ?>"></i>
																			<div class="o_hidden">
																	<?php echo $this->locale()->toCurrency($siteevent->price, $currency) ?>
																			</div>
																	</div>
																<?php else :?>
																	<div class="siteevent_listings_stats siteevent_listings_price_free">
																			<i class="siteevent_icon_strip siteevent_icon siteevent_icon_price" title="<?php echo $this->translate('Price') ?>"></i>
																			<div class="o_hidden">
																				<?php echo $this->translate("FREE") ?>
																			</div>
																	</div>
																<?php endif;?>
                            <?php endif; ?>

                            <div class="siteevent_listings_stats">
                                <i class="siteevent_icon_strip siteevent_icon siteevent_icon_tag" title="<?php echo $this->translate('Category') ?>"></i>
                                <div class="o_hidden">
        <?php echo $this->htmlLink($siteevent->getCategory()->getHref(), $siteevent->getCategory()->getTitle(true)) ?>
                                </div>
                            </div>

                            <?php $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1); ?>
                            <?php if ($locationEnabled && !empty($siteevent->location)): ?>
                                <div class="siteevent_listings_stats">
                                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_location" title="<?php echo $this->translate('Location') ?>"></i>
                                    <div class="o_hidden">
                                <?php echo $siteevent->location; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <span class="seaocore_button fright"><a href="<?php echo $siteevent->getHref(); ?>"><span><?php echo $this->translate('More Details'); ?></span></a></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>
                    <div class="txt_center">
                <?php 
                
                     if(isset($this->params['ismanage']) && $this->params['ismanage'])
                        echo $this->translate('You do not have any event matching this criteria.'); 
                     else
                       echo $this->translate('Nobody has created an event with this criteria.');
                     ?>
                    </div>
                </li>
            <?php endif; ?>
            <?php if ($this->params['page'] == 1) : ?>
            </ul>
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
        </div>
    </div>
<?php endif; ?>

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
                responseContainer: $('days_eventlisting')
            }

            params.requestParams.page =<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>;
            view_more_content.setStyle('display', 'none');
            $('siteevent_dayevents').getElements('.seaocore_loading').setStyle('display', '');

            sendAjaxRequestCalendar(params);
        });

    });

    function sendAjaxRequestCalendar(params) {

        var url = en4.core.baseUrl + 'widget/index/mod/siteevent/name/calendarview-siteevent';

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

                Elements.from(responseHTML).inject(params.responseContainer, 'bottom');
                $('siteevent_dayevents').getElements('.seaocore_loading').setStyle('display', 'none');

                en4.core.runonce.trigger();
                // Smoothbox.bind(params.responseContainer);                                      
            }
        });
        en4.core.request.send(request);

    }
</script>
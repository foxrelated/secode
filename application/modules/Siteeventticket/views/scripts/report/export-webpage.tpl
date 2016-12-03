<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: export-webpage.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'); ?>
<div class="layout_middle">
  <div class="siteevent_dashboard_content">
    <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
    <div class="siteeventticket_manage_account">
      <h3><?php echo $this->translate('View Sales Report') ?></h3>
      <a class="buttonlink icon_previous mbot5" href="<?php echo $this->url(array('event_id' => $this->event_id, 'tab' => '1'), 'siteeventticket_report_general', true) ?>">
        <?php echo $this->translate("Back to Sales Report") ?>
      </a>

      <div class="siteevent_detail_table">
        <table>
          <tr class="siteevent_detail_table_head">
            <th><?php echo $this->translate('Summarize By') ?></th>
            <th><?php echo $this->translate('Time Summary') ?></th>
            <th><?php echo $this->translate('Duration') ?></th>
          </tr>	
          <tr>
            <td>
              <?php
              if ($this->report_type == 'ticket'):
                echo $this->translate('Ticket');
              elseif ($this->report_type == 'order'):
                echo $this->translate('Order');
              endif;
              ?>
            </td>
            <td>
              <?php echo $this->translate($this->values['time_summary']); ?>
            </td>
            <td>
              <?php
              $startTime = $endTime = date('Y-m-d');
              if (!empty($this->values['time_summary'])) :
                if ($this->values['time_summary'] == 'Monthly') :
                  $startTime = date('M d, Y', mktime(0, 0, 0, $this->values['month_start'], date('d'), $this->values['year_start']));
                  $endTime = date('M d, Y', mktime(0, 0, 0, $this->values['month_end'], date('d'), $this->values['year_end']));
                else:
                  if (!empty($this->values['start_daily_time'])) :
                    $start = $this->values['start_daily_time'];
                  endif;
                  if (!empty($this->values['start_daily_time'])) :
                    $end = $this->values['end_daily_time'];
                  endif;
                  $startTime = date('M d, Y', $start);
                  $endTime = date('M d, Y', $end);
                endif;
              endif;
              echo $this->timestamp($startTime) . $this->translate(" to ") . $this->timestamp($endTime);
              ?>
            </td>
          </tr>
        </table>
      </div>

      <div id='stat_table' style="clear:both;">
        <?php if (@COUNT($this->rawdata) > 0) : ?>
          <div class="siteevent_detail_table">
            <table class="widthfull">
              <tr class="siteevent_detail_table_head">
                <th><?php echo $this->translate('Date') ?></th>
                <th><?php echo $this->translate('Event Name') ?></th>
                <?php if ($this->report_type == 'ticket'): ?>
                  <th><?php echo $this->translate('Ticket Title') ?></th>
                <?php endif; ?>
                <th><?php echo $this->translate('Order Count') ?></th>
                <th><?php echo $this->translate('Ticket Quantity') ?></th>
                <?php if ($this->report_type == 'order'): ?>
                  <th><?php echo $this->translate('Event Subtotal') ?></th>
                  <th><?php echo $this->translate('Tax') ?></th>
                  <th><?php echo $this->translate('Commission') ?></th>
                  <th><?php echo $this->translate('Order Total') ?></th>
                <?php endif; ?>
              </tr>	
              <?php foreach ($this->rawdata as $data) : ?>
                <tr>                
                  <?php $siteevent = Engine_Api::_()->getItem('siteevent_event', $data->event_id); ?>
                  <td><?php echo $this->timestamp($data->creation_date) ?></td>
                  <td><?php echo $this->htmlLink($siteevent->getHref(), $siteevent->getTitle(), array('target' => '_blank')); ?></td>
                  <?php if ($this->report_type == 'ticket'): ?>
                    <td><?php echo $data->title ?></td>
                  <?php endif; ?>
                  <td><?php echo $data->order_count ?></td>
                  <td><?php echo $data->quantity ?></td>
                  <?php if ($this->report_type == 'order'): ?>
                    <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->sub_total) ?></td>
                    <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->tax_amount) ?></td>
                    <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->commission) ?></td>
                    <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->grand_total) ?></td>
                  <?php endif; ?>
                </tr>	
              <?php endforeach; ?>
            </table>
          </div>
        <?php else : ?>
          <div class="tip">
            <span>
              <?php echo $this->translate("There are no activities found in the selected date range.") ?>
            </span>
          </div>
        <?php endif; ?>
      </div> 
    </div>
  </div>
</div>
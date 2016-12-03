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

<div class="headline">
  <h2><?php echo "Advanced Events Plugin" ?></h2>
  <?php if (@count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
      <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
  <?php endif; ?>
</div>

<div class='tabs'>
  <ul class="navigation">
    <li class="<?php echo empty($this->reportType) ? 'active' : '' ?>">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'report', 'type' => 0), 'Order Wise Sales Report') ?>
    </li>
    <li class="<?php echo!empty($this->reportType) ? 'active' : '' ?>">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'report', 'type' => 1), 'Ticket Wise Sales Report') ?>
    </li>
  </ul>
</div>

<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteeventticket/externals/images/back.png" class="icon" />
<?php if (empty($this->reportType)): ?>
  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'report', 'action' => 'index', 'type' => 0), 'Back to Order Wise Sales Report', array('class' => 'buttonlink', 'style' => 'padding-left:0px;')) ?>
<?php else: ?>
  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'report', 'action' => 'index', 'type' => 1), 'Back to Ticket Wise Sales Report', array('class' => 'buttonlink', 'style' => 'padding-left:0px;')) ?>
<?php endif; ?>
<br /><br />

<?php
switch ($this->values['time_summary']) {
  case 'Monthly':
    $date_label = 'Month';
    break;
  case 'Daily':
    $date_label = 'Date';
    break;
}
?>

<?php if (empty($this->reportType)) : ?>
  <h3><?php echo 'View Sales Report' ?></h3>
<?php else: ?>
  <h3><?php echo 'View Tickets Report' ?></h3>
<?php endif; ?>

<div class="clr mtop10">
  <b class="bold"><?php echo 'Time Summary' ?>:</b>
<?php echo $this->values['time_summary']; ?>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

  <b class="bold"><?php echo 'Duration' ?>:</b>
  <?php
  $startTime = $endTime = @date('Y-m-d');
  if (!empty($this->values['time_summary'])) :
    if ($this->values['time_summary'] == 'Monthly') :
      $startTime = date('M d, Y', mktime(0, 0, 0, $this->values['month_start'], date('d'), $this->values['year_start']));
      $endTime = date('M d, Y', mktime(0, 0, 0, $this->values['month_end'], date('d'), $this->values['year_end']));
    else :
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
  echo $startTime . " to " . $endTime;
  ?>
</div>

<div id='stat_table' class="mtop10 clr">
<?php if (@count($this->rawdata)) : ?>
    <table class="admin_table seaocore_admin_table" style="width:100%;">
      <thead>
        <tr>
          <th class='admin_table_short'><?php echo $date_label ?></th>
          <?php if (empty($this->reportType)) : ?>
            <th class='admin_table_short'><?php echo 'Event Name' ?></th>
          <?php endif; ?>
  <?php if (!empty($this->reportType)): ?>
            <th class='admin_table_short' style="width:5%"><?php echo 'Ticket Title' ?></th>
          <?php endif; ?>
          <th class='admin_table_short admin_table_centered'><?php echo 'Order Count' ?></th>
          <th class='admin_table_short admin_table_centered'><?php echo 'Ticket Quantity' ?></th>
  <?php if (empty($this->reportType)): ?>
            <th class='admin_table_short'><?php echo 'Event Subtotal' ?></th>
            <th class='admin_table_short'><?php echo 'Tax' ?></th>
            <th class='admin_table_short'><?php echo 'Commission' ?></th>
            <th class='admin_table_short'><?php echo 'Order Total' ?></th>
        <?php endif; ?>
        </tr>	
      </thead>
      <tbody> 	
          <?php foreach ($this->rawdata as $data) : ?>
          <tr>
            <?php $siteevent = Engine_Api::_()->getItem('siteevent_event', $data->event_id); ?>
            <?php if (empty($siteevent)): continue;
            endif; ?>
            <?php if (!empty($this->reportType)): ?>
              <?php $siteeventTicket = Engine_Api::_()->getItem('siteeventticket_ticket', $data->ticket_id); ?>
            <?php endif; ?>
            <td><?php echo $data->creation_date ?></td>                        
              <?php if (empty($this->reportType)) : ?>
              <td><?php echo $this->htmlLink($siteevent->getHref(), $siteevent->getTitle(), array('target' => '_blank')); ?></td>
            <?php endif; ?>
            <?php if (!empty($this->reportType)): ?>
              <td class='admin_table_bold' style="white-space:normal;" title="<?php echo $siteeventTicket->getTitle() ?>">
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'manage', 'action' => 'detail', 'id' => $data->ticket_id), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteeventTicket->getTitle(), 10), array('class' => 'smoothbox')) ?>
              </td>
    <?php endif; ?>
            <td class="admin_table_centered"><?php echo $data->order_count ?></td>
            <td class="admin_table_centered"><?php echo $data->quantity ?></td>
            <?php if (empty($this->reportType)): ?>
              <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->sub_total) ?></td>
              <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->tax_amount) ?></td>
              <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->commission) ?></td>
              <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->grand_total) ?></td>
    <?php endif; ?>
          </tr>	
    <?php endforeach; ?>
      </tbody>	
    </table>

<?php elseif (!count($this->rawdata) && $this->post == 1) : ?>
    <div class="tip">
      <span>
  <?php echo "There are no activities found in the selected date range." ?>
      </span>
    </div>
<?php endif; ?>
</div>
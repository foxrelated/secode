<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: export-excel.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (@count($this->rawdata)) : ?>
  <?php
  header("Expires: 0");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-event, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
  header("Content-type: application/vnd.ms-excel;charset:UTF-8");
  header("Content-Disposition: attachment; filename=Report.xls");
  print "\n"; // Add a line, unless excel error..
  ?>

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
  <div id='stat_table'>
    <table>
      <thead>
        <tr>
          <th><?php echo $date_label ?></th>
          <th><?php echo 'Event Name' ?></th>
          <?php if (!empty($this->reportType)): ?>
            <th><?php echo 'Ticket Title' ?></th>
          <?php endif; ?>
          <th><?php echo 'Order Count' ?></th>
          <th><?php echo 'Ticket Quantity' ?></th>
          <?php if ($this->reportType == 'ticket'): ?>
            <th><?php echo 'Total' ?></th>
  <?php endif; ?>
  <?php if (empty($this->reportType)): ?>
            <th><?php echo 'Event Subtotal' ?></th>
            <th><?php echo 'Tax' ?></th>
            <th><?php echo 'Shipping Price' ?></th>
            <th><?php echo 'Commission' ?></th>
            <th><?php echo 'Order Total' ?></th>
  <?php endif; ?>
        </tr>	
      </thead>

      <tbody>
        <?php $temp_creation_date = null;
        foreach ($this->rawdata as $data):
          ?>   
          <?php $tempEventTitle = Engine_Api::_()->getDbtable('events', 'siteevent')->getEventName($data->event_id); ?>
          <?php
          if ($temp_creation_date != $data->creation_date):
            $temp_creation_date = $data->creation_date;
            $temp_date = $data->creation_date;
            $event_title = $tempEventTitle;
            $temp_event_id = $data->event_id;
            echo '<tr><td>&nbsp;</td></tr>';
          else:
            if ($temp_event_id != $data->event_id):
              $temp_event_id = $data->event_id;
              $event_title = $tempEventTitle;
            else:
              $event_title = "";
              $temp_event_id = $data->event_id;
            endif;
            $temp_date = "";
            $temp_creation_date = $data->creation_date;
          endif;
          ?>
          <tr>
            <td><b><?php echo $temp_date ?></b></td>
            <td><b><?php echo $event_title ?></b></td>
            <?php if (!empty($this->reportType)): ?>
              <td><?php echo $data->title ?></td>
            <?php endif; ?>
            <td><?php echo $data->order_count ?></td>
            <td><?php echo $data->quantity ?></td>
    <?php if (!empty($this->reportType)): ?>
              <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->price) ?></td>
    <?php endif; ?>
    <?php if (empty($this->reportType)): ?>
              <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->sub_total) ?></td>
              <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->tax_amount) ?></td>
              <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->shipping_price) ?></td>
              <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->commission) ?></td>
              <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->grand_total) ?></td>
    <?php endif; ?>
          </tr>	
  <?php endforeach; ?>        
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
  <?php echo "There are no activities found in the selected date range." ?>
    </span>
  </div>
<?php endif; ?>
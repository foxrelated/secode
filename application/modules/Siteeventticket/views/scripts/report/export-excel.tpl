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

<?php if (count($this->rawdata)) : ?>
  <?php
  header("Expires: 0");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-event, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
  header("Content-type: application/vnd.ms-excel;charset:UTF-8;");
  header("Content-Disposition: attachment; filename=Report.xls");
  print "\n"; // Add a line, unless excel error..

  switch ($this->values['time_summary']) {
    case 'Monthly':
      $date_label = 'Month';
      break;

    case 'Daily':
      $date_label = 'Date';
      break;
  }
  ?>

  <table class='admin_table'>
    <thead>
      <tr>
        <th><?php echo $date_label ?></th>
        <th><?php echo $this->translate('Event Name') ?></th>
        <?php if ($this->values['report_depend'] == 'ticket'): ?>
          <th><?php echo $this->translate('Ticket Title') ?></th>
        <?php endif; ?>
        <th><?php echo $this->translate('Order Count') ?></th>
        <th><?php echo $this->translate('Ticket Quantity') ?></th>
        <?php if ($this->values['report_depend'] == 'ticket'): ?>
          <th><?php echo $this->translate('Total') ?></th>
        <?php endif; ?>
        <?php if ($this->values['report_depend'] == 'order'): ?>
          <th><?php echo $this->translate('Event Subtotal') ?></th>
          <th><?php echo $this->translate('Event Tax') ?></th>
          <th><?php echo $this->translate('Commission') ?></th>
          <th><?php echo $this->translate('Order Total') ?></th>
        <?php endif; ?>
      </tr>	
    </thead>

    <tbody>
      <?php
      $temp_creation_date = null;
      foreach ($this->rawdata as $data):
        ?>  
        <?php $tempEventTitle = Engine_Api::_()->getDbtable('events', 'siteevent')->getEventName($data->event_id); ?>
        <?php
        if ($temp_creation_date != $data->creation_date):
          $temp_creation_date = $data->creation_date;
          $temp_date = $data->creation_date;
          $temp_event_id = $data->event_id;
          $event_title = $tempEventTitle;
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
          <?php if ($this->values['report_depend'] == 'ticket'): ?>
            <td><?php echo $data->title ?></td>
    <?php endif; ?>
          <td><?php echo $data->order_count ?></td>
          <td><?php echo $data->quantity ?></td>
          <?php if ($this->values['report_depend'] == 'ticket'): ?>
            <td align="right" ><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->price) ?></td>
          <?php endif; ?>
    <?php if ($this->values['report_depend'] == 'order'): ?>
            <td align="right" ><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->sub_total) ?></td>
            <td align="right" ><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->tax_amount) ?></td>
            <td align="right" ><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->commission) ?></td>
            <td align="right" ><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($data->grand_total) ?></td>
        <?php endif; ?>
        </tr>	
  <?php endforeach; ?>        
    </tbody>
  </table>
<?php else: ?>
  <div class="tip">
    <span>
  <?php echo $this->translate("There are no activities found in the selected date range.") ?>
    </span>
  </div>
<?php endif; ?>
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
<?php if (count($this->memberDetails)): ?>

    <?php
        header("Expires: 0");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-type: application/vnd.ms-excel;charset:UTF-8");
        header("Content-Disposition: attachment; filename=" . $this->translate('Ticket_Buyers_List') . ".xls");
        print "\n";
    ?>
    <?php echo $this->translate("This list contain information of all the members who have purchased tickets for this event."); ?><br /><br />
    <?php echo $this->translate('Event Title: %s', $this->event->title); ?><br /><br />
    <?php if($this->occurrence_id):?>
      <?php echo $this->translate('Start Date: %s', $this->eventDates['starttime']); ?><br />
      <?php echo $this->translate('End Date: %s', $this->eventDates['endtime']); ?><br /><br /><br />
    <?php endif;?>
    <table border="0">
        <tr>
            <th><?php echo $this->translate('Order Id'); ?></th>
            <?php if($this->buyerDetails): ?>
                <th><?php echo $this->translate('First Name'); ?></th>
                <th><?php echo $this->translate("Last Name") ?></th>
                <th><?php echo $this->translate('Ticket Type'); ?></th>
                <th><?php echo $this->translate("Quantity") ?></th>                 
                <th><?php echo $this->translate("Booked By") ?></th>
            <?php else: ?>
                <th><?php echo $this->translate('Ticket Type'); ?></th>
                <th><?php echo $this->translate("Quantity") ?></th>                  
                <th><?php echo $this->translate("Booked By") ?></th>       
            <?php endif; ?>
        </tr>
        <?php
            $buyerDetailsTable = Engine_Api::_()->getDbTable('buyerdetails', 'siteeventticket');
            $orderTicketsTable = Engine_Api::_()->getDbTable('orderTickets', 'siteeventticket');
        ?>
        <?php foreach ($this->memberDetails as $member) :?>
            <?php if($this->buyerDetails): ?>
                <?php $buyerDetails = $buyerDetailsTable->getBuyerDetails(array('order_id' => $member->order_id, 'groupBy' => 1));?>
                <?php foreach($buyerDetails as $buyerDetail): ?>
                    <?php $ticketTitle = $orderTicketsTable->getTicketDetails(array('order_id' => $buyerDetail->order_id, 'ticket_id' => $buyerDetail->ticket_id));?>
                    <tr>
                        <td><?php echo $member->order_id; ?></td>    
                        <td><?php echo $buyerDetail->first_name; ?></td>
                        <td><?php echo $buyerDetail->last_name; ?></td>
                        <td> <?php echo $ticketTitle; ?></td>
                        <td> <?php echo $buyerDetail->total_tickets; ?></td>
                        <td><?php echo ($member->displayname) ? $member->displayname : $member->username; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <?php $buyerDetails = $orderTicketsTable->getOrderTicketsDetail(array('order_id' => $member->order_id, 'columns' => array('title', 'quantity')));?>    
                <?php foreach($buyerDetails as $buyerDetail): ?>    
                    <tr>
                        <td><?php echo $member->order_id; ?></td>     
                        <td><?php echo $buyerDetail->title; ?></td> 
                        <td><?php echo $buyerDetail->quantity; ?></td> 
                        <td><?php echo ($member->displayname) ? $member->displayname : $member->username; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
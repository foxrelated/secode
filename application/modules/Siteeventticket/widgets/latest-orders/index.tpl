<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
    Asset.css('<?php echo $this->layout()->staticBaseUrl
 . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'
?>');
</script>

<?php if (COUNT($this->latestOrders) > 0): ?>

    <div class="siteevent_detail_table">
        <table>
            <tr class="siteevent_detail_table_head">
                <th style="width:5%;"><?php echo $this->translate("Id") ?></th>
                <th style="width:20%;"><?php echo $this->translate("Status") ?></th>
                <th style="width: 20%;"><?php echo $this->translate("Buyer Name") ?></th>
                <th style="width: 25%;"><?php echo $this->translate("Placed On") ?></th>
                <th style="width: 10%;"><?php echo $this->translate("Tickets") ?></th>
                <th class="txt_center" style="width: 20%;"><?php echo $this->translate("Total") ?></th>
            </tr>
    <?php foreach ($this->latestOrders as $latestOrder) : ?>
                <tr>
                    <td class="txt_center ">
                        <a href="javascript:void(0)" onclick="manage_event_dashboard(55, 'view/order_id/<?php echo $latestOrder->order_id; ?>', 'order')"><?php echo '#' . $latestOrder->order_id ?></a>
                    </td>
        <?php $tempStatus = $this->getTicketOrderStatus($latestOrder->order_status, "true"); ?>
                    <td class="<?php echo $tempStatus['class'] ?>"><?php echo $tempStatus['title'] ?></td>
                    <td><?php echo $this->htmlLink($latestOrder->getHref(), ucfirst($latestOrder->getTitle())) ?></td>
                    <td><?php echo $this->locale()->toDateTime($latestOrder->order_date) ?></td>
                    <td class="txt_center "><?php echo $latestOrder->ticket_qty ?></td>
                    <td class="txt_right"><b><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($latestOrder->grand_total) ?></b></td>
                </tr>
    <?php endforeach; ?>
        </table>
    </div>
<?php else: ?>
    <div class="tip">
        <span>
            <?php
            echo $this->translate("No orders have been placed for your event yet.");
            if (Engine_Api::_()->hasModuleBootstrap('siteeventinvite')):
                $invitePeople = $this->htmlLink(array('route' => 'siteeventinvite_invite', 'user_id' => $this->viewer_id, 'siteevent_id' => $this->eventId), $this->translate('Invite people'));
                echo $this->translate(" %s to buy tickets for your event.", $invitePeople);
            endif;
            ?>
        </span> 
    </div>
<?php endif; ?>
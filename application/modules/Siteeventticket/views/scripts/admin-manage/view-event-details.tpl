<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-event-details.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="siteevent_admin_popup">
  <div>
    <h3><?php echo 'Event Details'; ?></h3>
    <br />
    <table cellpadding="0" cellspacing="0" class="siteevent-view-detail-table">
      <tr>
        <td>
          <table cellpadding="0" cellspacing="0" width="350">
            <tr>
              <td width="200"><b><?php echo 'Title:'; ?></b></td>
              <td>
                <?php echo $this->htmlLink($this->item('siteevent_event', $this->siteeventDetail->event_id)->getHref(), $this->siteeventDetail->title, array('target' => '_blank')) ?>&nbsp;&nbsp;
              </td>
            </tr>

            <tr>
              <td><b><?php echo 'Owner:'; ?></b></td>
              <td>
                <?php echo $this->htmlLink($this->siteeventDetail->getOwner()->getHref(), $this->siteeventDetail->getOwner()->getTitle(), array('target' => '_blank')) ?>
              </td>
            </tr>

            <tr>
              <td><b><?php echo 'Number of Order’s:'; ?></b></td>
              <td><?php echo ($this->event_statistics['order_count']) ? $this->event_statistics['order_count'] : '0'; ?></td>
            </tr>

            <tr>
              <td><b><?php echo 'Grand Total:'; ?></b></td>
              <?php if (isset($this->event_statistics['grand_total']) && !empty($this->event_statistics['grand_total'])) : ?>
                <?php $grandTotal = $this->event_statistics['grand_total']; ?>
              <?php else: ?>
                <?php $grandTotal = 0; ?>
              <?php endif; ?>
              <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($grandTotal) ?></td>
            </tr>

            <?php if (!empty($this->total_sale_this_year)) : ?>
              <tr>
                <td><b><?php echo 'Total Sale This Year:'; ?></b></td>
                <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->total_sale_this_year) ?></td>
              </tr>
            <?php endif; ?>

            <tr>
              <td><b><?php echo 'Total Commission:'; ?></b></td>
              <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->event_statistics['commission']) ?></td>
            </tr>

            <tr>
              <td><b><?php echo 'Total Tax:'; ?></b></td>
              <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->event_statistics['tax_amount']) ?></td>
            </tr>

            <tr>
              <td><b><?php echo 'Total Ticket Sale:'; ?></b></td>
              <td><?php echo ($this->event_statistics['ticket_qty']) ? $this->event_statistics['ticket_qty'] : '0' ?></td>
            </tr>

            <tr>
              <td><b><?php echo 'Approval Pending Orders:'; ?></b></td>
              <td><?php echo ($this->approval_pending_orders) ? $this->approval_pending_orders : '0' ?></td>
            </tr>

            <tr>
              <td><b><?php echo 'Payment Pending Orders:'; ?></b></td>
              <td><?php echo ($this->payment_pending_orders) ? $this->payment_pending_orders : '0' ?></td>
            </tr>

            <tr>
              <td><b><?php echo 'Complete Orders:'; ?></b></td>
              <td><?php echo ($this->complete_orders) ? $this->complete_orders : '0' ?></td>
            </tr>

          </table>
        </td>
        <td align="right">
          <?php echo $this->htmlLink($this->siteeventDetail->getHref(), $this->itemPhoto($this->siteeventDetail, 'thumb.icon', '', array('align' => 'right')), array('target' => '_blank')) ?>
        </td>	
      </tr>
    </table>		
    <br />
    <button  onclick='javascript:parent.Smoothbox.close()' ><?php echo 'Close' ?></button>
  </div>
</div>
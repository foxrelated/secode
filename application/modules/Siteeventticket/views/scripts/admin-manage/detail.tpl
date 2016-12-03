<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'); ?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<div class="global_form_popup siteevent_details_view">
  <h3><?php echo 'Ticket Details'; ?></h3>
  <table class="clr">
    <tr>
      <td width="200"><b><?php echo 'Ticket Name :'; ?></b></td>
      <td><?php echo $this->siteeventticketDetail->getTitle(); ?>&nbsp;&nbsp;</td>
    </tr>
    <tr>
      <td width="200"><b><?php echo 'Event Name :'; ?></b></td>
      <td><?php echo $this->siteevent->getTitle(); ?>&nbsp;&nbsp;</td>
    </tr>
    <tr>
      <td><b><?php echo ' 	Owner :'; ?></b></td>
      <td><?php echo $this->siteeventticketDetail->getOwner()->getTitle(); ?></td>
    </tr>

    <tr>
      <td><b><?php echo 'Creation Date :'; ?></b></td>
      <td>
        <?php echo gmdate('M d,Y, g:i A', strtotime($this->siteeventticketDetail->creation_date)); ?>
      </td>
    </tr>

    <tr>
      <td><b><?php echo 'Last Modified Date :'; ?></b></td>
      <td>
        <?php echo gmdate('M d,Y, g:i A', strtotime($this->siteeventticketDetail->modified_date)); ?>
      </td>
    </tr>      
  
      <tr>
        <td><b><?php echo 'Price :'; ?></b></td>
        <td>
          <?php if ($this->siteeventticketDetail->price > 0): ?>
          <?php echo $this->locale()->toCurrency($this->siteeventticketDetail->price, $currency); ?>
          <?php else: echo 'Free';?>
          <?php endif; ?>
        </td>
      </tr>
    
      
    <?php if ($this->siteeventticketDetail->quantity > 0): ?>
      <tr>
        <td><b><?php echo 'Quantity :'; ?></b></td>
        <td><?php echo $this->siteeventticketDetail->quantity ?></td>
      </tr>
    <?php endif; ?>

    <tr>           
      <td><b><?php echo 'Sell Start Time:'; ?></b></td>
      <td>
        <?php if ($this->siteeventticketDetail->sell_starttime && $this->siteeventticketDetail->sell_starttime != '0000-00-00 00:00:00'): ?>
          <?php echo date('M d,Y, g:i A', strtotime($this->siteeventticketDetail->sell_starttime)); ?>
        <?php else: ?>
          ---
        <?php endif; ?>
      </td>
    </tr>

    <tr>           
      <td><b><?php echo 'Sell End Time :'; ?></b></td>
      <td>
        <?php if ($this->siteeventticketDetail->is_same_end_date): ?>
          <?php echo "Just before event occurrence starts"; ?>
        <?php else: ?>
          <?php if ($this->siteeventticketDetail->sell_endtime && $this->siteeventticketDetail->sell_endtime != '0000-00-00 00:00:00'): ?>
            <?php echo date('M d,Y, g:i A', strtotime($this->siteeventticketDetail->sell_endtime)); ?>
          <?php else: ?>
            ---
          <?php endif; ?>
        <?php endif; ?>
      </td>
    </tr>

    <tr>           
      <td><b><?php echo 'Minimum Buying Limit :'; ?></b></td>
      <td>
        <?php echo $this->siteeventticketDetail->buy_limit_min; ?>
      </td>
    </tr>

    <tr>           
      <td><b><?php echo 'Maximum Buying Limit :'; ?></b></td>
      <td>
        <?php echo $this->siteeventticketDetail->buy_limit_max; ?>
      </td>
    </tr>

    <tr>           
      <td><b><?php echo 'Status :'; ?></b></td>
      <td>
        <?php echo strtoupper($this->siteeventticketDetail->status); ?>
      </td>
    </tr>

    <tr>           
      <td><b><?php echo 'Description :'; ?></b></td>
      <td>
        <?php echo $this->siteeventticketDetail->description; ?>
      </td>
    </tr>

  </table>
  <br />
  <button  onclick='javascript:parent.Smoothbox.close()' ><?php echo 'Close'; ?></button>
</div>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
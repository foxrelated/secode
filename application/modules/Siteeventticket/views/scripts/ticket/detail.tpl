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
<?php $item = $this->ticket; ?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<div class="global_form_popup siteevent_details_view">
  <h3><?php echo $this->translate('Ticket Details'); ?></h3>
  <table class="clr">
    <tr>
      <td width="200"><b><?php echo $this->translate('Title :'); ?></b></td>
      <td><?php echo $item->getTitle(); ?>&nbsp;&nbsp;</td>
    <tr >
      <td><b><?php echo $this->translate('Owner :'); ?></b></td>
      <td><?php echo $item->getOwner()->getTitle(); ?></td>
    </tr>

    <tr>
      <td><b><?php echo $this->translate('Creation Date :'); ?></b></td>
      <td>
        <?php echo $this->translate(gmdate('M d,Y, g:i A', strtotime($item->creation_date))); ?>
      </td>
    </tr>

    <tr>
      <td><b><?php echo $this->translate('Last Modified Date :'); ?></b></td>
      <td>
        <?php echo $this->translate(gmdate('M d,Y, g:i A', strtotime($item->modified_date))); ?>
      </td>
    </tr>      

    <tr>
      <td><b><?php echo $this->translate('Price :'); ?></b></td>
      <td>
        <?php if ($item->price > 0): ?>        
          <?php echo $this->locale()->toCurrency($item->price, $currency); ?>
        <?php else: echo $this->translate('Free'); ?>
        <?php endif; ?>
      </td>
    </tr>


    <?php if ($item->quantity > 0): ?>
      <tr>
        <td><b><?php echo $this->translate('Quantity :'); ?></b></td>
        <td><?php echo $item->quantity; ?></td>
      </tr>
    <?php endif; ?>

    <tr>           
      <td><b><?php echo $this->translate('Sell Start Time :'); ?></b></td>
      <td>
        <?php if ($item->sell_starttime && $item->sell_starttime != '0000-00-00 00:00:00'): ?>
          <?php echo $this->translate(date('M d,Y, g:i A', strtotime($item->sell_starttime))); ?>
        <?php else: ?>
          ---
        <?php endif; ?>
      </td>
    </tr>

    <tr>           
      <td><b><?php echo $this->translate('Sell End Time :'); ?></b></td>
      <td>
        <?php if ($item->is_same_end_date): ?>
          <?php echo $this->translate("Just before event occurrence starts"); ?>
        <?php else: ?>
          <?php if ($item->sell_endtime && $item->sell_endtime != '0000-00-00 00:00:00'): ?>
            <?php echo $this->translate(date('M d,Y, g:i A', strtotime($item->sell_endtime))); ?>
          <?php else: ?>
            ---
          <?php endif; ?>
        <?php endif; ?>
      </td>
    </tr>

    <tr>           
      <td><b><?php echo $this->translate('Minimum Buying Limit :'); ?></b></td>
      <td>
        <?php echo $item->buy_limit_min; ?>
      </td>
    </tr>

    <tr>           
      <td><b><?php echo $this->translate('Maximum Buying Limit :'); ?></b></td>
      <td>
        <?php echo $item->buy_limit_max; ?>
      </td>
    </tr>

    <tr>           
      <td><b><?php echo $this->translate('Status :'); ?></b></td>
      <td>
        <?php echo $this->translate($item->status); ?>
      </td>
    </tr>

    <tr>           
      <td><b><?php echo $this->translate('Description :'); ?></b></td>
      <td>
        <?php if($item->description): ?>  
            <?php echo $this->translate($item->description); ?>
        <?php else: ?>
            ---
        <?php endif; ?>  
      </td>
    </tr>

  </table>
  <br />
  <div class="buttons mtop10">
    <button type="button" name="cancel" onclick="javascript:parent.SmoothboxSEAO.close();"><?php echo $this->translate("Close") ?></button>
  </div>
</div>


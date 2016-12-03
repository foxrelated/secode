<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _packageInfo.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
$controller = $request->getControllerName();
$action = $request->getActionName();
?>

<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<?php if (!empty($this->viewer->level_id)): ?>
  <?php $level_id = $this->viewer->level_id; ?>
<?php else: ?>
  <?php $level_id = 0; ?>
<?php endif; ?>
<?php if (!empty($this->packageInfoArray)): ?>
  <div class="siteeventpaid_package_stat">
    <?php if (in_array('price', $this->packageInfoArray)): ?>
      <span>
        <b><?php echo $this->translate("Price") . ": "; ?> </b>
        <?php
        if ($item->price > 0):echo $this->locale()->toCurrency($item->price, $currency);
        else: echo $this->translate('Free');
        endif;
        ?>
      </span>
  <?php endif; ?>
    <?php if (Engine_Api::_()->siteevent()->hasTicketEnable() && in_array('ticket_type', $this->packageInfoArray)): ?>
      <span>
        <b><?php echo $this->translate("Ticket Types") . ": "; ?> </b>
        <?php
        if ($item->ticket_type):echo $this->translate("PAID & FREE");
        else: echo $this->translate('FREE');
        endif;
        ?>
        <img class="mleft5" style="margin-bottom: -3px;" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/help.png" title="<?php echo $this->translate("Allowed ticket types (Free or Paid) that you can create for events of this package."); ?>" >
      </span>
  <?php endif; ?>      
      <?php if (in_array('billing_cycle', $this->packageInfoArray)): ?>
      <span>
        <b><?php echo $this->translate("Billing Cycle") . ": "; ?> </b>
      <?php echo $item->getBillingCycle() ?>
      </span>
  <?php endif; ?>
      <?php if (in_array('duration', $this->packageInfoArray)): ?>
      <span>
        <b><?php echo ($item->price > 0 && $item->recurrence > 0 && $item->recurrence_type != 'forever' ) ? $this->translate("Billing Duration") . ": " : $this->translate("Duration") . ": "; ?> </b>
      <?php echo $item->getPackageQuantity(); ?>
      </span>
    <?php endif; ?>
    <!--<br/>-->
      <?php if (in_array('featured', $this->packageInfoArray)): ?>
      <span>
        <b><?php echo $this->translate("Featured") . ": "; ?> </b>
        <?php
        if ($item->featured == 1)
          echo $this->translate("Yes");
        else
          echo $this->translate("No");
        ?>
      </span>
  <?php endif; ?>
      <?php if (in_array('Sponsored', $this->packageInfoArray)): ?>
      <span>
        <b><?php echo $this->translate("Sponsored") . ": "; ?> </b>
        <?php
        if ($item->sponsored == 1)
          echo $this->translate("Yes");
        else
          echo $this->translate("No");
        ?>
      </span>
    <?php endif; ?>

      <?php if (in_array('rich_overview', $this->packageInfoArray) && ($this->overview && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "overview")))): ?>
      <span>
        <b><?php echo $this->translate("Rich Overview") . ": "; ?> </b>
        <?php
        if ($item->overview == 1)
          echo $this->translate("Yes");
        else
          echo $this->translate("No");
        ?>
      </span>
      <!--<br/>-->
    <?php endif; ?> 

      <?php if (in_array('videos', $this->packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "video"))): ?>
      <span>
        <b><?php echo $this->translate("Videos") . ": "; ?> </b>
        <?php
        if ($item->video == 1)
          if ($item->video_count)
            echo $item->video_count;
          else
            echo $this->translate("Unlimited");
        else
          echo $this->translate("No");
        ?>
      </span>
    <?php endif; ?>

      <?php if (in_array('photos', $this->packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "photo"))): ?>
      <span>
        <b><?php echo $this->translate("Photos") . ": "; ?> </b>
        <?php
        if ($item->photo == 1)
          if ($item->photo_count)
            echo $item->photo_count;
          else
            echo $this->translate("Unlimited");
        else
          echo $this->translate("No");
        ?>
      </span>
    <?php endif; ?>
    <!--TICKET & COMMISSION DISPLAY-->
  <?php if (Engine_Api::_()->siteevent()->hasTicketEnable() && in_array('commission', $this->packageInfoArray)): ?>
        <?php
        if (!empty($item->ticket_settings)):
          $siteeventticketInfo = @unserialize($item->ticket_settings);
          $commissionType = $siteeventticketInfo['commission_handling'];
          $commissionFee = $siteeventticketInfo['commission_fee'];
          $commissionRate = $siteeventticketInfo['commission_rate'];
//      else:       
//        $commissionFee = $commissionRate = $commissionType = 1;
        endif;
        ?>  
        <span>
          <b><?php echo $this->translate("Commission") . ": "; ?> </b>
          <?php if (!empty($item->ticket_settings) && isset($commissionType)): ?>
            <?php
            if (empty($commissionType)):
              echo $this->locale()->toCurrency((int) $commissionFee, $currency);
            else:
              echo $commissionRate . '%';
            endif;
            ?>
            <img class="mleft5" style="margin-bottom: -3px;" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/help.png" title="<?php echo $this->translate("Commission charged for the tickets booked for events of this package."); ?>" > 
          <?php else: ?>
          <?php echo $this->translate("N/A"); ?>
        <?php endif; ?>
        </span>
  <?php endif; ?>
    <!--END WORK FOR TICKET PLUGIN-->
  </div>

    <?php if (in_array('description', $this->packageInfoArray) || ($controller == 'package' && $action != 'index')): ?>
    <div class="siteeventpaid_list_details">
      <?php if (empty($this->detailPackage)): ?>
        <?php echo $this->viewMore($this->translate($item->description), 425); ?>
      <?php else: ?>
      <?php echo $this->translate($item->description); ?>
    <?php endif; ?>
    </div>
  <?php endif; ?>
<?php endif; ?>
<div class="clr"></div>



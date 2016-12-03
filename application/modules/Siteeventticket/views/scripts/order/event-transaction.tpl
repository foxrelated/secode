<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: event-transaction.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
?>
<?php if (!$this->only_list_content): ?>
  <?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
  <div class="siteevent_dashboard_content">
    <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
    <div class="siteevent_event_form">
      <div id="siteevent_manage_order_content"> 
      <?php endif; ?>
      <?php $paginationCount = count($this->paginator); ?>
      <?php if (empty($this->call_same_action)) : ?>
        <div class="siteeventticket_manage_event">
          <h3 class="mbot10">
            <?php echo $this->translate('Transactions') ?>
          </h3>

          <?php if (empty($this->commissionFreePackage)) : ?>
            <div class='tabs mbot10'>
              <ul class="navigation">
                <li <?php if (empty($this->tab)) : ?> class="active" <?php endif; ?>>
                  <a href="javascript:void(0)" onclick="manage_event_dashboard(54, 'event-transaction/tab/0', 'order')">
                    <?php echo $this->translate("For Event Bookings") ?>
                  </a>
                </li>		
                <li <?php if (!empty($this->tab)) : ?> class="active" <?php endif; ?>>
                  <a href="javascript:void(0)" onclick="manage_event_dashboard(54, 'event-transaction/tab/1', 'order')">
                    <?php echo $this->translate("Commissions Paid") ?>
                  </a>
                </li>    
              </ul>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (empty($this->tab)) : ?>
          <?php include APPLICATION_PATH . '/application/modules/Siteeventticket/views/scripts/_orderRelatedTransaction.tpl'; ?>
        <?php else: ?>
          <?php include APPLICATION_PATH . '/application/modules/Siteeventticket/views/scripts/_siteAdminRelatedTransaction.tpl'; ?>
        <?php endif; ?>
        <?php if (empty($this->call_same_action)) : ?>
        </div>
      <?php endif; ?>
      <?php if (!$this->only_list_content): ?>
      </div>
    </div>	
  </div>	
<?php endif; ?>
</div>

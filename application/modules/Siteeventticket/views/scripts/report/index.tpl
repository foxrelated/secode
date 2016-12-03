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
            <?php echo $this->translate('Sales Reports') ?>
          </h3>
          <div class='tabs mbot10'>
            <ul class="navigation">
              <li <?php if (empty($this->tab)) : ?> class="active" <?php endif; ?>>
                <a href="javascript:void(0)" onclick="manage_event_dashboard(60, 'sales-statistics/tab/0', 'report')">
                  <?php echo $this->translate("Sales statistics") ?>
                </a>
              </li>
              <li <?php if (!empty($this->tab)) : ?> class="active" <?php endif; ?>>
                <?php echo $this->htmlLink($this->url(array('action' => 'index', 'event_id' => $this->siteevent->event_id, 'tab' => '1'), 'siteeventticket_report_general', true), $this->translate("Sales reports"), array('title' => $this->translate("Sales reports"))) ?>
              </li>    
            </ul> 
          </div>
        <?php endif; ?>
        <?php include APPLICATION_PATH . '/application/modules/Siteeventticket/views/scripts/report/_salesReport.tpl'; ?>
        <?php if (empty($this->call_same_action)) : ?>
        </div>
      <?php endif; ?>
      <?php if (!$this->only_list_content): ?>
      </div>
    </div>	
  </div>	
<?php endif; ?>
</div>

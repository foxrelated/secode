<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _verticalPackageInfo.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooHorizontalScrollBar.js'); ?>

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

<li class="siteeventpaid_package_vertical">
  <div class="fleft">
    <?php if (in_array('price', $this->packageInfoArray)): ?>
      <div class="contentblock_left_text highlightleft"><b><?php echo $this->translate('Price'); ?></b></div>
    <?php endif; ?>
    <?php if (Engine_Api::_()->siteevent()->hasTicketEnable() && in_array('ticket_type', $this->packageInfoArray)): ?>
      <div class="contentblock_left_text"><b><?php echo $this->translate('Ticket Types'); ?><img class="mleft5" style="margin-bottom: -3px;" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/help.png" title="<?php echo $this->translate("Allowed ticket types (Free or Paid) that you can create for events of this package."); ?>" ></b></div>
    <?php endif; ?>      
    <?php if (in_array('billing_cycle', $this->packageInfoArray)): ?>
      <div class="contentblock_left_text"><b><?php echo $this->translate('Billing Cycle'); ?></b></div>
    <?php endif; ?>
    <?php if (in_array('duration', $this->packageInfoArray)): ?>
      <div class="contentblock_left_text"><b><?php echo $this->translate("Duration") . " "; ?></b></div>
    <?php endif; ?>
    <?php if (in_array('featured', $this->packageInfoArray)): ?>
      <div class="contentblock_left_text"><b><?php echo $this->translate('Featured'); ?></b></div>
    <?php endif; ?>
    <?php if (in_array('Sponsored', $this->packageInfoArray)): ?>
      <div class="contentblock_left_text"><b><?php echo $this->translate('Sponsored'); ?></b></div>
    <?php endif; ?>
    <?php if (in_array('rich_overview', $this->packageInfoArray) && ($this->overview && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "overview")))): ?>
      <div class="contentblock_left_text"><b><?php echo $this->translate('Rich Overview'); ?></b></div>
    <?php endif; ?>        
    <?php if (in_array('videos', $this->packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "video"))): ?>
      <div class="contentblock_left_text"><b><?php echo $this->translate('Videos'); ?></b></div>
    <?php endif; ?>
    <?php if (in_array('photos', $this->packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "photo"))): ?>
      <div class="contentblock_left_text"><b><?php echo $this->translate('Photos'); ?></b></div>
    <?php endif; ?>
    <?php if (Engine_Api::_()->siteevent()->hasTicketEnable() && in_array('commission', $this->packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "ticket_create"))): ?>
      <div class="contentblock_left_text"><b><?php echo $this->translate('Commission'); ?><img class="mleft5" style="margin-bottom: -3px;" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/help.png" title="<?php echo $this->translate("Commission charged for the tickets booked for events of this package."); ?>" >	</b></div>
    <?php endif; ?>
    <?php if (in_array('description', $this->packageInfoArray)): ?>
      <div class="contentblock_left_text">
        <b><?php echo $this->translate("Description"); ?> </b>
      </div>
    <?php endif; ?>
    <?php if (in_array('price', $this->packageInfoArray)): ?>
      <div class="contentblock_left_text highlightleft"><b><?php echo $this->translate('Price'); ?></b></div>
    <?php endif; ?>
  </div>
  <div class="paidEvents scroll-pane" id="paidEventsPanel" style="overflow-x: hidden; overflow-y: hidden; ">
    <div class=" " id ="scrollbar_before"></div>
    <div id="scroll-areas-main" >
      <div id="list-scroll-areas" style=" float:left;overflow:hidden;"> 
        <div class="scroll-content" id="scroll-content" style="margin-left: 0px;width:100%; display:table;">
          <?php foreach ($this->paginator as $item): ?>
            <div class="contentblock_right_inner">
              <div class="contentblock_right_inner_heading o_hidden">
                <b><a href='<?php echo $this->url(array("action" => "detail", 'id' => $item->package_id), "siteevent_package", true) ?>' onclick="owner(this);
                      return false;" title="<?php echo $this->translate(ucfirst($item->title)) ?>"><?php echo $this->translate(ucfirst($item->title)); ?></a></b>
              </div>
              <div class="contentblock_right_text">
                <div class="contentblock_right_inner_btn">
                  <?php if ($controller == 'package' && $action == 'update-package'): ?>
                    <?php
                    echo $this->htmlLink(
                        array('route' => "siteevent_package", 'action' => 'update-confirmation', "event_id" => $this->event_id, "package_id" => $item->package_id), $this->translate('Change Package'), array('onclick' => 'owner(this);return false', 'class' => 'siteevent_buttonlink', 'title' => $this->translate('Change Package')));
                    ?>
                  <?php else: ?>
                    <?php if (!empty($this->parent_id) && !empty($this->parent_type)): ?>
                      <?php $url = $this->url(array("action" => "create", 'id' => $item->package_id, 'parent_id' => $this->parent_id, 'parent_type' => $this->parent_type), 'siteevent_general', true); ?>
                      <a class="siteevent_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate("Create an Event"); ?> &raquo;</a>
                    <?php else: ?>
                      <?php $url = $this->url(array("action" => "create", 'id' => $item->package_id), "siteevent_general", true); ?>
                      <a class="siteevent_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate("Create an Event"); ?> &raquo;</a>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>
              <?php if (in_array('price', $this->packageInfoArray)): ?>
                <div class="contentblock_right_text highlightright"><b><?php
                    if ($item->price > 0):echo $this->locale()->toCurrency($item->price, $currency);
                    else: echo $this->translate('FREE');
                    endif;
                    ?></b> </div>
              <?php endif; ?>
              <?php if (Engine_Api::_()->siteevent()->hasTicketEnable() && in_array('ticket_type', $this->packageInfoArray)): ?>
                <div class="contentblock_right_text"><?php
                    if ($item->ticket_type):echo $this->translate("PAID & FREE");
                    else: echo $this->translate('FREE');
                    endif;
                    ?></div>
              <?php endif; ?>                
              <?php if (in_array('billing_cycle', $this->packageInfoArray)): ?>
                <div class="contentblock_right_text"><?php echo $item->getBillingCycle() ?></div>
              <?php endif; ?>
              <?php if (in_array('duration', $this->packageInfoArray)): ?>
                <div class="contentblock_right_text"><?php echo $item->getPackageQuantity(); ?></div>
                <?php endif; ?>
                <?php if (in_array('featured', $this->packageInfoArray)): ?>
                <div class="contentblock_right_text">     
                  <?php if ($item->featured == 1): ?>
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/tick.png">
                  <?php else: ?>
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/cross.png">
                <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if (in_array('Sponsored', $this->packageInfoArray)): ?>
                <div class="contentblock_right_text">     
                  <?php if ($item->sponsored == 1): ?>
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/tick.png">
                  <?php else: ?>
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/cross.png">
                <?php endif; ?>
                </div>
              <?php endif; ?>

                <?php if (in_array('rich_overview', $this->packageInfoArray) && ($this->overview && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "overview")))): ?>
                <div class="contentblock_right_text">     
                  <?php if ($item->overview == 1): ?>
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/tick.png">
                  <?php else: ?>
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/cross.png">
                <?php endif; ?>
                </div>
              <?php endif; ?>

                <?php if (in_array('videos', $this->packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "video"))): ?>
                <div class="contentblock_right_text">     
                  <?php if ($item->video == 1): ?>
                    <?php if ($item->video_count): ?>
                      <?php echo $item->video_count; ?>
                    <?php else: ?>
                      <?php echo $this->translate("Unlimited"); ?>
                    <?php endif; ?>
                  <?php else: ?>
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/cross.png">
                <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if (in_array('photos', $this->packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "photo"))): ?>
                <div class="contentblock_right_text">     
                  <?php if ($item->photo == 1): ?>
                    <?php if ($item->photo_count): ?>
                      <?php echo $item->photo_count; ?>
                    <?php else: ?>
                      <?php echo $this->translate("Unlimited"); ?>
                    <?php endif; ?>
                  <?php else: ?>
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/cross.png">
                <?php endif; ?>
                </div>
              <?php endif; ?>
              <!--TICKET & COMMISSION DISPLAY-->
                <?php if (Engine_Api::_()->siteevent()->hasTicketEnable() && in_array('commission', $this->packageInfoArray)): ?>
                  <?php
                  if (!empty($item->ticket_settings)):
                    $siteeventticketInfo = @unserialize($item->ticket_settings);
                    $commissionType = $siteeventticketInfo['commission_handling'];
                    $commissionFee = $siteeventticketInfo['commission_fee'];
                    $commissionRate = $siteeventticketInfo['commission_rate'];
//                     else:       
//                       $commissionFee = $commissionRate = $commissionType = 1;
                  endif;
                  ?> 
                  <div class="contentblock_right_text">
                    <?php if (!empty($item->ticket_settings) && isset($commissionType)): ?>                
                      <?php
                      if (empty($commissionType)):
                        echo $this->locale()->toCurrency((int) $commissionFee, $currency);
                      else:
                        echo $commissionRate . '%';
                      endif;
                      ?>
                    <?php else: ?>
                    <?php echo $this->translate("N/A"); ?>
                  <?php endif; ?>
                  </div>
              <?php endif; ?>
              <!--END WORK FOR TICKET PLUGIN-->
                <?php if (in_array('description', $this->packageInfoArray)): ?>
                <div class="contentblock_right_text contentblock_description">
                <?php echo $this->viewMore($this->translate($item->description), 100); ?>
                </div>
  <?php endif; ?>
                  <?php if (in_array('price', $this->packageInfoArray)): ?>
                <div class="contentblock_right_text highlightright">
                  <b><?php
                    if ($item->price > 0):echo $this->locale()->toCurrency($item->price, $currency);
                    else: echo $this->translate('FREE');
                    endif;
                    ?></b> 
                </div>
                  <?php endif; ?>
              <div class="contentblock_right_text">
                <div class="contentblock_right_inner_btn">
                  <?php if ($controller == 'package' && $action == 'update-package'): ?>
                    <?php
                    echo $this->htmlLink(
                        array('route' => "siteevent_package", 'action' => 'update-confirmation', "event_id" => $this->event_id, "package_id" => $item->package_id), $this->translate('Change Package'), array('onclick' => 'owner(this);return false', 'class' => 'siteevent_buttonlink', 'title' => $this->translate('Change Package')));
                    ?>
                  <?php else: ?>
                    <?php if (!empty($this->parent_id) && !empty($this->parent_type)): ?>
                      <?php $url = $this->url(array("action" => "create", 'id' => $item->package_id, 'parent_id' => $this->parent_id, 'parent_type' => $this->parent_type), 'siteevent_general', true); ?>
                      <a class="siteevent_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate("Create an Event"); ?> &raquo;</a>
                    <?php else: ?>
      <?php $url = $this->url(array("action" => "create", 'id' => $item->package_id), "siteevent_general", true); ?>
                      <a class="siteevent_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate("Create an Event"); ?> &raquo;</a>
              <?php endif; ?>
            <?php endif; ?>
                </div>
              </div>
            </div>
<?php endforeach; ?>
        </div>
      </div>
    </div>
    <div class="scrollbarArea" id ="scrollbar_after">		</div>
  </div>
</li>



<script type="text/javascript" >

  var totalLsit = <?php echo $this->paginator->getTotalItemCount(); ?>;
  en4.core.runonce.add(function () {
    resetContent();
    (function () {
      $('list-scroll-areas').setStyle('height', $('scroll-content').offsetHeight + 'px');
      $('list-scroll-areas').setStyle('width', $('paidEventsPanel').offsetWidth + 'px');
      scrollBarContentArea = new SEAOMooHorizontalScrollBar('scroll-areas-main', 'list-scroll-areas', {
        'arrows': false,
        'horizontalScroll': true,
        'horizontalScrollElement': 'scrollbar_after',
        'horizontalScrollBefore': true,
        'horizontalScrollBeforeElement': 'scrollbar_before'
      });
    }).delay(700);
  });

  var resetContent = function () {
    var width = ($('paidEventsPanel').offsetWidth / totalLsit);
    width = width - 2;
    if (width < 200)
      width = 200;
    width++;
    var numberOfItem = ($('paidEventsPanel').offsetWidth / width);
    var numberOfItemFloor = Math.floor(numberOfItem);
    var extra = (width * (numberOfItem - numberOfItemFloor) / numberOfItemFloor);
    width = width + extra;
    $('scroll-content').setStyle('width', (width * totalLsit) + 'px');
    $('scroll-content').getElements('.contentblock_right_inner').each(function (el) {
      el.setStyle('width', width - 1 + 'px');

    });
  };
</script>
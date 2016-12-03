<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: update-package.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (empty($this->is_ajax)) : ?>
  <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
  <?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
  <div class="siteevent_dashboard_content">
    <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
    <div id="show_tab_content">
    <?php endif; ?> 
    <div class="siteevent_package_page">
      <ul class="siteevent_package_list">        
        <li>
          <div class="siteevent_package_list_title">
            <div class="siteevent_create_link">
              <?php if (Engine_Api::_()->siteeventpaid()->canShowPaymentLink($this->siteevent->event_id)): ?>
                <div class="fleft mright10">  
                  <a href='javascript:void(0);' onclick="submitSession(<?php echo $this->siteevent->event_id ?>);"><?php echo $this->translate('Make Payment'); ?></a>
                  <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), "siteevent_session_payment", true) ?>">
                    <input type="hidden" name="event_id_session" id="event_id_session" />
                  </form>
                </div>
              <?php endif; ?>
              <?php if (Engine_Api::_()->siteeventpaid()->canShowRenewLink($this->siteevent->event_id)): ?>
                <div class="fleft mright10">  
                  <a href='javascript:void(0);' onclick="submitSession(<?php echo $this->siteevent->event_id ?>);"><?php echo $this->translate('Renew'); ?></a>
                  <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), "siteevent_session_payment", true) ?>">
                    <input type="hidden" name="event_id_session" id="event_id_session" />
                  </form>
                </div>
              <?php endif; ?>
              <!--Start Cancel Plan-->
              <?php if (Engine_Api::_()->siteeventpaid()->canShowCancelLink($this->siteevent->event_id)): ?>
                <div class="fleft mright10">  
                  <a href='<?php echo $this->url(array('action' => 'cancel', 'package_id' => $this->package->package_id, 'event_id' => $this->siteevent->event_id), "siteevent_package", true); ?>' class="smoothbox" >
                    <?php echo $this->translate('Cancel Package') ?>
                  </a>
                </div>
              <?php endif; ?>
              <!--End Cancel Plan-->
            </div>
            <h3><?php echo $this->translate("Current Package: ") . $this->translate(ucfirst($this->package->title)); ?></h3>
          </div>
          <?php $item = $this->package; ?>
          <?php include APPLICATION_PATH . '/application/modules/Siteeventpaid/views/scripts/package/_packageInfo.tpl'; ?>
        </li>
      </ul>
    </div>
    <div class='siteevent_package_page mtop15'>
      <?php if (count($this->paginator)): ?>
        <ul class="siteevent_package_list o_hidden mbot10">
          <li>
            <h3><?php echo $this->translate('Available Packages') ?></h3>
            <span>  <?php echo $this->translate("If you want to change the package for your event, please select one package from the below list."); ?></span>
          </li>
          <li>
            <div class="tip o_hidden mbot10">
              <span>
                <?php echo $this->translate("Note: Once you change package for your event, all the settings of the event will be applied according to the new package, including features available, price, etc."); ?>
              </span>
            </div>
          </li>
          <?php foreach ($this->paginator as $item): ?>
            <li>
              <?php if (empty($this->package_view)): ?>
                <div class="siteevent_package_list_title">
                  <div class="siteevent_create_link">
                    <?php
                    echo $this->htmlLink(
                        array('route' => "siteevent_package", 'action' => 'update-confirmation', "event_id" => $this->event_id, "package_id" => $item->package_id), $this->translate('Change Package'), array('onclick' => 'owner(this);return false', 'title' => $this->translate('Change Package'), 'class' => 'siteevent_buttonlink'));
                    ?>
                  </div>
                  <h3>             
                    <a href='<?php echo $this->url(array("action" => "detail", 'id' => $item->package_id), "siteevent_package", true) ?>' onclick="owner(this);
                              return false;" title="<?php echo $this->translate(ucfirst($item->title)) ?>"><?php echo $this->translate(ucfirst($item->title)); ?></a>
                  </h3>                 
                </div>
                <?php include APPLICATION_PATH . '/application/modules/Siteeventpaid/views/scripts/package/_packageInfo.tpl'; ?>
              <?php else: ?>
                <?php include APPLICATION_PATH . '/application/modules/Siteeventpaid/views/scripts/package/_verticalPackageInfo.tpl'; ?>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
          <br />
          <div>
            <?php echo $this->paginationControl($this->paginator); ?>
          </div>
        </ul>
      <?php else: ?>
        <div class="tip">
          <span>
            <?php echo $this->translate("There are no other packages yet.") ?>
          </span>
        </div>
      <?php endif; ?>
    </div>
    <?php if (empty($this->is_ajax)) : ?>		
    </div>
  </div>
  </div>
<?php endif; ?>
<script type="text/javascript">

  function submitSession(id) {
    document.getElementById("event_id_session").value = id;
    document.getElementById("setSession_form").submit();
  }

  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }

</script>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>

<?php if (!empty($this->viewer->level_id)): ?>
  <?php $level_id = $this->viewer->level_id; ?>
<?php else: ?>
  <?php $level_id = 0; ?>
<?php endif; ?>

<div class="layout_middle siteeventpaid_create_wrapper clr">
  <h3><?php echo $this->translate("Create New Event") ?></h3>
  <p><?php echo $this->translate("Create an event using these quick, easy steps and get going."); ?></p>	
  <h4 class="siteeventpaid_create_step fleft"><?php echo $this->translate("1. Choose an event Package"); ?></h4>
  <div class='siteeventpaid_package_page'>
    <?php if ($this->paginator->getTotalItemCount()): ?>
      <ul class="siteeventpaid_package_list" id="packages">
        <li style="width:100%;">
          <span><?php echo $this->translate("Select a package that best matches your requirements. Packages differ in terms of prices and features available to events created under them. You can change your package anytime later."); ?></span>
        </li>
        <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
        <?php foreach ($this->paginator as $item): ?>
          <li>
            <div class="siteeventpaid_package_list_title">
              <div class="siteeventpaid_create_link">
                <?php if (!empty($this->parent_id) && !empty($this->parent_type)): ?>
                  <?php $url = $this->url(array("action" => "create", 'id' => $item->package_id, 'parent_id' => $this->parent_id, 'parent_type' => $this->parent_type), 'siteevent_general', true); ?>
                  <a class="siteevent_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate("Create an Event"); ?> &raquo;</a>                     
                <?php else: ?>
                  <?php $url = $this->url(array("action" => "create", 'id' => $item->package_id), "siteevent_general", true); ?>
                  <a class="siteevent_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate("Create an Event"); ?> &raquo;</a>
                <?php endif; ?>

              </div> 	 
              <h3>        
                <a href='<?php echo $this->url(array("action" => "detail", 'id' => $item->package_id), "siteevent_package", true) ?>' onclick="owner(this);
                        return false;" title="<?php echo $this->translate(ucfirst($item->title)) ?>"><?php echo $this->translate(ucfirst($item->title)); ?></a>
              </h3>
            </div> 
            <?php include APPLICATION_PATH . '/application/modules/Siteeventpaid/views/scripts/package/_packageInfo.tpl'; ?>
          </li> 
        <?php endforeach; ?>
        <br />
        <div><?php echo $this->paginationControl($this->paginator); ?></div>
      </ul>	
    <?php else: ?>
      <div class="tip">
        <span>
          <?php echo $this->translate("There are no packages yet.") ?>
        </span>
      </div>
    <?php endif; ?>
  </div>
</div>

<!--vertcal design code end-->
<script type="text/javascript" >
  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
</script>

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

<script type="text/javascript">
  var currentOrder = '<?php echo $this->filterValues['order'] ?>';
  var currentOrderDirection = '<?php echo $this->filterValues['direction'] ?>';
  var changeOrder = function(order, default_direction) {
    // Just change direction
    if (order == currentOrder) {
      $('direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
    }
    else {
      $('order').value = order;
      $('direction').value = default_direction;
    }
    $('filter_form').submit();
  }

</script>
<h2>
  <?php echo 'Advanced Events Plugin'; ?>
</h2>

<?php if ( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<?php if ( count($this->navigationGeneral) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
  </div>
<?php endif; ?>

<h3>
  <?php echo "Manage Package for Events"; ?>
</h3>

<p>
  <?php echo 'This page allows you to create highly configurable and effective event packages, enabling you to get the best results. You can create both Free and Paid event packages. Below, you can manage existing event packages on your site, or create new ones. You can also set the sequence of event packages in the order in which they should appear to members on the package event page in the first step of event creation. To do so, drag-and-drop the packages vertically and click on "Save Order" to save the sequence. (Note: You will be able to create a new package over here only if you have enabled Packages from Package Settings.)'; ?>
</p>
<br />

<?php if ( !empty($this->isEnabled2Checkout) ): ?>
  <div class="tip">
    <span><?php echo "Please edit all the packages you have created, after enabling 2Checkout gateway on your site."; ?></span>
  </div>
  <br />
<?php endif; ?> 

<?php if ( !empty($this->error) ): ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->error ?>
    </li>
  </ul>

  <br />
<?php endif; ?>
<?php if ( !empty($this->paginator) && Engine_Api::_()->siteevent()->hasPackageEnable() && !empty($this->canCreate) ) : ?>
  <div class="createpackages">
    <?php
    echo $this->htmlLink(array('action' => 'create', 'reset' => false), 'Create New Package', array(
        'class' => 'buttonlink seaocore_icon_add',
    ))
    ?>
  </div>
<?php elseif ( !Engine_Api::_()->siteevent()->hasPackageEnable() ): ?>
  <div class="tip mtop10 fleft">
    <span>
      <?php echo "Packages setting is not enabled."; ?>
    </span>
  </div>
  <div class="clear"></div>
<?php endif; ?>
<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<br />

<?php if ( !empty($this->paginator) ): ?>
  <div class='admin_results'>
    <div>
      <?php $count = $this->paginator->getTotalItemCount() ?>
      <?php echo $this->translate(array("%s package found.", "%s packages found.", $count), $count) ?>
    </div>
    <div>
      <?php
      echo $this->paginationControl($this->paginator, null, null, array(
          'query' => $this->filterValues,
          'pageAsQuery' => true,
      ));
      ?>
    </div>
  </div>
  <br />
<?php endif; ?>

<?php if ( !empty($this->paginator) && $this->paginator->getTotalItemCount() > 0 ): ?>
  <table class='admin_table' width="100%">
    <thead>
      <tr>
        <th style="padding:7px 0;">
          <?php $class = ( $this->order == 'package_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
    <div style='width: 2%;' class="<?php echo $class ?> admin_table_centered">
      <a href="javascript:void(0);" onclick="javascript:changeOrder('package_id', 'DESC');">
        <?php echo "ID"; ?>
      </a>
    </div>
    <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
    <div  style='width: 25%;' class="<?php echo $class ?>">
      <a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');">
        <?php echo "Title"; ?>
      </a>
    </div>

    <?php $class = ( $this->order == 'price' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
    <div  style='width: 8%;'  class="<?php echo $class ?> ">
      <a href="javascript:void(0);" onclick="javascript:changeOrder('price', 'DESC');">
        <?php echo "Price"; ?>
      </a>
    </div>
    <div style='width: 7%;' class="">
      <?php echo "Duration"; ?>
    </div>
    <div style='width: 11%;' class="">
      <?php echo "Billing Cycle"; ?>
    </div>
    <?php $class = ( $this->order == 'enabled' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
    <div style='width: 7%;' class='admin_table_centered <?php echo $class ?>'>
      <a href="javascript:void(0);" onclick="javascript:changeOrder('enabled', 'DESC');">
        <?php echo "Enabled"; ?>
      </a>
    </div>

    <div style='width: 11%;' class='admin_table_centered'>
      <?php echo "Total Events"; ?>
    </div>
    <div style='width: 20%;' class='admin_table_options'>
      <?php echo "Options"; ?>
    </div>
  </th>
  </tr>
  </thead>
  </table>
  <form id='saveorder_form' method='post' action='<?php echo $this->url(array('action' => 'update')) ?>'>
    <input type='hidden'  name='order' id="order" value=''/>
    <div class="seaocore_admin_order_list" id='order-element'>
      <ul>
        <?php foreach ( $this->paginator as $item ) :
          ?>
          <li class="package-event">
            <input type='hidden'  name='order[]' value='<?php echo $item->package_id; ?>'>
            <table class='admin_table' width='100%'>
              <tbody>
                <tr>
                  <td style="padding:7px 0;">
                    <div style="width:2%;" class="admin_table_centered"><?php echo $item->package_id ?></div>
                    <div style="width:25%;" class='admin_table_bold'>
                      <?php echo $item->title ?>
                    </div>
                    <div style="width:8%;" class="">
                      <?php echo ($item->isFree()) ? 'FREE' : $this->locale()->toCurrency($item->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) ?>
                    </div>
                    <div style="width:7%;" class="">
                      <?php echo $item->getPackageQuantity() ?>
                    </div>
                    <div style="width:11%;" class="">
                      <?php echo $item->getBillingCycle() ?>
                    </div>
                    <!--          <div style="width:7%;" class="">
                    <?php //echo $eventTypeTitle; ?>
                              </div>-->
                    <div style="width:7%;" class='admin_table_centered'>
                      <?php if ( $item->enabled ): ?> 
                        <?php $disabledPackage = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getDisabledPackage(); ?>
                        <?php $totalPackage = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getTotalPackage(); ?>
                        <?php if ( $totalPackage == $disabledPackage + 1 ): ?>
                          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventpaid', 'controller' => 'package', 'action' => 'enabled', 'id' => $item->package_id,), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('onclick' => "showMessage($item->package_id); return false;", 'title' => 'Disable Package')), array()); ?>
                        <?php else: ?>
                          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventpaid', 'controller' => 'package', 'action' => 'enabled', 'id' => $item->package_id,), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => 'Disable Package')), array('class' => 'smoothbox')); ?>
                        <?php endif; ?>
                      <?php else: ?>
                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventpaid', 'controller' => 'package', 'action' => 'enabled', 'id' => $item->package_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => 'Enable Package'))); ?>
                      <?php endif; ?>
                    </div>
                    <div style="width:11%;" class='admin_table_centered'>
                      <?php $eventCount = $this->locale()->toNumber(@$this->memberCounts[$item->package_id]); ?>
                      <?php echo $eventCount; ?>
                    </div>

                    <div style="width:20%;" class='admin_table_options'>
                      <?php if ( Engine_Api::_()->siteevent()->hasPackageEnable() && !empty($this->canCreate) ) : ?>
                        <a href='<?php echo $this->url(array('action' => 'edit', 'package_id' => $item->package_id)) ?>'>
                          <?php echo "Edit" ?>
                        </a>
                        |
                      <?php endif; ?>
                      <a href="javascript:void(0);" onclick="viewEvents(<?php echo $item->package_id ?>)" ><?php echo 'View Events'; ?></a>
                      <?php if ( empty($item->defaultpackage) ): ?>
                        |
                        <?php
                        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventpaid', 'controller' => 'package', 'action' => 'delete', 'id' => $item->package_id), 'Delete', array(
                            'onclick' => "deletePackage($item->package_id, $eventCount); return false;"
                        ))
                        ?>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

  </form>
  <br />
  <button onClick="javascript:saveOrder(true);" type='submit'>
    <?php echo "Save Order"; ?>
  </button>

  <form id='view_selected' method='post' action='<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'manage', 'action' => 'index'), 'admin_default') ?>'>
    <input type="hidden" id="package_id" name="package_id" value=""/>
    <input type="hidden" id="search" name="search" value="1"/>
  </form>
<?php endif; ?>

<?php if ( !empty($this->paginator) && $this->paginator->getTotalItemCount() > 0 ): ?>
  <script type="text/javascript">
  var viewEvents = function(id) {

    $('package_id').value = id;
    $('view_selected').submit();
  }

  var saveFlag = false;
  var origOrder;
  var changeOptionsFlag = false;

  function saveOrder(value) {
    saveFlag = value;
    var finalOrder = [];
    var li = $('order-element').getElementsByTagName('li');
    for (i = 1; i <= li.length; i++)
      finalOrder.push(li[i]);
    $("order").value = finalOrder;

    $('saveorder_form').submit();
  }
  window.addEvent('domready', function() {
    //         We autogenerate a list on the fly
    var initList = [];
    var li = $('order-element').getElementsByTagName('li');
    for (i = 1; i <= li.length; i++)
      initList.push(li[i]);
    origOrder = initList;
    var temp_array = $('order-element').getElementsByTagName('ul');
    temp_array.innerHTML = initList;
    new Sortables(temp_array);
  });

  window.onbeforeunload = function(event) {
    var finalOrder = [];
    var li = $('order-element').getElementsByTagName('li');
    for (i = 1; i <= li.length; i++)
      finalOrder.push(li[i]);



    for (i = 0; i <= li.length; i++) {
      if (finalOrder[i] != origOrder[i])
      {
        changeOptionsFlag = true;
        break;
      }
    }

    if (changeOptionsFlag == true && !saveFlag) {
      var answer = confirm("<?php echo $this->string()->escapeJavascript("A change in the order of the packages has been detected. If you click Cancel, all unsaved changes will be lost. Click OK to save change and proceed."); ?>");
      if (answer) {
        $('order').value = finalOrder;
        $('saveorder_form').submit();

      }
    }
  }

  function showMessage(packageId) {

    var confirmation = confirm('After disabling this package, all packages for this event type will be disabled and members are unable to create events if package setting is enabled for this event type.');
    if (confirmation != '') {
      var url = en4.core.baseUrl + 'admin/siteeventpaid/package/enabled/id/' + packageId;
      Smoothbox.open(url);
    }
  }

  function deletePackage(packageId, eventCount) {

    if (eventCount == 0) {
      var url = en4.core.baseUrl + 'admin/siteeventpaid/package/delete/id/' + packageId;
      Smoothbox.open(url);
    }
    else {
      alert('Please first delete all events associated with this package.');
    }
  }
  </script>
<?php endif; ?>

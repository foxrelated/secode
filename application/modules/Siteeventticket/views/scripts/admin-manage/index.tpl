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
<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function (order, default_direction) {

    if (order == currentOrder) {
      $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }

  function multiDelete()
  {
    return confirm("Are you sure you want to delete selected tickets ?");
  }

  function selectAll()
  {
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;

    for (i = 1; i < inputs.length - 1; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }
</script>

<h2>
  <?php echo 'Advanced Events Plugin'; ?>
</h2>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if (count($this->navigationGeneral)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
  </div>
<?php endif; ?>

<h3><?php echo 'Manage Tickets'; ?></h3>
<p class="description"><?php echo 'This page lists all the tickets which event owners have created. You can use this page to monitor these tickets and delete offensive material if necessary. Entering criteria into the search boxes will help you in finding the specific ticket entries. Leaving the search boxes blank will show all the ticket entries available on your social network.'; ?></p>

<div class="admin_search siteeventticket_admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="" width="100%">

      <div>
        <label>
          <?php echo "Title"; ?>
        </label>
        <?php if (empty($this->title)): ?>
          <input type="text" name="title" /> 
        <?php else: ?>
          <input type="text" name="title" value="<?php echo $this->title; ?>"/>
        <?php endif; ?>
      </div>

      <div>
        <label>
          <?php echo "Event"; ?>
        </label>
        <?php if (empty($this->event)): ?>
          <input type="text" name="event" /> 
        <?php else: ?>
          <input type="text" name="event" value="<?php echo $this->event; ?>"/>
        <?php endif; ?>
      </div>

      <div>
        <label>
          <?php echo "Owner"; ?>
        </label>	
        <?php if (empty($this->owner)): ?>
          <input type="text" name="owner" /> 
        <?php else: ?> 
          <input type="text" name="owner" value="<?php echo $this->owner; ?>" />
        <?php endif; ?>
      </div>

      <div>
        <label>
          <?php echo "Price"; ?>
        </label>
        <div>
          <?php if ($this->price_min == ''): ?>
            <input type="text" name="price_min" placeholder="min" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="price_min" placeholder="min" value="<?php echo $this->price_min ?>" class="input_field_small" />
          <?php endif; ?>
          <?php if ($this->price_max == ''): ?>
            <input type="text" name="price_max" placeholder="max" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="price_max" placeholder="max" value="<?php echo $this->price_max ?>" class="input_field_small" />
          <?php endif; ?>
        </div>    
      </div>

      <div>
        <label>
          <?php echo "Status"; ?>	
        </label>
        <select id="" name="status">
          <option value="0" ><?php echo "Select"; ?></option>
          <option value="open" <?php if ($this->status == "open") echo "selected"; ?> ><?php echo "Open"; ?></option>
          <option value="hidden" <?php if ($this->status == "hidden") echo "selected"; ?> ><?php echo "Hidden"; ?></option>
          <option value="closed" <?php if ($this->status == "closed") echo "selected"; ?> ><?php echo "Closed"; ?></option>
        </select>
      </div>

      <div class="clear mtop10">
        <button type="submit" name="search" ><?php echo "Search"; ?></button>
      </div>
    </form>
  </div>
</div>
<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<div class='admin_members_results'>
  <?php $counter = $this->paginator->getTotalItemCount(); ?>
  <?php if (!empty($counter)): ?>
    <div class="">
      <?php echo $this->translate(array('%s ticket found.', '%s tickets found.', $counter), $this->locale()->toNumber($counter)) ?>
    </div>
  <?php else: ?>
    <div class="tip"><span>
        <?php echo "No results were found."; ?></span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>
<br />

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" onSubmit="return multiDelete()">

    <table class='admin_table seaocore_admin_table' width="100%">
      <thead>
        <tr>
          <th style="width: 2%;"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>

          <?php $class = ( $this->order == 'ticket_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th style="width: 3%;" class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('ticket_id', 'DESC');"><?php echo 'ID'; ?></a></th>

          <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th style="width: 15%;" class="<?php echo $class ?>"  align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo 'Title'; ?></a></th>

          <?php $class = ( $this->order == 'event' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th style="width: 10%;" class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('event', 'ASC');"><?php echo 'Event'; ?></a></th>

          <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th style="width: 10%;" class="<?php echo $class ?>"  align="left" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo 'Owner'; ?></a></th>

          <?php $class = ( $this->order == 'price' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th style="width: 10%;" class="<?php echo $class ?> admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('price', 'DESC');"><?php echo 'Price'; ?></a></th>


          <th style="width: 10%;" class="admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('quantity', 'DESC');"><?php echo 'Qty'; ?></a></th>

          <th style="width: 10%;" class="admin_table_centered"><?php echo 'Status'; ?></a></th>

        <!--          <th align="center" class="admin_table_centered" title="<?php echo "Expiry / End Date of tickets"; ?>" ><?php echo 'Ex / En'; ?></th>-->

          <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th style="width: 10%;" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo 'Creation Date'; ?></a></th>

          <th style="width: 10%;" class="<?php echo $class ?>"  class='admin_table_centered'><?php echo 'Options'; ?></th>
        </tr>
      </thead>

      <tbody>
        <?php if (count($this->paginator)): ?>
          <?php foreach ($this->paginator as $item): ?>
            <tr>

              <td><input name='delete_<?php echo $item->ticket_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->ticket_id ?>"/></td>

              <td><?php echo $item->ticket_id; ?></td>
              <td class='admin_table_bold' style="white-space:normal;" title="<?php echo $item->title; ?>">
                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'manage', 'action' => 'detail', 'id' => $item->ticket_id), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->title, 10), array('class' => 'smoothbox')) ?>
              </td>

              <td class='admin_table_bold' style="white-space:normal;" title="<?php echo $item->event ?>"><?php echo $this->htmlLink($this->item('siteevent_event', $item->event_id)->getHref(), $this->string()->truncate($this->string()->stripTags($item->event), 10), array('title' => $item->event, 'target' => '_blank')) ?></td>

              <td class='admin_table_bold' title="<?php echo $item->getOwner()->getTitle() ?>"> <?php echo $this->htmlLink($item->getOwner()->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getOwner()->getTitle(), 10), array('target' => '_blank')) ?></td>

              <td align="center" class="admin_table_centered"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($item->price) ?></td>

              <td align="center" class="admin_table_centered"><?php echo $item->quantity ?></td>

              <td align="center" class="admin_table_centered"><?php echo $item->status ?></td>

                        <!--              <td align="center" class="admin_table_centered" title="End Date of ticket">
              <?php if ($item->is_same_end_date): ?>
                <?php echo "Just before the event start time"; ?>
              <?php else: ?>
                <?php echo (empty($item->sell_endtime) || $item->sell_endtime == '0000-00-00 00:00:00') ? 'Never' : gmdate('M d,Y, g:i A', strtotime($item->sell_endtime)) ?>
              <?php endif; ?>
                        </td>             -->

              <td><?php echo gmdate('M d,Y, g:i A', strtotime($item->creation_date)) ?></td>                 

              <td class='admin_table_options'>
                <?php // echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'general', 'action' => 'change-owner', 'ticket_id' => $item->ticket_id), 'change owner'), array('class' => 'smoothbox')) ?>
                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'manage', 'action' => 'detail', 'id' => $item->ticket_id), 'details', array('class' => 'smoothbox')) ?> |     
                <?php
                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'manage', 'action' => 'delete', 'ticket_id' => $item->ticket_id), 'delete', array(
                 'class' => 'smoothbox',
                ))
                ?>
              </td>
            </tr>

          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
    <br />
    <div class='buttons mtop10 fleft'>
      <button type='submit'><?php echo 'Delete Selected'; ?></button>
    </div>
  </form>
<?php endif; ?>		
</div>

<script type="text/javascript">
  function clear(element)
  {
    for (var i = (element.options.length - 1); i >= 0; i--) {
      element.options[ i ] = null;
    }
  }

  var search_category_id, search_subcategory_id, search_subsubcategory_id;

</script>
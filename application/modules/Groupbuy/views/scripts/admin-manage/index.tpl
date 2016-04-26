<style>
	td.option a:not(:last-child) {
	  border-right: 1px solid gray;
	  padding-right: 5px;
	  padding-left: 5px;
	}
</style>
<h2><?php echo $this->translate("Group Buy Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("GROUPBUY_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>

<br /> 
<div class='admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>
 <?php echo $this->count." ".$this->translate('deal(s)');   ?>
 <br/>
<?php if( count($this->paginator) ): ?>
<script type="text/javascript">
  en4.core.runonce.add(function(){
      $$('th.admin_table_short input[type=checkbox]').addEvent('click', 
      function(){ 
           var checkboxes = $$('td.minh input[type=checkbox]');
           checkboxes.each(function(item, index){
               item.checked = $('check_all').checked;
           });
      })});

  var delectSelected =function(){
    var checkboxes = $$('td.minh input[type=checkbox]');
    var selecteditems = [];
    checkboxes.each(function(item, index){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });

    $('ids').value = selecteditems;
    $('delete_selected').submit();
  }
  var approveSelected =function(){
    var checkboxes = $$('td.minh input[type=checkbox]');
    var selecteditems = [];
    checkboxes.each(function(item, index){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });

    $('ids1').value = selecteditems;
    $('approve_selected').submit();
  }
  function deal_good(deal_id,checbox){
      
      checbox = document.getElementById('gooddeal_' + deal_id);
      var status = 1;     
      if(checbox.checked == true) 
      {
          status = 1;
      }
      else
      {
        status = 0;
      }
    new Request.JSON({
      'format': 'json',
      'url' : '<?php echo $this->url(array('module' => 'groupbuy', 'controller' => 'manage', 'action' => 'featured'), 'admin_default') ?>',
      'data' : {
        'format' : 'json',
        'deal' : deal_id,
        'good' : status
      }
    }).send();
  }
  function deal_stop(deal_id,checbox){
        
      checbox = document.getElementById('stopdeal_' + deal_id);
      var status = 1;     
      if(checbox.checked == true) 
      {
          status = 1;
      }
      else
      {
        status = 0;
      }
        new Request.JSON({
          'format': 'json',
          'url' : '<?php echo $this->url(array('module' => 'groupbuy', 'controller' => 'manage', 'action' => 'stop','admin'=>1), 'admin_default') ?>',
          'data' : {
            'format' : 'json',
            'deal' : deal_id,
            'stop' : status
          }
        }).send();
    }
    var currentOrder = '<?php echo $this->filterValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->filterValues['direction'] ?>';
    var changeOrder = function(order, default_direction){
      // Just change direction
      if( order == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      } else {
        $('order').value = order;
        $('direction').value = default_direction;
      }
      $('filter_form').submit();
    }
  </script>
<div style="overflow: auto;">
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input id="check_all" type='checkbox' class='checkbox' /></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'DESC');"><?php echo $this->translate("Name") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('cat_title', 'DESC');"><?php echo $this->translate("Category") ?></a></th> 
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'DESC');"><?php echo $this->translate("Seller") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('location_title', 'DESC');"><?php echo $this->translate("Location") ?></a></th> 
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('address', 'DESC');"><?php echo $this->translate("Address") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('start_time', 'DESC');"><?php echo $this->translate("Start Time") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('end_time', 'DESC');"><?php echo $this->translate("End Time") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'DESC');"><?php echo $this->translate("Featured") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('status', 'DESC');"><?php echo $this->translate("Status") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('published', 'DESC');"><?php echo $this->translate("Published") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('stop', 'DESC');"><?php echo $this->translate("Stopped") ?></a></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td class="minh"><input type='checkbox' class='checkbox' value="<?php echo $item->deal_id ?>"/></td>
        <td><a href="<?php echo $item->getHref()?>"><?php echo $item->getTitle() ?></a></td>
         <td><?php echo Engine_Api::_()->getItem('groupbuy_category',$item->category_id)->title ?></td> 
         <td><a href="<?php echo $this->user($item->user_id)->getHref() ?>"><?php echo $this->user($item->user_id)->getTitle() ?></a></td>
        <td><?php echo Engine_Api::_()->getItem('groupbuy_location',$item->location_id)->title ?></td> 
        <td><?php echo $item->address; ?></td>
        <td><?php echo $this->locale()->toDateTime($item->start_time); ?></td>
        <td><?php echo $this->locale()->toDateTime($item->end_time); ?></td>
        <td>
         <?php if($item->featured == 1): ?>
        <input type="checkbox" id='gooddeal_<?php echo $item->deal_id; ?>'  onclick="deal_good(<?php echo $item->deal_id; ?>,this)" checked />
              <?php else: ?>
               <input type="checkbox" id='gooddeal_<?php echo $item->deal_id; ?>'  onclick="deal_good(<?php echo $item->deal_id; ?>,this)" />
              <?php endif; ?> 
         </td>
          <td><?php echo $this->translate($item->getStatusString()); ?> </td>
          
          <td><?php  echo  $this->translate($item->getPublishedString());?> </td>
           <td>
          <?php if($item->stop == 1): ?>
        <input type="checkbox" id='stopdeal_<?php echo $item->deal_id; ?>'  onclick="deal_stop(<?php echo $item->deal_id; ?>,this)" checked />
              <?php else: ?>
               <input type="checkbox" id='stopdeal_<?php echo $item->deal_id; ?>'  onclick="deal_stop(<?php echo $item->deal_id; ?>,this)" />
              <?php endif; ?> </td>  
            <td  class="option">
          <?php if ($item->isViewable()) :?>
          <?php echo $this->htmlLink($item->getHref(), $this->translate('view')) ?>
          <?php endif;?>
          
          <?php if ($item->isEditable()) :?>
          <?php echo $this->htmlLink(array(
              'action' => 'admin-edit',
              'deal' => $item->getIdentity(),
              'route' => 'groupbuy_general',
              'reset' => true,
            ), $this->translate('edit'), array(
              'class' => ' ',
            )) ?>
          <?php endif;?>
          
          <?php if ($item->isDeleteable()) :?>
          <?php echo $this->htmlLink(array(
              'action' => 'delete',
              'deal' => $item->getIdentity(),
              'admin' => 1,
              'route' => 'groupbuy_general',
              'reset' => true,
            ), $this->translate('delete'), array(
              'class' => ' smoothbox ',
            )) ?>
            <?php endif;?>
            
            <?php if($item->published < 20 && $item->status < 20):?>
          <?php echo $this->htmlLink(array(
              'action' => 'approve',
              'deal' => $item->getIdentity(),
              'route' => 'groupbuy_general',
              'reset' => true,
            ), $this->translate('Approve'), array(
              'class' => ' smoothbox ',
            )) ?>
          <?php echo $this->htmlLink(array(
              'action' => 'deny',
              'deal' => $item->getIdentity(),
              'route' => 'groupbuy_general',
              'reset' => true,
            ), $this->translate('Deny'), array(
              'class' => ' smoothbox ',
            )) ?>
            <?php endif; ?>
            <?php //if($item->getStatusString() == 'Closed'): ?>
           
          <?php //echo $this->htmlLink(array(
             // 'action' => 'reopen',
             // 'deal' => $item->getIdentity(),
             // 'route' => 'groupbuy_general',
             // 'reset' => true,
           // ), $this->translate('reopen'), array(
           //   'class' => ' smoothbox ',
           // )) ?>
          <?php// endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>  
<br />

<div class='buttons'>
  <button onclick="javascript:delectSelected();" type='submit'>
    <?php echo $this->translate("Delete Selected") ?>
  </button>
   <!--<button onclick="javascript:approveSelected();" type='submit'>
    <?php //echo $this->translate("Approve Selected") ?>
  </button> -->
</div>

<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'delete-selected')) ?>'>
  <input type="hidden" id="ids" name="ids" value=""/>
</form>
<form id='approve_selected' method='post' action='<?php echo $this->url(array('action' =>'approve-selected')) ?>'>
  <input type="hidden" id="ids1" name="ids1" value=""/>
</form>
<br/>
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
</div>

<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no deals yet.") ?>
    </span>
  </div>
<?php endif; ?>
    
<style type="text/css">

.admin_search {
    max-width: 950px !important;
}
</style>
<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
  height: 65px;
}
.search > form{width: 905px;}
</style>   
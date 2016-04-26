<h2><?php echo $this->translate("Store Plugin") ?></h2>

<!-- admin menu -->
<?php echo $this->content()->renderWidget('socialstore.admin-main-menu') ?>

<p>
  <?php echo $this->translate("STORE_VIEWS_SCRIPTS_ADMINMANAGEPRODUCT_INDEX_DESCRIPTION") ?>
</p>

<br /> 
<div class='admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>
 <?php echo $this->count." ".$this->translate('product(s)');   ?>
 <br/>
<?php if( count($this->paginator) ): ?>
<script type="text/javascript">
en4.core.runonce.add(function(){
	$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ 
		$$('td.checksub input[type=checkbox]').each(function(i){
 			i.checked = $$('th.admin_table_short input[type=checkbox]')[0].checked;
		});
	});
	$$('td.checksub input[type=checkbox]').addEvent('click', function(){
		var checks = $$('td.checksub input[type=checkbox]');
		var flag = true;
		for (i = 0; i < checks.length; i++) {
			if (checks[i].checked == false) {
				flag = false;
			}
		}
		if (flag) {
			$$('th.admin_table_short input[type=checkbox]')[0].checked = true;
		}
		else {
			$$('th.admin_table_short input[type=checkbox]')[0].checked = false;
		}
	});
});
  var delectSelected =function(){
    var checkboxes = $$('td.checksub input[type=checkbox]');
    var selecteditems = [];
    $$("td.checksub input[type=checkbox]:checked").each(function(i)
	{
    	selecteditems.push(i.value);
	});
    $('ids').value = selecteditems;
    $('delete_selected').submit();
  }
  var approveSelected =function(){
    var checkboxes = $$('td.checksub input[type=checkbox]');
    var selecteditems = [];
    $$("td.checksub input[type=checkbox]:checked").each(function(i){
    	selecteditems.push(i.value);
    });

    $('ids1').value = selecteditems;
    $('approve_selected').submit();
  }
  function featureProduct(product_id,checbox){
            
            if(checbox.checked==true) status =1;
            else status =0;
            new Request.JSON({
              'format': 'json',
              'url' : '<?php echo $this->url(array('module' => 'socialstore', 'controller' => 'manage-product', 'action' => 'featured'), 'admin_default') ?>',
              'data' : {
                'format' : 'json',
                'product' : product_id,
                'good' : status
              }
            }).send();
  }
  function gdaProduct(product_id,checbox){
            
            if(checbox.checked==true) status =1;
            else status =0;
            new Request.JSON({
              'format': 'json',
              'url' : '<?php echo $this->url(array('module' => 'socialstore', 'controller' => 'manage-product', 'action' => 'gda'), 'admin_default') ?>',
              'data' : {
                'format' : 'json',
                'product' : product_id,
                'good' : status
              }
            }).send();
  }
  function showProduct(product_id,checbox){
      
      if(checbox.checked==true) status =1;
      else status =0;
      new Request.JSON({
        'format': 'json',
        'url' : '<?php echo $this->url(array('module' => 'socialstore', 'controller' => 'manage-product', 'action' => 'show'), 'admin_default') ?>',
        'data' : {
          'format' : 'json',
          'product' : product_id,
          'show' : status
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
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
      <th style = "text-align: left;"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'DESC');"><?php echo $this->translate("Product") ?></a></th>
      <th style = "text-align: left;"><?php echo $this->translate("Store") ?></th>
      <th style = "text-align: left;"><?php echo $this->translate("Category") ?></th> 
      <th style = "text-align: left;"><?php echo $this->translate("Seller") ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('view_status', 'DESC');"><?php echo $this->translate("View Status") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('approve_status', 'DESC');"><?php echo $this->translate("Approve Status") ?></a></th> 
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'DESC');"><?php echo $this->translate("Featured") ?></a></th>
      <?php if(Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection()):?>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('gda', 'DESC');"><?php echo $this->translate("Deal Request") ?></a></th>
      <?php endif; ?>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Created Date") ?></a></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td class="checksub"><input type='checkbox' class='checkbox' value="<?php echo $item->product_id ?>"/></td>
        <td style = "text-align: left;"><a href="<?php echo $item->getHref()?>"><?php echo $item->getTitle() ?></a></td>
        <td style = "text-align: left;"> <?php $store = Engine_Api::_()->getItem('social_store', $item->store_id); ?>
        <a href="<?php echo $store->getHref()?>"><?php echo $store->getTitle() ?></a>
        </td>
         <td style = "text-align: left;"><?php echo Engine_Api::_()->getApi('core','Socialstore')->getCategoryName($item->category_id); ?></td> 
         <td style = "text-align: left;"><a href="<?php echo $this->user($item->owner_id)->getHref() ?>"><?php echo $this->user($item->owner_id)->getTitle() ?></a></td>
		<td>
		 <?php if($item->view_status == 'show'): ?>
        	<?php echo $this->htmlLink(array(
              'module' => 'socialstore',
        	  'controller' => 'manage-product',
        	  'action' => 'show',
              'product' => $item->getIdentity(),
              'show' => '0',
              'route' => 'admin_default',
              'reset' => true,
            ), $this->translate('Hide'), array(
              'class' => ' smoothbox ',
            )) ?>
 		 <?php else: ?>
             	<?php echo $this->htmlLink(array(
              'module' => 'socialstore',
        	  'controller' => 'manage-product',
        	  'action' => 'show',
              'product' => $item->getIdentity(),
              'show' => '1',
              'route' => 'admin_default',
              'reset' => true,
            ), $this->translate('Show'), array(
              'class' => ' smoothbox ',
            )) ?>
 		 <?php endif; ?> 
        </td>
		<td>
		<?php if ($item->approve_status == "new" || $item->approve_status == "waiting") : ?>
		<?php echo $this->translate('Status: ');?> <?php echo $item->approve_status;?>
		<br />
		<?php echo $this->htmlLink(array(
              'module' => 'socialstore',
        	  'controller' => 'manage-product',
		      'action' => 'approve-product',
              'product' => $item->getIdentity(),
              'route' => 'admin_default',
              'reset' => true,
            ), $this->translate('Approve'), array(
              'class' => ' smoothbox ',
            )) ?>
            |
          <?php echo $this->htmlLink(array(
              'module' => 'socialstore',
        	  'controller' => 'manage-product',
          	  'action' => 'deny-product',
              'product' => $item->getIdentity(),
              'route' => 'admin_default',
              'reset' => true,
            ), $this->translate('Deny'), array(
              'class' => ' smoothbox ',
            )) ?>
            <?php else: ?>
            	<?php echo $this->translate('Status: ');?><?php echo $item->approve_status;?>
            <?php endif;?>
		</td>
		<td>
         <?php if($item->featured == 1): ?>
        <input type="checkbox" id='featureproduct_<?php echo $item->product_id; ?>'  onclick="featureProduct(<?php echo $item->product_id; ?>,this)" checked />
              <?php else: ?>
               <input type="checkbox" id='featureproduct_<?php echo $item->product_id; ?>'  onclick="featureProduct(<?php echo $item->product_id; ?>,this)" />
              <?php endif; ?> 
         </td> 
        <?php if(Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection()):?>   
         <td>
         <?php if($item->gda == 1): ?>
        <input type="checkbox" id='gdaproduct_<?php echo $item->product_id; ?>'  onclick="gdaProduct(<?php echo $item->product_id; ?>,this)" checked />
              <?php else: ?>
               <input type="checkbox" id='gdaproduct_<?php echo $item->product_id; ?>'  onclick="gdaProduct(<?php echo $item->product_id; ?>,this)" />
              <?php endif; ?> 
         </td>   
        <?php endif; ?>     
        <td><?php date_default_timezone_set($this->viewer->timezone);
		echo date('Y-m-d H:i:s',strtotime($item->creation_date)); ?></td>
            <td>
          <?php echo $this->htmlLink($item->getHref(), $this->translate('view')) ?>
          |
          <?php echo $this->htmlLink(array(
			  'module' => 'socialstore',
        	  'controller' => 'manage-product',
			  'action' => 'edit-product',
              'product_id' => $item->getIdentity(),
              'route' => 'admin_default',    
              'reset' => true,
            ), $this->translate('edit'), array(
              'class' => ' ',
            )) ?>
          |<br/>
          <?php echo $this->htmlLink(array(
           	  'module' => 'socialstore',
        	  'controller' => 'manage-product',
			  'action' => 'delete-product',
              'product_id' => $item->getIdentity(),
              'route' => 'admin_default',    
              'reset' => true,
            ), $this->translate('delete'), array(
              'class' => ' smoothbox ',
            )) ?>

        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />

<div class='buttons'>
  <button onclick="javascript:delectSelected();" type='submit'>
    <?php echo $this->translate("Delete Selected") ?>
  </button>
   <button onclick="javascript:approveSelected();" type='submit'>
    <?php echo $this->translate("Approve Selected") ?>
  </button>
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
      <?php echo $this->translate("There are no products yet.") ?>
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
table.admin_table tbody tr td {
	text-align: center;
}
table.admin_table thead tr th {
	text-align: center;
}
.admin_search{
	clear: both;
    max-width: 850px;
    overflow: hidden;
}
</style>   
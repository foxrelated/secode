<h2><?php echo $this->translate("Store Plugin") ?></h2>

<!-- admin menu -->
<?php echo $this->content()->renderWidget('socialstore.admin-main-menu') ?>

<p>
  <?php echo $this->translate("STORE_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>

<br /> 
<div class='admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>
 <?php echo $this->count." ".$this->translate('store(s)');   ?>
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
	    $$("td.checksub input[type=checkbox]:checked").each(function(i){
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
  function featureStore(store_id,checbox){
            
            if(checbox.checked==true) status =1;
            else status =0;
            new Request.JSON({
              'format': 'json',
              'url' : '<?php echo $this->url(array('module' => 'socialstore', 'controller' => 'manage-store', 'action' => 'featured'), 'admin_default') ?>',
              'data' : {
                'format' : 'json',
                'socialstore' : store_id,
                'good' : status
              }
            }).send();
  }
  function showStore(store_id,checbox){
      
      if(checbox.checked==true) status =1;
      else status =0;
      new Request.JSON({
        'format': 'json',
        'url' : '<?php echo $this->url(array('module' => 'socialstore', 'controller' => 'manage-store', 'action' => 'show'), 'admin_default') ?>',
        'data' : {
          'format' : 'json',
          'socialstore' : store_id,
          'show' : status
        }
      }).send();
}

    var currentOrder = '<?php echo $this->formValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
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
      <th><?php echo $this->translate("Store") ?></th>
      <th><?php echo $this->translate("Location") ?></th> 
      <th><?php echo $this->translate("Category") ?></th> 
      <th><?php echo $this->translate("Seller") ?></th>
       <th><a href="javascript:void(0);" onclick="javascript:changeOrder('view_status', 'DESC');"><?php echo $this->translate("View Status") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('approve_status', 'DESC');"><?php echo $this->translate("Approve Status") ?></a></th> 
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'DESC');"><?php echo $this->translate("Featured") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Created Date") ?></a></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td class="checksub"><input type='checkbox' class='checkbox' value="<?php echo $item->store_id ?>"/></td>
        <td><a href="<?php echo $item->getHref()?>" ><?php echo $item->getTitle() ?></a></td>
         <td><?php echo $item->getLocation(); ?></td> 
         <td><?php echo $item->getCategory()->name; ?></td> 
         <td><a href="<?php echo $this->user($item->owner_id)->getHref() ?>"><?php echo $this->user($item->owner_id)->getTitle() ?></a></td>
		<td>
		 <?php 
		 if ($item->approve_status == 'denied') : 
		 	echo $this->translate('N/A');
		 
		 else :
		 if($item->view_status == 'show'): ?>
        	<?php echo $this->htmlLink(array(
              'module' => 'socialstore',
        	  'controller' => 'manage-store',
        	  'action' => 'show',
              'socialstore' => $item->getIdentity(),
              'show' => '0',
              'route' => 'admin_default',
              'reset' => true,
            ), $this->translate('Hide'), array(
              'class' => ' smoothbox ',
            )) ?>
 		 <?php else: ?>
             	<?php echo $this->htmlLink(array(
              'module' => 'socialstore',
        	  'controller' => 'manage-store',
        	  'action' => 'show',
              'socialstore' => $item->getIdentity(),
              'show' => '1',
              'route' => 'admin_default',
              'reset' => true,
            ), $this->translate('Show'), array(
              'class' => ' smoothbox ',
            )) ?>
 		 <?php endif; 
 		 endif; ?> 
        </td>
		<td>
		<?php if ($item->approve_status == "new" || $item->approve_status == "waiting") : ?>
		<?php echo $this->translate('Status: ');?> <?php echo $item->approve_status;?>
		<br />
		<?php echo $this->htmlLink(array(
              'module' => 'socialstore',
        	  'controller' => 'manage-store',
			  'action' => 'approve-store',
              'socialstore' => $item->getIdentity(),
              'route' => 'admin_default',
              'reset' => true,
            ), $this->translate('Approve'), array(
              'class' => ' smoothbox ',
            )) ?>
            |
          <?php echo $this->htmlLink(array(
              'module' => 'socialstore',
        	  'controller' => 'manage-store',
			  'action' => 'deny-store',
              'socialstore' => $item->getIdentity(),
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
        <input type="checkbox" id='featurestore_<?php echo $item->store_id; ?>'  onclick="featureStore(<?php echo $item->store_id; ?>,this)" checked />
              <?php else: ?>
               <input type="checkbox" id='featurestore_<?php echo $item->store_id; ?>'  onclick="featureStore(<?php echo $item->store_id; ?>,this)" />
              <?php endif; ?> 
         </td>       
        <td><?php date_default_timezone_set($this->viewer->timezone);
		echo date('Y-m-d H:i:s',strtotime($item->creation_date)); ?></td>
            <td>
          <?php echo $this->htmlLink($item->getHref(), $this->translate('view'),array('target'=>'_default')) ?>
          |
          <?php echo $this->htmlLink(array(
			  'module' => 'socialstore',
        	  'controller' => 'manage-store',
			  'action' => 'edit-store',
              'store_id' => $item->getIdentity(),
              'route' => 'admin_default',    
              'reset' => true,
            ), $this->translate('edit'), array(
              'class' => '',
              'target'=>'_default'
            )) ?>
          |<br/>
           <?php echo $this->htmlLink(array(
           	  'module' => 'socialstore',
        	  'controller' => 'manage-store',
			  'action' => 'delete-store',
              'store_id' => $item->getIdentity(),
              'route' => 'admin_default',    
              'reset' => true,
            ), $this->translate('delete'), array(
              'class' => ' smoothbox ',
            )) ?>
			|<br/>
          <?php echo $this->htmlLink(array(
              'module' => 'socialstore',
          	  'controller' => 'manage-store',
          	  'action' => 'statistic',
              'store_id' => $item->getIdentity(),
              'route' => 'admin_default',
              'reset' => true,
            ), $this->translate('statistic'), array(
            )) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />

<div class='buttons'>
<!--    <button onclick="javascript:delectSelected();" type='submit'>
    <?php echo $this->translate("Delete Selected") ?>
  </button> -->
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
      <?php echo $this->translate("There are no stores yet.") ?>
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
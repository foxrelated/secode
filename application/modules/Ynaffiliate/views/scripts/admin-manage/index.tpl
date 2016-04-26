
<script type = "text/javascript">
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
<script type="text/javascript">
   en4.core.runonce.add(function(){
      $$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ $$('td.checksub input[type=checkbox]').set('checked', $(this).get('checked')); })});

   var deleteSelected =function(){
      var checkboxes = $$('td.checksub input[type=checkbox]');
      var selecteditems = [];
      checkboxes.each(function(item, index){
         var checked = item.get('checked');
         var value = item.get('value');
         if (checked == true && value != 'on'){
            selecteditems.push(value);
         }
      });

      $('ids').value = selecteditems;
      $('delete_selected').submit();
   }
   var approveSelected =function(){
      var checkboxes = $$('td.checksub input[type=checkbox]');
      var selecteditems = [];
      checkboxes.each(function(item, index){
         var checked = item.get('checked');
         var value = item.get('value');
         if (checked == true && value != 'on'){
            selecteditems.push(value);
         }
      });

      $('ids1').value = selecteditems;
      $('approve_selected').submit();
   }

   var denySelected =function(){
      var checkboxes = $$('td.checksub input[type=checkbox]');
      var selecteditems = [];
      checkboxes.each(function(item, index){
         var checked = item.get('checked');
         var value = item.get('value');
         if (checked == true && value != 'on'){
            selecteditems.push(value);
         }
      });
      if($('ids2')){
         $('ids2').value = selecteditems;
      }
      $('deny_selected').submit();
   }

</script>

<h2><?php echo $this->translate("Affiliate Plugin") ?></h2>
<?php echo $this->content()->renderWidget('ynaffiliate.admin-main-menu') ?>

<p>
   <?php echo $this->translate("YNAFFILIATE_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>

<br />
<div class='admin_search'>
   <?php echo $this->form->render($this); ?>
</div>
<br/>
<?php if (count($this->paginator)): ?>
 <div class="admin_table_form">
   <table class='admin_table' id="anyid">
      <thead class="ynaff_thead">
         <tr>
            <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
            <th> <a href="javascript:void(0);" onclick="javascript:changeOrder('contact_name', 'DESC');">
                  <?php echo $this->translate('User Name'); ?>
               </a></th>
            <th><?php echo $this->translate("Email") ?></th>
            <th class=""><?php echo $this->translate("Status") ?></th>
            <th>
               <a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');">
                  <?php echo $this->translate('Registration Date'); ?>
               </a>
            </th>
            <th class=""><?php echo $this->translate("Options") ?></th>
         </tr>
      </thead>
      <tbody>
         <?php foreach ($this->paginator as $item): ?>
            <tr>
               <td class="checksub"><input type='checkbox' class='checkbox' value="<?php echo $item->account_id ?>"/></td>
               <td>
                  <?php $user = Engine_Api::_()->user()->getUser($item['user_id']) ?>
                  <a href="<?php echo $user->getHref() ?>"><?php echo $user->getTitle() ?></a>

               </td>
               <td>
                  <?php
                  if ($item['contact_email'] != ""):
                     echo $item['contact_email'];
                  else:
                     echo $user->email;
                  endif;
                  ?>
               </td>
               <td>
                  <?php
                  switch ($item['approved']) {
                     case 0:
                        echo $this->translate("Waiting");
                        break;
                     case 1:
                        echo $this->translate("Approved");
                        break;
                     case 2:
                        echo $this->translate("Denied");
                        break;
                  }
                  ?>
               </td>
               <td><?php echo $this->locale()->toDateTime($item["creation_date"])?></td>
               <td>
                  <?php
                  echo $this->htmlLink(array(
                      'module' => 'ynaffiliate',
                      'controller' => 'manage',
                      'action' => 'statistics',
                      'account_id' => $item['account_id'],
                      'route' => 'admin_default',
                      'reset' => true,
                          ), $this->translate('statistics'))
                  ?>



                  <?php
                  if ($item['approved'] == 0) {
                     ?>
                     |
                     <?php
                     echo $this->htmlLink(array(
                         'module' => 'ynaffiliate',
                         'controller' => 'manage',
                         'action' => 'approve-affiliate',
                         'account_id' => $item['account_id'],
                         'route' => 'admin_default',
                         'reset' => true,
                             ), $this->translate('approve'), array(
                         'class' => ' smoothbox ',
                     ))
                     ?>
                     |
                     <?php
                     echo $this->htmlLink(array(
                         'module' => 'ynaffiliate',
                         'controller' => 'manage',
                         'action' => 'deny-affiliate',
                         'account_id' => $item['account_id'],
                         'route' => 'admin_default',
                         'reset' => true,
                             ), $this->translate('deny'), array(
                         'class' => ' smoothbox ',
                     ))
                     ?>

                     <?php
                  }
                  ?>

                  |
                  <?php
                  echo $this->htmlLink(array(
                      'module' => 'ynaffiliate',
                      'controller' => 'manage',
                      'action' => 'delete-affiliate',
                      'account_id' => $item['account_id'],
                      'route' => 'admin_default',
                      'reset' => true,
                          ), $this->translate('delete'), array(
                      'class' => ' smoothbox ',
                  ))
                  ?>

                    |
                   <?php
                  echo $this->htmlLink(array(
                   'module' => 'ynaffiliate',
                   'controller' => 'manage',
                   'action' => 'view-network-clients',
                   'account_id' => $item['account_id'],
                   'route' => 'admin_default',
                   'reset' => true,
                   ), $this->translate('view Network Clients'))
                   ?>


               </td>
            </tr>
         <?php endforeach; ?>
      </tbody>
   </table>
 </div>
   <br />

   <div class='buttons'>
      <button onclick="javascript:deleteSelected();" type='submit'>
         <?php echo $this->translate("Delete Selected") ?>
      </button>

      <button onclick="javascript:approveSelected();" type='submit'>
         <?php echo $this->translate("Approve Selected") ?>
      </button>

      <button onclick="javascript:denySelected();" type='submit'>
         <?php echo $this->translate("Deny Selected") ?>
      </button>
   </div>

   <form id='delete_selected' method='post' action='<?php echo $this->url(array('action' => 'delete-selected')) ?>'>
      <input type="hidden" id="ids" name="ids" value=""/>
   </form>
   <form id='approve_selected' method='post' action='<?php echo $this->url(array('action' => 'approve-selected')) ?>'>
      <input type="hidden" id="ids1" name="ids1" value=""/>
   </form>

   <form id='deny_selected' method='post' action='<?php echo $this->url(array('action' => 'deny-selected')) ?>'>
      <input type="hidden" id="ids2" name="ids2" value=""/>
   </form>

   <br/>
   <div>
      <?php
      echo $this->paginationControl($this->paginator, null, null, array(
          'pageAsQuery' => false,
          'query' => $this->formValues,
      ));
      ?>
   </div>

<?php else: ?>
   <div class="tip">
      <span>
         <?php echo $this->translate("There are no affiliates yet.") ?>
      </span>
   </div>
<?php endif; ?>

<style type="text/css">

   .admin_search {
      max-width: 650px !important;
   }
</style>

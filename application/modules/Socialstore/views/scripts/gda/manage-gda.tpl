<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<div class="layout_right">
    <!-- render mini menu -->
    <?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>
<div class="layout_middle">
<h3><?php echo $this->translate('List of deal requests')?></h3>
<br />
<div class='admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>
<br />
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

<script type="text/javascript">
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
      <th><?php echo $this->translate("Product") ?></th>
      <th><?php echo $this->translate("Requester") ?></th>
      <th style = "text-align: right;"><a href="javascript:void(0);" onclick="javascript:changeOrder('org_qty', 'DESC');"><?php echo $this->translate("Quantity") ?></a></th> 
      <th style = "text-align: right;"><a href="javascript:void(0);" onclick="javascript:changeOrder('org_discount', 'DESC');"><?php echo $this->translate("Percentage")."(%)"; ?></a></th> 
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Request Date") ?></a></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
<?php foreach($this->paginator as $item):
$product = Engine_Api::_()->getItem('social_product',$item->product_id );
?>
<tr>
         <td>
             <?php 
             echo $this->htmlLink($product->getHref(), $product->title) ?>
         </td>
        <td>
        <?php if($item->deal_id):?> 
             <a href="<?php echo $this->user($item->user_id)->getHref() ?>">
                 <?php echo $this->user($item->user_id)->getTitle() ?>
             </a>
        <?php endif;?>
         </td>
        <td style = "text-align: right;">
         <?php echo $item->org_qty;?> 
         </td>        
         <td style = "text-align: right;">
         <?php echo $item->org_discount ;?> 
         </td>       
        <td>
        <?php date_default_timezone_set($this->viewer->timezone);
        echo date('Y-m-d H:i:s',strtotime($item->creation_date)); ?>
        </td>
         <td>
          <?php 
          if ($item->status == 'approved') { 
            echo $this->translate('Approved');
          }
          else if($item->status == 'waiting')
          {
            echo $this->htmlLink(array(
              'route' => 'socialstore_extended',
              'controller' => 'gda',
              'action' => 'approve-request',
              'gdaId' => $item->gdarequest_id,
            ), $this->translate('Approve'), array(
               //'class' => 'smoothbox' 
            ));
            echo " | ";
          }
             
          if ($item->status == 'refused') 
          {
            echo $this->translate('Refused');
          }
          else if($item->status == 'waiting') 
          {
            echo $this->htmlLink(array(
                  'route' => 'socialstore_extended',
                  'action' => 'refuse-request',
                  'controller' => 'gda',
                  'gdaId' => $item->gdarequest_id,
                ), $this->translate('Refuse'), array(
                    'class' => ' smoothbox ',
                ));
          }
          if($item->status == 'waiting')
          {
             echo " | ";
             echo $this->htmlLink(array(
                  'route' => 'socialstore_extended',
                  'action' => 'delete-request',
                  'controller' => 'gda',
                  'gdaId' => $item->gdarequest_id,
                ), $this->translate('Delete'), array(
                    'class' => ' smoothbox ',
                ));
          }
           ?>
           </td>
      </tr>
<?php endforeach;?>
  </tbody>
</table>
 <?php else:
 if($this->search):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There is no deal request meets your criteria.');?>
      </span>
    </div>
 <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have no deal requests.');?>
      </span>
    </div>
   <?php endif; ?> 
  <?php endif; ?>
  <br/>
     <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
</div>
<style type="text/css">
 table.admin_table thead tr th {
    background-color: #E9F4FA;
    border-bottom: 1px solid #AAAAAA;
    font-weight: bold;
    padding: 7px 10px;
    white-space: nowrap;
}
table.admin_table tbody tr td {
    border-bottom: 1px solid #EEEEEE;
    font-size: 0.9em;
    padding: 7px 10px;
    vertical-align: top;
    white-space: normal;
}
#global_page_socialstore-gda-manage-gda form#filter_form {
    height: 35px;

}
#global_page_socialstore-gda-manage-gda form#filter_form div {
    height: 20px;
    float: left;
    margin-left: 15px;
}
#global_page_socialstore-gda-manage-gda  form#filter_form div div.buttons button#productsearch {
    margin-top: 6px;
}
#global_page_socialstore-gda-manage-gda #status-element
{
    height: 25px !important;
}
#global_page_socialstore-gda-manage-gda #status-label
{
    float: none !important;
    height: 15px !important;
}
#global_page_socialstore-gda-manage-gda #gda
{
    margin-top: 7px;
}
 </style>


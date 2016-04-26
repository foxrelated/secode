<?php


?>
<script type="text/javascript">


var currentOrder = '<?php echo $this->order ?>';
var currentOrderDirection = '<?php echo $this->order_direction ?>';
var changeOrder = function(order, default_direction){
  // Just change direction
  if( order == currentOrder ) {
    $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
  } else {
    $('order').value = order;
    $('order_direction').value = default_direction;
  }
  $('seo_admin_filter_form').submit();
}



  en4.core.runonce.add(function(){$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ $$('input[type=checkbox]').set('checked', $(this).get('checked', false)); })});

  var delectSelected =function(){
    var checkboxes = $$('input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item, index){
      var checked = item.get('checked', false);
      var value = item.get('value', false);
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });

    $('ids').value = selecteditems;
    $('delete_selected').submit();
  }
</script>

<h2><?php echo $this->translate("SEO Sitemap Plugin") ?></h2>

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
  <?php echo $this->translate("This page lists all of the SEO pages that you have created.") ?>
</p>

<br />
<div>
    <?php echo $this->htmlLink($this->url(array('action'=>'add')), 
      $this->translate('Add SEO Page Wizard'),
      array('class' => 'buttonlink icon_seo_page_create smoothbox')
    )?> 
  <?php if (!$this->hook_installed): ?>
      <?php echo $this->htmlLink($this->url(array('action'=>'hook')), 
      $this->translate('Install Layout Header Page Hook'),
      array('class' => 'buttonlink icon_seo_page_hook smoothbox')
    )?> 
    <br />
    <ul class="form-errors">
      <li><?php echo $this->translate('Could NOT find SEO Page layout hook. To fix this error, please click on "Install Layout Header Page Hook" link above.') ?>
      </li>
    </ul>
  <?php endif; ?>

   
</div>


<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />


<?php if( count($this->paginator) ): ?>

<div class='admin_results'>
  <div>
    <?php $feedCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s page found", "%s pages found", $feedCount), $this->locale()->toNumber($feedCount)) ?>
  </div>
  <div>    
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues
    )); ?>  
  </div>
</div>
<br />
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
        <th class='admin_table_short'>ID</th>
        
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('page_module', 'ASC');"><?php echo $this->translate("Module") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('page_controller', 'ASC');"><?php echo $this->translate("Controller") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('page_action', 'ASC');"><?php echo $this->translate("Action") ?></a></th>
        
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Title") ?></a></th>
        
        <th><?php echo $this->translate('Description'); ?></th>
        <th><?php echo $this->translate('Keywords'); ?></th>
        <th><?php echo $this->translate('Extra'); ?></th>
        
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('enabled', 'ASC');"><?php echo $this->translate("Enabled") ?></a></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' class='checkbox' value="<?php echo $item->page_id ?>"/></td>
          <td><?php echo $item->page_id ?></td>
          <td><?php echo $item->page_module; ?></td>
          <td><?php echo $item->page_controller; ?></td>
          <td><?php echo $item->page_action; ?></td>
          <td><?php echo $this->radcodes()->text()->truncate($item->getTitle(), 20); ?></td>
          
          <td><?php echo $item->description ? $item->description_mode : 'N/A' ?></td>
          <td><?php echo $item->keywords ? $item->keywords_mode : 'N/A' ?></td>
          <td><?php echo $this->translate($item->extra_headers ? 'yes' : 'no') ?></td>
          
          <td><?php echo $this->translate($item->enabled ? 'yes' : 'no'); ?></td>
          <td>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'seo', 'controller' => 'admin-pages', 'action' => 'edit', 'id' => $item->page_id), $this->translate('edit'), array(
              
            )) ?>            |
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'seo', 'controller' => 'admin-pages', 'action' => 'delete', 'id' => $item->page_id), $this->translate('delete'), array(
              'class' => 'smoothbox',
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
  </div>

  <form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'deleteselected')) ?>'>
    <input type="hidden" id="ids" name="ids" value=""/>
  </form>

  <br/>


<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no pages created that match your search criteria.") ?>
    </span>
  </div>
<?php endif; ?>

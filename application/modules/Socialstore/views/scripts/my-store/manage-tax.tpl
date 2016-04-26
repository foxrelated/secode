<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>

<div class="layout_middle">
<p>
        <?php echo $this->htmlLink(array(
              'action' => 'add-tax',
              'route' => 'socialstore_mystore_general'),
                                   $this->translate('Add Tax'),
                                   array('class' => 'smoothbox',
                                   )) ?>
</p>
<br />
    <form class="global_form">
    <?php if(count($this->paginator)>0):?>
      <table class="admin_table">
        <!--  VATs Listing Table Labels   -->
        <thead>
          <tr>
                  <th><?php echo $this->translate("Name") ?></th>
                  <th style = "text-align: right;"><?php echo $this->translate("Value") ?></th>
                  <th><?php echo $this->translate("Creation Date") ?></th>
                  <th><?php echo $this->translate("Modified Date") ?></th>
                  <th><?php echo $this->translate("Options") ?></th>
          </tr>
        </thead>
        <!--  Table Contents  -->
      <tbody>
        <?php foreach($this->paginator as $item): ?>
        <tr>
          <td><?php echo $item->name         ?></td>
          <td style = "text-align: right;"><?php echo $item->value."%"    ?></td>
          <td><?php echo $this->locale()->toDateTime($item->creation_date)?></td>
          <td><?php echo $this->locale()->toDateTime($item->modified_date)?></td>
          <td>
            <?php echo $this->htmlLink(array(
              'action' => 'edit-tax',
              'route' => 'socialstore_mystore_general',
              'tax_id'        => $item -> tax_id),
              $this->translate('Edit'),
              array('class' => 'smoothbox',));
            ?>
             |
            <?php echo $this->htmlLink(array(
              'action' => 'delete-tax',
              'route' => 'socialstore_mystore_general',
              'tax_id'  => $item -> tax_id),
            	$this->translate('Delete'),
             array('class' => 'smoothbox',));
            ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <br/>
        <!-- Page Changes  -->
    <div>
       <?php  echo $this->paginationControl($this->paginator, null, null, array(
          'pageAsQuery' => false,
          'query' => $this->formValues,
        ));     ?>
    </div>
        <!--  Display Tip If No Vats Found  -->
    <?php else:?>
        <br/>
        <div class="tip">
             <span><?php echo $this->translate("There are currently no taxes.") ?></span>
        </div>
    <?php endif;?>

    </form>
    </div>
    <style type="text/css">
 table.admin_table thead tr th {
    background-color: #E9F4FA;
    border-bottom: 1px solid #AAAAAA;
    font-weight: bold;
    padding: 7px 10px;
    white-space: nowrap;
    text-align: center;
}
table.admin_table tbody tr td {
    border-bottom: 1px solid #EEEEEE;
    font-size: 0.9em;
    padding: 7px 10px;
    vertical-align: top;
    white-space: normal;
    text-align: center;
}

</style> 
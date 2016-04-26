<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>

<div class="layout_middle">
<h3><?php echo $this->translate('Attribute Set')?></h3>
<br />
<p>
        <?php echo $this->htmlLink(array(
              'action' => 'add-attribute-set',
              'route' => 'socialstore_mystore_general'),
                                   $this->translate('Add Attribute Set'),
                                   array('class' => 'smoothbox',
                                   ));
			echo ' - '.$this->htmlLink(array(
              'action' => 'attribute-preset-list',
              'route' => 'socialstore_mystore_general'),
                                   $this->translate('Attribute Presets List'),
                                   array('class' => 'smoothbox',
                                   ));
        ?>
</p>
<br />
    <form class="global_form">
    <?php if(count($this->paginator)>0):?>
      <table class="admin_table">
        <!--  Sets Listing Table Labels   -->
        <thead>
          <tr>
                  <th><?php echo $this->translate("Name") ?></th>
                  <th><?php echo $this->translate("Options") ?></th>
          </tr>
        </thead>
        <!--  Table Contents  -->
      <tbody>
        <?php foreach($this->paginator as $item): ?>
        <tr>
          <td><?php echo $item->name         ?></td>
          <td>
            <?php echo $this->htmlLink(array(
              'action' => 'manage-attribute-set',
              'route' => 'socialstore_mystore_general',
              'set_id'        => $item -> set_id),
              $this->translate('Manage Attributes'),
              array());
            ?>
             |
            <?php echo $this->htmlLink(array(
              'action' => 'edit-attribute-set',
              'route' => 'socialstore_mystore_general',
              'set_id'        => $item -> set_id),
              $this->translate('Edit'),
              array('class' => 'smoothbox',));
            ?>
             |
            <?php echo $this->htmlLink(array(
              'action' => 'delete-attribute-set',
              'route' => 'socialstore_mystore_general',
              'set_id'  => $item -> set_id),
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
        <!--  Display Tip If No Sets Found  -->
    <?php else:?>
        <br/>
        <div class="tip">
             <span><?php echo $this->translate("There are currently no sets.") ?></span>
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
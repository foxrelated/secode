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
</style>

<h2><?php echo $this->translate("Group Buy Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate("VATs") ?></h3>
    <form class="global_form">
    <?php if(count($this->paginator)>0):?>
      <table class="admin_table">
        <!--  VATs Listing Table Labels   -->
        <thead>
          <tr>
                  <th><?php echo $this->translate("Name") ?></th>
                  <th><?php echo $this->translate("Value") ?></th>
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
          <td><?php echo $item->value."%"    ?></td>
          <td><?php echo $this->locale()->toDateTime($item->creation_date)?></td>
          <td><?php echo $this->locale()->toDateTime($item->modified_date)?></td>
          <td>
            <?php echo $this->htmlLink(array('route'       => 'admin_default',
                                                                     'module'      => 'groupbuy',
                                                                     'controller'  => 'vat',
                                                                     'action'      => 'edit-vat',
                                                                     'vat_id'        => $item -> vat_id),
                                                               $this->translate('Edit'),
                                                               array('class' => 'smoothbox',));
                                    ?>
                                    |
                                    <?php echo $this->htmlLink(array('route'       => 'admin_default',
                                                                     'module'      => 'groupbuy',
                                                                     'controller'  => 'vat',
                                                                     'action'      => 'delete-vat',
                                                                     'vat_id'        => $item -> vat_id),
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
             <span><?php echo $this->translate("There are currently no VATs.") ?></span>
        </div>
    <?php endif;?>
    <!--  Add VATs Button  -->
    <br/>
        <?php echo $this->htmlLink(array('route' => 'admin_default',
                                         'module' => 'groupbuy',
                                         'controller' => 'vat',
                                         'action' => 'add-vat'),
                                   $this->translate('Add VAT'),
                                   array('class' => 'smoothbox buttonlink',
                                         'style' => 'background-image: url(application/modules/Core/externals/images/admin/menus_addmenu.png);'
                                   )) ?>
    </form>
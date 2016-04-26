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

<h2><?php echo $this->translate("Store Plugin") ?></h2>

<!-- admin menu -->
<?php echo $this->content()->renderWidget('socialstore.admin-main-menu') ?>

<h3><?php echo $this->translate("Taxes") ?></h3>
<br />
<p>
        <?php echo $this->htmlLink(array('route' => 'admin_default',
                                         'module' => 'socialstore',
                                         'controller' => 'taxes',
                                         'action' => 'add'),
                                   $this->translate('+ Add Tax'),
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
            <?php echo $this->htmlLink(array('route'       => 'admin_default',
                                                                     'module'      => 'socialstore',
                                                                     'controller'  => 'taxes',
                                                                     'action'      => 'edit',
                                                                     'vat_id'        => $item -> vat_id),
                                                               $this->translate('Edit'),
                                                               array('class' => 'smoothbox',));
                                    ?>
                                    |
                                    <?php echo $this->htmlLink(array('route'       => 'admin_default',
                                                                     'module'      => 'socialstore',
                                                                     'controller'  => 'taxes',
                                                                     'action'      => 'delete',
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
             <span><?php echo $this->translate("There are currently no taxes.") ?></span>
        </div>
    <?php endif;?>

    </form>
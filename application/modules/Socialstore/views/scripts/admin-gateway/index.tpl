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

<h3><?php echo $this->translate("Payment Gateways") ?></h3>
<p>
  <?php echo $this->translate("STORE_VIEWS_ADMIN_GATEWAYS_INDEX_DESCRIPTION") ?>
</p>

<br />

<?php if( !empty($this->error) ): ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->error ?>
    </li>
  </ul>
  
  <br />
<?php endif; ?>


<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s gateway found", "%s gateways found", $count), $count) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
</div>

<br />


<table class='admin_table' style='width: 40%;'>
  <thead>
    <tr>
      <th style='width: 1%;'><?php echo $this->translate("ID") ?></th>
      <th><?php echo $this->translate("Title") ?></th>
      <th style='width: 1%;' class='admin_table_centered'><?php echo $this->translate("Enabled") ?></th>
      <th style='width: 1%;' class='admin_table_options'><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php if( count($this->paginator) ): ?>
      <?php foreach( $this->paginator as $item ): ?>
        <tr>
          <td>
            <?php echo $item->gateway_id ?>
          </td>
          <td class='admin_table_bold'>
            <?php echo $item->title ?>
          </td>
          <td class='admin_table_centered'>
            <?php echo ( $item->enabled ? $this->translate('Yes') : $this->translate('No') ) ?>
          </td>
          <td class='admin_table_options'>
            <a href='<?php echo $this->url(array('action' => 'edit', 'gateway_id' => $item->gateway_id));?>'>
              <?php echo $this->translate("edit") ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
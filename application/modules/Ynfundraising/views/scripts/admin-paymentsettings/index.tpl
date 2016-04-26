<?php
    echo $this->partial('_menu_admin.tpl', array('tab_select' => 'ynfundraising_admin_main_paymentsettings'));
?>
<h2>
  <?php echo $this->translate("Manage Payment Gateways") ?>
</h2>

<p>
  <?php echo $this->translate("YNFUNDRAISING_ADMIN_GATEWAYS_DESCRIPTION") ?>
</p>

<br />
<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array('%s gateway found', '%s gateways found', $count), $this->locale()->toNumber($count)) ?>
  </div>
</div>

<?php if (count($this->paginator)): ?>
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
            <?php echo $item->gateway_name ?>
          </td>
          <td class='admin_table_centered'>
            <?php echo ( $item->is_active  ? $this->translate('Yes') : $this->translate('No') ) ?>
          </td>
          <td class='admin_table_options'>
            <?php echo $this->htmlLink(array(
                    'route' => 'admin_default',
                    'module'      => 'ynfundraising',
                    'controller' => 'paymentsettings',
                    'action' => 'edit',
                    'gateway_id' => $item->gateway_id),
                    $this->translate("Edit"),
                    array('class' => 'smoothbox'))
            ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
<?php endif; ?>
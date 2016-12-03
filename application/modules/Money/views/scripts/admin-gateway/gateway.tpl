<?php
?>
<h2>
  <?php echo $this->translate("E-money") ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<?php if($this->paginator->getTotalItemCount()>0):?>
<table class="admin_table">
    <thead>
        <th><?php echo $this->translate('Title')?></th>
        <th><?php echo $this->translate('Enable')?></th>
        <th><?php echo $this->translate('Options')?></th>
    </thead>    
    <?php foreach($this->paginator as $item):?>
    <tr>
        <td><?php echo $item->title?></td>
        <td><?php echo ( $item->enabled ? $this->translate('Yes') : $this->translate('No') )  ?></td>
        <td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'money', 'controller' => 'gateway', 'action' => 'edit', 'gateway_id' => $item->gateway_id), $this->translate('edit'))?></td>
    </tr>    
    <?php endforeach;?>
</table>    
<?php else:?>
<?php endif; ?>

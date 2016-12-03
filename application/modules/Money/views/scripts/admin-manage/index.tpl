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
<?php if($this->paginator->getTotalItemCount() > 0):?>
<table class="admin_table">
    <thead>
        <th><?php echo $this->translate('User ID')?></th>
        <th><?php echo $this->translate('Display Name')?></th>
        <th><?php echo $this->translate('Money')?></th>
        <th><?php echo $this->translate('Options')?></th>
    </thead>    
    <?php foreach($this->paginator as $item):?>
        <tr>
            <td><?php echo $item->user_id?></td>
            <td><?php echo $item->displayname?></td>
            <td><?php echo $item->money?></td>
            <td><?php echo $this->htmlLink(array('route'=>'admin_default', 'module'=>'money', 'controller'=>'manage', 'action'=>'edit', 'user_id'=>$item->user_id), $this->translate('edit')) ?></td>
        </tr>    
    <?php endforeach;?>
</table>    
<?php endif; ?>
<br>
<?php echo $this->paginationControl($this->paginator, null, null, null);?>



<h2><?php echo $this->translate("Group Buy Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
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
<h3><?php echo $this->translate("Deal Currencies") ?></h3>
<?php if(count($this->paginator)>0):?>
<div>
	<table class="admin_table">
		<thead>
			<tr>
              <th><?php echo $this->translate("Code") ?></th>
              <th><?php echo $this->translate("Name") ?></th>
              <th><?php echo $this->translate("Symbol") ?></th>
              <th><?php echo $this->translate("Status") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
			</tr>
		</thead>
	<tbody>
		<?php foreach($this->paginator as $item): ?>
		<tr>
			<td><?php echo $item->code?></td>
			<td><?php echo $item->name?></td>
			<td><?php echo $item->symbol?></td>
			<td><?php echo $this->translate($item->status)?></td>
			<td>
				<?php echo $this->htmlLink(array('route'       => 'admin_default',
                                                                 'module'      => 'groupbuy',
                                                                 'controller'  => 'currency',
                                                                 'action'      => 'edit-currency',
                                                                 'code_id'        => $item -> code),
                                                           $this->translate('Edit'),
                                                           array('class' => 'smoothbox',))
                                ?>
			</td>
		</tr>
		<?php endforeach; ?>	    			
	</tbody>
</table>
</div>
<br/>
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
</div>
<?php else:?>
<br/>
<div class="tip">
     <span><?php echo $this->translate("There are currently no currencies.") ?></span>
</div>
<?php endif;?>
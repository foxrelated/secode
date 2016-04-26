<h2><?php echo $this->translate("Store Plugin") ?></h2>
<!-- admin menu -->
<?php echo $this->content()->renderWidget('socialstore.admin-main-menu') ?>
<h3>
	<?php echo $this->translate("Help Pages") ?>
</h3>
<br/>
<div>
	<a href="<?php echo $this->url(array('action'=>'create')) ?>">+ Add Help Page</a>
</div>
<br />
<table class="admin_table">
	<thead>
		<tr>
			<th><?php echo $this->translate("Title") ?></th>
		
			<th><?php echo $this->translate("Status") ?></th>
			
			<th style = "text-align: right;"><?php echo $this->translate("Order") ?></th>
		
			<th><?php echo $this->translate("Created") ?></th>
		
			<th><?php echo $this->translate("Options") ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($this->paginator as $item) :?>
		<tr>
			
			<td>
				<?php echo $item->title ?>
			</td>
			<td>
				<?php echo $this->translate(ucfirst($item->status)) ?>
			</td>
			<td style = "text-align: right;">
				<?php echo $item->ordering ?>
			</td>
			<td>
				<?php echo $item->creation_date ?>
			</td>
			<td>
				<a href="<?php echo $this->url(array('action'=>'delete','id'=>$item->getIdentity())) ?>" class="smoothbox">
					<?php echo $this->translate("Delete") ?>
				</a> |
				<a href="<?php echo $this->url(array('action'=>'edit','id'=>$item->getIdentity())) ?>">
					<?php echo $this->translate("Edit") ?>
				</a>
			</td>
			
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<br/>
            <!-- Page Paginator -->
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
</div>

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
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>
<div class='layout_middle'>
    <form class="global_form">
      <div>      	

        <br />
        <p>
        <?php echo $this->htmlLink(array('route' => 'socialstore_mystore_general', 'action' => 'add-custom-cat','pid'=>0), $this->translate('Add Root Category'), array(
          'class' => 'smoothbox',
          )) ?>        
          <?php if(is_object($this->category)): ?>
       | <?php echo $this->htmlLink(array('route' => 'socialstore_mystore_general', 'action' => 'add-custom-cat','pid'=>$this->category->getIdentity()), $this->translate('Add Category'), array(
          'class' => 'smoothbox',
	)) ?>
          <?php endif; ?>
        </p>
        <?php if(is_object($this->category)): ?>
        <div style = "margin-top: 20px" >
         <?php  
		echo $this->htmlLink(array('route' => 'socialstore_mystore_general', 'action' => 'custom-categories'), $this->translate('Home'), array()); ?> 
        &raquo; 
	<?php
	foreach($this->category->getAscendant() as $node): ?>
        		<?php 
        		echo $this->htmlLink(array('route' => 'socialstore_mystore_general', 'action' => 'custom-categories', 'pid' =>$node->customcategory_id), $node->name, array()) ?>
        		&raquo;
         <?php endforeach ; ?><strong><?php echo $this->category->name ?></strong>
       </div>
       <br />
         <?php
		endif; 
         ?>

        
          <?php if(count($this->categories)>0):?>
         <table class='admin_table' style = "width: 100%;">
          <thead>

            <tr>
              <th style = "text-align: left;"><?php echo $this->translate("Category Name") ?></th>
              <th style = "text-align: right;"><?php echo $this->translate("Sub-Categories") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->categories as $category): ?>
              <tr>
                <td style = "text-align: left;"><?php echo $this->translate($category->getTitle()); ?></td>
                <td style = "text-align: right;"><?php 
                echo (count($category->getDescendantIds()));?></td>
                <td>
                  
                  <?php echo $this->htmlLink(array('route' => 'socialstore_mystore_general', 'action' => 'custom-cat-edit', 'id' =>$category->customcategory_id), $this->translate('edit'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'socialstore_mystore_general', 'action' => 'custom-cat-delete', 'id' =>$category->getIdentity()), $this->translate('delete'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'socialstore_mystore_general', 'action' => 'add-custom-cat', 'pid' =>$category->getIdentity()), $this->translate('add sub-category'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'socialstore_mystore_general', 'action' => 'custom-categories', 'pid' =>$category->getIdentity()), $this->translate('view sub-categories'), array(
                    
                  )) ?>

                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no categories. Click here to <a href='%s' class='smoothbox'>post</a> a new one",
	  $this->url(array('action'=>'add-custom-cat','pid'=>$this->pid))) ?></span>
      </div>
      <?php endif;?>
      
    <br />  
	<?php echo $this->paginationControl($this->categories, null, null, array(
    'pageAsQuery' => true,
    //'query' => '',
    //'params' => $this->formValues,
  )); ?>
    </div>
    </form>

</div>
<style type="text/css">
table.admin_table thead tr th {
    background-color: #E9F4FA;
    border-bottom: 1px solid #AAAAAA;
    font-weight: bold;
    padding: 7px 10px;
    white-space: nowrap;
}
table.admin_table tbody tr td {
    border-bottom: 1px solid #EEEEEE;
    font-size: 0.9em;
    padding: 7px 10px;
    vertical-align: top;
    white-space: normal;
}

</style>   
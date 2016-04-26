<h2><?php echo $this->translate("Store Plugin") ?></h2>

<!-- admin menu -->
<?php echo $this->content()->renderWidget('socialstore.admin-main-menu') ?>


    <form class="global_form">
      <div>      	
        <h3><?php echo $this->translate("Manage Locations") ?></h3>
        <br />
        <p>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'socialstore', 'controller' => 'location', 'action' => 'create','pid'=>0), $this->translate('+ Add Root Location'), array(
          'class' => 'smoothbox',
          )) ?>   
          <?php if(is_object($this->location)): ?>
        | <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'socialstore', 'controller' => 'location', 'action' => 'create','pid'=>$this->location->getIdentity()), $this->translate('+ Add Location'), array(
          'class' => 'smoothbox ',
          )) ?>
          <?php endif; ?>     
        </p>
        <div style = "margin-top: 20px" >
        <?php if(is_object($this->location)): ?>
         <?php  
		echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'socialstore', 'controller' => 'location', 'action' => 'index'), $this->translate('Home'), array()); ?> 
        &raquo; 
	<?php foreach($this->location->getAscendant() as $node): ?>
        		<?php 
        		echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'socialstore', 'controller' => 'location', 'action' => 'index', 'pid' =>$node->getId()), $node->getTitle(), array()) ?>
        		&raquo;
         <?php endforeach; ?><strong><?php echo $this->location->name ?></strong>
 
         <?php
		endif; 
         ?>
        </div>
        <br />
          <?php if(count($this->locations)>0):?>
         <table class='admin_table'>
          <thead>

            <tr>
              <th style = "text-align: left;"><?php echo $this->translate("Location Name") ?></th>
              <th style = "text-align: right;"><?php echo $this->translate("Sub-Locations") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->locations as $location): ?>
              <tr>
                <td style = "text-align: left;"><?php echo $this->translate($location->getTitle()) ?></td>
                <td style = "text-align: right;"><?php echo (count($location->getDescendantIds()) - 1);?></td>
                
                <td>
                  
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'socialstore', 'controller' => 'location', 'action' => 'edit', 'id' =>$location->location_id), $this->translate('edit'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'socialstore', 'controller' => 'location', 'action' => 'delete', 'id' =>$location->getIdentity()), $this->translate('delete'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'socialstore', 'controller' => 'location', 'action' => 'create', 'pid' =>$location->getIdentity()), $this->translate('add sub-location'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'socialstore', 'controller' => 'location', 'action' => 'index', 'pid' =>$location->getIdentity()), $this->translate('view sub-location'), array(
                    
                  )) ?>

                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no locations. Click here to <a href='%s' class='smoothbox'>post</a> a new one",
	  $this->url(array('action'=>'create','pid'=>$this->pid))) ?></span>
      </div>
      <?php endif;?>
     <br />  
      <?php echo $this->paginationControl($this->locations, null, null, array(
    'pageAsQuery' => true,
    //'query' => '',
    //'params' => $this->formValues,
  )); ?>
    </div>
    </form>
     
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
table.admin_table tbody tr td {
	text-align: center;
}

table.admin_table thead tr th {
	text-align: center;
}


</style>   
<h2><?php echo $this->translate("YouNet Responsive Metro Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<img width="100%" src="<?php echo $this->baseUrl();?>/application/modules/Ynresponsivemetro/externals/images/metro_blocks_default.png">
<br />
<div class="table_scroll" style="padding-top: 15px;">
    <table class='admin_table' style="width: 100%">
        <thead>
            <tr>
                <th>
                    <?php echo $this->translate("Block Name") ?>
                </th>
                <th>
                     <?php echo $this->translate("Title") ?>
                </th>
                 <th>
                     <?php echo $this->translate("Description") ?>
                </th>
                <th>
                     <?php echo $this->translate("Icon") ?>
                </th>
                <th>
                     <?php echo $this->translate("Photo") ?>
                </th>
                <th>
                     <?php echo $this->translate("Options") ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
            	<?php
            	$metroblock = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> getBlocks(array('block' => 1)); 
            	?>
                <td><?php echo $this -> translate("Video Block") ?></td>
                <?php if($metroblock):?>
                	<td>
                		<?php echo $metroblock -> title;?>
	                </td>
	                <td><?php echo $this -> string() -> truncate($metroblock->description, 100); ?></td>
                    <td>
                		<?php if($metroblock -> icon):?>
                			<img width="32" height="32" src="<?php echo $metroblock -> icon;?>" />
                		<?php endif;?>
                    </td>
                    <td>
                		<?php $image_url = $metroblock -> getPhotoUrl("thumb.normal", 1);?>
                		<img width="100px" src="<?php echo $image_url;?>" />
                    </td>
                <?php else:?>
                	<td></td><td></td><td></td><td></td>
                <?php endif;?>
                <td>
                    <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-blocks', 'action' => 'edit', 'block' => 1), $this->translate('edit'), array('class' => 'smoothbox'));
                    ?>
                    |
                    <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-blocks', 'action' => 'delete', 'block' => 1), $this->translate('clear'), array('class' => 'smoothbox'));
                    ?>
                </td>
            </tr>
            <tr>
            	<?php
            	$metroblock = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> getBlocks(array('block' => 2)); 
            	?>
                <td><?php echo $this -> translate("Event Block") ?></td>
                <?php if($metroblock):?>
                	<td>
                		<?php echo $metroblock -> title;?>
	                </td>
	                <td><?php echo $this -> string() -> truncate($metroblock->description, 100); ?></td>
                    <td>
                		<?php if($metroblock -> icon):?>
                			<img width="32" height="32" src="<?php echo $metroblock -> icon;?>" />
                		<?php endif;?>
                    </td>
                    <td>
                		<?php $image_url = $metroblock -> getPhotoUrl("thumb.normal", 2);?>
                		<img width="100px" src="<?php echo $image_url;?>" />
                    </td>
                <?php else:?>
                	<td></td><td></td><td></td><td></td>
                <?php endif;?>
                <td>
                   <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-blocks', 'action' => 'edit', 'block' => 2), $this->translate('edit'), array('class' => 'smoothbox'));
                    ?>
                    |
                    <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-blocks', 'action' => 'delete', 'block' => 2), $this->translate('clear'), array('class' => 'smoothbox'));
                    ?>
                </td>
            </tr>
            <tr>
            	<?php
            	$metroblocks = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> getBlocks(array('block' => 3, 'limit' => 5)); 
            	?>
                <td><?php echo $this -> translate("Photos Block") ?></td>
                <td></td>
	            <td></td>
                <td></td>
                <td>
                	<ul class="metro_block_photos">
	                <?php foreach($metroblocks as $metroblock):?>
	                	<li>
	                		<?php $image_url = $metroblock -> getPhotoUrl("thumb.normal", 3);?>
	                		<img width="100px" src="<?php echo $image_url;?>" />
	                		<?php
		                    	echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-blocks', 'action' => 'delete-photo', 'block_id' => $metroblock -> getIdentity()), '', array('class' => 'smoothbox photo-delete'));
		                    ?>
	                    </li>
	                <?php endforeach;?>
                	</ul>
                </td>
                <td>
                   <?php if(count($metroblocks) < 5):
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-blocks', 'action' => 'add-photo', 'block' => 3), $this->translate('add photo'), array('class' => 'smoothbox'));
                    ?>
                    |
                    <?php
					endif;
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-blocks', 'action' => 'delete-photos', 'block' => 3), $this->translate('clear'), array('class' => 'smoothbox'));
                    ?>
                </td>
            </tr>
            <tr>
            	<?php
            	$metroblock = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> getBlocks(array('block' => 4)); 
            	?>
                <td><?php echo $this -> translate("Group Block") ?></td>
                <?php if($metroblock):?>
                	<td>
                		<?php echo $metroblock -> title;?>
	                </td>
	                <td><?php echo $this -> string() -> truncate($metroblock->description, 100); ?></td>
                    <td>
                		<?php if($metroblock -> icon):?>
                			<img width="32" height="32" src="<?php echo $metroblock -> icon;?>" />
                		<?php endif;?>
                    </td>
                    <td>
                		<?php $image_url = $metroblock -> getPhotoUrl("thumb.normal", 4);?>
                		<img width="100px" src="<?php echo $image_url;?>" />
                    </td>
                <?php else:?>
                	<td></td><td></td><td></td><td></td>
                <?php endif;?>
                <td>
                   <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-blocks', 'action' => 'edit', 'block' => 4), $this->translate('edit'), array('class' => 'smoothbox'));
                    ?>
                    |
                    <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-blocks', 'action' => 'delete', 'block' => 4), $this->translate('clear'), array('class' => 'smoothbox'));
                    ?>
                </td>
            </tr>
            <tr>
            	<?php
            	$metroblock = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> getBlocks(array('block' => 5)); 
            	?>
                <td><?php echo $this -> translate("Other Block 1") ?></td>
                <?php if($metroblock):?>
                	<td>
                		<?php echo $metroblock -> title;?>
	                </td>
	                <td><?php echo $this -> string() -> truncate($metroblock->description, 100); ?></td>
                    <td>
                		<?php if($metroblock -> icon):?>
                			<img width="32" height="32" src="<?php echo $metroblock -> icon;?>" />
                		<?php endif;?>
                    </td>
                    <td>
                		<?php $image_url = $metroblock -> getPhotoUrl("thumb.normal", 5);?>
                		<img width="100px" src="<?php echo $image_url;?>" />
                    </td>
                <?php else:?>
                	<td></td><td></td><td></td><td></td>
                <?php endif;?>
                <td>
                    <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-blocks', 'action' => 'edit', 'block' => 5), $this->translate('edit'), array('class' => 'smoothbox'));
                    ?>
                    |
                    <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-blocks', 'action' => 'delete', 'block' => 5), $this->translate('clear'), array('class' => 'smoothbox'));
                    ?>
                </td>
            </tr>
            <tr>
            	<?php
            	$metroblock = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> getBlocks(array('block' => 6)); 
            	?>
                <td><?php echo $this -> translate("Other Block 2") ?></td>
               <?php if($metroblock):?>
                	<td>
                		<?php echo $metroblock -> title;?>
	                </td>
	                <td><?php echo $this -> string() -> truncate($metroblock->description, 100); ?></td>
                    <td>
                		<?php if($metroblock -> icon):?>
                			<img width="32" height="32" src="<?php echo $metroblock -> icon;?>" />
                		<?php endif;?>
                    </td>
                    <td>
                		<?php $image_url = $metroblock -> getPhotoUrl("thumb.normal", 6);?>
                		<img width="100px" src="<?php echo $image_url;?>" />
                    </td>
                <?php else:?>
                	<td></td><td></td><td></td><td></td>
                <?php endif;?>
                <td>
                   <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-blocks', 'action' => 'edit', 'block' => 6), $this->translate('edit'), array('class' => 'smoothbox'));
                    ?>
                    |
                    <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-blocks', 'action' => 'delete', 'block' => 6), $this->translate('clear'), array('class' => 'smoothbox'));
                    ?>
                </td>
            </tr>
            <tr>
            	<?php
            	$metroblock = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> getBlocks(array('block' => 7)); 
            	?>
                <td><?php echo $this -> translate("Other Block 3") ?></td>
                <?php if($metroblock):?>
                	<td>
                		<?php echo $metroblock -> title;?>
	                </td>
	                <td><?php echo $this -> string() -> truncate($metroblock->description, 100); ?></td>
                    <td>
                		<?php if($metroblock -> icon):?>
                			<img width="32" height="32" src="<?php echo $metroblock -> icon;?>" />
                		<?php endif;?>
                    </td>
                    <td>
                		<?php $image_url = $metroblock -> getPhotoUrl("thumb.normal", 7);?>
                		<img width="100px" src="<?php echo $image_url;?>" />
                    </td>
                <?php else:?>
                	<td></td><td></td><td></td><td></td>
                <?php endif;?>
                <td>
                   <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-blocks', 'action' => 'edit', 'block' => 7), $this->translate('edit'), array('class' => 'smoothbox'));
                    ?>
                    |
                    <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-blocks', 'action' => 'delete', 'block' => 7), $this->translate('clear'), array('class' => 'smoothbox'));
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>

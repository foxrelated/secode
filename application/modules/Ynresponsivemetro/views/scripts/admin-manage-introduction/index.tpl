<h2><?php echo $this->translate("YouNet Responsive Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<img width="100%" src="<?php echo $this->baseUrl();?>/application/modules/Ynresponsivemetro/externals/images/introduction_default.png">
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
                     <?php echo $this->translate("Content") ?>
                </th>
                <th>
                     <?php echo $this->translate("Logo") ?>
                </th>
                <th>
                     <?php echo $this->translate("Background Color") ?>
                </th>
                <th>
                     <?php echo $this->translate("Options") ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
            	<?php
            	$metroblock = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> getBlocks(array('block' => 9)); 
            	?>
                <td><?php echo $this -> translate("Block 1") ?></td>
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
                		<span style="width: 30px; display: block; height: 30px; background-color: #<?php echo $metroblock -> link?>"></span>
                    </td>
                <?php else:?>
                	<td></td><td></td><td></td><td></td>
                <?php endif;?>
                <td>
                    <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-introduction', 'action' => 'edit', 'block' => 9), $this->translate('edit'), array('class' => 'smoothbox'));
                    ?>
                    |
                    <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-introduction', 'action' => 'delete', 'block' => 9), $this->translate('clear'), array('class' => 'smoothbox'));
                    ?>
                </td>
            </tr>
            <tr>
            	<?php
            	$metroblock = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> getBlocks(array('block' => 10)); 
            	?>
                <td><?php echo $this -> translate("Block 2") ?></td>
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
                		<span style="width: 30px; display: block; height: 30px; background-color: #<?php echo $metroblock -> link?>"></span>
                    </td>
                <?php else:?>
                	<td></td><td></td><td></td><td></td>
                <?php endif;?>
                <td>
                   <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-introduction', 'action' => 'edit', 'block' => 10), $this->translate('edit'), array('class' => 'smoothbox'));
                    ?>
                    |
                    <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-introduction', 'action' => 'delete', 'block' => 10), $this->translate('clear'), array('class' => 'smoothbox'));
                    ?>
                </td>
            </tr>
            <tr>
            	<?php
            	$metroblock = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> getBlocks(array('block' => 11)); 
            	?>
                <td><?php echo $this -> translate("Block 3") ?></td>
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
                		<span style="width: 30px; display: block; height: 30px; background-color: #<?php echo $metroblock -> link?>"></span>
                    </td>
                <?php else:?>
                	<td></td><td></td><td></td><td></td>
                <?php endif;?>
                <td>
                   <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-introduction', 'action' => 'edit', 'block' => 11), $this->translate('edit'), array('class' => 'smoothbox'));
                    ?>
                    |
                    <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-introduction', 'action' => 'delete', 'block' => 11), $this->translate('clear'), array('class' => 'smoothbox'));
                    ?>
                </td>
            </tr>
            <tr>
            	<?php
            	$metroblock = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> getBlocks(array('block' => 12)); 
            	?>
                <td><?php echo $this -> translate("Block 4") ?></td>
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
                		<span style="width: 30px; display: block; height: 30px; background-color: #<?php echo $metroblock -> link?>"></span>
                    </td>
                <?php else:?>
                	<td></td><td></td><td></td><td></td>
                <?php endif;?>
                <td>
                    <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-introduction', 'action' => 'edit', 'block' => 12), $this->translate('edit'), array('class' => 'smoothbox'));
                    ?>
                    |
                    <?php
                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-introduction', 'action' => 'delete', 'block' => 12), $this->translate('clear'), array('class' => 'smoothbox'));
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>

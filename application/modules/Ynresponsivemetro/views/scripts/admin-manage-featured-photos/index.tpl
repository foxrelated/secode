<script type="text/javascript">
    function multiDelete()
    {
        return confirm("<?php echo $this->translate('Are you sure you want to delete the selected photos?'); ?>");
    }

    en4.core.runonce.add(function(){
		$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ 
			$$('td.checksub input[type=checkbox]').each(function(i){
	 			i.checked = $$('th.admin_table_short input[type=checkbox]')[0].checked;
			});
		});
		$$('td.checksub input[type=checkbox]').addEvent('click', function(){
			var checks = $$('td.checksub input[type=checkbox]');
			var flag = true;
			for (i = 0; i < checks.length; i++) {
				if (checks[i].checked == false) {
					flag = false;
				}
			}
			if (flag) {
				$$('th.admin_table_short input[type=checkbox]')[0].checked = true;
			}
			else {
				$$('th.admin_table_short input[type=checkbox]')[0].checked = false;
			}
		});
	});
</script>
<h2><?php echo $this->translate("YouNet Responsive Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<p style="padding-bottom: 10px">
	<?php echo $this -> translate("If the site already has Photos/ Advanced Photos and admin adds more photos for this widget, system shows photos only.")?>
</p>
<div style="padding: 5px">  
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-featured-photos', 'action' => 'create'), $this->translate('Add New Photo'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'background-image: url(application/modules/Ynresponsive1/externals/images/add.png);')) ?>
</div>
<br />
<?php if (count($this->paginator)): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete()">
        <div class="table_scroll">
            <table class='admin_table'>
                <thead>
                    <tr>
                        <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
                        <th>
                            <?php echo $this->translate("Title") ?>
                        </th>
                        <th>
                            <?php echo $this->translate("Description") ?>
                        </th>
                        <th style="width: 40%">
                             <?php echo $this->translate("Photo") ?>
                        </th>
                        <th style="width: 10%">
                             <?php echo $this->translate("Options") ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->paginator as $item):?>
                        <tr>
                            <td class="checksub"><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value='<?php echo $item -> getIdentity(); ?>' /></td>
                            <td><?php echo $item->title ?></td>
                            <td><?php echo $this -> string() -> truncate($item->description, 100); ?></td>
                            <td>
                        		<?php $image_url = $item -> getPhotoUrl("thumb.normal", 8);?>
                        		<img width="100px" src="<?php echo $image_url;?>" />
                            </td>
                            <td>
                                <?php
                                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-featured-photos', 'action' => 'edit', 'id' => $item -> getIdentity()), $this->translate('edit'), array('class' => 'smoothbox'));
                                ?>
                                |
                                <?php
                                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsivemetro', 'controller' => 'manage-featured-photos', 'action' => 'delete', 'id' => $item -> getIdentity()), $this->translate('delete'), array('class' => 'smoothbox'));
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <br />

        <div class='buttons'>
            <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
        </div>
    </form>

    <br />

    <div>
        <?php echo $this->paginationControl($this->paginator); ?>
    </div>

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no photos posted yet.") ?>
        </span>
    </div>
<?php endif; ?>

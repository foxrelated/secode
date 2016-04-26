<h2>
    <?php echo $this->translate('Younet Mobile Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class="tabs">
    <?php
    	echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<p>
    <a href="<?php echo $this->url(array('action' => 'create')) ?>"><strong>Add Themes</strong></a>
    <br />
</p>

<div class="clear">
    <table class="admin_table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Build Number</th>
            <th>Publish</th>
            <th>Last Modify</th>
            <th>Options</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($this->paging as $item): ?>
        <tr>
            <td><?php echo $item->theme_id; ?></td>
            <td><?php echo $item->name; ?></td>
            <td><?php echo $item->build_number; ?></td>
            <td><?php echo $item->is_publish ?'Yes': 'No'; ?></td>
            <td><?php echo $item->modified_date; ?></td>
            <td>
                <?php if(!$item->is_publish): ?>

                <?php echo $this->htmlLink(array('reset' => false, 'action' => 'edit', 'id' => $item->theme_id), $this->translate('edit'), array('class' => '')) ?>
                |
                <?php echo $this->htmlLink(array('reset' => false, 'action' => 'delete', 'id' => $item->theme_id), $this->translate('delete'), array('class' => 'smoothbox')) ?>
                |
                <?php echo $this->htmlLink(array('reset' => false, 'action' => 'publish', 'id' => $item->theme_id), $this->translate('publish'), array('class' => 'smoothbox')) ?>
                <?php else: ?>
                <?php echo $this->htmlLink(array('reset' => false, 'action' => 'edit', 'id' => $item->theme_id), $this->translate('edit'), array('class' => '')) ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>




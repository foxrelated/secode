<h2>
    <?php echo $this->translate('Social Slider plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class="tabs">
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<div class='clear'>
    <div class='settings'>
        <form class="global_form">
            <div>
                <h3><?php echo $this->translate("Social Buttons") ?></h3>
                
                <?php if (count($this->buttons) > 0): ?>

                    <table class='admin_table'>
                        <thead>

                            <tr>
                                <th><?php echo $this->translate("Button's Name") ?></th>
                                <th class="admin_table_options"><?php echo $this->translate('Options'); ?></th>
                            </tr>

                        </thead>
                        <tbody>
                            <?php foreach ($this->buttons as $button): ?>
                                <tr>
                                    <td><?php echo $button->button_name ?></td>
                                    <td class="admin_table_options">

                                        <?php $action_name = ($button->button_show == 0) ? 'enable' : 'disable'; ?>
                                        <?php $edit = ($button->button_default == 1  ) ? 'sedit' : 'edit'; ?>
                                        
                                        <?php if($button->button_default == 1 && empty($button->button_code) && $action_name!='disable'){ $action = 'sedit'; $skey = 'sn'; $svalue = $button->button_type; } else { $action = $action_name ; $skey= 'id' ; $svalue = $button->button_id; } ?>
                                        
                                        <?php if($edit=='edit'){ $key = 'id'; $value = $button->button_id ;  } else{ $key = 'sn'; $value = $button->button_type; } ?>


                                        <a class="smoothbox" href="<?php echo $this->url(array('action' => $action, $skey => $svalue)); ?>"> 
                                            <?php echo $this->translate($action_name); ?>
                                        </a>
                                        |
                                        <a class="smoothbox"href="<?php echo $this->url(array('action' => $edit, $key => $value)); ?>" >
                                            <?php echo $this->translate('edit'); ?>
                                        </a>
                                        <?php if ($button->button_default != 1): ?>
                                            |
                                            <a class="smoothbox" href="<?php echo $this->url(array('action' => 'delete', 'id' => $button->button_id)); ?>">
                                                <?php echo $this->translate('delete'); ?> 
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                <?php else: ?>
                    <br/>
                    <div class="tip">
                        <span><?php echo $this->translate("There are currently no Buttons.") ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
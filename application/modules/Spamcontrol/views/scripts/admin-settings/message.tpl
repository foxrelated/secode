<?php ?>
<script type="text/javascript">
  
    function selectAll()
    {
        var i;
        var multimodify_form = $('message_form');
        var inputs = multimodify_form.elements;
        for (i = 1; i < inputs.length - 1; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }

</script>

<h2>Spam Control</h2>
<div class="tabs">
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>	
</div>
<div>
    <div class="admin_search" style="margin-bottom: 10px">
        <?php echo $this->form ?>
    </div>
    <?php if (count($this->paginator) > 0): ?>
        <div style="margin-top: 10px; margin-bottom: 20px">
            <form id='message_form' method="post" action="<?php echo $this->url(array('action' => 'multi-modify')); ?>">
                <table class="admin_table">
                    <thead>
                        <tr>
                            <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
                            <th><?php echo $this->translate('Modified Date'); ?></th>
                            <th><?php echo $this->translate('Conversation'); ?></th>

                        <tr>    
                    </thead>    
                    <?php foreach ($this->paginator as $message): ?>
                        <tr>
                            <td>
                                <input name='message_<?php echo $message->conversation_id ?>' value='<?php echo $message->conversation_id; ?>' type='checkbox' class='checkbox'>
                            </td>    
                            <td>
                                <?php echo $this->locale()->toDate($message->modified) ?>
                            </td>


                            <td>
                                <?php if ('' != ($title = trim($message->getTitle()))): ?>
                                   
                                <?php else: ?>
               
                                        <?php $title = $this->translate('(No Subject)') ?>
                                
                                <?php endif; ?>
                                <?php echo $this->htmlLink($this->url(array('action' => 'message-view', 'id' => $message->getIdentity())), $title) ?>
                            </td>

                        </tr>    
                    <?php endforeach; ?>
                </table>
                <br />
                <div class='buttons'>

                    <button type='submit' name="submit_button" onClick="return confirm('<?php echo $this->translate("Are you sure you want to delete the selected message?") ?>')" value="delete"><?php echo $this->translate("Delete Selected") ?></button>
                </div>   
            </form>    
        </div>
    <?php else: ?><br />
        <div class="tip">
            <span><?php echo $this->translate('No Messages yet.') ?></span>
        </div>
    <?php endif; ?>

    <?php if (count($this->paginator) > 1): ?>
        <div>  
            <?php echo $this->paginationControl($this->paginator); ?>
        </div>
    <?php endif; ?>
</div>


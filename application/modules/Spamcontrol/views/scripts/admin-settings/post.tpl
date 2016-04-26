<?php ?>
<script type="text/javascript">
  
    function selectAll()
    {
        var i;
        var multimodify_form = $('post_form');
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
    <div class="admin_search">
        <?php echo $this->form ?>
    </div>
    <?php if (count($this->paginator) > 0): ?>
        <div style="margin-top: 10px;">
            <form id='post_form' method="post" action="<?php echo $this->url(array('action' => 'comment-modify')); ?>">
                <table class="admin_table">
                    <thead>
                        <tr>
                            <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
                            <th><?php echo $this->translate('Date'); ?></th>
                            <th><?php echo $this->translate('User'); ?></th>
                            <th><?php echo $this->translate('Warn'); ?></th>
                            <th><?php echo $this->translate('Body'); ?></th>
                            <th><?php echo $this->translate('Comments'); ?></th>
                            <th><?php echo $this->translate('Type') ?></th>
                            <th><?php echo $this->translate('Options'); ?></th>
                        <tr>    
                    </thead>    
                    <?php foreach ($this->paginator as $post): ?>
                        <?php $user = $this->item('user', $post->subject_id); ?>
                        <?php
                        ?>
                        <tr>
                            <td>
                                <input name='item_<?php echo $post->action_id ?>' value='<?php echo $post->action_id; ?>' type='checkbox' class='checkbox'>
                                <input name="type" type="hidden" value="<?php echo $post->getType()?>">
                            </td>    
                            <td>
                                <?php echo $this->timestamp($post->date) ?><?php //echo $this->locale()->toDate($post->date) ?>
                            </td>
                            <td>
                                <?php
                                echo $this->htmlLink($user->getHref(), $this->string()->truncate($user->getTitle(), 10), array('target' => '_blank'))
                                ?>
                            </td>
                            <td>
                                <?php
                                for ($i = 1; $i <= $post->count; $i++) {
                                    echo $this->htmlImage($this->baseUrl() . '/application/modules/Spamcontrol/externals/images/spam.png');
                                }
                                ?>

                            </td>
                            <td style="max-width: 300px; overflow: hidden">
        <?php echo $this->string()->truncate($this->string()->stripTags($post->body), 200) ?>
                            </td>
                            <td>

                                <?php
                                if ($post->comments()->getCommentCount() > 0) {
                                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'spamcontrol', 'controller' => 'settings', 'action' => 'postcomment', 'action_id' => $post->action_id), $this->translate(array('View all %s comment', 'View all %s comments', $post->comments()->getCommentCount()), $this->locale()->toNumber($post->comments()->getCommentCount())));
                                } else {
                                    echo $this->translate('no comments');
                                }
                                ?>
                            </td>    
                            <td>
        <?php echo $post->type ?>
                            </td>    
                            <td>

                                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'spamcontrol', 'controller' => 'settings', 'action' => 'messagewarn', 'item_id' => $post->getIdentity(), 'item_type' => $post->getType()), $this->translate('Take Action'), array('class' => 'smoothbox')) ?>
                                |
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'manage', 'action' => 'delete', 'id' => $post->getOwner()->getIdentity()), $this->translate('Delete User'), array('class' => 'smoothbox')) ?>


                            </td>    
                        </tr>    
    <?php endforeach; ?>
                </table>
                <br />
                <div class='buttons'>

                    <button type='submit' name="submit_button" onClick="return confirm('<?php echo $this->translate("Are you sure you want to delete the selected message?")?>')" value="delete"><?php echo $this->translate("Delete Selected") ?></button>
                </div>   
            </form>    
        </div>
<?php else: ?><br />
        <div class="tip">
            <span><?php echo $this->translate('No posts found.') ?></span>
        </div>    
    <?php endif; ?>

        <?php if (count($this->paginator) > 1): ?>
        <div style="margin-top: 10px;">  
        <?php echo $this->paginationControl($this->paginator); ?>
        </div>
<?php endif; ?>
</div>    

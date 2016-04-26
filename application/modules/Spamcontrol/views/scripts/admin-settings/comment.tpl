<?php

?>
<script type="text/javascript">
  
function selectAll()
{
  var i;
  var multimodify_form = $('comment_form');
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
    <?php if(count($this->paginator)>0):?>
   <div style="margin-top: 10px; margin-bottom: 15px;">
       <form id='comment_form' method="post" action="<?php echo $this->url(array('action'=>'comment-modify'));?>">
           <table class="admin_table">
               <thead>
                   <tr>
                       <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
                       <th><?php echo $this->translate('Date')?></th>
                       <th><?php echo $this->translate('Poster Name')?></th>
                       <th><?php echo $this->translate('Warn')?></th>
                       <th><?php echo $this->translate('Body')?></th>
                       <th><?php echo $this->translate('Resource')?></th>
                       <th><?php echo $this->translate('Option')?></th>
                   </tr>    
               </thead>    
                <?php foreach($this->paginator as $comment):?>
                    <?php $user = $this->item('user', $comment->poster_id);?>
                        <tr>
                            <td>
                                <input name='item_<?php echo $comment->comment_id?>' value='<?php echo $comment->comment_id;?>' type='checkbox' class='checkbox'>
                                <input name="type" type="hidden" value="<?php echo $comment->getType()?>">
                            </td>          
                            <td>
                                <?php echo $this->locale()->toDate($comment->creation_date)?>
                            </td>
                            <td>
                                <?php echo $this->htmlLink($user->getHref(),
                                $this->string()->truncate($user->getTitle(), 10),
                               array('target' => '_blank'))?>
                            </td>
                            <td>
                                <?php for($i = 1; $i <= $comment->count; $i++){
                                    echo $this->htmlImage($this->baseUrl().'/application/modules/Spamcontrol/externals/images/spam.png');
                                }?>
                            </td>    
                            <td>
                                <?php echo $this->translate($comment->body)?>
                            </td>
                            <td>
                                
                                <?php $item = $this->item($comment->resource_type, $comment->resource_id)?>
                                <?php echo $this->htmlLink($item->getHref(), $item->getShortType())?>
                            </td>
                            <td>
                              <?php if($user->level_id != 1):?>
                             <?php echo $this->htmlLink(array('route'=>'admin_default', 'module'=> 'spamcontrol', 'controller'=>'settings', 'action'=>'messagewarn',  'item_id'=> $comment->getIdentity(), 'item_type' => $comment->getType()), $this->translate('Take Action'), array('class'=>'smoothbox'))?>
                                |
                                <?php echo $this->htmlLink(array('route'=>'admin_default', 'module' => 'user', 'controller' => 'manage', 'action' => 'delete', 'id' => $comment->poster_id), $this->translate('Delete User'), array('class' => 'smoothbox'))?>
                                <?php endif;?>
                            </td>    
                            
                        </tr>    
               <?php endforeach;?>
           </table>
           <br />
            <div class='buttons'>
                
                <button type='submit' name="submit_button" onClick="return confirm('<?php echo $this->translate("Are you sure you want to delete the selected message?")?>')" value="delete"><?php echo $this->translate("Delete Selected") ?></button>
            </div>
       </form>    
   </div>
    <?php else:?>
        <div class="tip" style="margin-top: 10px;">
            <span><?php echo $this->translate('No comments yet.')?></span>
        </div>    
    <?php endif;?>
    
    <?php if(count($this->paginator)>1):?>
        <div>  
            <?php echo $this->paginationControl($this->paginator); ?>
        </div>
    <?php endif;?>
    
    
</div>    
<?php ?>
<script type="text/javascript">
  
function selectAll()
{
  var i;
  var multimodify_form = $('blog_form');
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
    <?php if(count($this->paginator) > 0):?>
    <div style="margin-top: 10px;">
        <form id='blog_form' method="post" action="<?php echo $this->url(array('action'=>'comment-modify'));?>">
              <table class="admin_table" >
                  <thead>
                      <tr>
                          <th>
                              <input onclick="selectAll()" type='checkbox' class='checkbox'>
                               <?php echo $this->translate('Select All');?>
                          </th>           
                      </tr>
                  </thead>
              </table>    
                <div style="margin-top: 10px; margin-bottom: 20px;">
                <?php foreach($this->paginator as $photo):?>
                    <?php $user = $this->item('user',   $photo->owner_id);?>
                    
                   <div style="text-align: center; float: left; width: 150px; border: 1px solid #EEE; padding: 5px; margin: 5px; height: 145px; overflow: hidden" >
                       <div style="height: 106px; overflow: hidden; text-align: center;">
                       <div style="position: absolute; margin-left: 129px; margin-top: -2px;">
                           <input name='item_<?php echo $photo->photo_id?>' value='<?php echo $photo->photo_id;?>' type='checkbox' class='checkbox'>
                           <input name="type" type="hidden" value="<?php echo $photo->getType()?>">
                       </div>
                           <a href="<?php echo $photo->getHref()?>"><?php echo $this->htmlImage($photo->getPhotoUrl('thumb.normal'))?></a>
                     </div>
                            <div style="text-align: left; padding-top: 3px;">
                              
                                <?php echo $this->htmlLink(array('route'=>'admin_default', 'module'=> 'spamcontrol', 'controller'=>'settings', 'action'=>'messagewarn',  'item_id'=> $photo->getIdentity(), 'item_type' => $photo->getType()), $this->translate('Take Action'), array('class'=>'smoothbox'))?>
                                
                                
                                |
                            <?php echo $this->htmlLink(array('route'=>'admin_default', 'module' => 'user', 'controller' => 'manage', 'action' => 'delete', 'id' => $photo->owner_id), $this->translate('Delete User'), array('class' => 'smoothbox'))?>
                                    
                             </div>   
                          <div style="text-align: left">
                                
                                <?php echo $this->translate('By: ').$this->htmlLink($user->getHref(),
                                $this->string()->truncate($user->getTitle(), 10),
                               array('target' => '_blank'))?><br>
                              
                          </div>
                   </div>
                 <?php endforeach;?>
                   </div>
           <div style="clear:both"></div>
            <br />
            <div class='buttons'>
                
                <button type='submit' name="submit_button" onClick="return confirm('<?php echo $this->translate("Are you sure you want to delete the selected message?")?>')" value="delete"><?php echo $this->translate("Delete Selected") ?></button>
            </div>   
        </form>    
    </div>
    <?php else:?><br />
    <div class="tip">
        <span><?php echo $this->translate('No Photos yet.')?></span>
    </div>    
    <?php endif;?>
    
     <?php if(count($this->paginator)>1):?>
        <div style="margin-top: 10px;">  
            <?php echo $this->paginationControl($this->paginator); ?>
        </div>
    <?php endif;?>
</div>    

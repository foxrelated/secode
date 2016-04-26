<h2>Spam Control</h2>
<div class="tabs">
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>	
</div>
<div>
    <div style="padding: 5px"><?php echo $this->translate('You may edit setting of this page, and enter how many numbers allowed in usernames and emails.')?></div>
     <?php if(count($this->paginator)>0):?>
      <table class="admin_table">
          <thead>
              <tr>
                  <th><?php echo $this->translate('User')?></th>
                  <th><?php echo $this->translate('Warns')?></th>
                  <th><?php echo $this->translate('Options')?></th>
              </tr>    
          </thead>    
       <?php foreach($this->paginator as $user):?>
                <tr>
                    <td>
                         <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('target' => '_blank'))?>
                       </td>
                       <td>
                         <?php $count = Engine_Api::_()->getDbtable('warn', 'spamcontrol')->getWarnCount($user);
                         for ($i = 1; $i <= $count; $i++) {
                                    echo $this->htmlImage($this->baseUrl() . '/application/modules/Spamcontrol/externals/images/spam.png');
                                }
                         ?>
                       </td>
                       <td>
                           <?php if($user->level_id != 1):?>
                            <a class='smoothbox' href='<?php echo $this->url(array('action' => 'delete', 'id' => $user->user_id));?>'>
                            <?php echo $this->translate("Delete User") ?>
                            </a>
                          <?php endif;?>
                       </td>    
  
          </tr>
        <?php endforeach;?>
      </table>
    
     <?php else:?>
    <div class="tip"><br />
        <span><?php echo $this->translate('No Users with criteria entered in Settings.')?></span>
    </div>
    <?php endif;?>

   
    <?php if(count($this->paginator)>1):?>
        <div style="margin-top: 10px;">  
            <?php echo $this->paginationControl($this->paginator); ?>
        </div>
    <?php endif;?>
</div>    

<?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');   
 function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
      return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
 }      
       ?>
<div class="headline">
  <h2>
    <?php echo $this->translate('Mp3 Music');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
<img src='./application/modules/Mp3music/externals/images/music/account.jpg' width="48px" height="48px" border='0' class='icon_big' style="margin-bottom: 15px;">
<div class='page_header'><?php echo $this->translate('Edit Personal Information'); ?></div>


<?php if ($this->result != 0): ?>
  <div class='success'><img src='./application/modules/Mp3music/externals/images/music/success.gif' border='0' class='icon'><?php echo $this->translate(' Your changes have been saved.'); ?></div>
<?php endif; ?>
<?php if ($this->error != ''): ?>
  <div class='success'><img src='./application/modules/Mp3music/externals/images/music/error.gif' border='0' class='icon'><span style="color:red; padding-left: 10px"><?php echo $this->translate($this->error); ?></span></div>
<?php endif; ?>
<table cellpadding="0" cellspacing="5" width="100%">

<tr><td valign="top" width="50%">
    <form name="addalbum" class="" action="" method="post">
          <div class="table">
              <div class="table_left"><span class="required">*</span><?php echo $this->translate('Full name:'); ?></div>
              <div class="table_right"><input style="width: 100%" type="text" name="val[full_name]" id="title" value="<?php echo $this->info['displayname']; ?>"/> </div>
          </div>

          <div class="table">
              <div class="table_left"><?php echo $this->translate('Email:'); ?></div>
              <div class="table_right">  <input style="width:  100%" id="price" type="text" name="val[email]" value="<?php echo $this->info['email']; ?>"></div>
          </div>


         <div class="table">
              <div class="table_left"><?php echo $this->translate('Finance account:'); ?></div>
              <?php if ($this->info['account_username'] == '' && $this->mail_empty != 'emailAccount'): ?>
              <div class="table_right"><div class="message" style="margin:0px;"><?php echo $this->translate('You have not finance account.'); ?><a href="<?php echo $this->url(array(),'mp3music_account_create'); ?>"><?php echo $this->translate('Click here'); ?></a> <?php echo $this->translate('  to add your account.'); ?></div></div>
              <?php else: ?>
                    <div class="table_right"><input style="*margin-left:167px; width:  100%" id="account_username" type="text" style="width:300px;" name="val[account_username]" value="<?php echo $this->info['account_username'];?>"></div>
              <?php endif; ?>
          </div>
        
          <div class="table">
              <div class="table_left"><?php echo $this->translate('Status:'); ?></div>
              <div class="table_right"><textarea style="*margin-left:167px;  width: 100%" id="description" name="val[status]" cols="45" rows="6"><?php echo $this->info['status'];?></textarea></div>
          </div>
          <div class="table_clear">
        <button type="submit" name="submit"><?php echo $this->translate('Save Changes'); ?></button>
        </div>
</form>
        </td>
    </tr>
</table>

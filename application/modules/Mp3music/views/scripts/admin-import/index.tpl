 <h2><?php echo $this->translate("Mp3 Music Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<?php if($this->message != ''):   ?>
<div class = "message"><?php echo $this->message;?>  </div>
<?php endif; ?>
<form action="" method="POST" id="import_form">
<div style="color: red; font-weight: bold"><?php echo $this->translate('Import database from "Mp3 Music" to "Mp3 Music Selling".'); ?></div>
         <br/>
         <button type="submit" name="submit"> <?php echo $this->translate('Import'); ?></button>
</form>        

<style type="text/css">
div.message {
    background: none repeat scroll 0 0 #FEFBD9;
    border: 1px solid #EEE9B5;
    color: #6B6B6B;
    font-size: 10pt;
    font-weight: bold;
    margin: 4px;
    padding: 4px;
    position: relative;
}
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
}
</style>
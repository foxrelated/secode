
<h2><?php echo $this->translate("Tom Friend Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
      
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("Find some quick answers on this page") ?>
</p>

<br />


<div class="admin_statistics">

  <div style='cursor: pointer; margin-bottom: 10px; padding: 5px 10px; font-weight: bold; background-color: #F6F6F6' onclick="($('semtomfriend_faq_1').style.display=='block') ? $('semtomfriend_faq_1').style.display='none' : $('semtomfriend_faq_1').style.display='block';">
    <?php echo $this->translate('How Auto-Friending Works?') ?>
  </div>
  <div style='border: 1px solid #F6F6F6; padding: 5px; display: none; margin-bottom: 10px' id='semtomfriend_faq_1'>
    
    When a new member signs up, he will receive a friend request from the "friends users" you have selected.
    
  </div>


  <div style='cursor: pointer; margin-bottom: 10px; padding: 5px 10px; font-weight: bold; background-color: #F6F6F6' onclick="($('semtomfriend_faq_2').style.display=='block') ? $('semtomfriend_faq_2').style.display='none' : $('semtomfriend_faq_2').style.display='block';">
    <?php echo $this->translate('What happends to existing site members?') ?>
  </div>
  <div style='display: none;' id='semtomfriend_faq_2'>
  <div style='border: 1px solid #F6F6F6; padding: 5px; margin-bottom: 10px;'>
    
    There are no effect on existing site members, only on the new members signing up

  </div>
  </div>


  <!--
  <div style='cursor: pointer; margin-bottom: 10px; padding: 5px 10px; font-weight: bold; background-color: #F6F6F6' onclick="($('semtomfriend_faq_2').style.display=='block') ? $('semtomfriend_faq_2').style.display='none' : $('semtomfriend_faq_2').style.display='block';">
    <?php echo $this->translate('Q') ?>
  </div>
  <div style='display: none;' id='semtomfriend_faq_2'>
  <div style='border: 1px solid #F6F6F6; padding: 5px'>
    A
  </div>  
  </div>
  -->

</div>

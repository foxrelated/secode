    <?php
    $host = (isset($_SERVER['HTTPS']) ? "https" : "http");
    $host_url = $host.'://'.str_ireplace('heevents','',$_SERVER['HTTP_HOST'].Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'heevent_extended')).'admin/heevent/index/ticketsview?id=';


    foreach( $this->paginator as $item ): ?>

      <?php if($this->type == 1): ?>
      <li class="items" style="" id="item_id-<?php echo $item->getIdentity()?>" onclick="setUser(<?php echo $item->getIdentity()?>)">

        <div class="id" style="float: left"><?php echo $item->getIdentity()?></div>
        <div class="id" style="float: left"><?php echo $this->string()->truncate($item->getTitle(), 10)?></div>
        <div class="id" style="float: left"><?php   echo  $this->item('user', $item->user_id)->username?></div>
        <div class="id" style="float: left"><?php   echo $item->email?></div>

      </li>


        <?php elseif($this->type == 2):?>
    <li class="items" style="" id="item_id-<?php echo $item->getIdentity()?>" onclick="setEvent(<?php echo $item->getIdentity()?>)">
        <div class="id" style="float: left"><?php echo $item->getIdentity()?></div>
        <div class="id" style="float: left"><?php echo $this->string()->truncate($item->getTitle(), 20)?></div>
    </li>
      <?php endif?>




    <?php endforeach; ?>


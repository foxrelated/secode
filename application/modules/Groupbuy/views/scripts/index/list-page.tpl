<div class="headline">
  <h2>
    <?php echo $this->translate('GroupBuy');?>
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

<?php if(count($pages = $this->pages)>0): ?>
<ul>
     <?php foreach($pages as $page) :?>
     <li style="padding-top: 10px;">
         <span style ="float: left;">
              <a href="<?php echo $page->getHref();?>"> 
                <img src="<?php echo Zend_Controller_Front::getInstance()->getBaseUrl()?>/application/modules/Groupbuy/externals/images/page.png"
                     alt=""
                     width="48"
                     height="48"/>
              </a>
         </span>
          <span style ="float: left;">
            <li>
                <h3 style="padding-top: 12px;">
                        <?php if(strlen($page->title)>50){
                                  echo $this->htmlLink($page->getHref(),substr($page->title,0,50)." ...");
                              }
                              else{
                                echo $this->htmlLink($page->getHref(),$page->title);
                              }
                        ?>
                </h3>

            </li>
              <br/>
          </span>
     </li>
     <?php endforeach;?>
</ul>
<?php else:?>
        <div class="tip">
             <span><?php echo $this->translate("There are no instruction pages.") ?></span>
        </div>
<?php endif;?>
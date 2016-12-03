<div class="store_product_recent">

    <ul>
        <?php foreach( $this->products as $product ): ?>
        <?php 
            $owner = $product->getOwner(); 
            if(!$this->viewer()->hasPermission($owner)) {
                continue;
            }
        ?>

            <li>
                <div class='product_name'>
                    <?php echo $this->htmlLink($product->getHref(), $this->string()->truncate($product->getTitle(), 40, '...')) ?>
                </div>

                <?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.main'), array('class' => 'product_thumb')) ?>

                <div class='product_info'>
                        <?php echo $this->getPriceBlock($product); ?>
                        <span id="owner_product">
                     <?php
                     $subject = Engine_Api::_()->user()->getUser($product->owner_id);
                     ?>
                            <a href="/profile/<?php echo($subject->username);?>" target="_blank" ><?php echo($subject->username);?></a>
            </span>
                </div>
            </li>

        <?php endforeach; ?>
    </ul>

<br>

    <div style="font-size: 24px;">
        <a href="/store"><?php echo $this->translate('View all Items') ?></a> <img src="/application/modules/Core/externals/images/next.png">
    </div>

</div>
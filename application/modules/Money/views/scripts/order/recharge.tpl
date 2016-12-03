<?php ?>
<div class="headline">
    <h2>
        <?php echo $this->translate('Money'); ?>
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

<div class="tip">
    <span><?php echo $this->translate('You do not have the balance. ') ?><?php echo $this->htmlLink(array('route' => 'money_subscription', 'action' => 'choose'), $this->translate('Replenish the balance'));?></span>
</div>

<div class="product_photo">
    <?php echo $this->htmlImage($this->subject->getPhotoUrl());?>
</div>
<div class="product_description">
    <div class="product_title">
        <?php echo $this->subject->getTitle()?>
    </div>
    <div class="product_options">
        <div class="amount_title">
            <?php echo $this->translate('Amount');?>
        </div>
        <div class="product_amount">
            <?php echo $this->locale()->toCurrency($this->subject_cost, Engine_Api::_()->getApi('settings',
            'core')->getSetting('money.site.currency', 'USD'));?>
        </div>
    </div>
    <div class="product_info">
        <div class="product_owner">
            <?php echo $this->translate('Owner : %s', $this->htmlLink($this->subject->getOwner()->getHref(),
                                    $this->subject->getOwner()->getTitle()));?>
        </div>
        <div class="product_body">
            <?php echo $this->subject->getDescription()?>
        </div>
    </div>
</div>




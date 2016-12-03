<form method="get" action="<?php echo $this->url(array('order_id'=>$this->order->ukey),'store_purchase',true)?>" class="global_form store-transaction-form">
	<h3>
        <?php echo $this->translate('Business Request sent') ?>
      </h3>
      <p class="form-description">
        <?php echo $this->translate('Business Request successfully sent') ?>
      </p>
      <div class="form-elements">
        <div class="form-wrapper">
          <button type="submit">
            <?php echo $this->translate('Finish') ?>
          </button>
        </div>
      </div>
</form>
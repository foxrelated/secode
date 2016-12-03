<form method="get" action="<?php echo $this->user->getHref();?>" class="global_form store-transaction-form">
	<h3>
        <?php echo $this->translate('Business Request approved') ?>
      </h3>
        <p class="form-description">
          <?php echo $this->translate('Business Request successfully approved') ?>
         </p>
      <div class="form-elements">
        <div class="form-wrapper">
          <button type="submit">
            <?php echo $this->translate('Finish') ?>
          </button>
        </div>
      </div>
</form>
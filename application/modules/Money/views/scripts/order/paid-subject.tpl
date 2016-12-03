<?php ?>
<?php if ($this->user_balance > $this->subject_cost): ?>

<div>
    <form id="paid_form" enctype="application/x-www-form-urlencoded" class="global_form_popup"
          action="<?php echo $this->url() ?>" method="post">
        <div>
            <?php echo $this->translate('You balance %s', $this->user_balance) ?>
        </div>
        <div>
            <?php echo $this->translate('This %1s amount %2s', array($this->subject->getTitle(), $this->subject_cost))
            ?>
        </div>
        <div class="sep_plugin_buttons">
            <a href="javascript:void(0);" class="sep_btn_icon sep_yellow" onclick="$('paid_form').submit()" id=""
               target="">Buy now
                <div class="sep_icon_purchase_16"></div>
            </a> <span>or</span>
            <a name="cancel" id="cancel" type="button" href="javascript:void(0);"
               onclick="history.go(-1); return false;">cancel</a>
        </div>

    </form>
</div>
<div class="sharebox">
    <?php if ($this->subject->getPhotoUrl()): ?>
    <div class="sharebox_photo">
        <?php echo $this->htmlLink($this->subject->getHref(), $this->itemPhoto($this->subject, 'thumb.icon'),
        array('target' => '_parent')) ?>
    </div>
    <?php endif; ?>
    <div>
        <div class="sharebox_title">
            <?php echo $this->htmlLink($this->subject->getHref(), $this->subject->getTitle(), array('target' =>
            '_parent')) ?>
        </div>
        <div class="sharebox_description">
            <?php echo $this->subject->getDescription() ?>
        </div>
    </div>
</div>
<?php else: ?>
<div class="tip">
    <span><?php echo $this->translate('You do not have the balance. ') ?><?php echo $this->htmlLink(array('route' => 'money_general', 'action' => 'browse'), $this->translate('Replenish the balance')); ?></span>
</div>
<?php endif; ?>

<?php ?>
<?php if ($this->money > $this->subject->amount): ?>
    <div>
        <?php echo $this->translate('You balance %s', $this->money) ?>
    </div>
    <div>
        <?php echo $this->translate('This %1s amount %2s', array($this->item->getTitle(), $this->item->amount)) ?>
    </div>
    <div>
        <form id="paid_form" enctype="application/x-www-form-urlencoded" class="global_form_popup" action="<?php echo $this->url() ?>" method="post">
            <div class="sep_plugin_buttons">
                <a href="javascript:void(0);" class="sep_btn_icon sep_yellow" onclick="$('paid_form').submit()" id="" target="">Buy now
                    <div class="sep_icon_purchase_16"></div>
                </a> <span>or</span>
                <a name="cancel" id="cancel" type="button" href="javascript:void(0);" onclick="history.go(-1); return false;">cancel</a>
            </div>

        </form>
    </div>
    <div class="sharebox">
        <?php if ($this->item->getPhotoUrl()): ?>
            <div class="sharebox_photo">
                <?php echo $this->htmlLink($this->item->getHref(), $this->itemPhoto($this->item, 'thumb.icon'), array('target' => '_parent')) ?>
            </div>
        <?php endif; ?>
        <div>
            <div class="sharebox_title">
                <?php echo $this->htmlLink($this->item->getHref(), $this->item->getTitle(), array('target' => '_parent')) ?>
            </div>
            <div class="sharebox_description">
                <?php echo $this->item->getDescription() ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="tip">
        <span><?php echo $this->translate('You do not have the balance. ') ?><?php echo $this->htmlLink(array('route' => 'money_general', 'action' => 'browse'), $this->translate('Replenish the balance')); ?></span>
    </div>
<?php endif; ?>

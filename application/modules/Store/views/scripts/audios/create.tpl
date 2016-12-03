<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl  19.09.11 14:09 TeaJay $
 * @author     Taalay
 */
?>


<?php
	$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/swfobject/swfobject.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js')
?>

<script type="text/javascript">
var product_id = <?php echo $this->product->getIdentity() ?>;

if (product_id > 0) {
  $$('#product_id option').each(function(el, index) {
    if (el.value == product_id)
      $('product_id').selectedIndex = index;
  });
}

en4.core.runonce.add(function(){
  $('form-upload-audio').setStyle('clear', 'none');
});
</script>


<div class="layout_left">
<?php if (!$this->isPublic): ?>
<div id='panel_options'>
<?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
echo $this->navigation()
->menu()
->setContainer($this->navigation)
->setPartial(array('_navIcons.tpl', 'core'))
->render()
?>
</div>
<?php endif; ?>
</div>


 <div class="layout_middle">


<h3>
<?php echo $this->htmlLink($this->url(array('action' => 'edit', 'product_id' => $this->product->getIdentity()), 'store_products'), $this->translate('Edit Item'), array(
                        'id' => 'store_product_editsettings',
                    )) ?> <img src="/application/modules/Core/externals/images/next.png"> <?php echo $this->translate("Audios") ?> <img src="/application/modules/Core/externals/images/next.png">
  <?php echo $this->translate('%s', $this->htmlLink($this->product->getHref(), $this->product->getTitle())); ?>
</h3>


    <p class="flying-text-imp">
      <?php echo $this->translate("STORE_PAGE_PRODUCTS_AUDIOS_DESCRIPTION") ?>
    </p>


<div class="he-items">
  <ul>
    <li>
      <div class="he-item-options-inline">
        <?php echo $this->htmlLink($this->url(array('action' => 'edit','product_id' => $this->product->getIdentity()), 'store_products'), $this->translate('Back'), array(
            'class' => 'buttonlink product_back',
            'id' => 'store_product_editsettings',
          )) ?>

        <?php if (count($this->audios)) : ?>
          <?php echo $this->htmlLink($this->url(array('controller' => 'audios', 'action' => 'edit', 'product_id' => $this->product->getIdentity()), 'store_extended'), $this->translate('Edit Audios'), array(
              'class' => 'buttonlink product_audios_edit',
              'id' => 'store_product_editaudios',
            )) ?>

        <?php endif; ?>
      </div>
    </li>
  </ul>
</div>

</div>

<?php echo $this->form->render($this) ?>
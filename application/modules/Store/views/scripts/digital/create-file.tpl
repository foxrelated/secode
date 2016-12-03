<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create-file.tpl  21.09.11 16:47 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
en4.core.runonce.add(function(){
  $('form-upload-file').setStyle('clear', 'none');

    $('link-wrapper').setStyle('display', 'none');
    $('file-wrapper').setStyle('display', 'none');

    var file = $$('.description')[0];
    var link = $$('.description')[1];
    file.addEvent('click', function(){
        link.removeClass('active');
        file.addClass('active');
        $('file-wrapper').setStyle('display', 'block');
        $('link-wrapper').setStyle('display', 'none');
        $('submit-wrapper').setStyle('display', 'none');
    });
    link.addEvent('click', function(){
        file.removeClass('active');
        link.addClass('active');
        $('link-wrapper').setStyle('display', 'block');
        $('file-wrapper').setStyle('display', 'none');
        $('submit-wrapper').setStyle('display', 'block');
    });

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
                    )) ?> <img src="/application/modules/Core/externals/images/next.png"> <?php echo $this->translate("Downloadable Item") ?> <img src="/application/modules/Core/externals/images/next.png">
  <?php echo $this->translate('%s', $this->htmlLink($this->product->getHref(), $this->product->getTitle())); ?>
</h3>




    <p class="flying-text-imp">
      <?php echo $this->translate("STORE_PAGE_PRODUCTS_INTAGIBLE_DESCRIPTION") ?>
    </p>


<div class="he-items">
  <ul>
    <li>
      <div class="he-item-options-inline">
        <?php echo $this->htmlLink($this->url(array('action' => 'edit','product_id' => $this->product->getIdentity()), 'store_products'), $this->translate('Back'), array(
            'class' => 'buttonlink product_back',
            'id' => 'store_product_editsettings',
          )) ?>
          <br>
      </div>
    </li>
  </ul>
</div>

<?php echo $this->form->render($this) ?>

</div>
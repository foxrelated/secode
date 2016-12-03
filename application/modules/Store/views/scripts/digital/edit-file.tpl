<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit-file.tpl  21.09.11 17:50 TeaJay $
 * @author     Taalay
 */
?>


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
  <?php echo $this->translate('STORE_Manage File') . ' <img src="/application/modules/Core/externals/images/next.png"> ' . $this->htmlLink($this->product->getHref(), $this->product->getTitle()); ?>
</h3>


    <p class="flying-text-imp">
      <?php echo $this->translate("STORE_PAGE_PRODUCTS_MANAGE_DESCRIPTION") ?>
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




<ul class="store_product_file">
  <li>
    <div class="store_product_file_options">
      <ul>
        <li>
          <?php echo $this->htmlLink(
            $this->url(array('controller' => 'digital', 'action' => 'delete-file', 'product_id' => $this->product->getIdentity()), 'store_extended', true),
            '<img title="'.$this->translate('Delete').'" src="application/modules/Store/externals/images/delete_file.png">',
            array('class' => 'smoothbox')
          );?>
        </li>
      </ul>
    </div>
    <div class="store_product_file_title">
      <span>
        <?php echo $this->file->name;?>
      </span>
    </div>
  </li>
</ul>

</div>
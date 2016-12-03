<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: add.tpl 2011-09-19 17:07:11 taalay $
 * @author     Taalay
 */

?>
<?php echo $this->content()->renderWidget('store.navigation-tabs'); ?>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    $('form-upload').style.clear = 'none';
  });
</script>

<div class="he-items" style="float: right; margin: 30px 0">
  <ul class="he-item-list">
    <li>
      <div class="he-item-options">
        <?php echo $this->htmlLink($this->url(array('action' => 'edit','product_id' => $this->product->getIdentity()), 'store_products'), $this->translate('Back'), array(
          'class' => 'buttonlink product_back',
          'id' => 'store_product_editsettings',
        )) ?>
        <br>
        <?php echo $this->htmlLink($this->url(array('controller' => 'photo', 'action' => 'edit', 'product_id' => $this->product->getIdentity()), 'store_extended', true), $this->translate('Edit photos'), array(
          'class' => 'buttonlink product_photos_edit',
          'id' => 'store_product_addphotos',
        )) ?>
        <br>
      </div>
    </li>
  </ul>
</div>

<?php echo $this->form->render($this); ?>

  
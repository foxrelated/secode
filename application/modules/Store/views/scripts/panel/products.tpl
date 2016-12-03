<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: products.tpl  17.09.11 11:57 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">

function product_pagination(page) {
	new Request.JSON({
		'url': '<?php echo $this->url(array('action' => 'products'), 'store_panel'); ?>',
		'method' : 'post',
		'data' : {
			'p' : page,
			'format' : 'json'
		},
    eval: true,
		onSuccess: function(response) {
			$$('.he-items')[0].innerHTML = response.html;
		}
	}).send();
}
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
      <?php echo $this->translate('My Products'); ?>
    </h3>

    <p class="flying-text-imp">
      <?php echo $this->translate("STORE_PAGE_PRODUCTS_DESCRIPTION") ?>
    </p>
    
    <?php echo $this->render('_product_list_edit.tpl'); ?>
</div>


<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $id: store.tpl  26.08.11 19:07 taalay $
 * @author     Taalay
 */
?>

<?php if ($this->pageCount > 1): ?>
  <ul class="paginationControl">
    <?php if (isset($this->previous)): ?>
			<li>
				<a href="javascript:void(0)" onclick="product_pagination(<?php echo $this->previous;?>)"><img class="raquo-image" src="/application/modules/Core/externals/images/back.png"> <?php echo $this->translate('Previous');?></a>
			</li>
    <?php endif; ?>

    <?php foreach ($this->pagesInRange as $page): ?>
      <li class="<?php if ($page == $this->current): ?>selected<?php endif; ?>" >
        <a onclick="product_pagination(<?php echo $page;?>)" href="javascript:void(0)"><?php echo $page; ?></a>
      </li>
    <?php endforeach; ?>

    <?php if (isset($this->next)): ?>
    	<li>
        <a href="javascript:void(0)" onclick="product_pagination(<?php echo $this->next;?>)"><?php echo $this->translate('Next');?> <img class="raquo-image" src="/application/modules/Core/externals/images/next.png"></a>
      </li>
    <?php endif; ?>
  </ul>
<?php endif; ?>
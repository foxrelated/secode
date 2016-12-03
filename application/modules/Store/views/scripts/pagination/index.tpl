<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl:  11.07.11 14:52 taalay $
 * @author     Taalay
 */
?>

<?php if ($this->pageCount > 1): ?>
  <ul class="paginationControl">
    <?php if (isset($this->previous)): ?>
    <li>
     <a href="javascript:void(0)" onclick="product_manager.setPage(<?php echo $this->previous;?>)"><img class="raquo-image" src="/application/modules/Core/externals/images/back.png"> <?php echo $this->translate('Previous');?></a>
   	</li>
    <?php endif; ?>

    <?php foreach ($this->pagesInRange as $page): ?>
      <li class="<?php if ($page == $this->current): ?>selected<?php endif; ?>" >
        <a onclick="product_manager.setPage(<?php echo $page;?>)" href="javascript:void(0)"><?php echo $page; ?></a>
      </li>
    <?php endforeach; ?>

    <?php if (isset($this->next)): ?>
    	<li>
        <a href="javascript:void(0)" onclick="product_manager.setPage(<?php echo $this->next;?>)"><?php echo $this->translate('Next');?> <img class="raquo-image" src="/application/modules/Core/externals/images/next.png"></a>
      </li>
    <?php endif; ?>
  </ul>
<?php endif; ?>
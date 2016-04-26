<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _pagging.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php if ($this->pageCount && $this->pageCount > 1):
				$numPages = $this->pageCount;
 ?>
 <?php 
 if(isset($this->identityWidget)){
 	$idFunction = 'paggingNumber'.$this->identityWidget;
 }else
 	$idFunction = 'paggingNumber';  
 ?>
<div class="sesbasic_loading_cont_overlay overlay_<?php echo $this->identityWidget; ?>" style="display:none"></div>
<div class="sesbasic_paging clear sesbasic_clearfix sesbasic_bxs" id="ses_pagging">
  <ul class="sesbasic_clearfix">
    <li style="display:none;"><span><?php echo $this->current; ?> of <?php echo $this->pageCount; ?></span></li>
    <?php if (isset($this->previous)): ?>
    	<li><a href="javascript:;" onclick="<?php echo $idFunction ?>('<?php echo $this->previous; ?>')"  title="<?php echo $this->translate('Previous'); ?>"> <?php echo $this->translate('Previous') ?></a></li>
    <?php else: ?>
      <li class="sesbasic_paging_disabled"><span><?php echo $this->translate('Previous'); ?></span></li>
    <?php endif; ?>
    <?php 
    $pagingEcho = '';
    $selectPageNum = $this->current;
    for($runPage=1;$runPage<=$numPages;$runPage++){
        if($selectPageNum == $runPage){
          $pagingEcho .= "<li class=\"sesbasic_paging_current_page\"><a href=\"javascript:;\">".$runPage."</a></li>";
        }else{
          $pagingEcho .= "<li class=\"pages\"><a href=\"javascript:;\" onclick=\"".$idFunction."(".$runPage.")\">".$runPage."</a></li>";
        }
        if($runPage < ($selectPageNum - 3) && ($runPage+3) < $numPages){
          $pagingEcho .= "<li class=\"sesbasic_paging_disabled sesbasic_paging_bl\"><span>&sdot;&sdot;&sdot;</span></li>";
          $runPage = $selectPageNum - 3;
        }
        if($runPage > ($selectPageNum + 2) && ($runPage+2) < $numPages){
          $pagingEcho .= "<li class=\"sesbasic_paging_disabled sesbasic_paging_bl\"><span>&sdot;&sdot;&sdot;</span></li>";
          $runPage = $numPages-1;
        }
     }
      echo $pagingEcho;
    ?>
    <?php if (isset($this->next)): ?>
      <li><a href="javascript:;" onclick="<?php echo $idFunction ?>('<?php echo $this->next; ?>')"  title="<?php echo $this->translate('Next'); ?>"> <?php echo $this->translate('Next') ?></a></li> 
    <?php else: ?>
    	<li class="sesbasic_paging_disabled"><span><?php echo $this->translate('Next') ?></span></li>
    <?php endif; ?>
  </ul>
</div>
<?php endif; ?>
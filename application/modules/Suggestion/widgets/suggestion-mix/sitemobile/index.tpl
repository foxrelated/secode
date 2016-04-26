<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sm-content-list <?php echo $this->recommendationView == 'grid' ? "iscroll_wapper":""?>">
    <?php if ($this->recommendationView == 'list'): ?>
        <ul data-role="listview" data-icon="false">
        <?php elseif ($this->recommendationView == 'grid'): ?>
           <ul class="sugg_list_grid <?php if($this->carouselView): ?> iscroll_container <?php endif;?>">
          <?php endif; ?>
            <?php
            $recommendedFlag = 0;
            $recommendedEndFlag = @COUNT($this->modArray) - 1;
            foreach ($this->modArray as $modArray) {
                echo $this->partial('application/modules/Suggestion/widgets/sitemobile_templatePartial.tpl', array('modInfo' => $modArray, 'recommendedStartFlag' => $recommendedFlag, 'recommendedEndFlag' => $recommendedEndFlag, 'recommendationView' => $this->recommendationView,
                    'contentType'=>'mix'));
                $recommendedFlag++;
            }
            ?>
        </ul>
</div>
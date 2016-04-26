<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<ul class="seaocore_sidebar_list">
    <?php foreach ($this->diaries as $diary): ?>
        <li>
            <?php echo $this->htmlLink($diary->getHref(), $this->itemPhoto($diary->getCoverItem(), 'thumb.icon'), array('class' => 'popularmembers_thumb', 'title' => $diary->getTitle()), array('title' => $diary->getTitle())) ?>
            <div class='seaocore_sidebar_list_info'>
                <div class='seaocore_sidebar_list_title'>
                    <?php echo $this->htmlLink($diary->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($diary->getTitle(), $this->title_truncation), array('title' => $diary->getTitle())) ?>
                </div>

                <div class='seaocore_sidebar_list_details'>
                    <?php echo $this->translate('By %s', $diary->getOwner()->toString()) ?>
                </div>

                <?php if (!empty($this->statisticsDiary)): ?>
                    <div class='seaocore_sidebar_list_details'>
                        <?php
                        $statistics = '';

                        if (in_array('entryCount', $this->statisticsDiary)) {
                            $statistics .= $this->translate(array('%s event', '%s events', $diary->total_item), $this->locale()->toNumber($diary->total_item)) . ', ';
                        }

                        if (in_array('viewCount', $this->statisticsDiary)) {
                            $statistics .= $this->translate(array('%s view', '%s views', $diary->view_count), $this->locale()->toNumber($diary->view_count)) . ', ';
                        }

                        if (in_array('likeCount', $this->statisticsDiary)) {
                            $statistics .= $this->translate(array('%s like', '%s likes', $diary->like_count), $this->locale()->toNumber($diary->like_count)) . ', ';
                        }

                        $statistics = trim($statistics);
                        $statistics = rtrim($statistics, ',');
                        ?>
                        <?php echo $statistics; ?>
                    </div>
                <?php endif; ?>        
            </div>
        </li>
    <?php endforeach; ?>
</ul>  

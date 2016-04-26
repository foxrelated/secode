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

<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
        . 'application/modules/Seaocore/externals/styles/styles.css');
?>

<?php if ($this->is_ajax_load): ?>
    <script type="text/javascript">

        function showEventPhoto(ImagePath, category_id, event_id, href) {
            var elem = document.getElementById('event_elements_' + category_id).getElementsByTagName('a');
            for (var i = 0; i < elem.length; i++)
            {
                var cat_eventid = elem[i].id;
                $(cat_eventid).erase('class');
            }
            $('event_link_class_' + event_id).set('class', 'active');

            $('eventImage_' + category_id).src = ImagePath;
            $('eventImage_' + category_id).getParent('a').set('href', href);
        }

    </script>

    <ul class="seaocore_categories_box">
        <li> 
            <?php $ceil_count = 0;
            $k = 0;
            ?>
            <?php for ($i = 0; $i <= count($this->categories); $i++) { ?>
                    <?php if ($ceil_count == 0) : ?>      
                    <div>      
                        <?php endif; ?>  
                    <div class="seaocore_categories_list_row">
                        <?php $ceil_count++; ?>				
                        <?php
                        $category = "";
                        if (isset($this->categories[$k]) && !empty($this->categories[$k])):
                            $category = $this->categories[$k];
                        endif;
                        $k++;

                        if (empty($category)) {
                            break;
                        }
                        ?>

                        <div class="seaocore_categories_list">
                                <?php $total_subcat = Count($category['category_events']); ?>
                            <h6>
        <?php echo $this->htmlLink($this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $category['category_id'])->getCategorySlug()), Engine_Api::_()->siteevent()->getCategoryHomeRoute()), $this->translate($category['category_name'])) ?>
                            </h6>	
                            <div class="sub_cat" id="subcat_<?php echo $category['category_id'] ?>">

                                <?php $total_count = 1; ?>

                                <?php foreach ($category['category_events'] as $categoryEvents) : ?>

                                    <?php
                                    $imageSrc = $categoryEvents['imageSrc'];
                                    if (empty($imageSrc)) {
                                        $imageSrc = $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_event_thumb_icon.png';
                                    }
                                    $category_id = $category['category_id'];
                                    $event_id = $categoryEvents['event_id'];
                                    ?>
                                    <?php $siteevent = Engine_Api::_()->getItem('siteevent_event', $categoryEvents['event_id']); ?>
            <?php if ($total_count == 1): ?>
                                        <div class="seaocore_categories_img" >
                                            <a href='<?php echo $siteevent->getHref(array('showEventType' => $this->showEventType)) ?>' ><img src="<?php echo $imageSrc; ?>" id="eventImage_<?php echo $category['category_id'] ?>" alt="" class="thumb_icon" /></a>
                                        </div>
                                        <div id='event_elements_<?php echo $category_id; ?>'>
                                            <?php $href = $siteevent->getHref(array('showEventType' => $this->showEventType)); ?>
                                            <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), Engine_Api::_()->seaocore()->seaocoreTruncateText($categoryEvents['event_title'], $this->title_truncation) . " (" . $categoryEvents['populirityCount'] . ")", array('onmouseover' => "javascript:showEventPhoto('$imageSrc', '$category_id', '$event_id','$href');", 'title' => $categoryEvents['event_title'], 'class' => 'active', 'id' => "event_link_class_$event_id")); ?>
                                        <?php else: ?> 
                                            <?php $href = $siteevent->getHref(array('showEventType' => $this->showEventType)); ?>
                                            <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), Engine_Api::_()->seaocore()->seaocoreTruncateText($categoryEvents['event_title'], $this->title_truncation) . " (" . $categoryEvents['populirityCount'] . ")", array('onmouseover' => "javascript:showEventPhoto('$imageSrc', '$category_id', '$event_id','$href');", 'title' => $categoryEvents['event_title'], 'id' => "event_link_class_$event_id")); ?>
                                        <?php endif; ?>

                                        <?php $total_count++; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($ceil_count % 2 == 0) : ?>      
                        </div>
                        <?php $ceil_count = 0; ?>
                    <?php endif; ?>
            <?php } ?> 
        </li>	
    </ul>
<?php else: ?>

    <div id="layout_siteevent_category_events_<?php echo $this->identity; ?>">
        <!--    <div class="seaocore_content_loader"></div>-->
    </div>

    <script type="text/javascript">
        var requestParams = $merge(<?php echo json_encode($this->params); ?>, {'content_id': '<?php echo $this->identity; ?>'})
        var params = {
            'detactLocation': <?php echo $this->detactLocation; ?>,
            'responseContainer': 'layout_siteevent_category_events_<?php echo $this->identity; ?>',
            requestParams: requestParams
        };

        en4.seaocore.locationBased.startReq(params);
    </script>  

<?php endif; ?>
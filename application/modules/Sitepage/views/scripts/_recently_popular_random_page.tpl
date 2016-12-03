<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _recently_popular_random_page.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$enableBouce = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.sponsored', 1);
$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
?>
<?php $latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.latitude', 0); ?>
<?php $longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.longitude', 0); ?>
<?php $defaultZoom = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.zoom', 1); ?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1); ?>
<script type="text/javascript" >
    function owner(thisobj) {
        var Obj_Url = thisobj.href;
        Smoothbox.open(Obj_Url);
    }
</script>
<script>
    var sitepages_likes = function (resource_id, resource_type) {
        var content_type = 'sitepage';
        //var error_msg = '<?php //echo $this->result['0']['like_id'];    ?>';

        // SENDING REQUEST TO AJAX
        var request = createLikepage(resource_id, resource_type, content_type);

        // RESPONCE FROM AJAX
        request.addEvent('complete', function (responseJSON) {
            if (responseJSON.error_mess == 0) {
                $(resource_id).style.display = 'block';
                if (responseJSON.like_id)
                {
                    $('backgroundcolor_' + resource_id).className = "sitepage_browse_thumb sitepage_browse_liked";
                    $('sitepage_like_' + resource_id).value = responseJSON.like_id;
                    $('sitepage_most_likes_' + resource_id).style.display = 'none';
                    $('sitepage_unlikes_' + resource_id).style.display = 'block';
                    $('show_like_button_child_' + resource_id).style.display = 'none';
                }
                else
                {
                    $('backgroundcolor_' + resource_id).className = "sitepage_browse_thumb";
                    $('sitepage_like_' + resource_id).value = 0;
                    $('sitepage_most_likes_' + resource_id).style.display = 'block';
                    $('sitepage_unlikes_' + resource_id).style.display = 'none';
                    $('show_like_button_child_' + resource_id).style.display = 'none';
                }

            }
            else {
                en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
                return;
            }
        });
    }
    // FUNCTION FOR CREATING A FEEDBACK
    var createLikepage = function (resource_id, resource_type, content_type)
    {
        if ($('sitepage_most_likes_' + resource_id).style.display == 'block')
            $('sitepage_most_likes_' + resource_id).style.display = 'none';


        if ($('sitepage_unlikes_' + resource_id).style.display == 'block')
            $('sitepage_unlikes_' + resource_id).style.display = 'none';
        $(resource_id).style.display = 'none';
        $('show_like_button_child_' + resource_id).style.display = 'block';

        if (content_type == 'sitepage') {
            var like_id = $(content_type + '_like_' + resource_id).value
        }
        //	var url = '<?php echo $this->url(array('action' => 'global-likes'), 'sitepage_like', true); ?>';
        var request = new Request.JSON({
            url: '<?php echo $this->url(array('action' => 'global-likes'), 'sitepage_like', true); ?>',
            data: {
                format: 'json',
                'resource_id': resource_id,
                'resource_type': resource_type,
                'like_id': like_id
            }
        });
        request.send();
        return request;
    }
</script>

<?php
$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
$MODULE_NAME = 'sitepage';
$RESOURCE_TYPE = 'sitepage_page';
?>
<?php if ($this->list_view): ?>
    <div id="rgrid_view_page"  style="display: none;">
        <?php $sitepage_entry = Zend_Registry::isRegistered('sitepage_entry') ? Zend_Registry::get('sitepage_entry') : null; ?>
        <?php if (count($this->sitepagesitepage)): ?>
            <?php
            $counter = '1';
            $limit = $this->active_tab_list;
            ?>
            <ul class="seaocore_browse_list">
                <?php foreach ($this->sitepagesitepage as $sitepage): ?>
                    <?php
                    if ($counter > $limit):
                        break;
                    endif;
                    $counter++;
                    ?>
                    <li <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)): ?><?php if ($sitepage->featured): ?> class="lists_highlight"<?php endif; ?><?php endif; ?>>
                        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)): ?>
                            <?php if ($sitepage->featured): ?>
                                <span title="<?php echo $this->translate('Featured') ?>" class="seaocore_list_featured_label"><?php echo $this->translate('Featured') ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <div class='seaocore_browse_list_photo'>
                            <?php
                            if (!empty($sitepage_entry)) {
                                echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), $this->itemPhoto($sitepage, 'thumb.normal', '', array('align' => 'left')));
                            } else {
                                exit();
                            }
                            ?>
                            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)): ?>
                                <?php if (!empty($sitepage->sponsored)): ?>
                                    <?php
                                    //$sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.image', 1);
                                    //if (!empty($sponsored)) { 
                                    ?>
                                    <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
                                        <?php echo $this->translate('SPONSORED'); ?>                 
                                    </div>
                                    <?php //}  ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class='seaocore_browse_list_info'>            
                            <div class='seaocore_browse_list_info_title'>
                                <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)): ?>
                                    <span>
                                        <?php if ($sitepage->sponsored == 1): ?>
                                            <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                                        <?php endif; ?>
                                        <?php if ($sitepage->featured == 1): ?>
                                            <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                                <div class="seaocore_title">
                                    <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), Engine_Api::_()->sitepage()->truncation($sitepage->getTitle(), $this->listview_turncation), array('title' => $sitepage->getTitle())) ?>
                                </div>
                            </div>

                            <?php if (@in_array('reviewCount', $this->statistics) && $this->ratngShow): ?>
                                <?php if (($sitepage->rating > 0)): ?>

                                    <?php
                                    $currentRatingValue = $sitepage->rating;
                                    $difference = $currentRatingValue - (int) $currentRatingValue;
                                    if ($difference < .5) {
                                        $finalRatingValue = (int) $currentRatingValue;
                                    } else {
                                        $finalRatingValue = (int) $currentRatingValue + .5;
                                    }
                                    ?>

                                    <span class="clr" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                                        <?php for ($x = 1; $x <= $sitepage->rating; $x++): ?>
                                            <span class="rating_star_generic rating_star" ></span>
                                        <?php endfor; ?>
                                        <?php if ((round($sitepage->rating) - $sitepage->rating) > 0): ?>
                                            <span class="rating_star_generic rating_star_half" ></span>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <div class='seaocore_browse_list_info_date'>
                                <?php echo $this->timestamp(strtotime($sitepage->creation_date)) ?>
                                <?php if (!empty($this->showpostedBy) && $postedBy): ?>
                                    - <?php echo $this->translate('posted by'); ?>
                                    <?php echo $this->htmlLink($sitepage->getOwner()->getHref(), $sitepage->getOwner()->getTitle()) ?>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($this->statistics)) : ?>
                                <div class='seaocore_browse_list_info_date'>
                                    <?php
                                    $statistics = '';

                                    if (@in_array('likeCount', $this->statistics)) {
                                        $statistics .= $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count)) . ', ';
                                    }
                                    if (@in_array('followCount', $this->statistics)) {
                                        $statistics .= $this->translate(array('%s follower', '%s followers', $sitepage->follow_count), $this->locale()->toNumber($sitepage->follow_count)) . ', ';
                                    }

                                    if (@in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                                        $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.title', 1);
                                        if ($sitepage->member_title && $memberTitle) {
                                            echo $sitepage->member_count . ' ' . $sitepage->member_title . ', ';
                                        } else {
                                            $statistics .= $this->translate(array('%s member', '%s members', $sitepage->member_count), $this->locale()->toNumber($sitepage->member_count)) . ', ';
                                        }
                                    }

                                    if (!empty($sitepage->review_count) && @in_array('reviewCount', $this->statistics) && !empty($this->ratngShow)) {
                                        $statistics .= $this->translate(array('%s review', '%s reviews', $sitepage->review_count), $this->locale()->toNumber($sitepage->review_count)) . ', ';
                                    }

                                    if (@in_array('commentCount', $this->statistics)) {
                                        $statistics .= $this->translate(array('%s comment', '%s comments', $sitepage->comment_count), $this->locale()->toNumber($sitepage->comment_count)) . ', ';
                                    }

                                    if (@in_array('viewCount', $this->statistics)) {
                                        $statistics .= $this->translate(array('%s view', '%s views', $sitepage->view_count), $this->locale()->toNumber($sitepage->view_count)) . ', ';
                                    }
                                    $statistics = trim($statistics);
                                    $statistics = rtrim($statistics, ',');
                                    ?>
                                    <?php echo $statistics; ?>
                                </div>
                            <?php endif; ?>


                            <?php if (!empty($sitepage->price) && $this->enablePrice): ?>
                                <div class='seaocore_browse_list_info_date'>
                                    <?php
                                    echo $this->translate("Price: ");
                                    echo $this->locale()->toCurrency($sitepage->price, $currency);
                                    ?>
                                </div>
                            <?php endif; ?>						
                            <?php
                            if (!empty($sitepage->location) && $this->enableLocation):
                                echo "<div class='seaocore_browse_list_info_date'>";
                                echo $this->translate("Location: ");
                                echo $this->translate($sitepage->location);
                                $location_id = Engine_Api::_()->getDbTable('locations', 'sitepage')->getLocationId($sitepage->page_id, $sitepage->location);
                                ?><?php if (!empty($this->showgetdirection)) : ?>&nbsp; - <b> <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $sitepage->page_id, 'resouce_type' => 'sitepage_page', 'location_id' => $location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')); ?> </b><?php endif; ?>
                                <?php
                                echo "</div>";
                            endif;
                            ?>

                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('Nobody has created a page with that criteria.') ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php if ($this->grid_view): ?>
    <div id="rimage_view_page" style="display: none;">
        <?php if (count($this->sitepagesitepage)): ?>

            <?php
            $counter = 1;
            $total_sitepage = count($this->sitepagesitepage);
            $limit = $this->active_tab_image;
            ?> 
            <div class="sitepage_img_view o_hidden">
                <div class="sitepage_img_view_sitepage">
                    <?php foreach ($this->sitepagesitepage as $sitepage): ?>
                        <?php // start like Work on the browse page  ?>
                        <?php
                        if ($counter > $limit):
                            break;
                        endif;
                        $counter++;
                        ?>
                        <?php
                        $likePage = false;
                        if (!empty($viewer_id)):
                            $likePage = Engine_Api::_()->sitepage()->hasPageLike($sitepage->page_id, $viewer_id);
                        endif;
                        ?>

                        <div class="sitepage_browse_thumb <?php if ($likePage): ?> sitepage_browse_liked <?php endif; ?>" id = "backgroundcolor_<?php echo $sitepage->page_id; ?>" style="width:<?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;" >

                            <div class="sitepage_browse_thumb_list" <?php if (!empty($viewer_id) && !empty($this->showlikebutton)) : ?> onmouseOver=" $('like_<?php echo $sitepage->getIdentity(); ?>').style.display = 'block';
                                        if ($('<?php echo $sitepage->getIdentity(); ?>').style.display == 'none')
                                            $('<?php echo $sitepage->getIdentity(); ?>').style.display = 'block';"  onmouseout="$('like_<?php echo $sitepage->getIdentity(); ?>').style.display = 'none';
                                                    $('<?php echo $sitepage->getIdentity(); ?>').style.display = 'none';" <?php endif; ?> >
                                     <?php // end like Work on the browse page ?>

                                <?php if (!empty($this->showlikebutton)) : ?>
                                    <a href="javascript:void(0);">
                                    <?php else : ?>
                                        <a href="<?php echo Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()) ?>">
                                        <?php endif; ?>
                                        <?php
                                        if ($this->photoWidth >= 720):
                                            $photo_type = 'thumb.main';
                                        elseif ($this->photoWidth >= 140):
                                            $photo_type = 'thumb.normal';
                                        elseif ($this->photoWidth >= 100):
                                            $photo_type = 'thumb.icon';
                                        else:
                                            $photo_type = 'thumb.profile';
                                        endif;
                                        $url = $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png';
                                        $temp_url = $sitepage->getPhotoUrl($photo_type);
                                        if (!empty($temp_url)): $url = $sitepage->getPhotoUrl($photo_type);
                                        endif;
                                        ?>
                                        <span style="background-image: url(<?php echo $url; ?>);"> </span>
                                        <?php if (empty($this->showlikebutton)) : ?>
                                            <div class="sitepage_browse_title">
                                                <p title="<?php echo $sitepage->getTitle() ?>"><?php echo Engine_Api::_()->sitepage()->truncation($sitepage->getTitle(), $this->turncation); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </a>

                                    <?php if (!empty($viewer_id)) : ?>
                                        <div id="like_<?php echo $sitepage->getIdentity() ?>" style="display:none;">
                                            <?php $RESOURCE_ID = $sitepage->getIdentity(); ?>
                                            <div class="" style="display:none;" id="<?php echo $RESOURCE_ID; ?>">
                                                <?php
                                                // Check that for this 'resurce type' & 'resource id' user liked or not.
                                                $check_availability = Engine_Api::_()->$MODULE_NAME()->checkAvailability($RESOURCE_TYPE, $RESOURCE_ID);
                                                if (!empty($check_availability)) {
                                                    $label = 'Unlike this';
                                                    $unlike_show = "display:block;";
                                                    $like_show = "display:none;";
                                                    $like_id = $check_availability[0]['like_id'];
                                                } else {
                                                    $label = 'Like this';
                                                    $unlike_show = "display:none;";
                                                    $like_show = "display:block;";
                                                    $like_id = 0;
                                                }
                                                ?>
                                                <div class="sitepage_browse_thumb_hover_color">
                                                </div>
                                                <div class="seaocore_like_button sitepage_browse_thumb_hover_unlike_button" id="sitepage_unlikes_<?php echo $RESOURCE_ID; ?>" style='<?php echo $unlike_show; ?>' >
                                                    <a href = "javascript:void(0);" onclick = "sitepages_likes('<?php echo $RESOURCE_ID; ?>', 'sitepage_page');">
                                                        <i class="seaocore_like_thumbdown_icon"></i>
                                                        <span><?php echo $this->translate('Unlike') ?></span>
                                                    </a>
                                                </div>
                                                <div class="seaocore_like_button sitepage_browse_thumb_hover_like_button" id="sitepage_most_likes_<?php echo $RESOURCE_ID; ?>" style='<?php echo $like_show; ?>'>
                                                    <a href = "javascript:void(0);" onclick = "sitepages_likes('<?php echo $RESOURCE_ID; ?>', 'sitepage_page');">
                                                        <i class="seaocore_like_thumbup_icon"></i>
                                                        <span><?php echo $this->translate('Like') ?></span>
                                                    </a>
                                                </div>
                                                <input type ="hidden" id = "sitepage_like_<?php echo $RESOURCE_ID; ?>" value = '<?php echo $like_id; ?>' />
                                            </div>
                                        </div>
                                        <div id="show_like_button_child_<?php echo $RESOURCE_ID; ?>" style="display:none;" >
                                            <div class="sitepage_browse_thumb_hover_color"></div>
                                            <div class="sitepage_browse_thumb_hover_loader">
                                                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loader.gif" class="mtop5" />
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php // end like Work on the browse page  ?>

                                    <?php if ($sitepage->featured == 1 && !empty($this->showfeaturedLable)): ?>
                                        <span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured') ?>"><?php echo $this->translate('Featured') ?></span>
                                    <?php endif; ?>

                                    <?php if (!empty($this->showlikebutton)): ?>
                                        <div class="sitepage_browse_title">
                                            <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), Engine_Api::_()->sitepage()->truncation($sitepage->getTitle(), $this->turncation), array('title' => $sitepage->getTitle())); ?>
                                        </div>
                                    <?php endif; ?>

                            </div>

                            <?php if (!empty($sitepage->sponsored) && !empty($this->showsponsoredLable)): ?>
                                <?php
                                //$sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.image', 1);
                                //if (!empty($sponsored)) { 
                                ?>
                                <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
                                    <?php echo $this->translate('SPONSORED'); ?>                 
                                </div>
                                <?php //}  ?>
                            <?php endif; ?>

                            <div class="sitepage_browse_thumb_info">

                                <?php if (@in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) : ?>
                                    <?php
                                    $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.title', 1);
                                    if ($sitepage->member_title && $memberTitle) :
                                        ?>
                                        <div class="member_count">
                                            <?php echo $sitepage->member_count . ' ' . ucfirst($sitepage->member_title); ?>
                                        </div>
                                    <?php else : ?>
                                        <div class="member_count">
                                            <?php echo $this->translate(array('%s ' . ucfirst('member'), '%s ' . ucfirst('members'), $sitepage->member_count), $this->locale()->toNumber($sitepage->member_count)) ?>
                                        </div> 
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if (!empty($this->statistics)) : ?>         
                                    <div class='sitepage_browse_thumb_stats seaocore_txt_light'>
                                        <?php
                                        $statistics = '';
                                        if (@in_array('likeCount', $this->statistics)) {
                                            $statistics .= $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count)) . ', ';
                                        }

                                        if (@in_array('followCount', $this->statistics)) {
                                            $statistics .= $this->translate(array('%s follower', '%s followers', $sitepage->follow_count), $this->locale()->toNumber($sitepage->follow_count)) . ', ';
                                        }

                                        if (@in_array('commentCount', $this->statistics)) {
                                            $statistics .= $this->translate(array('%s comment', '%s comments', $sitepage->comment_count), $this->locale()->toNumber($sitepage->comment_count)) . ', ';
                                        }
                                        if (@in_array('viewCount', $this->statistics)) {
                                            $statistics .= $this->translate(array('%s view', '%s views', $sitepage->view_count), $this->locale()->toNumber($sitepage->view_count)) . ', ';
                                        }
                                        $statistics = trim($statistics);
                                        $statistics = rtrim($statistics, ',');
                                        ?>
                                        <?php echo $statistics; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (@in_array('reviewCount', $this->statistics) && $this->ratngShow): ?>
                                    <div class='sitepage_browse_thumb_stats seaocore_txt_light'>
                                        <?php if ($sitepage->review_count) : ?>
                                            <?php echo $this->translate(array('%s review', '%s reviews', $sitepage->review_count), $this->locale()->toNumber($sitepage->review_count)); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <?php endif; ?>
                                        <?php if (($sitepage->rating > 0)): ?>

                                            <?php
                                            $currentRatingValue = $sitepage->rating;
                                            $difference = $currentRatingValue - (int) $currentRatingValue;
                                            if ($difference < .5) {
                                                $finalRatingValue = (int) $currentRatingValue;
                                            } else {
                                                $finalRatingValue = (int) $currentRatingValue + .5;
                                            }
                                            ?>

                                            <span class="clr" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                                                <?php for ($x = 1; $x <= $sitepage->rating; $x++): ?>
                                                    <span class="rating_star_generic rating_star" ></span>
                                                <?php endfor; ?>
                                                <?php if ((round($sitepage->rating) - $sitepage->rating) > 0): ?>
                                                    <span class="rating_star_generic rating_star_half" ></span>
                                                <?php endif; ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($this->showpostedBy) && $postedBy): ?>
                                    <div class='seaocore_browse_list_info_date'>
                                        <?php echo $this->translate('posted by'); ?>
                                        <?php echo $this->htmlLink($sitepage->getOwner()->getHref(), $sitepage->getOwner()->getTitle()) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($this->showdate)) : ?>
                                    <div class='seaocore_browse_list_info_date'>
                                        <?php echo $this->timestamp(strtotime($sitepage->creation_date)) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($this->showprice) && !empty($sitepage->price) && $this->enablePrice): ?>
                                    <div class='seaocore_browse_list_info_date'>
                                        <?php
                                        echo $this->translate("Price: ");
                                        echo $this->locale()->toCurrency($sitepage->price, $currency);
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <?php
                                if (!empty($sitepage->location) && $this->enableLocation && !empty($this->showlocation)):
                                    echo "<div class='seaocore_browse_list_info_date'>";
                                    echo $this->translate("Location: ");
                                    echo $this->translate($sitepage->location);
                                    $location_id = Engine_Api::_()->getDbTable('locations', 'sitepage')->getLocationId($sitepage->page_id, $sitepage->location);
                                    ?><?php if (!empty($this->showgetdirection)) : ?>&nbsp; - <b> <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $sitepage->page_id, 'resouce_type' => 'sitepage_page', 'location_id' => $location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')); ?> </b><?php endif; ?>
                                    <?php
                                    echo "</div>";
                                endif;
                                ?>
                            </div>
                        </div>
        <?php endforeach; ?>
                </div>
            </div>
    <?php else: ?>
            <div class="tip">
                <span>
        <?php echo $this->translate('Nobody has created a page with that criteria.') ?>
                </span>
            </div>
    <?php endif; ?>
    </div>
<?php endif; ?>
<div id="rmap_canvas_view_page" style="display: none;">
    <div class="seaocore_map clr" style="overflow:hidden;">
        <div id="rmap_canvas_page"> </div>
        <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
        <?php if (!empty($siteTitle)) : ?>
            <div class="seaocore_map_info"><?php echo "Locations on "; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
<?php endif; ?>
    </div>	

<?php if ($this->enableLocation && $this->flageSponsored && $this->map_view && $enableBouce): ?>
        <a href="javascript:void(0);" onclick="rtoggleBouncePage()" class="stop_bounce_link"> <?php echo $this->translate('Stop Bounce'); ?></a>
        <br />
<?php endif; ?>
</div>

<?php if ($this->enableLocation && $this->map_view): ?>
    <?php
    $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
    $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
    ?>

    <script type="text/javascript">
    // arrays to hold copies of the markers and html used by the side_bar
    // because the function closure trick doesnt work there
        var rgmarkersPage = [];

    // global "map" variable
        var rmap_page = null;
    // A function to create the marker and set up the event window function
        function rcreateMarkerPage(latlng, name, html, title_page) {
            var contentString = html;
            if (name == 0)
            {
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: rmap_page,
                    title: title_page,
                    animation: google.maps.Animation.DROP,
                    zIndex: Math.round(latlng.lat() * -100000) << 5
                });
            }
            else {
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: rmap_page,
                    title: title_page,
                    draggable: false,
                    animation: google.maps.Animation.BOUNCE
                });
            }
            rgmarkersPage.push(marker);
            google.maps.event.addListener(marker, 'click', function () {
                infowindow.setContent(contentString);
                google.maps.event.trigger(rmap_page, 'resize');

                infowindow.open(rmap_page, marker);

            });
        }

        function rinitializePage() {
    // create the map
            var myOptions = {
                zoom: <?php echo $defaultZoom; ?>,
                center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
                navigationControl: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }

            rmap_page = new google.maps.Map(document.getElementById("rmap_canvas_page"),
                    myOptions);

            $$("li.tab_<?php echo $this->identity ?>").addEvent('click', function () {
                google.maps.event.trigger(rmap_page, 'resize');
                rmap_page.setZoom(<?php echo $defaultZoom ?>);
                rmap_page.setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
            });

            google.maps.event.addListener(rmap_page, 'click', function () {
                infowindow.close();
                google.maps.event.trigger(rmap_page, 'resize');

            });
    <?php $textPostedBy = ''; ?>
    <?php foreach ($this->locations as $location) : ?>
        <?php if ($postedBy): ?>
            <?php $textPostedBy = $this->string()->escapeJavascript($this->translate('posted by')); ?>
            <?php $textPostedBy.= " " . $this->htmlLink($this->sitepage[$location->page_id]->getOwner()->getHref(), $this->string()->escapeJavascript($this->sitepage[$location->page_id]->getOwner()->getTitle())) ?>
        <?php endif; ?>
                // obtain the attribues of each marker
                var lat = <?php echo $location->latitude ?>;
                var lng =<?php echo $location->longitude ?>;
                var point = new google.maps.LatLng(lat, lng);
        <?php if (!empty($enableBouce)): ?>
                    var sponsored = <?php echo $this->sitepage[$location->page_id]->sponsored ?>
        <?php else: ?>
                    var sponsored = 0;
        <?php endif; ?>
                // create the marker
        <?php $page_id = $this->sitepage[$location->page_id]->page_id; ?>
                var contentString = '<div id="content">' +
                        '<div id="siteNotice">' +
                        '</div>' + '  <ul class="sitepages_locationdetails"><li>' +
                        '<div class="sitepages_locationdetails_info_title">' +
                        '<a href="<?php echo $this->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id)), 'sitepage_entry_view', true) ?>">' + "<?php echo $this->string()->escapeJavascript($this->sitepage[$location->page_id]->getTitle()); ?>" + '</a>' +
                        '<div class="firght">' +
                        '<span >' +
        <?php if ($this->sitepage[$location->page_id]->featured == 1): ?>
                    '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Featured')))) ?>' + <?php endif; ?>
                '</span>' +
                        '<span>' +
        <?php if ($this->sitepage[$location->page_id]->sponsored == 1): ?>
                    '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>' +
        <?php endif; ?>
                '</span>' +
                        '</div>' +
                        '<div class="clr"></div>' +
                        '</div>' +
                        '<div class="sitepages_locationdetails_photo" >' +
                        '<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage[$location->page_id]->page_id, $this->sitepage[$location->page_id]->owner_id, $this->sitepage[$location->page_id]->getSlug()), $this->itemPhoto($this->sitepage[$location->page_id], 'thumb.normal')) ?>' +
                        '</div>' +
                        '<div class="sitepages_locationdetails_info">' +
        <?php if (@in_array('reviewCount', $this->statistics) && $this->ratngShow): ?>
            <?php if (($this->sitepage[$location->page_id]->rating > 0)): ?>
                        '<span class="clr">' +
                <?php for ($x = 1; $x <= $this->sitepage[$location->page_id]->rating; $x++): ?>
                            '<span class="rating_star_generic rating_star"></span>' +
                <?php endfor; ?>
                <?php if ((round($this->sitepage[$location->page_id]->rating) - $this->sitepage[$location->page_id]->rating) > 0): ?>
                            '<span class="rating_star_generic rating_star_half"></span>' +
                <?php endif; ?>
                        '</span>' +
            <?php endif; ?>
        <?php endif; ?>

                '<div class="sitepages_locationdetails_info_date">' +
                        '<?php echo $this->timestamp(strtotime($this->sitepage[$location->page_id]->creation_date)) ?>' + ' - <?php echo $textPostedBy ?>' +
                        '</div>' +
        <?php if (!empty($this->statistics)) : ?>
                    '<div class="sitepages_locationdetails_info_date">' +
            <?php
            $statistics = '';
            if (@in_array('likeCount', $this->statistics)) {
                $statistics .= $this->string()->escapeJavascript($this->translate(array('%s like', '%s likes', $this->sitepage[$location->page_id]->like_count), $this->locale()->toNumber($this->sitepage[$location->page_id]->like_count))) . ', ';
            }

            if (@in_array('followCount', $this->statistics)) {
                $statistics .= $this->string()->escapeJavascript($this->translate(array('%s follower', '%s followers', $this->sitepage[$location->page_id]->follow_count), $this->locale()->toNumber($this->sitepage[$location->page_id]->follow_count))) . ', ';
            }

            if (@in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.title', 1);
                if ($this->sitepage[$location->page_id]->member_title && $memberTitle) {
                    $statistics .= $this->sitepage[$location->page_id]->member_count . ' ' . $this->string()->escapeJavascript($this->sitepage[$location->page_id]->member_title) . ', ';
                } else {
                    $statistics .= $this->string()->escapeJavascript($this->translate(array('%s member', '%s members', $this->sitepage[$location->page_id]->member_count), $this->locale()->toNumber($this->sitepage[$location->page_id]->member_count))) . ', ';
                }
            }

            if (@in_array('reviewCount', $this->statistics) && !empty($this->ratngShow)) {
                $statistics .= $this->string()->escapeJavascript($this->translate(array('%s review', '%s reviews', $this->sitepage[$location->page_id]->review_count), $this->locale()->toNumber($this->sitepage[$location->page_id]->review_count))) . ', ';
            }
            if (@in_array('commentCount', $this->statistics)) {
                $statistics .= $this->string()->escapeJavascript($this->translate(array('%s comment', '%s comments', $this->sitepage[$location->page_id]->comment_count), $this->locale()->toNumber($this->sitepage[$location->page_id]->comment_count))) . ', ';
            }
            if (@in_array('viewCount', $this->statistics)) {
                $statistics .= $this->string()->escapeJavascript($this->translate(array('%s view', '%s views', $this->sitepage[$location->page_id]->view_count), $this->locale()->toNumber($this->sitepage[$location->page_id]->view_count))) . ', ';
            }
            $statistics = trim($statistics);
            $statistics = rtrim($statistics, ',');
            ?>
                    '<?php echo $statistics; ?>' +
                            '</div>' +
        <?php endif; ?>
                '<div class="sitepages_locationdetails_info_date">' +
        <?php if (!empty($this->sitepage[$location->page_id]->phone)): ?>
                    "<?php echo $this->string()->escapeJavascript($this->translate("Phone: ")) . $this->sitepage[$location->page_id]->phone ?><br />" +
        <?php endif; ?>
        <?php if (!empty($this->sitepage[$location->page_id]->email)): ?>
                    "<?php echo $this->string()->escapeJavascript($this->translate("Email: ")) . $this->sitepage[$location->page_id]->email ?><br />" +
        <?php endif; ?>
        <?php if (!empty($this->sitepage[$location->page_id]->website)): ?>
                    "<?php echo $this->string()->escapeJavascript($this->translate("Website: ")) . $this->sitepage[$location->page_id]->website ?>" +
        <?php endif; ?>
                '</div>' +
        <?php if ($this->sitepage[$location->page_id]->price && $this->enablePrice): ?>
                    '<div class="sitepages_locationdetails_info_date">' +
                            "<?php
            echo $this->string()->escapeJavascript($this->translate("Price: "));
            echo $this->locale()->toCurrency($this->sitepage[$location->page_id]->price, $currency)
            ?>" +
                            '</div>' +
        <?php endif; ?>
                '<div class="sitepages_locationdetails_info_date">' +
                        "<?php
        echo $this->translate("Location: ");
        echo $this->string()->escapeJavascript($location->location);
        ?>" +
                        '</div>' +
                        '</div>' +
                        '<div class="clr"></div>' +
                        ' </li></ul>' +
                        '</div>';

                var marker = rcreateMarkerPage(point, sponsored, contentString, "<?php echo str_replace('"', ' ', $this->sitepage[$location->page_id]->getTitle()); ?>");
    <?php endforeach; ?>

        }

        var infowindow = new google.maps.InfoWindow(
                {
                    size: new google.maps.Size(250, 50)
                });

        function rtoggleBouncePage() {
            for (var i = 0; i < rgmarkersPage.length; i++) {
                if (rgmarkersPage[i].getAnimation() != null) {
                    rgmarkersPage[i].setAnimation(null);
                }
            }
        }
    //]]>
    </script>
<?php endif; ?>

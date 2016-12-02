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
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>        
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>

<script type="text/javascript">
    var seaocore_content_type = 'siteevent';
    var seaocore_like_url = en4.core.baseUrl + 'siteevent/index/globallikes';
</script>

<?php if (count($this->paginator) > 0): ?>
    <script type="text/javascript">
        function switchDiaryView(el, viewtype) {
            var form = null;
            if ($('filter_form')) {
                form = $('filter_form');
            } else {
                form = $('diary_withoutsearch_form');
            }
            if (form.getElement('#viewType').value == viewtype)
                return;
            form.getElement('#viewType').value = viewtype;
            el.getParent('div').getElements('.siteevent_select_view_tooltip_wrapper').removeClass('active');
            el.addClass('active');
            var params = form.toQueryString();
            $('tab_icon_loading_view').removeClass('dnone');
            en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'widget/index/content_id/<?php echo sprintf('%d', $this->identity) ?>?' + params,
                data: $merge(<?php echo json_encode($this->params) ?>, {
                    format: 'html',
                    method: 'get',
                    isAjax: true
                }),
                onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                    $('tab_icon_loading_view').addClass('dnone');
                    var container = document.getElement('.layout_siteevent_diary_browse');
                    container.empty();
                    Elements.from(responseHTML).inject(container);
                    en4.core.runonce.trigger();
                    Smoothbox.bind(container);
                    var windowUrl = window.location.href.split("?")[0];
                    if (('pushState' in window.history)) {
                        window.history.pushState(null, null, windowUrl + '?' + params);
                    }
                    if (document.getElement('.paginationControl')) {
                        document.getElement('.paginationControl').getElements('a').each(function(el) {
                            el.set('href', el.get('href').replace(en4.core.baseUrl + 'widget/index/content_id/<?php echo sprintf('%d', $this->identity) ?>', windowUrl));
                        });
                    }
                }
            }), {
                'force': true
            });
        }
        en4.core.runonce.add(function() {
            if ($('filter_form')) {
                $('filter_form').getElement('#viewType').value = $('diary_withoutsearch_form').getElement('#viewType').value;
            }
        });
    </script>
    <form id="diary_withoutsearch_form" method="post">
        <input type="hidden" name="viewType" value="<?php echo $this->formValues['viewType'] ?>" id="viewType">
    </form>
    <div class="siteevent_browse_lists_view_options b_medium">
        <div class="fleft">
            <?php echo $this->translate(array('%s event diary found.', '%s event diaries found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
        </div>
        <?php if (count($this->viewTypes) > 1): ?>
            <?php if (in_array('list', $this->viewTypes)): ?>
                <span class="seaocore_tab_select_wrapper fright <?php if ($this->formValues['viewType'] == 'list'): ?> active <?php endif; ?>">
                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
                    <span class="seaocore_tab_icon tab_icon_list_view" onclick="switchDiaryView(this, 'list');" ></span>
                </span>
            <?php endif; ?>
            <?php if (in_array('grid', $this->viewTypes)): ?>
                <span class="seaocore_tab_select_wrapper fright <?php if ($this->formValues['viewType'] == 'grid'): ?> active <?php endif; ?>">
                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Pinboard View"); ?></div>
                    <span class="seaocore_tab_icon tab_icon_pin_view" onclick="switchDiaryView(this, 'grid');" ></span>
                </span>
            <?php endif; ?>
            <span class="fright dnone mright5" id="tab_icon_loading_view">
                <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/loading.gif' ?>" />
            </span>
        <?php endif; ?>
    </div>
    <?php if ($this->formValues['viewType'] == 'list'): ?>
        <ul class='seaocore_browse_list'>
            <?php foreach ($this->paginator as $diary): ?>
                <li>
                    <div class='seaocore_browse_list_photo'>
                        <?php echo $this->htmlLink($diary->getHref(), $this->itemPhoto($diary->getCoverItem(), 'thumb.normal')) ?>
                    </div>

                    <div class="seaocore_browse_list_info">
                        <div class="seaocore_browse_list_info_title">

                            <p><?php echo $this->htmlLink($diary->getHref(), $diary->title) ?></p>
                        </div>
                        <div class="seaocore_browse_list_info_date">
                            <?php echo $this->translate('%s - created by %s', $this->timestamp($diary->creation_date), $diary->getOwner()->toString()) ?>
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

                                $statistics = trim($statistics);
                                $statistics = rtrim($statistics, ',');
                                ?>
                                <?php echo $statistics; ?>
                            </div>
                        <?php endif; ?>             

                        <?php if (Engine_Api::_()->authorization()->isAllowed($diary, null, "view") && !empty($diary->body)): ?>
                            <div class='seaocore_browse_list_info_blurb'>
                                <?php echo $diary->body; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ($this->formValues['viewType'] == 'grid'): ?>
        <ul class='siteevent_diary_browse_grid'>
            <?php foreach ($this->paginator as $diary): ?>
                <li>
                    <div>
                        <div class="siteevent_diary_contener b_medium">
                            <div class="item_holder">
                                <div class="item_cover">
                                    <?php echo $this->itemPhoto($diary->getCoverItem(), 'thumb.profile'); ?>
                                </div>
                                <div class="item_thumbs">
                                    <?php
                                    $lists = $diary->getDiaryMap(array('limit' => $this->listThumbsValue, 'orderby' => 'random'));
                                    $count = $lists->getTotalItemCount();
                                    ?>
                                    <?php foreach ($lists as $siteevent): ?>
                                        <?php echo $this->itemPhoto($siteevent, 'thumb.profile'); ?>
                                    <?php endforeach; ?>
                                    <?php for ($i = ($this->listThumbsValue - $count); $i > 0; $i--): ?>
                                        <span class="empty"></span>
                                <?php endfor; ?>
                                </div>
                                <?php echo $this->htmlLink($diary->getHref(), '', array('class' => 'diarylink')); ?>
                            </div>

                        </div>
                        <div class="siteevent_diary_title">
                            <?php echo $this->htmlLink($diary->getHref(), $diary->title) ?>
                        </div>
                        <div class="siteevent_diary_stats seaocore_txt_light bold mbot10">
                        	<i></i>
                          <div><?php echo $diary->total_item; ?></div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <div class="seaocore_pagination">
        <?php echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues, 'pageAsQuery' => true)); ?>
    </div>
<?php elseif ($this->isSearched > 2): ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('Nobody has created a diary with that criteria.'); ?>
            <?php if ($this->can_create): ?>
                <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a class="smoothbox" href="' . $this->url(array('action' => 'create'), "siteevent_diary_general") . '">', '</a>'); ?>
    <?php endif; ?>  
        </span>
    </div>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('Nobody has created a diary yet.'); ?>
            <?php if ($this->can_create): ?>
                <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a class="smoothbox" href="' . $this->url(array('action' => 'create'), "siteevent_diary_general") . '">', '</a>'); ?>
    <?php endif; ?>    
        </span>
    </div>
<?php endif; ?>

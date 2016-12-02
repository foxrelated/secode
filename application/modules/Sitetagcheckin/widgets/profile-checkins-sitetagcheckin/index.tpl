<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->show_map == 2) : ?>

  <div id="sitetagcheckin_profile_items" class="stcheckin_feeds">
    <?php
      echo $this->SitetagcheckinActivityLoop($this->actions, array(
        'show_map' => 2,
        'sitetagcheckin_id' => 'profile_checkins',
        'isajax' => 0,
        'getUpdate' => $this->getUpdate,
        'noList' => $this->noList
      ));
    ?>
    <script type="text/javascript">
      var feedPage = <?php echo sprintf('%d', $this->actions->getCurrentPageNumber()) ?>;
      var paginateProfileFeeds = function(page) 
      {
        $('show-background-pagination-image').style.display = "block";
        var url = en4.core.baseUrl + 'widget/index/mod/sitetagcheckin/name/profile-checkins-sitetagcheckin';	
        en4.core.request.send(new Request.HTML({
          'url' : url,
          'data' : {
            'format' : 'html',
            'subject' : en4.core.subject.guid,
            'isajax' : '1',
            'page' : page, 
            'show_map' : '2'
          },
          onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            location.hash = 'sitetagcheckin_profile_items';
            Smoothbox.bind($("sitetagcheckin_profile_items"));
            en4.core.runonce.trigger();
          }
        }), {
          'element' : $('sitetagcheckin_profile_items')
        });
      }
    </script>

    <?php if ($this->actions->getTotalItemCount() > 1): ?>
      <div>
        <?php if ($this->actions->getCurrentPageNumber() > 1): ?>
          <div id="sitetagcheckin_members_previous" class="paginator_previous">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                'onclick' => 'paginateProfileFeeds(feedPage - 1)',
                'class' => 'buttonlink icon_previous'
            ));
            ?>
          </div>
        <?php endif; ?>
        <?php if ($this->actions->getCurrentPageNumber() < $this->actions->count()): ?>
          <div id="sitetagcheckin_members_next" class="paginator_next">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                'onclick' => 'paginateProfileFeeds(feedPage + 1)',
                'class' => 'buttonlink_right icon_next'
            ));
            ?>
          </div>
        <?php endif; ?>
      </div>
			<div id="show-background-pagination-image" style="display:none"> 
				<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitetagcheckin/externals/images/loading.gif" /></center>
			</div>
    <?php endif; ?>
  </div>

<?php endif; ?>
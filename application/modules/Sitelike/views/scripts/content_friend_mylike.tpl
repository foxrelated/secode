<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_friend_mylike.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
if (!empty($this->paginator))
{
	$currentpage_no  = $this->paginator->getCurrentPageNumber();
}
else
{
	$currentpage_no =  0;
}
?>
<a id="dynamic_app_like_anchor" class="pabsolute"></a>

<?php if (empty($this->isajaxrequest))	{ ?>
	<script type="text/javascript">
		var active_tab = '<?php echo $this->activetab;?>';
	</script>
<?php } ?>

<script type="text/javascript">
	var applikepage = <?php echo sprintf('%d', $currentpage_no) ?>;
	var appname = '<?php echo $this->appname;?>';
	var url = '<?php echo $this->url(array('module' => 'sitelike', 'controller' => 'index', 'action' => $this->urlAction), 'default', true) ?>';
</script>

<?php if (empty($this->isajaxrequest)) { ?>
	<div class="headline">
		<h2><?php echo $this->translate('Likes');  ?></h2>
		<div class='tabs'>
			<?php echo $this->navigation($this->navigation)->render() ?>
		</div>
	</div>
<?php }
if (($this->urlAction == 'mycontent' || $this->urlAction == 'mylikes') && Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'suggestion'))
{
	//THIS FILE USE FOR SUGGESTION LINK SHOW ON THE "MY CONTENT OR MY LIKES" TAB.
	$this->member_sugg = Engine_Api::_()->suggestion()->getModSettings('user', 'link');
	$this->group_sugg = Engine_Api::_()->suggestion()->getModSettings('group', 'link');
	$this->classified_sugg = Engine_Api::_()->suggestion()->getModSettings('classified', 'link');
	$this->video_sugg = Engine_Api::_()->suggestion()->getModSettings('video', 'link');
	$this->blog_sugg = Engine_Api::_()->suggestion()->getModSettings('blog', 'link');
	$this->album_sugg = Engine_Api::_()->suggestion()->getModSettings('album', 'link');
	$this->music_sugg = Engine_Api::_()->suggestion()->getModSettings('music', 'link');
	$this->poll_sugg = Engine_Api::_()->suggestion()->getModSettings('poll', 'link');
	$this->forum_sugg = Engine_Api::_()->suggestion()->getModSettings('forum', 'link');
	$this->event_sugg = Engine_Api::_()->suggestion()->getModSettings('event', 'link');
	$this->list_sugg = Engine_Api::_()->suggestion()->getModSettings('list', 'link');
	$this->recipe_sugg = Engine_Api::_()->suggestion()->getModSettings('recipe', 'link');

	$this->page_sugg = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'link');
	$this->pagemusic_sugg = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'music_sugg_link');
	$this->pagepoll_sugg = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'poll_sugg_link');
	$this->pagevideo_sugg = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'video_sugg_link');
	$this->pageevent_sugg = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'event_sugg_link');
	$this->pagereview_sugg = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'review_sugg_link');
	$this->pagealbum_sugg = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'album_sugg_link');
	$this->pagenote_sugg = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'note_sugg_link');
	$this->pagedocument_sugg = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'document_sugg_link');
}
$show_like_button = 0;
include_once APPLICATION_PATH . '/application/modules/Sitelike/views/scripts/_my-friends-likes.tpl';
?>
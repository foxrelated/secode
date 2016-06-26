<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<?php
$event = $this->event;
$eventPhotoUrl = $event->getPhotoUrl();
$owner = $event->getOwner();
if(!$eventPhotoUrl)
  $eventPhotoUrl = $this->layout()->staticBaseUrl ."application/modules/Heevent/externals/images/event-cover-nophoto.gif";
?>
<div id='heevent_cover' class="heevent-block">
  <div class="cover-wrapper">
    <?php if($event->authorization()->isAllowed(null, 'photo')){ ?>
      <button class="heevent-hover-fadein heevent-abs-btn heevent-abs-btn-right" onclick="Smoothbox.open('<?php echo $this->url(array('controller' => 'photo', 'action' => 'upload','subject' => $event->getGuid(), 'format' => 'smoothbox'), 'event_extended') ?>')"><?php echo $this->translate('HEEVENT_Add photos'); ?></button>
    <?php } ?>
    <img class="fake-img" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Heevent/externals/images/fake-29x8.gif" style="<?php echo $event->getCoverBgStyle() ?>background-image: url(<?php echo $eventPhotoUrl?>)">
    <div class="events_author heevent-hover-fadein">
      <a class="owner_icon wall_liketips wp_init" href="<?php echo $owner->getHref() ?>"
         title="<?php echo $owner->getTitle() ?>"
         style="background-image: url(<?php echo $owner->getPhotoUrl('thumb.normal') ? $owner->getPhotoUrl('thumb.normal') : 'application/modules/User/externals/images/nophoto_user_thumb_icon.png' ?>)"></a>
      <a class="owner_name" href="<?php echo $owner->getHref()?>"><?php echo $owner->getTitle() ?></a>
    </div>
  </div>
  <div class="heevent-widget">
    <h2><?php echo $event->getTitle() ?></h2>
    <?php if($this->viewer()->getIdentity()){ ?>
    <div class="events_action heevent-widget-inner">
      <?php if($this->hasLike){ ?>
      <button id="heevent-like-btn" class="like like_btn" value="2" name="like" toggle-href="<?php echo $this->likeToggleHref; ?>"><i class="hei hei-thumbs-<?php echo !$this->isLiked ? 'up' : 'down' ?>"></i> <?php echo !$this->isLiked ? $this->translate('Like') : $this->translate('Unlike') ?></button>
      <?php } ?>
      <button class="share abs-btn" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'activity','controller' => 'index','action' => 'share','type' => $event->getType(),'id' => $event->getIdentity(),'format' => 'smoothbox'), 'default', true) ?>')"><?php echo $this->translate('Share'); ?> <i class="hei hei-share-alt"></i></button>
    </div>
      <?php } ?>
  </div>
</div>
<?php if($this->hasLike){ ?>
<script type="text/javascript">
  en4.core.runonce.add(function (){
    var likeBtn = $('heevent-like-btn');
//    console.log(likeBtn);
    likeBtn._toggleText = '<?php echo $this->isLiked ? $this->translate('Like') : $this->translate('Unlike') ?>';

    likeBtn._href = '<?php echo $this->likeHref; ?>';
    likeBtn._toggleHref = '<?php echo $this->likeToggleHref; ?>';
    likeBtn._isLiked = <?php echo $this->isLiked ? 'true' : 'false'; ?>;
    likeBtn.addEvent('click', function(e){
      var params = {
        url:likeBtn._href,
        data:{},
        success:function (response) {
//          console.log(response);
        },
        error:function (error) {
//          console.log(error);
        }
      };
      _hem.post(params);
      var toggleHref = likeBtn._toggleHref;
      likeBtn._toggleHref = likeBtn._href;
      likeBtn._href = toggleHref;

      var toggleText = likeBtn._toggleText;
      likeBtn._toggleText = likeBtn.get('text');
      var icon = 'hei hei-thumbs-' + (likeBtn._isLiked ? 'up' : 'down');
      likeBtn.set('html', '<i class="' + icon  + '"></i> ' + toggleText);
      likeBtn._isLiked = !likeBtn._isLiked;
    });
  });

</script>
<?php } ?>

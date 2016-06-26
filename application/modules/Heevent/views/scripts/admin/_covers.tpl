<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _covers.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<div class="heevent-admin-covers-upload">
  <input id="covers" type="file" multiple="multiple" accept="image/*" name="covers[]">
  <button style="float:left;" class="btn cancel" name="cancel" id="cancel_upload" type="button"><i class="hei hei-reply"></i> <?php echo $this->translate('Cancel') ?></button>
  <button style="float: right;" class="btn submit" name="go" id="submit" type="submit"><i class="hei hei-plus"></i> <?php echo $this->translate('HEEVENT_Add Selected') ?></button>
  <button class="btn upload" name="upload" id="upload" type="button"><i class="hei hei-upload"></i> <?php echo $this->translate('Upload') ?></button>
</div>
<div class="heevent-admin-cover empty">
  <img class="fake-img" style="background-repeat: <?php echo $this->bgRepeat ?>"
       src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Heevent/externals/images/fake-29x8.gif"/>
</div>
<div id="heevent-admin-nocover" class="heevent-admin-nocover" style="display: none;">
  <h1><?php echo $this->translate('HEEVENT_There are no cover photos for this category') ?></h1>
  <img class="fake-img"
       src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Heevent/externals/images/fake-29x8.gif"/>
</div>
<div class="heevent-admin-covers" id="heevent-admin-covers">
  <?php if (count($this->covers)) { ?>
  <?php foreach ($this->covers as $cover) { ?>
    <div cat-id="<?php echo $cover->category_id ?>" cover-id="<?php echo $cover->photo_id ?>" order="<?php echo $cover->order ?>" class="heevent-admin-cover cover-item">
      <div class="hev-admin-abs-btn-wrap">
        <button id="heevent-admin-delete-cover" class="btn delete" title="<?php echo $this->translate('Delete'); ?>" onclick="return false;"><i class="hei hei-times"></i></button>
      </div>
      <img class="fake-img" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Heevent/externals/images/fake-29x8.gif" style="background-image: url(<?php echo $cover->getPhotoUrl(); ?>); background-repeat: <?php echo $this->bgRepeat ?>"/>
    </div>
    <?php } ?>
  <?php } else { ?>
  <div class="heevent-admin-nocover">
    <h1><?php echo $this->translate('HEEVENT_There are no cover photos for this category') ?></h1>
    <img class="fake-img"
         src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Heevent/externals/images/fake-29x8.gif"/>
  </div>
  <?php } ?>
</div>
<script type="text/javascript">
  <?php if ($this->format != 'html') { ?>
    window.addEvent('domready', function(){
    <?php } ?>
    (function () {
      _initUploader();
      var h = $('heevent-admin-themelist').getHeight() - 69;
      var covers = $('heevent-admin-covers');
      covers.setStyle('max-height', h + 'px');
    <?php if (count($this->covers)) { ?>
      new Sortables(covers, {
        revert:{ duration:500, transition:'elastic:out' },
        clone:true,
        onSort: function(element, clone){
          clone.removeClass('cover-item');
        },
        onComplete:function (element) {
          reorder(covers.getElements('.cover-item'));
        }
      });
      <?php } ?>
//      if(window.reorder)
//        delete window.reorder;


    })();

  <?php if ($this->format != 'html') { ?>
  });
  function reorder(items){
    var orderData = {};
    var len = items.length;
    if(!len){
      var noCoverEl = $('heevent-admin-nocover').clone();
      noCoverEl.set('id', '');
      noCoverEl.show();
      $('heevent-admin-covers').grab(noCoverEl);
      return;
    }
    for (var i = 0; i < len; i++) {
      var item = items[i];
      item.set('order', i + '');
      orderData[parseInt(item.get('cover-id'))] = i;
    }
    new Request.JSON({
        'url': '<?php echo $this->url(array('action' => 'cover-order'), 'admin_default') ?>',
        'method': 'post',
        'data': {format:'json', orders:orderData},
        'evalScripts' : false,
        'onSuccess': function (response){

        }
    }).send();
  }

    <?php } ?>
</script>
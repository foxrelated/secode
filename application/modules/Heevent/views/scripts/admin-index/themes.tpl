<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: themes.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<?php if ($this->format == 'html') { ?>
<?php echo $this->render('admin/_covers.tpl') ?>
<?php } else { ?>
  <?php
  $this->headScript()
      ->appendFile( $this->baseUrl() . '/application/modules/Heevent/externals/scripts/manager.js');
//  $this->headLink()
//          ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Heevent/externals/styles/main.css');
  ?>
<h2><?php echo $this->translate("HEEVENT_Advanced Events Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
    <form method="post" class="heevent-admin-form" enctype="multipart/form-data">
      <div>
        <h3> <?php echo $this->translate("HEEVENT_Event Category Covers") ?> </h3>

        <?php if (count($this->categories) > 0): ?>
        <div class="heevent-admin-themelist-wrapper">
          <table id="heevent-admin-themelist" class='admin_table heevent-admin-themelist'>
            <thead>
            <tr>
              <th><?php echo $this->translate("Categories") ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td>
                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'event', 'controller' => 'settings', 'action' => 'add-category'), $this->translate('Add New Category'), array('class' => 'smoothbox buttonlink')) ?>
              </td>
            </tr>
              <?php foreach ($this->categories as $category): ?>
              <?php
              $class = 'ajax';
              if ($category->category_id == $this->category_id) {
                $class .= ' active';
              }
              ?>
            <tr>
              <td>
                <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'heevent', 'controller' => 'index', 'action' => 'themes', 'id' => $category->category_id),
                $category->title,
                array('class' => $class,
                )) ?>
              </td>
            </tr>

              <?php endforeach; ?>
            </tbody>
          </table>
          <div id="heevent-admin-covers-wrapper" class="heevent-admin-covers-wrapper">
            <?php echo $this->render('admin/_covers.tpl') ?>
          </div>
        </div>
        <?php else: ?>
        <br/>
        <div class="tip">
          <span><?php echo $this->translate("There are currently no categories.") ?></span>
        </div>
        <?php endif;?>
        <br/>


      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
  function _initUploader() {
    if (window.URL && window.URL.createObjectURL) {
      var handleFileSelect = function (evt) {
        var files = evt.target.files; // FileList object
        var len = files.length;
//        console.log(evt.target.files);
//        console.log(window.URL.createObjectURL(evt.target.files[0]));
        var coversEl = $('heevent-admin-covers');
        var coverTpl = $('heevent-admin-covers-wrapper').getElement('.heevent-admin-cover');
        var scrollHeight = coversEl.scrollHeight;
        for (var i = 0; i < len; i++) {
          var file = evt.target.files[i];
          var coverEl = coverTpl.cloneNode(true);
          coverEl.removeClass('empty');
          coverEl.addClass('pre-upload');
          var blobUrl = window.URL.createObjectURL(file);
          coverEl.getElement('img').setStyle('background-image', ['url("', blobUrl, '")'].join(''));
          _hem.getImageDimensions(blobUrl, (function(cover){ return function(size) {
              if (Math.abs((size.width * 8) / (size.height * 29) - 1) < .3333 ||
                  (size.width > 1092 &&
                  size.height > 301 &&
                  size.width > size.height) ||
                  (size.width / size.height > 29/8)
              ) {
                cover.getElement('img').setStyle('background-size', 'cover');
              }
            }})(coverEl));
          coversEl.grab(coverEl);
//          console.log(file);
//          console.log(window.URL.createObjectURL(file));
        }
        coversEl.scrollTop = scrollHeight;
        $('submit').show();
        $('upload').hide();
        $('cancel_upload').show();
      };
      document.getElementById('covers').addEvent('change', handleFileSelect, false);
    }
  }

  function deleteCover(element){
    if(confirm('<?php echo $this->translate('HEEVENT_Are you sure to delete this cover photo?') ?>')){
      if(element){
        element.hide();
        new Request.JSON({
            'url': '<?php echo $this->url(array('module' => 'heevent', 'controller' => 'index', 'action' => 'cover-delete'), 'admin_default', true) ?>',
            'method': 'post',
            'data': {
              format:'json',
              id:parseInt(element.get('cat-id')),
              photo_id:parseInt(element.get('cover-id'))
            },
            'onSuccess': function (response){
              if(response.status){
                element.destroy();
                reorder($$('.heevent-admin-covers .cover-item'));
              }else{
                alert(response.error);
              }
            }
        }).send();
      }
    }
  }
  window.addEvent('domready', function (e) {
//    _initUploader();
    $$('a.ajax').addEvent('click', function(e){
      e.preventDefault();
      $('heevent-admin-themelist').getElement('a.active').removeClass('active');
      $('heevent-admin-covers-wrapper').setStyle('opacity', '.4');
      this.addClass('active');
      var self = this;
      new Request.HTML({
          'url': this.href,
          'method': 'get',
          'data': {format:'html'},
          'evalScripts' : false,
          'onComplete': function (responseTree, responseElements, responseHTML, responseJavaScript){
            $('heevent-admin-covers-wrapper').setStyle('opacity', '1').innerHTML = responseHTML;
            $$('form').set('action', self.href);
            eval(responseJavaScript);
          }
      }).send();
      return false;
    });
    $('heevent-admin-covers-wrapper').addEvent('click', function(e){
      if (e.target.tagName == 'BUTTON') {
        var btn = $(e.target);
        if(btn.hasClass('delete'))
          deleteCover(btn.getParent('.cover-item'));
        else if(btn.hasClass('upload')){
          _hem.fireEvent(document.getElementById('covers'), 'click');
        } else if(btn.hasClass('cancel')){
          $('submit').hide();
          $('upload').show();
          btn.hide();
          var coversEl = $('heevent-admin-covers');
          coversEl.getElements('.pre-upload').destroy();
          coversEl.scrollTop = 0
        }
      }
    });
  });

</script>
<?php } ?>
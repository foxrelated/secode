<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: viewmoreresults.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (empty($this->is_ajax)) : ?>
  <script type="text/javascript">
    function viewMore()
    { 
      $('seaocore_view_more').style.display = 'none';
      $('loding_image').style.display = '';
      var params = {
        requestParams:<?php echo json_encode($this->params) ?>
      };
      setTimeout(function() {
        en4.core.request.send(new Request.HTML({
          method: 'get',
          'url': url,
          data: $merge(params.requestParams, {
            format: 'html',
            subject: en4.core.subject.guid,
            page: getNextPage(),
            isajax: 1,
            user_id: '<?php echo $this->user_id;?>',
            show_content: '<?php echo $this->showContent;?>',
            loaded_by_ajax: true
          }),
          evalScripts: true,
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('hideResponse_div').innerHTML = responseHTML;
            $('list_view').getElement(ulClass).innerHTML = $('list_view').getElement(ulClass).innerHTML + $('hideResponse_div').getElement(ulClass).innerHTML;
            $('hideResponse_div').innerHTML = '';
            $('loding_image').style.display = 'none';
          }
        }));
      }, 800);

      return false;
    }
  </script>
<?php endif; ?>

<?php if ($this->showContent == 3): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      $('seaocore_view_more').style.display = 'block';
      hideViewMoreLink('<?php echo $this->showContent; ?>');
    });</script>
<?php elseif ($this->showContent == 2): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      $('seaocore_view_more').style.display = 'block';
      hideViewMoreLink('<?php echo $this->showContent; ?>');
    });</script>
<?php else: ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      $('seaocore_view_more').style.display = 'none';
    });
  </script>
  <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "siteadvsearch"), array("query" => $this->formValues)); ?>
<?php endif; ?>

<script type="text/javascript">

  function getNextPage() {
    return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
  }

  function hideViewMoreLink(showContent) {

    if (showContent == 3) {
      $('seaocore_view_more').style.display = 'none';
      var totalCount = '<?php echo $this->paginator->count(); ?>';
      var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

      function doOnScrollLoadPage()
      {
        if (typeof($('scroll_bar_height').offsetParent) != 'undefined') {
          var elementPostionY = $('scroll_bar_height').offsetTop;
        } else {
          var elementPostionY = $('scroll_bar_height').y;
        }
        if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {
          if ((totalCount != currentPageNumber) && (totalCount != 0))
            viewMore();
        }
      }
      
      window.onscroll = doOnScrollLoadPage;

    }
    else if (showContent == 2)
    {
      var view_more_content = $('seaocore_view_more');
      view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
      view_more_content.removeEvents('click');
      view_more_content.addEvent('click', function() {
        viewMore();
      });
    }
  }
</script>

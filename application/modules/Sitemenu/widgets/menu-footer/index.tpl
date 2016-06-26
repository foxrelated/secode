<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php echo $this->translate('Copyright &copy;%s', date('Y')) ?>
<?php
foreach ($this->navigation as $item):
  $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
      'reset_params', 'route', 'module', 'controller', 'action', 'type',
      'visible', 'label', 'href'
  )));
  ?>
  &nbsp;-&nbsp; <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
<?php endforeach; ?>
<?php if ($this->is_language): ?>
  <?php echo $this->content()->renderWidget('sitemenu.language-sitemenu'); ?>
<?php endif; ?>

<?php
switch ($this->showOption) {
  case 2:
    ?>
    <div class="fright">
      <ul class="socialshare_links">
        <li>
    <?php if (!empty($this->facebook_url)) : ?>
            <a target="_blank" href="<?php echo $this->facebook_url ?>">
            <?php if (!empty($this->facebook_default_icon)) : ?>
                <img onmouseover="this.src = '<?php if (!empty($this->facebook_hover_icon)) : echo $this->facebook_hover_icon;
        endif;
              ?>';" onmouseout="this.src = '<?php echo $this->facebook_default_icon; ?>';" src="<?php echo $this->facebook_default_icon ?>" title="<?php echo $this->facebook_title ?>" height="32px" width="32px" />
                   <?php else: ?>
                <span class="sitemenu_sociallinks"><?php echo $this->facebook_title ?></span>
              <?php endif; ?>
            </a>
          <?php endif; ?>

          <?php if (!empty($this->twitter_url)) : ?>
            <a target="_blank" href="<?php echo $this->twitter_url ?>">
              <?php if (!empty($this->twitter_default_icon)) : ?>
                <img onmouseover="this.src = '<?php if (!empty($this->twitter_hover_icon)) : echo $this->twitter_hover_icon;
                endif;
                ?>';" onmouseout="this.src = '<?php echo $this->twitter_default_icon; ?>';" src="<?php echo $this->twitter_default_icon ?>" title="<?php echo $this->twitter_title ?>" height="32px" width="32px" />
                   <?php else: ?>
                <span class="sitemenu_sociallinks"><?php echo $this->twitter_title ?></span>
              <?php endif; ?>
            </a>
          <?php endif; ?>

          <?php if (!empty($this->linkedin_url)) : ?>
            <a target="_blank" href="<?php echo $this->linkedin_url ?>">
              <?php if (!empty($this->linkedin_default_icon)) : ?>
                <img onmouseover="this.src = '<?php if (!empty($this->linkedin_hover_icon)) : echo $this->linkedin_hover_icon;
        endif;
                ?>';" onmouseout="this.src = '<?php echo $this->linkedin_default_icon; ?>';" src="<?php echo $this->linkedin_default_icon ?>" title="<?php echo $this->linkedin_title ?>" height="32px" width="32px" />
              <?php else: ?>
                <span class="sitemenu_sociallinks"><?php echo $this->linkedin_title ?></span>
            <?php endif; ?>
            </a>
          <?php endif; ?>

            <?php if (!empty($this->youtube_url)) : ?>
            <a target="_blank" href="<?php echo $this->youtube_url ?>">
                   <?php if (!empty($this->youtube_default_icon)) : ?>
                <img onmouseover="this.src = '<?php if (!empty($this->youtube_hover_icon)) : echo $this->youtube_hover_icon;
                     endif;
                     ?>';" onmouseout="this.src = '<?php echo $this->youtube_default_icon; ?>';" src="<?php echo $this->youtube_default_icon ?>" title="<?php echo $this->youtube_title ?>" height="32px" width="32px" />
              <?php else: ?>
                <span class="sitemenu_sociallinks"><?php echo $this->youtube_title ?></span>
            <?php endif; ?>
            </a>
          <?php endif; ?>

            <?php if (!empty($this->pinterest_url)) : ?>
            <a target="_blank" href="<?php echo $this->pinterest_url ?>">
                   <?php if (!empty($this->pinterest_default_icon)) : ?>
                <img onmouseover="this.src = '<?php if (!empty($this->pinterest_hover_icon)) : echo $this->pinterest_hover_icon;
             endif;
             ?>';" onmouseout="this.src = '<?php echo $this->pinterest_default_icon; ?>';" src="<?php echo $this->pinterest_default_icon ?>" title="<?php echo $this->pinterest_title ?>" height="32px" width="32px" />
            <?php else: ?>
                <span class="sitemenu_sociallinks"><?php echo $this->pinterest_title ?></span>
      <?php endif; ?>
            </a>
    <?php endif; ?>

        </li>
      </ul>

        <?php
        break;
      case 3:
        ?>
      <div class="fright">
      <?php echo $this->content()->renderWidget('sitemenu.searchbox-sitemenu', array('isMainMenu' => 2, 'advancedMenuProductSearch' => 1)); ?>
      </div>
      <?php
      break;
    case 4:
      if (!empty($this->sitestoreproductEnable)) :
        ?>
        <div class="fright">
        <?php echo $this->content()->renderWidget('sitemenu.searchbox-sitemenu', array('isMainMenu' => 2, 'advancedMenuProductSearch' => 2)); ?>
        </div>
        <?php
      endif;
      break;
  }
  ?>
    <?php if (!empty($this->affiliateCode)): ?>
    <div class="affiliate_banner">
    <?php
    echo $this->translate('Powered by %1$s', $this->htmlLink('http://www.socialengine.com/?source=v4&aff=' . urlencode($this->affiliateCode), $this->translate('SocialEngine Community Software'), array('target' => '_blank')))
    ?>
    </div>
<?php endif; ?>


  <style type="text/css">
    /*Global search and Product search width setting in footer menu*/
    .layout_page_footer .sitestoreproduct-search-box .form-elements input[type="text"]{
      width: <?php echo $this->footerSearchWidth ?>px !important;
    }  
  </style>
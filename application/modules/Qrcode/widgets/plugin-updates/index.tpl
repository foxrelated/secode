<?php
/**
 * iPragmatech Solution Pvt. Ltd.
 *
 * @category   Application_Core
 * @package    Qr Code
 * @copyright  Copyright 2008-2013 iPragmatech Solution Pvt. Ltd.
 * @license    http://www.ipragmatech.com/license/
 * @author     iPragmatech
 */

?>

<div class ="iprag_description">
      <div class="company_products">
		 <a href="http://www.ipragmatech.com">	<img src= 'http://www.ipragmatech.com/wp-content/uploads/2012/10/logo.png'/></a>
	  </div>
	   <div class ="iprag_title">
			<div>
			    <a href="http://www.ipragmatech.com">
			     <h3><?php echo $this->channel['title'];?></h3> 
			    </a>
			   <p><?php echo $this->channel['description'];?>
			     <a href="http://www.ipragmatech.com">view more</a> </p>
			</div>
	     </div>
 
<div><h3 class="prod_heading"><?php echo $this->translate('Recomended products');?></h3></div>
<div class="admin_home_news">
   <?php if( !empty($this->channel) ): ?>
    <ul>
      <?php foreach( $this->channel['items'] as $item ): ?>
        <li>
          <div class="admin_home_news_date">
          <a href="<?php echo @$item['link'] ? $item['link'] : $item['guid'] ?>" target="_blank">
            <img class="product_img" src="<?php echo $item['thumb']; ?>"></img>
            </a>
           <div style="color:black;font-weight:bold;"><?php  echo $this->translate('Price: '); ?>
           <?php if ($item['price'] ==0):?>
           <?php echo "free";?><?php else:?>
           <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');?>
            <?php echo $this->locale()->toCurrency($item['price'],$currency) ?>
            <?php endif;?></div>
          </div>
          <div class="admin_home_news_info">
            <a href="<?php echo @$item['link'] ? $item['link'] : $item['guid'] ?>" target="_blank">
              <?php echo $item['title'] ?>
            </a>
            <span class="admin_home_news_blurb">
              <?php echo $this->string()->truncate($this->string()->stripTags($item['description']), 450) ?>
            </span>
          </div>
        </li>
      <?php endforeach; ?>
      <li>
        <div class="admin_home_news_date">
          &nbsp;
        </div>
        <div class="admin_home_news_info">
          &#187; <a href="http://www.ipragmatech.com/products" target ="_blank"><?php echo $this->translate("More About iPragmatch Products") ?></a>
        </div>
      </li>
    </ul>

  <?php elseif( $this->badPhpVersion ): ?>

  <div>
    <?php echo $this->translate('The news feed requires the PHP DOM extension.') ?>
  </div>

  <?php else: ?>

  <div>
    <?php echo $this->translate('There are no news items, or we were unable to fetch the news.') ?>
  </div>

  <?php endif; ?>
</div>

<?php if( false ): ?>
  <br />
  <span class="rss_fetched_timestamp">
    <?php if( $this->isCached ): ?>
      <?php echo $this->translate('Results last fetched at %1$s',
          $this->locale()->toDateTime($this->channel['fetched'])) ?>
    <?php else: ?>
      <?php echo $this->translate('Results are current') ?>
    <?php endif ?>
  </span>
<?php endif ?>


<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<h3><?php echo $this->translate( 'Likes' ) ?></h3>
<ul class="sitelike_users_block">
	<?php
		$container = 1 ;
		foreach ( $this->random_like_obj as $row_mix_fetch ) { 
			if ( $container % 3 == 1 ) : ?>
				<li><?php
			endif ;
			$show_like = 0 ;
			$item = $row_mix_fetch['object'][0];
			if (!empty($item)) {

				$itemTitle = $item->getTitle();
				if (empty ($itemTitle) && substr($row_mix_fetch['type'], -6) == "_photo") {
					$parent=$item->getParent();
					$itemTitle = $parent->getTitle();
					if(empty ($itemTitle)) {
						$parent=$parent->getParent();
						$itemTitle= $parent->getTitle();
					} else {
						$itemTitle = $itemTitle. '\'s photo';
					}
				}
				if ($row_mix_fetch['type'] == 'blog' || $row_mix_fetch['type'] == 'member') {
					$thumb_photo =  $this->htmlLink($item->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon'), array('title' => $itemTitle, 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));
				} else {
					$thumb_photo =  $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title' => $itemTitle, 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));
				}
				$title = $this->htmlLink($item->getHref(), Engine_Api::_()->sitelike()->turncation( $itemTitle), array('title' => $itemTitle, 'target' => '_parent', 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));

			}
			?>
			<?php if ( !empty( $item ) ) { 	$show_like = 1 ; ?>
				<div class="likes_member_list">
					<div class="likes_member_thumb">
						<?php if (!empty($thumb_photo)) { echo $thumb_photo; }?>
					</div>
					<div class="likes_member_name">
						<?php if (!empty($title)) { echo $title; } ?>
					</div>
				</div>
			<?php } ?>
			<?php if ( $container % 3 == 0 ) : ?>
			</li>
			<?php endif ; 
			if ( $show_like == 1 )
			$container++ ;
			} ?>
	<li>
		<div class="sitelike_users_block_links">
			<?php echo $this->htmlLink( array ( 'route' => 'like_profileuser' , 'controller' => 'index' , 'action' => 'profileuserlikes' , 'profileuser_id' => $this->profileuser_id , 'mutual' => 0 ) , $this->translate( array ( '%s like' , '%s likes' , $this->user_likes ) , $this->locale()->toNumber( $this->user_likes ) ) , array ( 'class' => 'smoothbox fleft' ) ) ;
				$loggden_user_id = $this->viewer()->getIdentity() ;
				if ( empty( $this->ownerview ) && !empty( $this->total_mutual_likes ) && !empty( $loggden_user_id ) )
				{
					echo $this->htmlLink( array ( 'route' => 'like_profileuser' , 'controller' => 'index' , 'action' => 'profileuserlikes' , 'profileuser_id' => $this->profileuser_id , 'mutual' => 1 ) , $this->translate( array ( '%s like in common' , '%s likes in common' , $this->total_mutual_likes ) , $this->locale()->toNumber( $this->total_mutual_likes ) ) , array ( 'class' => 'smoothbox fright' ) ) ;
				} ?>
		</div>
	</li>
</ul>
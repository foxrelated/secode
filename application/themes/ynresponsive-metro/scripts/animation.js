(function( $ ) {
	 $(function() {
		var width_screen = $(window).width();
		if(width_screen >= 1100)
		{
			var offset_define = 350;
			
			/* introduction */
			if($(".yn-widget-introduction").length )
			{
				var wg_introduction = $(".yn-widget-introduction").offset().top - offset_define -50;
				$('.yn-widget-introduction .wrap_col3_center').addClass("yn-hidden").viewportChecker({
					classToAdd: 'yn-show animated bounce',
					offset: wg_introduction
				});
				$('.yn-widget-introduction .wrap_col3_left').addClass("yn-hidden").viewportChecker({
					classToAdd: 'yn-show animated bounceInLeft',
					offset: wg_introduction
				});
				$('.yn-widget-introduction .wrap_col3_right').addClass("yn-hidden").viewportChecker({
					classToAdd: 'yn-show animated bounceInRight',
					offset: wg_introduction
				});
				
				 $('.yn-widget-introduction .item').hover(
					 function () {
					   $(this).find(".item-icon").addClass("animated shake");
					 }, 
					 function () {
					   $(this).find(".item-icon").removeClass("animated shake");
					 }
				 );
			}
			
			/* member */
			if($(".widget-member").offset())
			{
				var wg_member = $(".widget-member").offset().top;
				$('.widget-member').addClass("yn-hidden").viewportChecker({
					classToAdd: 'yn-show animated flipInX',
					offset: wg_introduction - 200
				});
			}
		}
	});
})(jQuery);
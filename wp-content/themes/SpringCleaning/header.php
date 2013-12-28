<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <title><?php
        if ( is_single() ) { single_post_title(); }
        elseif ( is_home() || is_front_page() ) { bloginfo('name'); }
        elseif ( is_page() ) { single_post_title(''); print ' | '; bloginfo('name'); }
        elseif ( is_404() ) { bloginfo('name'); print ' | Not Found'; }
        else { bloginfo('name'); wp_title('|'); }
    	?>
    </title>
 
    <link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" /> 
    <?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
    <?php wp_head(); ?>
 
    <link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url'); ?>" title="<?php printf( __( '%s latest posts', 'your-theme' ), wp_specialchars( get_bloginfo('name'), 1 ) ); ?>" />
    <link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="<?php printf( __( '%s latest comments', 'your-theme' ), wp_specialchars( get_bloginfo('name'), 1 ) ); ?>" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<link href="<?php bloginfo('template_directory'); ?>/js/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Overlock' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Lusitana:400,700' rel='stylesheet' type='text/css'>

<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/css/style-IE7.css" />
<![endif]-->

<!--[if lte IE 8]>
	<style>
		.portfolio-thumbs .mCS-light-thick>.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar, .portfolio-thumbs .mCS-light-thick>.mCSB_scrollTools .mCSB_dragger:hover .mCSB_dragger_bar { background:#94bac9 !important; }
    </style>
<![endif]-->

<?php wp_head(); ?>

<!--<script type="text/javascript" src="<?php bloginfo('url'); ?>/wp-content/themes/SpringCleaning/js/jquery.cycle.all.js"></script>-->
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.mCustomScrollbar.concat.min.js"></script>

<script type="text/javascript">		
	jQuery(document).ready(function($){
		
		//customized scroll bar on portfolio page
        $(".portfolio-thumbs").mCustomScrollbar({
			theme:"light-thick"	
		});
		
		//Portfolio
		//show first list item as default portfolio item when page loads
		$('ul.portfolio-thumbs li:first .portfolio-content').clone().appendTo('.portfolio-right').removeClass('hidden');
		 $('.portfolio-right').children('.portfolio-content').on({
			mouseenter: function() {
				$(this).find('.portfolio-description').removeClass('hidden');
			},
			mouseleave: function() {
				$(this).find('.portfolio-description').addClass('hidden');
			}
		  });

		
		//serve up portfolio item in right area when thumbnail is clicked
		$('.thumb-link').click(function(){
			$('.portfolio-right').children('.portfolio-content').remove();
			$(this).siblings('.portfolio-content').clone().appendTo('.portfolio-right').removeClass('hidden');			
			 $('.portfolio-right').children('.portfolio-content').on({
                mouseenter: function() {
                    $(this).find('.portfolio-description').removeClass('hidden');
                },
                mouseleave: function() {
                    $(this).find('.portfolio-description').addClass('hidden');
                }
            });

			return false;
		});
	});
</script>

<!-- Google Analytics -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-6377083-36']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</head>

<body>
<div id="wrapper" class="hfeed">
  <div id="contentWell">
    <div id="header">
        
        <a id="logo" href="<?php bloginfo('url'); ?>"><h1><?php bloginfo('name')?></h1></a>
        
        <?php wp_nav_menu(array('sort_column' => 'menu_order', 'container_class' => 'mainNav')); ?>
    </div><!-- #header -->
 
    <div id="main">
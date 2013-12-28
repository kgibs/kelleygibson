<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package HomeCooked
 * @subpackage html5
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyeleven' ), max( $paged, $page ) );

	?></title>
<link href='http://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	wp_head();
?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed">
	<header id="branding" class="shadow" role="banner">
    	<div id="inner" class="inner-shadow">
            <hgroup>
				<h1 id="site-title"><span><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><img src="<?php echo get_template_directory_uri(); ?>/images/muniserv-logo.png" alt="<?php bloginfo('name'); ?>" /></a></span></h1>
                <h2 id="site-tagline-1">A Real Business Solution for Both of Us.</h2>
                <h3 id="site-tagline-2">Connecting municipalities with the services they need.</h3>
			</hgroup>
			<div id="municipalities">
            	<a href="<?php echo site_url('/municipalities/start-here/'); ?>"><p id="municipal">Municipalities <span>Start Here</span></p></a>
            </div>
            <div id="log-share">
            	<div id="log-in">
                <?php
                    if (function_exists('theme_my_login')) {
                        theme_my_login(array('default_action'=>'login', 'logged_in_widget'=>true, 'logged_out_widget'=>true, 'show_title'=>false, 'login_template'=>'tml-widget-login-form.php', 'user_template'=>'tml-widget-user-panel.php'));
                    }
                ?>
                </div>
                <div id="share-bar" class="cf">
                    <div class="addthis_toolbox addthis_default_style">
                        <a href="http://www.addthis.com/bookmark.php?v=250&amp;username=xa-4bce44720ec3c5bc" class="addthis_button_compact link">Share</a>
                        <span class="addthis_separator">|</span>
                        <a class="addthis_button_facebook"></a>
                        <a class="addthis_button_linkedin"></a>
                        <a class="addthis_button_twitter"></a>
                        <a class="addthis_button_google_plusone_share"></a>
                    </div>
                    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4bce44720ec3c5bc"></script>
                </div>
            </div>
        </div>
	</header><!-- #branding -->
    	 
    <nav id="access" class="shadow" role="navigation">
        <div class="inner-shadow">
            <div id="search">
              <?php get_search_form(); ?>
            </div>
            <?php /* Our secondary menu. */ ?>
            <?php wp_nav_menu( array( 'theme_location' => 'secondary' ) ); ?>
         </div>    
     </nav><!-- #access -->
     <nav id="main-menu-list" role="navigation">
        <h3 class="assistive-text"><?php _e( 'Main menu', 'twentyeleven' ); ?></h3>
        <?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff. */ ?>
        <div class="skip-link"><a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to primary content', 'twentyeleven' ); ?>"><?php _e( 'Skip to primary content', 'twentyeleven' ); ?></a></div>
        <div class="skip-link"><a class="assistive-text" href="#secondary" title="<?php esc_attr_e( 'Skip to secondary content', 'twentyeleven' ); ?>"><?php _e( 'Skip to secondary content', 'twentyeleven' ); ?></a></div>
        <?php /* Our navigation menu. */ ?>
        <img id="flag" src="<?php echo get_template_directory_uri(); ?>/images/can-flag.gif" alt="Canadian flag"  />
        <div id="inner-main-menu-list">
            <img id="edge" src="<?php echo get_template_directory_uri(); ?>/images/nav-tags.png"  />
            <?php wp_nav_menu( array( 'theme_location' => 'primary', 'walker' => new ComingSoon_Walker_Nav_Menu ) ); ?>
        </div>
    </nav> 
	<div id="main" class="cf">
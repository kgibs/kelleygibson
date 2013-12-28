<?php
/**
 * Twenty Eleven functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, twentyeleven_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'twentyeleven_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package HomeCooked
 * @subpackage html5
 */

/**
 * Import any custom post types, widgets and shortcodes.
 */
require_once 'lib/cpt-consultant.php';
require_once 'lib/cpt-municipality.php';
require_once 'lib/sc-paypal-button.php';

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 584;

/**
 * Tell WordPress to run twentyeleven_setup() when the 'after_setup_theme' hook is run.
 */
add_action( 'after_setup_theme', 'twentyeleven_setup' );

if ( ! function_exists( 'twentyeleven_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override twentyeleven_setup() in a child theme, add your own twentyeleven_setup to your child theme's
 * functions.php file.
 *
 * @uses add_editor_style() To style the visual editor.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links, and Post Formats.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 */
function twentyeleven_setup() {

    // Disable admin bar for all users
    show_admin_bar(false);

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' => 'Primary Navigation',
        'footer' => 'Footer Navigation',
		'secondary' =>'Secondary Navigation',
	) );

	// Add support for a variety of post formats
	//add_theme_support( 'post-formats', array( 'aside', 'link', 'gallery', 'status', 'quote', 'image' ) );

	// This theme uses Featured Images (also known as post thumbnails)
	add_theme_support( 'post-thumbnails' );
    add_image_size('home_box_image', 220, 140);

}
endif; // twentyeleven_setup

/**
 * Sets the post excerpt length to 40 words.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 */
function twentyeleven_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'twentyeleven_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 */
function twentyeleven_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">read more</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and twentyeleven_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 */
function twentyeleven_auto_excerpt_more( $more ) {
	return ' &hellip;' . twentyeleven_continue_reading_link();
}
add_filter( 'excerpt_more', 'twentyeleven_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 */
function twentyeleven_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= twentyeleven_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'twentyeleven_custom_excerpt_more' );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function twentyeleven_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'twentyeleven_page_menu_args' );

/**
 * Register our sidebars and widgetized areas.
 */
function twentyeleven_widgets_init() {

	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'twentyeleven' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="shadow"><div class="inner-shadow">',
		'after_widget' => "</div></div></aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
    register_sidebar( array(
		'name' => __( 'Articles Sidebar', 'twentyeleven' ),
		'id' => 'sidebar-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="shadow"><div class="inner-shadow">',
		'after_widget' => "</div></div></aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
    register_sidebar( array(
		'name' => __( 'Homepage Sidebar', 'twentyeleven' ),
		'id' => 'sidebar-7',
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="shadow"><div class="inner-shadow">',
		'after_widget' => "</div></div></aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
    register_sidebar( array(
		'name' => __( 'Consultant Basic Profile Sidebar', 'twentyeleven' ),
		'id' => 'sidebar-6',
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="shadow"><div class="inner-shadow">',
		'after_widget' => "</div></div></aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Municipality Profile Sidebar', 'twentyeleven' ),
		'id' => 'sidebar-8',
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="shadow"><div class="inner-shadow">',
		'after_widget' => "</div></div></aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Footer Area One', 'twentyeleven' ),
		'id' => 'sidebar-3',
		'description' => __( 'An optional widget area for your site footer', 'twentyeleven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area Two', 'twentyeleven' ),
		'id' => 'sidebar-4',
		'description' => __( 'An optional widget area for your site footer', 'twentyeleven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area Three', 'twentyeleven' ),
		'id' => 'sidebar-5',
		'description' => __( 'An optional widget area for your site footer', 'twentyeleven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'twentyeleven_widgets_init' );

if ( ! function_exists( 'twentyeleven_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 */
function twentyeleven_content_nav( $nav_id ) {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $nav_id; ?>">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentyeleven' ); ?></h3>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyeleven' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyeleven' ) ); ?></div>
		</nav><!-- #nav-above -->
	<?php endif;
}
endif; // twentyeleven_content_nav

/**
 * Return the URL for the first link found in the post content.
 *
 * @return string|bool URL or false when no link is present.
 */
function twentyeleven_url_grabber() {
	if ( ! preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', get_the_content(), $matches ) )
		return false;

	return esc_url_raw( $matches[1] );
}

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 */
function twentyeleven_footer_sidebar_class() {
	$count = 0;

	if ( is_active_sidebar( 'sidebar-3' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-4' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-5' ) )
		$count++;

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'one';
			break;
		case '2':
			$class = 'two';
			break;
		case '3':
			$class = 'three';
			break;
	}

	if ( $class )
		echo 'class="' . $class . '"';
}

if ( ! function_exists( 'twentyeleven_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentyeleven_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 */
function twentyeleven_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
            <?php if ( $comment->comment_approved == '0' ) : ?>
                <footer class="comment-meta">
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentyeleven' ); ?></em>
					<br />
                </footer>
            <?php endif; ?>

			<div class="comment-content">
                <?php comment_text(); ?>
                <?php edit_comment_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
            </div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'twentyeleven' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for twentyeleven_comment()

if ( ! function_exists( 'twentyeleven_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 * Create your own twentyeleven_posted_on to override in a child theme
 */
function twentyeleven_posted_on() {
	printf( __( '<span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="by-author"> <span class="sep"> by </span> <span class="author vcard">%5$s</span></span>', 'twentyeleven' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		get_the_author()
	);
}
endif;

/**
 * Adds a class to the array of body classes if a singular post being displayed
 */
function twentyeleven_body_classes( $classes ) {

	if ( is_singular() && ! is_home() && ! is_page_template( 'showcase.php' ) && ! is_page_template( 'sidebar-page.php' ) )
		$classes[] = 'singular';

	return $classes;
}
add_filter( 'body_class', 'twentyeleven_body_classes' );


/**
 * Redirects users that are loading the admin for the first time to the Edit Pages page instead of the Dashboard.
 */
function redirect_dashboard() {
    if (substr($_SERVER["REQUEST_URI"], -10) == '/wp-admin/')
        wp_redirect(get_option('siteurl') . '/wp-admin/edit.php?post_type=page');
}
add_action( 'admin_init', 'redirect_dashboard' );

/**
 * Adds first-menu-item and last-menu-item classes to menus representing first and last elements in the menu.
 *
 * @return string Menu Html
 */
function add_first_and_last($output) {
    $output = preg_replace('/class="menu-item/', 'class="first-menu-item menu-item', $output, 1);
    if (strripos($output, 'class="menu-item') !== false)
        $output = substr_replace($output, 'class="last-menu-item menu-item', strripos($output, 'class="menu-item'), strlen('class="menu-item'));
    return $output;
}
add_filter('wp_nav_menu', 'add_first_and_last');

/**
 * Administer the menu item that a custom post type should fall under and add the name the custom post type in the Description attribute
 * This adds the current-menu-item class to the menu item when an item of this post type is shown
 * It also removes any parent or ancestor classes from any other items such as the Blog menu item
 *
 * @return string classes
 */
function cpt_nav_class($classes, $item) {
    $post_type = get_post_type();
    if (!empty($post_type) && $post_type != 'post' && $post_type != 'page') {
        $classes = array_filter($classes, create_function('$class', 'return ($class == "current_page_parent" || $class == "current_page_ancestor") ? FALSE : TRUE;'));
    }
    if (!empty($post_type) && $item->description != '' && strpos($item->description, $post_type) !== false) {
        array_push($classes, 'current-menu-item');
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'cpt_nav_class', 10, 2 );

/**
 * Add css file to admin
 *
 * @return string stylesheet reference
 */
function admin_css() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_template_directory_uri().'/admin.css">';
}
add_action( 'admin_head', 'admin_css' );

/**
 * Gets a snippet from a longer text based on length and won't break up words.
 *
 * @return string text
 */
function snippet($text,$length=64,$tail="...") {
    $text = trim($text);
    $txtl = strlen($text);
    if($txtl > $length) {
        for($i=1;$text[$length-$i]!=" ";$i++) {
            if($i == $length) {
                return substr($text,0,$length) . $tail;
            }
        }
        $text = substr($text,0,$length-$i+1) . $tail;
    }
    return $text;
}

/**
 * Displays the latest tweet for given Twitter oAuth info.
 *
 * @return array tweet
 */
function get_latest_tweet_array($consumerkey, $consumersecret, $accesstoken, $accesstokensecret, $twitteruser, $numtweets = 1){
    require_once("lib/twitteroauth/twitteroauth.php");
    $connection = new TwitterOAuth($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
    $tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser."&count=".$numtweets);
    return $tweets;
}

/**
 * Replace URLs with linked URLs.
 *
 * @return string text
 */
function link_the_urls($text){
    return preg_replace('/https?:\/\/[\w\-\.!~#?&=+\*\'"(),\/]+/','<a href="$0" target="_blank">$0</a>',$text);
}

/**
 * Added query_var for the use of CSS3Pie references in the stylesheet (for IE7/8 rounded corners)
 * In the stylesheet, reference the bahavior as url(?pie=true)
 *
 * @return string text
 */
function css_pie ( $vars ) {
    $vars[] = 'pie';
    return $vars;
}
add_filter( 'query_vars' , 'css_pie' ); //WordPress will now interpret the PIE variable in the url

function load_pie() {
    if ( get_query_var( 'pie' ) == "true" ) {
        header( 'Content-type: text/x-component' );
        wp_redirect( get_bloginfo('template_url').'/pie/PIE.htc' );
        exit; // Stop WordPress entirely since we just want PIE.htc
    }
}
add_action( 'template_redirect', 'load_pie' );

// Add custom walker for main nav to inject coming soon graphic.  Can be removed once no longer needed.
class ComingSoon_Walker_Nav_Menu extends Walker_Nav_Menu {
    function start_el ( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = $args->before;
        if ($item->title == 'Upload an RFP')
            $item_output .= '<img src="'.get_template_directory_uri().'/images/Coming-Soon-for-Main-Navigation.png" style="position:absolute; top:-5px; left:15px;" />';
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}












// Return shortcode for municipality join form
function municipality_profile_form_shortcode($atts) {
    global $post;
    
    extract(shortcode_atts(array(
        'thankyou' => false
	), $atts));
    
    // Add scripts/styles
    wp_enqueue_script('parsley', get_template_directory_uri() . '/js/parsley.min.js', array('jquery'));
    // The jQuery UI widget factory, can be omitted if jQuery UI is already included
    wp_enqueue_script('jquery.ui.widget', get_template_directory_uri() . '/js/jquery.ui.widget.js', array('jquery'), null, true);
    // The Iframe Transport is required for browsers without support for XHR file uploads
    wp_enqueue_script('jquery.iframe-transport', get_template_directory_uri() . '/js/jquery.iframe-transport.js', array('jquery'), null, true);
    // The File Upload plugins
    wp_enqueue_script('jquery.fileupload', get_template_directory_uri() . '/js/jquery.fileupload.js', array('jquery'), null, true);
    wp_enqueue_script('jquery.fileupload-process', get_template_directory_uri() . '/js/jquery.fileupload-process.js', array('jquery'), null, true);
    wp_enqueue_script('jquery.fileupload-validate', get_template_directory_uri() . '/js/jquery.fileupload-validate.js', array('jquery'), null, true);
    wp_enqueue_script('municipality-form-fileupload', get_template_directory_uri() . '/js/municipality-form-fileupload.js', array('jquery', 'jquery.ui.widget', 'jquery.iframe-transport', 'jquery.fileupload', 'jquery.fileupload-process', 'jquery.fileupload-validate'));
    wp_localize_script('municipality-form-fileupload', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
    
	// Get info to generate dropdown of related municipalities
	global $wpdb;
	$prefix = 'ms_';
	$sql = "SELECT * FROM ${prefix}municipalities ORDER BY ProvinceCode ASC;";
	$results = $wpdb->get_results( $sql , ARRAY_A );
	
	$muni_by_province = array();
	foreach($results as $result){
		$muni_by_province[$result['ProvinceCode']][] = $result['Municipality'];	
	}	
    ob_start();
	?>
    <script> var muni_by_province = <?php echo json_encode($muni_by_province);?>; 
    jQuery(function($){
		$('#municipality_province').change(function(e){
			e.preventDefault();
			/* Set variable to selected province in drop down */
			var thisVal = $(this).val();
			var valOptions = '<option value="">Select A Municipality</option>';
			// Loop through each of the province entries and create an html option for each
			if(typeof muni_by_province[thisVal] == 'undefined'){
				$('.muni-list').val('').parent().hide();
				return false;	
			};
			for(i=0; i < muni_by_province[thisVal].length; i++){
				valOptions += '<option value="'+muni_by_province[thisVal][i]+'">'+muni_by_province[thisVal][i]+'</option>';
			}
			$('.muni-list').html(valOptions).parent().show();
			return false;
		});
		
		$('#new_post input[type="text"]').removeClass('required');
		$('#new_post #captcha-form').addClass('required');
		$('#new_post input[type="email"]').removeClass('required');
		
		// Run the municipality check when the municipality dropdown selection is made
		$('.muni-list').change(function(event) {
			//set variable to get selected municipality to output title as title in form
			var selectedMuni = jQuery('.muni-list').val();
			//console.log(selectedMuni);
			jQuery('.selected-muni').remove();
			jQuery('input#municipality_title').val(selectedMuni);
			/* Act on the event */
			var data = {
				action: 'check_municipality',
				municipality: $(this).val()
			};
			var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
					//console.log(ajaxurl);
			jQuery.ajax({
				url:ajaxurl, 
				data: data, 
				method:'POST',
				success: function(response) {
					//console.log(response);
					if(response.exists){
						// the municipality exists.
						// response.id will work here
						jQuery('#new_post').show();
						jQuery('.cpt-exists').hide();
					} else {
						// the municipality does not exist.
						jQuery('.cpt-exists').show();
						jQuery('#new_post').hide();
					}
				}
			});
		});
	});
    
    </script>
    <?php
	
    $complete = false;
	    
    // Form submission handling
    if('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action']) &&  $_POST['action'] == "new_post") {
        $_POST = stripslashes_deep($_POST);
        // Do some minor form validation to make sure there is content
        if (empty($_SESSION['captcha']) || trim(strtolower($_POST['captcha'])) != $_SESSION['captcha']) {
            echo '<p class="error">ERROR:  Incorrect Human Verification word. Please try again.</p>';
        } else {
            //send emails
            $headers = 'From: muniSERV <info@muniserv.ca>' . "\r\n";
            add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));

            //Email admin about new municipality
            //Read in template and populate with passed variables
			
			global $fieldNames;
			$fieldNames = array(
				"municipality_contact_fname" => "First Name",
				"municipality_contact_lname" => "Last Name",
				"municipality_contact_title" => "Job Title",
				"municipality_email" => "Email",
				"municipality_phone" => "Telephone",
				"municipality_fax" => "Fax",
				"municipality_profile_image" => "Profile Image",
				"municipality_title" => "Name of Municipality",
				"municipality_address" => "Address",
				"municipality_city" => "City",
				"municipality_province" => "Province",
				"municipality_postal" => "Postal Code",
				"municipality_website" => "Website",
				"municipality_map_lat" => "Latitude",
				"municipality_map_long" => "Longitude"
			);
			
            ob_start();
            include 'lib/emails/admin-municipality-edited.php';
            $message = ob_get_contents();
            ob_end_clean();
            wp_mail(get_option('admin_email'), 'Municipality Edit Request - Approval Required', $message, $headers);

            $complete = true;
        }
    }

    if ($complete == true) :
        if ($thankyou) :
            wp_redirect($thankyou);
            exit;
        else :
        ?>
        <p>Success!  Your requested edit is being processed. The approval process may take up to 24 hours.</p>
        <p>Thank you!</p>
        <p><a href="<?php echo site_url(); ?>">Back to homepage</a></p>
        <?php 
        endif;
    else :
        
        $check_municipality_declaration[$_POST["municipality_declaration"]] = 'checked';
        $check_municipality_agree[$_POST["municipality_agree"]] = 'checked';
        // Form output
        ?>

        	<div class="cpt-fieldbox">
                <label for="municipality_province">Province:</label>
                <select id="municipality_province" name="municipality_province">
                    <option value="">- Select -</option>
                    <?php echo municipality_provinces_list(($custom)?$custom['municipality_province'][0]:$_POST['municipality_province']); ?>
                </select>
            </div>

        	<div class="cpt-fieldbox" style="display:none;">
                <label for="muni-list">Municipality:</label>
                <select id="municipality-list" class="muni-list" name="municipality-list">
                </select>
            </div>
            
            <div class="cpt-exists" style="display:none; margin-top:20px;">
            	<p>A profile for your municipality already exists. Please <a href="<?php bloginfo('url'); ?>/your-municipal-profile/‎">click here</a> to edit your profile.</p>
            </div>

        <form id="new_post" style="display:none;" name="new_post" method="post" action="" novalidate>
        	<input type="hidden" name="update-values" value="true" />
            <?php echo municipality_profile_form_fields(); ?>
            <fieldset class="round">
                <legend>Legal</legend>
                <div class="cpt-fieldbox last">
                    <label for="municipality_declaration">Declarations:</label>
                    <ul class="radio-list">
                        <li><label><input id="municipality_declaration" name="municipality_declaration" type="checkbox" value="1" <?php echo $check_municipality_declaration['1']; ?> class="required" />I have read the above information and certify that all the information provided is accurate to the best of my knowledge and ability.</label></li>
                        <li><label><input id="municipality_agree" name="municipality_agree" type="checkbox" value="1" <?php echo $check_municipality_agree['1']; ?> class="required" />I have read and agree to muniSERV's <a href="<?php echo site_url('/terms-conditions'); ?>" target="_blank">Terms & Conditions</a>.</label></li>
                    </ul>
                </div>
            </fieldset>
            <fieldset class="round">
                <legend>Human Verification</legend>
                <div id="captcha-wrapper" class="cpt-fieldbox last">
                    <label for="captcha-form">Write the following word: <a href="#" onclick="document.getElementById('captcha').src='<?php echo get_template_directory_uri(); ?>/lib/captcha.php?'+Math.random();document.getElementById('captcha-form').focus();return false;" id="change-image">Not readable? Change text.</a></label>
                    <img src="<?php echo get_template_directory_uri(); ?>/lib/captcha.php" id="captcha" width="200" height="55" /><br/>
                    <input type="text" name="captcha" id="captcha-form" title="Write the above word" autocomplete="off" class="required" />
                </div>
            </fieldset>
            <fieldset class="round">
                <legend>Final Step</legend>
                <div class="cpt-fieldbox last">
                    <input type="submit" name="submit" id="submit" value="Proceed" class="button" />
                </div>
            </fieldset>
            <input type="hidden" name="action" value="new_post" />
            <?php wp_nonce_field( 'new-post' ); ?>
        </form>
        </div>
        <div class="clear"></div>
    <?php
    endif;
    
    $widgetOutput .= ob_get_clean();
    
    return $widgetOutput;
	
}
add_shortcode('ms-municipalities-profile-form', 'municipality_profile_form_shortcode');

?>
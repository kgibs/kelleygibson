<?php
define('MEMBER_LEVEL', serialize(array('Basic','Standard','Premium')));
define('MEMBER_PRICING', serialize(array(0,1999,3499)));
define('MEMBER_TAX', serialize(array(0,259.87,454.87)));
define('MEMBER_DESC', serialize(array('muniSERV Consultant Membership - Basic','muniSERV Consultant Membership - Standard','muniSERV Consultant Membership - Premium')));
define('MEMBER_CATEGORIES', serialize(array(2,4,6)));
define('AWARD_FIELDS_COUNT', 3); //UPDATE THIS VALUE TO INCREASE OR DECREASE THE NUMBER OF AWARD OPTIONS
define('PROFILE_URL', site_url('/your-profile/'));
define('MEMBER_DISCOUNT_PRICING', serialize(array(0,999.50,1749.50)));
define('MEMBER_DISCOUNT_TAX', serialize(array(0,129.94,227.44)));
define('MEMBER_DISCOUNT_DESC', serialize(array('muniSERV Consultant Membership - Basic','muniSERV Consultant Membership Early Bird Special (50% Off) - Standard','muniSERV Consultant Membership Early Bird Special (50% Off) - Premium')));

// Ensure session has started
function consultant_start_session() {
    if(!session_id()) {
        session_start();
    }
}
add_action('init', 'consultant_start_session', 1);

/**
 * Registers a custom post type for Consultants and custom taxonomy for Consultant Categories
 */
function consultant_register() {

	$labels = array(
		'name' => _x('Consultants', 'post type general name'),
		'singular_name' => _x('Consultant', 'post type singular name'),
		'add_new' => _x('Add New', 'consultant item'),
		'add_new_item' => __('Add New Consultant'),
		'edit_item' => __('Edit Consultant'),
		'new_item' => __('New Consultant'),
		'view_item' => __('View Consultant'),
		'search_items' => __('Search Consultants'),
		'not_found' =>  __('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
        '_builtin' => false, // It's a custom post type, not built in
		'_edit_link' => 'post.php?post=%d',
		'query_var' => true,
		//'menu_icon' => get_stylesheet_directory_uri() . '/article16.png',
		'rewrite' => array('slug' => '/consultant', 'with_front' => false),
		'capability_type' => 'consultant',//custom capability
        'map_meta_cap' => true,
		'hierarchical' => false,
		'menu_position' => 5,
        'exclude_from_search' => false,
		'supports' => array('title','editor','revisions')
	  );

	register_post_type( 'consultant' , $args );
    
    // Register custom taxonomy
    $tax_labels = array(
        'name' => _x('Categories', 'taxonomy general name'),
        'singular_name' => _x('Category', 'taxonomy singular name'),
        'search_items' => __('Search Categories'),
        'popular_items' => __('Popular Categories'),
        'all_items' => __('All Categories'),
        'parent_item' => __('Parent Category'),
        'parent_item_colon' => __('Parent Category:'),
        'edit_item' => __('Edit Category'),
        'update_item' => __('Update Category'),
        'add_new_item' => __('Add New Category'),
        'new_item_name' => __('New Category Name'),
        'menu_name' => __('Categories'),
        'separate_items_with_commas' => __('Separate Categories with commas'),
        'add_or_remove_items' => __('Add or remove Categories'),
        'choose_from_most_used' => __('Choose from the most used Categories')
    );
    $tax_args = array(
		'labels' => $tax_labels,
		'public' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'consultants/category', 'hierarchical' => true, 'with_front' => false),
		'hierarchical' => true
	);
    register_taxonomy( 'consultant_categories', 'consultant', $tax_args );
    
}
add_action('init', 'consultant_register');

// Override Theme My Login's profile page link to use our custom profile page
function consultant_init() {
    if ('http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] == Theme_My_Login::get_page_link('profile')) {
        wp_redirect(PROFILE_URL);
        exit;
    }
}
add_action('init', 'consultant_init');

// Set consultant image sizes, roles, etc
function consultant_setup(){
    // New Image Sizes
    set_post_thumbnail_size( 118, 118 );
    add_image_size( 'consultant-large', 318, 250 );
    add_image_size( 'consultant-ad', 300, 171 );
    // New Role & Capabilities
    add_role('consultant', 'Consultant');
    $new_role = get_role('consultant');
    $new_caps = array(
        'read',
        'read_consultants',
        'edit_consultants',
        'edit_published_consultants',
        'publish_consultants'
    );
    foreach ($new_caps as $cap) {
      $new_role->add_cap($cap);
    }
    $admin_caps = array(
        'read',
        'read_consultants',
        'read_private_consultants',
        'edit_consultants',
        'edit_private_consultants',
        'edit_published_consultants',
        'edit_others_consultants',
        'publish_consultants',
        'delete_consultants',
        'delete_private_consultants',
        'delete_published_consultants',
        'delete_others_consultants'
    );
    $admin_role = get_role('administrator');
    foreach ($admin_caps as $cap) {
      $admin_role->add_cap($cap);
    }
}
add_action('after_setup_theme', 'consultant_setup');

// Alter the Users dropdown when selecting an Author for a post so that Premium members are available to be assigned to by the admin
function consultant_dropdown_users($output)
{
    global $wpdb, $post;
    $users = $wpdb->get_results(
        "SELECT u.ID, u.display_name FROM $wpdb->users u
        INNER JOIN $wpdb->usermeta um ON u.ID = um.user_id AND um.meta_key = 'consultant_id'
        INNER JOIN $wpdb->posts p ON p.ID = um.meta_value
        INNER JOIN $wpdb->postmeta pm ON p.ID = pm.post_id AND pm.meta_key = 'consultant_membership'
        WHERE pm.meta_value = 3
        ORDER BY u.display_name"
    );

    $output = "<select id=\"post_author_override\" name=\"post_author_override\" class=\"\">";
    $output .= "<option value=\"1\">cms-admin</option>";
    foreach($users as $user) {
        $sel = ($post->post_author == $user->ID)?"selected='selected'":'';
        $output .= '<option value="'.$user->ID.'"'.$sel.'>'.$user->display_name.'</option>';
    }
    $output .= "</select>";

    return $output;
}
add_filter('wp_dropdown_users', 'consultant_dropdown_users');

// Add WP user profile field to associate consultant to user
function consultant_profile_fk($profile_fields) {
	$profile_fields['consultant_id'] = 'Associated Consultant ID';
	return $profile_fields;
}
add_filter('user_contactmethods', 'consultant_profile_fk');

// Customize error message shown on login page when account is still disabled
function consultant_profile_disabled_message() {
    return __('Account has yet to be approved. Please try again later.', 'ja_disable_users');
}
add_filter('ja_disable_users_notice', 'consultant_profile_disabled_message');


// Remove slug from link shown in admin to make consultants be a top level page type
function consultant_remove_slug($permalink, $post, $leavename) {
	$permalink = str_replace(get_bloginfo('url') . '/consultant' , get_bloginfo('url'), $permalink);
	return $permalink;
}
add_filter('post_type_link', 'consultant_remove_slug', 10, 3);

// When we get posts, make consultants act like regular posts so they they can be a top level page type
function consultant_pre_get_posts($query) {
    global $wpdb;
 
    if(!$query->is_main_query())
        return;
 
    $post_name = $query->get('pagename');
    $post_type = '';
    if ($post_name) {
        $post_type = $wpdb->get_var(
            $wpdb->prepare('SELECT post_type FROM ' . $wpdb->posts . ' WHERE post_name = %s LIMIT 1', $post_name)
        );
    }
 
    switch($post_type) {
        case 'consultant':
            $query->set('consultant', $post_name);
            $query->set('post_type', $post_type);
            $query->is_single = true;
            $query->is_page = false;
            break;
    }
 
    return $query;
}
add_filter('pre_get_posts', 'consultant_pre_get_posts');

// Force unique slugs for all post types to avoid conflicts
function consultant_cross_type_unique_slugs( $slug, $post_ID, $post_status, $post_type, $post_parent ){
    global $wpdb, $wp_rewrite;
    //Don't touch hierarchical post types
    $hierarchical_post_types = get_post_types( array('hierarchical' => true) );
    if( in_array( $post_type, $hierarchical_post_types ) )
        return $slug;
    //Attachments are unique anyway
    if( 'attachment' == $post_type )
        return $slug;
    //Check for feeds
    $feeds = $wp_rewrite->feeds;
    if ( ! is_array( $feeds ) )
        $feeds = array();
    //Lets make sure the slug is really unique:
    $check_sql = "SELECT post_name FROM $wpdb->posts WHERE post_name = %s AND ID != %d LIMIT 1";
    $post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug, $post_ID ) );
    if ( $post_name_check || in_array( $slug, $feeds) ) {
        $suffix = 2;
        do {
            $alt_post_name = substr ($slug, 0, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
            $post_name_check = $wpdb->get_var( $wpdb->prepare($check_sql, $alt_post_name, $post_ID ) );
            $suffix++;
        } while ( $post_name_check );
        $slug = $alt_post_name;
    }
    return $slug;
}
add_filter('wp_unique_post_slug', 'consultant_cross_type_unique_slugs',10,5);


// Add scripts to edit pages for this post type
function consultant_admin_script() {
    global $post_type;
    if('consultant' == $post_type) {
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
        wp_enqueue_script('consultant-form-fileupload', get_template_directory_uri() . '/js/consultant-form-fileupload.js', array('jquery', 'jquery.ui.widget', 'jquery.iframe-transport', 'jquery.fileupload', 'jquery.fileupload-process', 'jquery.fileupload-validate'));
        wp_localize_script('consultant-form-fileupload', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
    }
}
add_action( 'admin_print_scripts-post-new.php', 'consultant_admin_script', 11 );
add_action( 'admin_print_scripts-post.php', 'consultant_admin_script', 11 );

// Add meta box to acquire custom fields
function consultant_admin_init(){
    add_meta_box("consultant_membership-meta", "Membership Level", "consultant_membership", "consultant", "side", "low");
    add_meta_box("consultant_photos-meta", "Featured Images", "consultant_photos", "consultant", "side", "low");
    add_meta_box("consultant_contact_details-meta", "Contact Details", "consultant_contact_details", "consultant", "normal", "low");
    add_meta_box("consultant_more_info-meta", "More Information", "consultant_more_info", "consultant", "normal", "low");
    add_meta_box("consultant_success_story-meta", "Success Story", "consultant_success_story", "consultant", "normal", "low");
    add_meta_box("consultant_social_media-meta", "Social Media", "consultant_social_media", "consultant", "normal", "low");
    add_meta_box("consultant_ad-meta", "Advertisement", "consultant_ad", "consultant", "normal", "low");
}
add_action("admin_init", "consultant_admin_init");

// Add class to metaboxes for styling purposes
function consultant_metabox_add_classes($classes) {
    array_push($classes,'cpt-metabox');
    return $classes;
}
add_filter('postbox_classes_consultant_consultant_membership-meta','consultant_metabox_add_classes');
add_filter('postbox_classes_consultant_consultant_photos-meta','consultant_metabox_add_classes');
add_filter('postbox_classes_consultant_consultant_contact_details-meta','consultant_metabox_add_classes');
add_filter('postbox_classes_consultant_consultant_more_info-meta','consultant_metabox_add_classes');
add_filter('postbox_classes_consultant_consultant_success_story-meta','consultant_metabox_add_classes');
add_filter('postbox_classes_consultant_consultant_social_media-meta','consultant_metabox_add_classes');
add_filter('postbox_classes_consultant_consultant_ad-meta','consultant_metabox_add_classes');

// Display code for meta boxes
function consultant_membership(){
  global $post;
  $custom = get_post_custom($post->ID);
  $check_consultant_membership[$custom["consultant_membership"][0]] = 'checked';
  ?>
    <div class="cpt-fieldbox last">
        <ul class="radio-list">
            <li><label><input id="consultant_membership_1" name="consultant_membership" type="radio" value="1" <?php echo $check_consultant_membership['1']; ?> />Basic</label></li>
            <li><label><input id="consultant_membership_2" name="consultant_membership" type="radio" value="2" <?php echo $check_consultant_membership['2']; ?> />Standard</label></li>
            <li><label><input id="consultant_membership_3" name="consultant_membership" type="radio" value="3" <?php echo $check_consultant_membership['3']; ?> />Premium</label></li>
        </ul>
    </div>
  <?php
}
function consultant_photos(){
  global $post;
  $obj_upload_dir = wp_upload_dir();
  $custom = get_post_custom($post->ID);
  $consultant_profile_image = $custom["consultant_profile_image"][0];
  $consultant_sidebar_image = $custom["consultant_sidebar_image"][0];
  ?>
    <div id="consultant_profile_image_box" class="cpt-fieldbox">
        <label for="consultant_profile_image">Profile Picture:</label>
        <div class="files consultant-profile-thumb">
            <img src="<?php echo ($consultant_profile_image) ? $obj_upload_dir['baseurl'].'/profile-pics/thumbnail/'.$consultant_profile_image : get_template_directory_uri().'/images/no-profile-picture.png'; ?>" />
        </div>
        <div class="fileinput-button">
            <input class="fileupload" type="file" name="files">
        </div>
        <div class="progress">
            <div class="progress-bar"></div>
        </div>
        <div class="files-error"></div>
        <input id="consultant_profile_image" name="consultant_profile_image" type="hidden" value="<?php echo $consultant_profile_image; ?>" />
    </div>
    <div id="consultant_sidebar_image_box" class="cpt-fieldbox">
        <label for="consultant_sidebar_image">Sidebar Picture:</label>
        <div class="files consultant-sidebar-image">
            <img src="<?php echo ($consultant_sidebar_image) ? $obj_upload_dir['baseurl'].'/profile-pics/'.$consultant_sidebar_image : get_template_directory_uri().'/images/no-sidebar-picture.png'; ?>" />
        </div>
        <div class="fileinput-button">
            <input class="fileupload" type="file" name="files">
        </div>
        <div class="progress">
            <div class="progress-bar"></div>
        </div>
        <div class="files-error"></div>
        <input id="consultant_sidebar_image" name="consultant_sidebar_image" type="hidden" value="<?php echo $consultant_sidebar_image; ?>" />
    </div>
  <?php
}
function consultant_contact_details(){
  global $post;
  $custom = get_post_custom($post->ID);
  $check_consultant_contact_prefix[$custom["consultant_contact_prefix"][0]] = 'checked';
  $consultant_contact_fname = $custom["consultant_contact_fname"][0];
  $consultant_contact_lname = $custom["consultant_contact_lname"][0];
  $consultant_contact_designations = $custom["consultant_contact_designations"][0];
  $consultant_contact_title = $custom["consultant_contact_title"][0];
  $consultant_phone = $custom["consultant_phone"][0];
  $consultant_fax = $custom["consultant_fax"][0];
  $consultant_address = $custom["consultant_address"][0];
  $consultant_city = $custom["consultant_city"][0];
  $consultant_province = $custom["consultant_province"][0];
  $consultant_postal = $custom["consultant_postal"][0];
  $consultant_map_lat = $custom["consultant_map_lat"][0];
  $consultant_map_long = $custom["consultant_map_long"][0];
  $consultant_email = $custom["consultant_email"][0];
  $consultant_website = $custom["consultant_website"][0];
  $consultant_serving = $custom["consultant_serving"][0];
  $consultant_hours = $custom["consultant_hours"][0];
  ?>
    <div class="cpt-fieldbox">
        <label for="consultant_contact_prefix_1">Contact Salutation:</label>
        <ul class="radio-list horizontal">
            <li><label><input id="consultant_contact_prefix_1" name="consultant_contact_prefix" type="radio" value="Mr." <?php echo $check_consultant_contact_prefix['Mr.']; ?> />Mr.</label></li>
            <li><label><input id="consultant_contact_prefix_2" name="consultant_contact_prefix" type="radio" value="Mrs." <?php echo $check_consultant_contact_prefix['Mrs.']; ?> />Mrs.</label></li>
            <li><label><input id="consultant_contact_prefix_3" name="consultant_contact_prefix" type="radio" value="Ms." <?php echo $check_consultant_contact_prefix['Ms.']; ?> />Ms.</label></li>
            <li><label><input id="consultant_contact_prefix_4" name="consultant_contact_prefix" type="radio" value="Miss" <?php echo $check_consultant_contact_prefix['Miss']; ?> />Miss</label></li>
            <li><label><input id="consultant_contact_prefix_5" name="consultant_contact_prefix" type="radio" value="Dr." <?php echo $check_consultant_contact_prefix['Dr.']; ?> />Dr.</label></li>
        </ul>
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_contact_fname">Contact First Name:</label>
        <input id="consultant_contact_fname" name="consultant_contact_fname" type="text" value="<?php echo $consultant_contact_fname; ?>" class="required" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_contact_lname">Contact Last Name:</label>
        <input id="consultant_contact_lname" name="consultant_contact_lname" type="text" value="<?php echo $consultant_contact_lname; ?>" class="required" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_contact_designations">Contact Designations:</label>
        <input id="consultant_contact_designations" name="consultant_contact_designations" type="text" value="<?php echo $consultant_contact_designations; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_contact_title">Contact Job Title:</label>
        <input id="consultant_contact_title" name="consultant_contact_title" type="text" value="<?php echo $consultant_contact_title; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_phone">Telephone:</label>
        <input id="consultant_phone" name="consultant_phone" type="text" value="<?php echo $consultant_phone; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_fax">Fax:</label>
        <input id="consultant_fax" name="consultant_fax" type="text" value="<?php echo $consultant_fax; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_address">Address:</label>
        <input id="consultant_address" name="consultant_address" type="text" value="<?php echo $consultant_address; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_city">Town/City:</label>
        <input id="consultant_city" name="consultant_city" type="text" value="<?php echo $consultant_city; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_province">Province:</label>
        <select id="consultant_province" name="consultant_province">
            <option value="">- Select -</option>
            <?php echo consultant_provinces_list($consultant_province); ?>
        </select>
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_postal">Postal Code:</label>
        <input id="consultant_postal" name="consultant_postal" type="text" value="<?php echo $consultant_postal; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_map_lat">Override map location with Co-ordinates:<br>
            Latitude:</label>
        <input id="consultant_map_lat" name="consultant_map_lat" type="text" value="<?php echo $consultant_map_lat; ?>" />
        <label for="consultant_map_long">Longitude:</label>
        <input id="consultant_map_long" name="consultant_map_long" type="text" value="<?php echo $consultant_map_long; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_email">Email:</label>
        <input id="consultant_email" name="consultant_email" type="text" value="<?php echo $consultant_email; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_website">Website:</label>
        <input id="consultant_website" name="consultant_website" type="text" value="<?php echo $consultant_website; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_serving">Areas Served:</label>
        <input id="consultant_serving" name="consultant_serving" type="text" value="<?php echo $consultant_serving; ?>" />
    </div>
    <div class="cpt-fieldbox last">
        <label for="consultant_hours">Hours of Operation:</label>
        <input id="consultant_hours" name="consultant_hours" type="text" value="<?php echo $consultant_hours; ?>" />
    </div>
  <?php
}
function consultant_more_info(){
  global $post;
  
  $custom = get_post_custom($post->ID);
  $consultant_tagline = $custom["consultant_tagline"][0];
  $consultant_testimonial_1 = $custom["consultant_testimonial_1"][0];
  $consultant_testimonial_1_by = $custom["consultant_testimonial_1_by"][0];
  $consultant_testimonial_2 = $custom["consultant_testimonial_2"][0];
  $consultant_testimonial_2_by = $custom["consultant_testimonial_2_by"][0];
  $consultant_testimonial_3 = $custom["consultant_testimonial_3"][0];
  $consultant_testimonial_3_by = $custom["consultant_testimonial_3_by"][0];
  $consultant_awards_other = unserialize($custom["consultant_awards_other"][0]);
  ?>
    <div class="cpt-fieldbox">
        <label for="consultant_tagline">Tagline:</label>
        <input id="consultant_tagline" name="consultant_tagline" type="text" value="<?php echo $consultant_tagline; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_testimonial_1">Testimonial 1:</label>
        <textarea id="consultant_testimonial_1" name="consultant_testimonial_1"><?php echo $consultant_testimonial_1; ?></textarea>
        <label for="consultant_testimonial_1_by">Testimonial By:</label>
        <input id="consultant_testimonial_1_by" name="consultant_testimonial_1_by" type="text" value="<?php echo $consultant_testimonial_1_by; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_testimonial_2">Testimonial 2:</label>
        <textarea id="consultant_testimonial_2" name="consultant_testimonial_2"><?php echo $consultant_testimonial_2; ?></textarea>
        <label for="consultant_testimonial_2_by">Testimonial By:</label>
        <input id="consultant_testimonial_2_by" name="consultant_testimonial_2_by" type="text" value="<?php echo $consultant_testimonial_2_by; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_testimonial_3">Testimonial 3:</label>
        <textarea id="consultant_testimonial_3" name="consultant_testimonial_3"><?php echo $consultant_testimonial_3; ?></textarea>
        <label for="consultant_testimonial_3_by">Testimonial By:</label>
        <input id="consultant_testimonial_3_by" name="consultant_testimonial_3_by" type="text" value="<?php echo $consultant_testimonial_3_by; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_awards_other_1">Other Awards, Certificates or Designations:</label>
        <?php for ($i=0; $i < AWARD_FIELDS_COUNT; $i++): ?>
        <input id="consultant_awards_other_<?php echo $i; ?>" name="consultant_awards_other[]" type="text" value="<?php echo $consultant_awards_other[$i]; ?>" />
        <?php endfor; ?>
    </div>
  <?php
}
function consultant_success_story(){
  global $post;
  $custom = get_post_custom($post->ID);
  $consultant_success_story = $custom["consultant_success_story"][0];
  ?>
    <div class="cpt-fieldbox last">
        <?php wp_editor($consultant_success_story,'consultant_success_story',array('media_buttons'=>false)); ?>
    </div>
  <?php
}
function consultant_social_media(){
  global $post;
  $custom = get_post_custom($post->ID);
  $consultant_facebook = $custom["consultant_facebook"][0];
  $consultant_twitter = $custom["consultant_twitter"][0];
  $consultant_linkedin = $custom["consultant_linkedin"][0];
  $consultant_googleplus = $custom["consultant_googleplus"][0];
  $consultant_youtube = $custom["consultant_youtube"][0];
  $consultant_rss = $custom["consultant_rss"][0];
  $consultant_twitter_username = $custom["consultant_twitter_username"][0];
  $consultant_twitter_key = $custom["consultant_twitter_key"][0];
  $consultant_twitter_secret = $custom["consultant_twitter_secret"][0];
  $consultant_twitter_token = $custom["consultant_twitter_token"][0];
  $consultant_twitter_access = $custom["consultant_twitter_access"][0];
  ?>
    <div class="cpt-fieldbox">
        <label for="consultant_facebook">Facebook Address:</label>
        <input id="consultant_facebook" name="consultant_facebook" type="text" value="<?php echo $consultant_facebook; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_twitter">Twitter Address:</label>
        <input id="consultant_twitter" name="consultant_twitter" type="text" value="<?php echo $consultant_twitter; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_linkedin">LinkedIn Address:</label>
        <input id="consultant_linkedin" name="consultant_linkedin" type="text" value="<?php echo $consultant_linkedin; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_googleplus">Google Plus Address:</label>
        <input id="consultant_googleplus" name="consultant_googleplus" type="text" value="<?php echo $consultant_googleplus; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_youtube">YouTube Channel Address:</label>
        <input id="consultant_youtube" name="consultant_youtube" type="text" value="<?php echo $consultant_youtube; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_rss">RSS Feed Address:</label>
        <input id="consultant_rss" name="consultant_rss" type="text" value="<?php echo $consultant_rss; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label>Twitter Feed Authentication Settings</label>
        These details are available in your <a href="https://dev.twitter.com/apps" target="_blank">Twitter dashboard</a>
        <br />&nbsp;
        <label for="consultant_twitter_username">Twitter Username:</label>
        <input id="consultant_twitter_username" name="consultant_twitter_username" type="text" value="<?php echo $consultant_twitter_username; ?>" />
        <label for="consultant_twitter_key">OAuth Consumer Key:</label>
        <input id="consultant_twitter_key" name="consultant_twitter_key" type="text" value="<?php echo $consultant_twitter_key; ?>" />
        <label for="consultant_twitter_secret">OAuth Consumer Secret:</label>
        <input id="consultant_twitter_secret" name="consultant_twitter_secret" type="text" value="<?php echo $consultant_twitter_secret; ?>" />
        <label for="consultant_twitter_token">OAuth Access Token:</label>
        <input id="consultant_twitter_token" name="consultant_twitter_token" type="text" value="<?php echo $consultant_twitter_token; ?>" />
        <label for="consultant_twitter_access">OAuth Access Secret:</label>
        <input id="consultant_twitter_access" name="consultant_twitter_access" type="text" value="<?php echo $consultant_twitter_access; ?>" />
    </div>
  <?php
}
function consultant_ad(){
  global $post;
  
  $obj_upload_dir = wp_upload_dir();
  $custom = get_post_custom($post->ID);
  $consultant_ad_image = $custom['consultant_ad_image'][0];
  $consultant_ad_title = $custom['consultant_ad_title'][0];
  $consultant_ad_subtitle = $custom['consultant_ad_subtitle'][0];
  $consultant_ad_blurb = $custom['consultant_ad_blurb'][0];
  ?>
    <div id="consultant_ad_image_box" class="cpt-fieldbox cf">
        <label for="consultant_ad_image">Upload your own Ad (to fill 300px by 171px):</label>
        <div class="files consultant-adspace-override" <?php if(!$consultant_ad_image) : ?>style="display:none;"<?php endif; ?>>
            <img src="<?php echo $obj_upload_dir['baseurl'].'/ad-images/'.$consultant_ad_image; ?>" />
        </div>
        <span class="fileinput-button">
            <input class="fileupload" type="file" name="files">
        </span>
        <div class="progress">
            <div class="progress-bar"></div>
        </div>
        <div class="files-error"></div>
        <button type="button" class="button green remove-button" <?php if(!$consultant_ad_image) : ?>style="display:none;"<?php endif; ?>>Remove Image</button>
        <input id="consultant_ad_image" name="consultant_ad_image" type="hidden" value="<?php echo $consultant_ad_image; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label>OR override default text with the following:</label>
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_ad_title">Title:</label>
        <input id="consultant_ad_title" name="consultant_ad_title" type="text" maxwidth="70" value="<?php echo $consultant_ad_title; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_ad_subtitle">Subtitle:</label>
        <input id="consultant_ad_subtitle" name="consultant_ad_subtitle" type="text" maxwidth="78" value="<?php echo $consultant_ad_subtitle; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="consultant_ad_blurb">Blurb:</label>
        <textarea id="consultant_ad_blurb" name="consultant_ad_blurb" rows="3"><?php echo $consultant_ad_blurb; ?></textarea>
    </div>
  <?php
}

// Save the custom fields
function consultant_save_details( $post_id ){
    // If this is an auto save, our form has not been submitted, so we don't want to do anything
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
    // Is the post type correct?
    if ( isset($_POST['post_type']) && 'consultant' != $_POST['post_type'] ) return $post_id;
    // Save meta data
    if ( isset($_POST["consultant_contact_lname"]) ) {
        //Membership
        update_post_meta($post_id, "consultant_membership", $_POST["consultant_membership"]);
        //Photos
        update_post_meta($post_id, "consultant_profile_image", $_POST["consultant_profile_image"]);
        update_post_meta($post_id, "consultant_sidebar_image", $_POST["consultant_sidebar_image"]);
        //Contact Info
        update_post_meta($post_id, "consultant_contact_prefix", $_POST["consultant_contact_prefix"]);
        update_post_meta($post_id, "consultant_contact_fname", $_POST["consultant_contact_fname"]);
        update_post_meta($post_id, "consultant_contact_lname", $_POST["consultant_contact_lname"]);
        update_post_meta($post_id, "consultant_contact_designations", $_POST["consultant_contact_designations"]);
        update_post_meta($post_id, "consultant_contact_title", $_POST["consultant_contact_title"]);
        update_post_meta($post_id, "consultant_phone", $_POST["consultant_phone"]);
        update_post_meta($post_id, "consultant_fax", $_POST["consultant_fax"]);
        update_post_meta($post_id, "consultant_address", $_POST["consultant_address"]);
        update_post_meta($post_id, "consultant_city", $_POST["consultant_city"]);
        update_post_meta($post_id, "consultant_province", $_POST["consultant_province"]);
        update_post_meta($post_id, "consultant_postal", $_POST["consultant_postal"]);
        update_post_meta($post_id, "consultant_map_lat", $_POST["consultant_map_lat"]);
        update_post_meta($post_id, "consultant_map_long", $_POST["consultant_map_long"]);
        update_post_meta($post_id, "consultant_email", sanitize_email($_POST["consultant_email"]));
        update_post_meta($post_id, "consultant_website", ($_POST["consultant_website"]=='http://')?'':$_POST["consultant_website"]);
        update_post_meta($post_id, "consultant_serving", $_POST["consultant_serving"]);
        update_post_meta($post_id, "consultant_hours", $_POST["consultant_hours"]);
        //More Info
        update_post_meta($post_id, "consultant_tagline", wp_kses($_POST["consultant_tagline"], array()));
        update_post_meta($post_id, "consultant_testimonial_1", wp_kses_post($_POST["consultant_testimonial_1"]));
        update_post_meta($post_id, "consultant_testimonial_1_by", $_POST["consultant_testimonial_1_by"]);
        update_post_meta($post_id, "consultant_testimonial_2", wp_kses_post($_POST["consultant_testimonial_2"]));
        update_post_meta($post_id, "consultant_testimonial_2_by", $_POST["consultant_testimonial_2_by"]);
        update_post_meta($post_id, "consultant_testimonial_3", wp_kses_post($_POST["consultant_testimonial_3"]));
        update_post_meta($post_id, "consultant_testimonial_3_by", $_POST["consultant_testimonial_3_by"]);
        update_post_meta($post_id, "consultant_awards_other", $_POST["consultant_awards_other"]);
        //Success Story
        update_post_meta($post_id, "consultant_success_story", wp_kses($_POST["consultant_success_story"], array('p'=>array(),'br'=>array())));
        //Social Media
        update_post_meta($post_id, "consultant_facebook", ($_POST["consultant_facebook"]=='http://')?'':$_POST["consultant_facebook"]);
        update_post_meta($post_id, "consultant_twitter", ($_POST["consultant_twitter"]=='http://')?'':$_POST["consultant_twitter"]);
        update_post_meta($post_id, "consultant_linkedin", ($_POST["consultant_linkedin"]=='http://')?'':$_POST["consultant_linkedin"]);
        update_post_meta($post_id, "consultant_googleplus", ($_POST["consultant_googleplus"]=='http://')?'':$_POST["consultant_googleplus"]);
        update_post_meta($post_id, "consultant_youtube", ($_POST["consultant_youtube"]=='http://')?'':$_POST["consultant_youtube"]);
        update_post_meta($post_id, "consultant_rss", ($_POST["consultant_rss"]=='http://')?'':$_POST["consultant_rss"]);
        update_post_meta($post_id, "consultant_twitter_username", $_POST["consultant_twitter_username"]);
        update_post_meta($post_id, "consultant_twitter_key", $_POST["consultant_twitter_key"]);
        update_post_meta($post_id, "consultant_twitter_secret", $_POST["consultant_twitter_secret"]);
        update_post_meta($post_id, "consultant_twitter_token", $_POST["consultant_twitter_token"]);
        update_post_meta($post_id, "consultant_twitter_access", $_POST["consultant_twitter_access"]);
        //Advertisement
        update_post_meta($post_id, "consultant_ad_image", $_POST["consultant_ad_image"]);
        update_post_meta($post_id, "consultant_ad_title", wp_kses($_POST["consultant_ad_title"], array()));
        update_post_meta($post_id, "consultant_ad_subtitle", wp_kses($_POST["consultant_ad_subtitle"], array()));
        update_post_meta($post_id, "consultant_ad_blurb", wp_kses($_POST["consultant_ad_blurb"], array()));
    }
}
add_action('save_post', 'consultant_save_details');

// Notify consultant when profile is published
function consultant_publish($post){
    // Is the post type correct?
    if ( wp_is_post_revision($post->ID) || !isset($post->post_type) || 'consultant' != $post->post_type ) return;
    // Enable associated user account
    $users = get_users(array('meta_key'=>'consultant_id', 'meta_value'=>$post->ID));
    if ($users && $users[0])
        update_user_meta($users[0]->ID, 'ja_disable_user', 0);
    // If we're publishing the profile, notify the consultant by email
    $email = get_post_meta($post->ID, 'consultant_email', true);
    if ($email) {
        $consultant_link = get_permalink( $post->ID );
        
        //Read in template and populate with passed variables
        ob_start();
        include 'emails/user-consultant-published.php';
        $message = ob_get_clean();
        
        $headers[] = 'From: muniSERV <info@muniserv.ca>';
        add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
        wp_mail($email, 'Your muniSERV consultant profile has been approved!', $message, $headers);
    }
}
add_action('pending_to_publish', 'consultant_publish');

// Add custom field columns to admin list
function consultant_edit_columns($columns){
  $columns = array(
    "cb" => "<input type=\"checkbox\" />",
    "post_id" => "ID",
    "title" => "Consultant Name",
    "consultant_contact_lname" => "Contact Name",
    "consultant_categories" => "Associated Categories",
    "consultant_membership" => "Membership Level",
    "date" => "Publish Date",
  );

  return $columns;
}
add_action("manage_posts_custom_column",  "consultant_custom_columns");

// Retrieve custom fields to show in admin list
function consultant_custom_columns($column){
  global $post;

  switch ($column) {
    case "post_id":
        the_ID();
        break;
    case "consultant_categories":
        $terms = get_the_terms( $post->ID, 'consultant_categories' );
        if( $terms ){
            $links = array();
            foreach( $terms as $term ){
                $links[] = '<a href="edit.php?post_type=consultant&consultant_categories='.$term->slug.'">'.$term->name.'</a>';
            }
            echo implode(', ', $links);
        }
        else
            echo 'No Associated Categories';
        break;
    case "consultant_contact_lname":
        $custom = get_post_custom();
        echo $custom["consultant_contact_fname"][0].' '.$custom["consultant_contact_lname"][0];
        break;
    case "consultant_membership":
        $member_level = unserialize(MEMBER_LEVEL);
        $custom = get_post_custom();
        echo $member_level[$custom["consultant_membership"][0]-1];
        break;
    case "date":
        the_date();
        break;
  }
}
add_filter("manage_edit-consultant_columns", "consultant_edit_columns");

// Add custom filter by taxonomy to admin list
function manage_consultant_by_category(){
    global $typenow;
    // If we are on our custom post type screen, add our custom taxonomy as a filter
    if( $typenow == 'consultant' ){
        $taxonomy = get_terms('consultant_categories'); 
        if( $taxonomy ): ?>
            <select name="consultant_categories" id="consultant_categories" class="postform">
                <option value="">Show all categories</option><?php
                foreach( $taxonomy as $terms ): ?>
                    <option value="<?php echo $terms->slug; ?>"<?php if( isset($_GET['consultant_categories']) && $terms->slug == $_GET['consultant_categories'] ) echo ' selected="selected"'; ?>><?php echo $terms->name; ?></option><?php
                endforeach; ?>
            </select><?php
        endif;
    }
}
add_action('restrict_manage_posts', 'manage_consultant_by_category');

// Added where filter for consultant searches to filter by consultant title
function consultant_posts_where( $where, &$wp_query )
{
    global $wpdb;
    if ( $s_consultant_title = $wp_query->get( 's_consultant_title' ) ) {
        if ($s_consultant_title == '0-9') //Numeric start
            $where .= $wpdb->prepare(' AND ' . $wpdb->posts . '.post_title REGEXP %s', '^[0-9]');
        else
            $where .= $wpdb->prepare(' AND ' . $wpdb->posts . '.post_title LIKE %s', ((strlen($s_consultant_title)>1)?'%':'').$s_consultant_title.'%');
    }
    return $where;
}
add_filter( 'posts_where', 'consultant_posts_where', 10, 2 );

// Filter the orderby query to ensure that membership postmeta value is always sorted in DESC order
// (this filter must be added before wp_query call and removed after)
function consultant_posts_orderby($orderby = '') {
    global $wpdb;
    $orderby = str_replace($wpdb->postmeta.'.meta_value+0', $wpdb->postmeta.'.meta_value+0 DESC', $orderby);
    return $orderby;
}

// Return list of select list of provinces
function consultant_provinces_list($selected = '', $abbr = false) {
    $check_consultant_province[$selected] = ' selected';
    return '<option value="AB"'.$check_consultant_province['AB'].'>'.($abbr?'AB':'Alberta').'</option>
            <option value="BC"'.$check_consultant_province['BC'].'>'.($abbr?'BC':'British Columbia').'</option>
            <option value="MB"'.$check_consultant_province['MB'].'>'.($abbr?'MB':'Manitoba').'</option>
            <option value="ON"'.$check_consultant_province['ON'].'>'.($abbr?'ON':'Ontario').'</option>
            <option value="NB"'.$check_consultant_province['NB'].'>'.($abbr?'NB':'New Brunswick').'</option>
            <option value="NL"'.$check_consultant_province['NL'].'>'.($abbr?'NL':'Newfoundland & Labrador').'</option>
            <option value="NT"'.$check_consultant_province['NT'].'>'.($abbr?'NT':'Northwest Territories').'</option>
            <option value="NS"'.$check_consultant_province['NS'].'>'.($abbr?'NS':'Nova Scotia').'</option>
            <option value="NU"'.$check_consultant_province['NU'].'>'.($abbr?'NU':'Nunavut').'</option>
            <option value="PE"'.$check_consultant_province['PE'].'>'.($abbr?'PE':'Prince Edward Island').'</option>
            <option value="QC"'.$check_consultant_province['QC'].'>'.($abbr?'QC':'Qu&eacute;bec').'</option>
            <option value="SK"'.$check_consultant_province['SK'].'>'.($abbr?'SK':'Saskatchewan').'</option>
            <option value="YT"'.$check_consultant_province['YT'].'>'.($abbr?'YT':'Yukon').'</option>';
}

// Call the Image UploadHandler code for Profile Images
function consultant_ajax_profile_image_fileupload(){
    require('class-upload-handler.php');
    $upload_handler = new UploadHandler();
    die();
}
add_action( 'wp_ajax_consultant_profile_image', 'consultant_ajax_profile_image_fileupload' );
add_action( 'wp_ajax_nopriv_consultant_profile_image', 'consultant_ajax_profile_image_fileupload' );

// Call the Image UploadHandler code for Advertisement Images
function consultant_ajax_ad_image_fileupload(){
    global $_wp_additional_image_sizes;
    require('class-upload-handler.php');
    $obj_upload_dir = wp_upload_dir();
    $options = array(
        'script_url' => admin_url('admin-ajax.php').'?action=consultant_ad_image',
        'upload_dir' => $obj_upload_dir['basedir'].'/ad-images/',
        'upload_url' => $obj_upload_dir['baseurl'].'/ad-images/',
        'image_versions' => array(
            '' => array(
                'max_width' => $_wp_additional_image_sizes['consultant-ad']['width'],
                'max_height' => $_wp_additional_image_sizes['consultant-ad']['height'],
                'jpeg_quality' => 90
            )
        )
    );
    $upload_handler = new UploadHandler($options);
    die();
}
add_action( 'wp_ajax_consultant_ad_image', 'consultant_ajax_ad_image_fileupload' );
add_action( 'wp_ajax_nopriv_consultant_ad_image', 'consultant_ajax_ad_image_fileupload' );

// Build consultant profile form (used in consultant-join-form and consultant-profile-form shortcodes)
function consultant_profile_form_fields($level = 1, $consultant = null, $custom = null, $selected_cats = null) {
    $ary_max_cats = unserialize(MEMBER_CATEGORIES);
    $max_cats = $ary_max_cats[$level - 1];
    // Add common scripts/styles
    wp_enqueue_script('parsley', get_template_directory_uri() . '/js/parsley.min.js', array('jquery'));
    // The jQuery UI widget factory, can be omitted if jQuery UI is already included
    wp_enqueue_script('jquery.ui.widget', get_template_directory_uri() . '/js/jquery.ui.widget.js', array('jquery'), null, true);
    // The Iframe Transport is required for browsers without support for XHR file uploads
    wp_enqueue_script('jquery.iframe-transport', get_template_directory_uri() . '/js/jquery.iframe-transport.js', array('jquery'), null, true);
    // The File Upload plugins
    wp_enqueue_script('jquery.fileupload', get_template_directory_uri() . '/js/jquery.fileupload.js', array('jquery'), null, true);
    wp_enqueue_script('jquery.fileupload-process', get_template_directory_uri() . '/js/jquery.fileupload-process.js', array('jquery'), null, true);
    wp_enqueue_script('jquery.fileupload-validate', get_template_directory_uri() . '/js/jquery.fileupload-validate.js', array('jquery'), null, true);
    wp_enqueue_script('consultant-form-fileupload', get_template_directory_uri() . '/js/consultant-form-fileupload.js', array('jquery', 'jquery.ui.widget', 'jquery.iframe-transport', 'jquery.fileupload', 'jquery.fileupload-process', 'jquery.fileupload-validate'));
    wp_localize_script('consultant-form-fileupload', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
    // Category selector && populate selected categories of submitted form if available
    // Get parent categories first
    $categories = get_categories(array('type' => 'consultant', 'taxonomy' => 'consultant_categories', 'hide_empty' => 0, 'parent' => 0));
    $cats = array();
    foreach($categories as $cat) {
        $cats['c'.$cat->cat_ID]->id = $cat->cat_ID;
        $cats['c'.$cat->cat_ID]->name = $cat->name;
    }
    // Then attach all children under the parents
    $allcategories = get_categories(array('type' => 'consultant', 'taxonomy' => 'consultant_categories', 'hide_empty' => 0));
    $posted_cats = array();
    foreach($allcategories as $cat) {
        $parent = $cat->parent;
        if ($parent && isset($cats['c'.$parent])) {
            $cats['c'.$parent]->subcats[] = array('id' => $cat->cat_ID, 'name' => $cat->name);
            // Populate selected categories of submitted form if available
            if (isset($_POST['consultant_categories'])) {
                foreach ($_POST['consultant_categories'] as $selected_cat) {
                    if ($selected_cat == $cat->cat_ID) {
                        $posted_cats[$cat->cat_ID] = $cat->name;
                    }
                }
            }
        }
    }
    wp_enqueue_script('consultant-profile', get_template_directory_uri() . '/js/consultant-profile.js', array('jquery'), null, true);
    wp_localize_script('consultant-profile', 'cats', array('l10n_print_after' => 'cats = '.html_entity_decode(json_encode($cats)).'; var maxcats = '.$max_cats));
    
    if (!is_array($selected_cats)) {
        $selected_cats = $posted_cats;
    }
    $check_consultant_contact_prefix[($custom)?$custom['consultant_contact_prefix'][0]:$_POST["consultant_contact_prefix"]] = 'checked';
    $check_consultant_catlist[$_POST['consultant_catlist']] = ' selected';
    $obj_upload_dir = wp_upload_dir();
    ob_start();
    ?>
        <fieldset class="round">
            <legend>About You</legend>
            <div class="cpt-fieldbox">
                <label for="consultant_contact_prefix_1">Preferred Salutation:</label>
                <ul class="radio-list horizontal">
                    <li><label><input id="consultant_contact_prefix_1" name="consultant_contact_prefix" type="radio" value="Mr." <?php echo $check_consultant_contact_prefix['Mr.']; ?> />Mr.</label></li>
                    <li><label><input id="consultant_contact_prefix_2" name="consultant_contact_prefix" type="radio" value="Mrs." <?php echo $check_consultant_contact_prefix['Mrs.']; ?> />Mrs.</label></li>
                    <li><label><input id="consultant_contact_prefix_3" name="consultant_contact_prefix" type="radio" value="Ms." <?php echo $check_consultant_contact_prefix['Ms.']; ?> />Ms.</label></li>
                    <li><label><input id="consultant_contact_prefix_4" name="consultant_contact_prefix" type="radio" value="Miss" <?php echo $check_consultant_contact_prefix['Miss']; ?> />Miss</label></li>
                    <li><label><input id="consultant_contact_prefix_5" name="consultant_contact_prefix" type="radio" value="Dr." <?php echo $check_consultant_contact_prefix['Dr.']; ?> />Dr.</label></li>
                </ul>
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_contact_fname">First Name:</label>
                <input id="consultant_contact_fname" name="consultant_contact_fname" type="text" value="<?php echo ($custom)?$custom['consultant_contact_fname'][0]:$_POST['consultant_contact_fname']; ?>" class="required" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_contact_lname">Last Name:</label>
                <input id="consultant_contact_lname" name="consultant_contact_lname" type="text" value="<?php echo ($custom)?$custom['consultant_contact_lname'][0]:$_POST['consultant_contact_lname']; ?>" class="required" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_contact_designations">Designations: <span class="cpt-desc">(PhD, CA, RMT, etc.)</span></label>
                <input id="consultant_contact_designations" name="consultant_contact_designations" type="text" value="<?php echo ($custom)?$custom['consultant_contact_designations'][0]:$_POST['consultant_contact_designations']; ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_contact_title">Job Title:</label>
                <input id="consultant_contact_title" name="consultant_contact_title" type="text" value="<?php echo ($custom)?$custom['consultant_contact_title'][0]:$_POST['consultant_contact_title']; ?>" class="required" />
            </div>
        </fieldset>
        <fieldset class="round">
            <legend>Business Name & Categories</legend>
            <div class="cpt-fieldbox">
                <label for="consultant_title">Name of your business/consultancy: <span class="cpt-desc">(75 character limit)</span></label>
                <input id="consultant_title" name="consultant_title" type="text" value="<?php echo ($consultant)?$consultant->post_title:$_POST['consultant_title']; ?>" maxlength="75" class="required" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_tagline">Business Tagline: <span class="cpt-desc">(90 character limit)</span></label>
                <input id="consultant_tagline" name="consultant_tagline" type="text" value="<?php echo ($custom)?$custom['consultant_tagline'][0]:$_POST['consultant_tagline']; ?>" maxlength="90" />
            </div>
            <div class="cpt-fieldbox cf">
                <div class="cat-selector-left">
                    <label for="consultant_catlist">Main Categories:</label>
                    <select id="consultant_catlist" name="consultant_catlist">
                        <?php
                        foreach ($cats as $cat) {
                            echo '<option value="'.$cat->id.'"'.$check_consultant_catlist[$cat->id].'>'.$cat->name.'</option>';
                        } ?>
                    </select>
                    <label for="consultant_subcatlist">Sub-Categories:</label>
                    <select id="consultant_subcatlist" name="consultant_subcatlist" size="10"></select>
                </div>
                <div class="cat-selector">
                    <button id="cat-add" type="button">&gt;</button>
                    <button id="cat-del" type="button">&lt;</button>
                </div>
                <div class="cat-selector-right">
                    <label for="consultant_categories">Selected Sub-Categories <span class="cpt-desc">(max <?php echo $max_cats; ?>)</span>:</label>
                    <select id="consultant_categories" name="consultant_categories[]" size="12" multiple>
                        <?php
                        foreach ($selected_cats as $id=>$name) {
                            echo '<option value="'.$id.'">'.$name.'</option>';
                        } ?>
                    </select>
                </div>
            </div>
            <div class="cpt-desc" style="margin-bottom:5px;">If you do not find a category that properly suites your business, please <a href="mailto:admin@muniserv.ca" style="color:#237b98;">email us</a> with a new category request. Tell us the Main Category and your suggested new Sub-Category. If approved, you will receive an email from us and you will need to log back into the system and update your category selection.</div>
        </fieldset>
        <fieldset class="round">
            <legend>Business Images</legend>
            <div id="consultant_profile_image_box" class="cpt-fieldbox cf">
                <label for="consultant_profile_image">Profile Picture: <span class="cpt-desc">(5MB max, your image will automatically be resized)</span></label>
                <div class="cpt-desc">If you have a Premium membership with us, your profile picture will be used in your automatically generated ad space.</div>
                <div class="files consultant-profile-thumb">
                    <span></span><img src="<?php if($custom && $custom['consultant_profile_image'][0]) 
                                                     echo $obj_upload_dir['baseurl'].'/profile-pics/thumbnail/'.$custom['consultant_profile_image'][0]; 
                                                 else if($_POST['consultant_profile_image']) 
                                                     echo $obj_upload_dir['baseurl'].'/profile-pics/thumbnail/'.$_POST['consultant_profile_image']; 
                                                 else 
                                                     echo get_template_directory_uri().'/images/no-profile-picture.png'; ?>" />
                </div>
                <span class="fileinput-button">
                    <span>Upload Profile Photo</span>
                    <input class="fileupload" type="file" name="files">
                </span>
                <div class="progress">
                    <div class="progress-bar"></div>
                </div>
                <div class="files-error"></div>
                <input id="consultant_profile_image" name="consultant_profile_image" type="hidden" value="<?php echo ($custom)?$custom['consultant_profile_image'][0]:$_POST['consultant_profile_image']; ?>" />
            </div>
            <?php if ($level > 1) : ?>
            <div id="consultant_sidebar_image_box" class="cpt-fieldbox cf">
                <label for="consultant_sidebar_image">Sidebar Picture: <span class="cpt-desc">(5MB max, your image will automatically be resized)</span></label>
                <div class="files consultant-sidebar-image">
                    <img src="<?php if($custom && $custom['consultant_sidebar_image'][0]) 
                                        echo $obj_upload_dir['baseurl'].'/profile-pics/'.$custom['consultant_sidebar_image'][0]; 
                                    else if($_POST['consultant_sidebar_image']) 
                                        echo $obj_upload_dir['baseurl'].'/profile-pics/'.$_POST['consultant_sidebar_image']; 
                                    else 
                                        echo get_template_directory_uri().'/images/no-sidebar-picture.png'; ?>" />
                </div>
                <span class="fileinput-button">
                    <span>Upload Sidebar Photo</span>
                    <input class="fileupload" type="file" name="files">
                </span>
                <div class="progress">
                    <div class="progress-bar"></div>
                </div>
                <div class="files-error"></div>
                <input id="consultant_sidebar_image" name="consultant_sidebar_image" type="hidden" value="<?php echo ($custom)?$custom['consultant_sidebar_image'][0]:$_POST['consultant_sidebar_image']; ?>" />
            </div>
            <?php endif; ?>
        </fieldset>
        <fieldset class="round">
            <legend>About Your Business</legend>
            <div class="cpt-fieldbox">
                <label for="consultant_address">Address:</label>
                <input id="consultant_address" name="consultant_address" type="text" value="<?php echo ($custom)?$custom['consultant_address'][0]:$_POST['consultant_address']; ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_city">Town/City:</label>
                <input id="consultant_city" name="consultant_city" type="text" value="<?php echo ($custom)?$custom['consultant_city'][0]:$_POST['consultant_city']; ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_province">Province:</label>
                <select id="consultant_province" name="consultant_province">
                    <option value="">- Select -</option>
                    <?php echo consultant_provinces_list(($custom)?$custom['consultant_province'][0]:$_POST['consultant_province']); ?>
                </select>
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_postal">Postal Code:</label>
                <input id="consultant_postal" name="consultant_postal" type="text" value="<?php echo ($custom)?$custom['consultant_postal'][0]:$_POST['consultant_postal']; ?>" maxlength="7" data-regexp="[ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ] ?\d[ABCEGHJKLMNPRSTVWXYZ]\d" data-regexp-flag="i" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_phone">Telephone:</label>
                <input id="consultant_phone" name="consultant_phone" type="text" value="<?php echo ($custom)?$custom['consultant_phone'][0]:$_POST['consultant_phone']; ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_fax">Fax:</label>
                <input id="consultant_fax" name="consultant_fax" type="text" value="<?php echo ($custom)?$custom['consultant_fax'][0]:$_POST['consultant_fax']; ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_website">Website:</label>
                <input id="consultant_website" name="consultant_website" type="text" value="<?php echo ($custom)?$custom['consultant_website'][0]:(($_POST['consultant_website'])?$_POST['consultant_website']:'http://'); ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_serving">Areas Served:</label>
                <input id="consultant_serving" name="consultant_serving" type="text" value="<?php echo ($custom)?$custom['consultant_serving'][0]:$_POST['consultant_serving']; ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_hours">Hours of Operation:</label>
                <input id="consultant_hours" name="consultant_hours" type="text" value="<?php echo ($custom)?$custom['consultant_hours'][0]:$_POST['consultant_hours']; ?>" />
            </div>
            <div class="cpt-fieldbox last">
                <label for="consultant_description">Business Description: <span class="cpt-desc">(Note - your use of keywords about what kind of consultancy or business service you provide will help narrow searches for municipalities.  1500 characters max, text only.)</span></label>
                <textarea id="consultant_description" name="consultant_description" rows="8" maxlength="1500"><?php echo ($consultant)?$consultant->post_content:$_POST['consultant_description']; ?></textarea>
            </div>
        </fieldset>
        <fieldset class="round">
            <div class="cpt-desc">If the Google Map on your profile does not work or you would like to override the location, please use latitude and longitude coordinates.  <a href="http://www.itouchmap.com/latlong.html" target="_blank">Visit this page</a> to figure out your coordinates.</div>
            <legend>Override Google Map Location with Coordinates</legend>
            <div class="cpt-fieldbox last">
                <label for="consultant_map_lat">Latitude:</label>
                <input id="consultant_map_lat" name="consultant_map_lat" type="text" value="<?php echo ($custom)?$custom['consultant_map_lat'][0]:$_POST['consultant_map_lat']; ?>" />
                <label for="consultant_map_long">Longitude:</label>
                <input id="consultant_map_long" name="consultant_map_long" type="text" value="<?php echo ($custom)?$custom['consultant_map_long'][0]:$_POST['consultant_map_long']; ?>" />
            </div>
        </fieldset>
        <fieldset class="round">
            <legend>Success Story</legend>
            <div class="cpt-fieldbox last">
                <span class="cpt-desc">(1500 characters max, text only)</span>
                <textarea id="consultant_success_story" name="consultant_success_story" rows="8" maxlength="1500"><?php echo ($custom)?$custom['consultant_success_story'][0]:$_POST['consultant_success_story']; ?></textarea>
            </div>
        </fieldset>
        <?php if ($level > 1) : ?>
        <fieldset class="round">
            <legend>Testimonials</legend>
            <div class="cpt-fieldbox">
                <label for="consultant_testimonial_1">Testimonial<?php if ($level > 2) : ?> 1<?php endif; ?>: <span class="cpt-desc">(300 characters max, text only)</span></label>
                <textarea id="consultant_testimonial_1" name="consultant_testimonial_1" maxlength="300"><?php echo ($custom)?$custom['consultant_testimonial_1'][0]:$_POST['consultant_testimonial_1']; ?></textarea>
                <label for="consultant_testimonial_1_by" class="cpt-desc">Testimonial By:</label>
                <input id="consultant_testimonial_1_by" name="consultant_testimonial_1_by" type="text" maxlength="100" value="<?php echo ($custom)?$custom['consultant_testimonial_1_by'][0]:$_POST['consultant_testimonial_1_by']; ?>" />
            </div>
            <?php if ($level > 2) : ?>
            <div class="cpt-fieldbox">
                <label for="consultant_testimonial_2">Testimonial 2: <span class="cpt-desc">(300 characters max, text only)</span></label>
                <textarea id="consultant_testimonial_2" name="consultant_testimonial_2" maxlength="300"><?php echo ($custom)?$custom['consultant_testimonial_2'][0]:$_POST['consultant_testimonial_2']; ?></textarea>
                <label for="consultant_testimonial_2_by" class="cpt-desc">Testimonial By:</label>
                <input id="consultant_testimonial_2_by" name="consultant_testimonial_2_by" type="text" maxlength="100" value="<?php echo ($custom)?$custom['consultant_testimonial_2_by'][0]:$_POST['consultant_testimonial_2_by']; ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_testimonial_3">Testimonial 3: <span class="cpt-desc">(300 characters max, text only)</span></label>
                <textarea id="consultant_testimonial_3" name="consultant_testimonial_3" maxlength="300"><?php echo ($custom)?$custom['consultant_testimonial_3'][0]:$_POST['consultant_testimonial_3']; ?></textarea>
                <label for="consultant_testimonial_3_by" class="cpt-desc">Testimonial By:</label>
                <input id="consultant_testimonial_3_by" name="consultant_testimonial_3_by" type="text" maxlength="100" value="<?php echo ($custom)?$custom['consultant_testimonial_3_by'][0]:$_POST['consultant_testimonial_3_by']; ?>" />
            </div>
            <?php endif; ?>
        </fieldset>
        <?php if ($level > 2) : ?>
        <fieldset class="round">
            <legend>Awards, Certificates, Designations and Affiliations</legend>
            <div class="cpt-fieldbox">
                <label for="consultant_awards_other_1">Other Awards, Certificates, Designations and Affiliations:</label>
                <?php 
                if ($custom)
                    $consultant_awards = unserialize($custom['consultant_awards_other'][0]);
                for ($i=0; $i < AWARD_FIELDS_COUNT; $i++): ?>
                <input id="consultant_awards_other_<?php echo $i; ?>" name="consultant_awards_other[]" type="text" maxlength="100" value="<?php echo ($custom)?$consultant_awards[$i]:$_POST['consultant_awards_other'][$i]; ?>" />
                <?php endfor; ?>
            </div>
        </fieldset>
        <?php endif; ?>
        <fieldset class="round">
            <legend>Social Media</legend>
            <div class="cpt-fieldbox">
                <label for="consultant_facebook">Facebook Address:</label>
                <input id="consultant_facebook" name="consultant_facebook" type="text" value="<?php echo ($custom)?$custom['consultant_facebook'][0]:(($_POST['consultant_facebook'])?$_POST['consultant_facebook']:'http://'); ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_twitter">Twitter Address:</label>
                <input id="consultant_twitter" name="consultant_twitter" type="text" value="<?php echo ($custom)?$custom['consultant_twitter'][0]:(($_POST['consultant_twitter'])?$_POST['consultant_twitter']:'http://'); ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_linkedin">LinkedIn Address:</label>
                <input id="consultant_linkedin" name="consultant_linkedin" type="text" value="<?php echo ($custom)?$custom['consultant_linkedin'][0]:(($_POST['consultant_linkedin'])?$_POST['consultant_linkedin']:'http://'); ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_googleplus">Google Plus Address:</label>
                <input id="consultant_googleplus" name="consultant_googleplus" type="text" value="<?php echo ($custom)?$custom['consultant_googleplus'][0]:(($_POST['consultant_googleplus'])?$_POST['consultant_googleplus']:'http://'); ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_youtube">YouTube Channel Address:</label>
                <input id="consultant_youtube" name="consultant_youtube" type="text" value="<?php echo ($custom)?$custom['consultant_youtube'][0]:(($_POST['consultant_youtube'])?$_POST['consultant_youtube']:'http://'); ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_rss">RSS Feed Address:</label>
                <input id="consultant_rss" name="consultant_rss" type="text" value="<?php echo ($custom)?$custom['consultant_rss'][0]:(($_POST['consultant_rss'])?$_POST['consultant_rss']:'http://'); ?>" />
            </div>
            <?php if ($level > 2) : ?>
            <fieldset class="round">
                <legend>Twitter Feed Authentication Settings</legend>
                <div class="cpt-fieldbox">
                    <div class="cpt-desc" style="margin-bottom:10px;">To add your Twitter feed to your profile, please provide these authentication details available in your <a href="https://dev.twitter.com/apps" target="_blank">Twitter dashboard</a>.</div>
                    <label for="consultant_twitter_username">Twitter Username:</label>
                    <input id="consultant_twitter_username" name="consultant_twitter_username" type="text" value="<?php echo ($custom)?$custom['consultant_twitter_username'][0]:$_POST['consultant_twitter_username']; ?>" />
                    <label for="consultant_twitter_key">OAuth Consumer Key:</label>
                    <input id="consultant_twitter_key" name="consultant_twitter_key" type="text" value="<?php echo ($custom)?$custom['consultant_twitter_key'][0]:$_POST['consultant_twitter_key']; ?>" />
                    <label for="consultant_twitter_secret">OAuth Consumer Secret:</label>
                    <input id="consultant_twitter_secret" name="consultant_twitter_secret" type="text" value="<?php echo ($custom)?$custom['consultant_twitter_secret'][0]:$_POST['consultant_twitter_secret']; ?>" />
                    <label for="consultant_twitter_token">OAuth Access Token:</label>
                    <input id="consultant_twitter_token" name="consultant_twitter_token" type="text" value="<?php echo ($custom)?$custom['consultant_twitter_token'][0]:$_POST['consultant_twitter_token']; ?>" />
                    <label for="consultant_twitter_access">OAuth Access Secret:</label>
                    <input id="consultant_twitter_access" name="consultant_twitter_access" type="text" value="<?php echo ($custom)?$custom['consultant_twitter_access'][0]:$_POST['consultant_twitter_access']; ?>" />
                </div>
            </fieldset>
            <?php endif; ?>
        </fieldset>
        <fieldset class="round">
            <legend>Advertisement</legend>
            <div class="cpt-desc" style="margin-bottom:5px;">This ad is automatically generated. Please watch that the main content is not cut off at the end. To override the content, fill in the fields below.</div>
            <div id="consultant_ad_image_box" class="cpt-fieldbox cf">
                <?php $isOverride = (($custom && $custom['consultant_ad_image'][0]) || $_POST['consultant_ad_image']); ?>
                <label for="consultant_ad_image">Instead, you can upload your own Ad:</label>
                <div class="cpt-desc">(must be 300px by 171px)</div>
                <div class="shadow">
                    <div class="inner-shadow">
                        <div class="files consultant-adspace-override" <?php if(!$isOverride): ?>style="display:none;"<?php endif; ?>>
                            <img src="<?php echo $obj_upload_dir['baseurl'].'/ad-images/'.(($custom && $custom['consultant_ad_image'][0])?$custom['consultant_ad_image'][0]:$_POST['consultant_ad_image']); ?>" alt="<?php echo ($consultant)?$consultant->post_title:$_POST['consultant_title']; ?>" />
                        </div>
                        <div class="consultant-adspace" <?php if($isOverride): ?>style="display:none;"<?php endif; ?>>
                            <header class="cf">
                                <div class="consultant-ad-image"<?php if((!$custom || !$custom['consultant_profile_image'][0]) && !$_POST['consultant_profile_image']) : ?> style="display:none;"<?php endif; ?>>
                                    <img src="<?php echo $obj_upload_dir['baseurl'].'/profile-pics/'.(($custom && $custom['consultant_profile_image'][0])?$custom['consultant_profile_image'][0]:$_POST['consultant_profile_image']); ?>" />
                                </div>
                                <h3 class="consultant-ad-title"><?php echo ($custom)?(($custom['consultant_ad_title'][0])?$custom['consultant_ad_title'][0]:$consultant->post_title):(($_POST['consultant_ad_title'])?$_POST['consultant_ad_title']:$_POST['consultant_title']); ?></h3>
                                <h4 class="consultant-ad-subtitle"><?php echo ($custom)?(($custom['consultant_ad_subtitle'][0])?$custom['consultant_ad_subtitle'][0]:$custom['consultant_tagline'][0]):(($_POST['consultant_ad_subtitle'])?$_POST['consultant_ad_subtitle']:$_POST['consultant_tagline']); ?></h4>
                            </header>
                            <div class="consultant-ad-blurb"><?php echo ($custom)?(($custom['consultant_ad_blurb'][0])?$custom['consultant_ad_blurb'][0]:snippet(strip_tags($consultant->post_content),230)):(($_POST['consultant_ad_blurb'])?$_POST['consultant_ad_blurb']:$_POST['consultant_description']); ?></div>
                        </div>
                    </div>
                </div>
                <span class="fileinput-button">
                    <span>Upload Ad Image</span>
                    <input class="fileupload" type="file" name="files">
                </span>
                <div class="progress">
                    <div class="progress-bar"></div>
                </div>
                <div class="files-error"></div>
                <button type="button" class="button green remove-button" <?php if((!$custom || !$custom['consultant_ad_image'][0]) && !$_POST['consultant_ad_image']) : ?>style="display:none;"<?php endif; ?>>Remove Image</button>
                <input id="consultant_ad_image" name="consultant_ad_image" type="hidden" value="<?php echo ($custom && $custom['consultant_ad_image'][0])?$custom['consultant_ad_image'][0]:$_POST['consultant_ad_image']; ?>" />
                <div class="cpt-desc" style="margin-top:20px;">If you're interested in having muniSERV create a custom ad for $100, please contact <a href="mailto:marketing@muniserv.ca">our Marketing Department</a>.</div>
            </div>
            <div class="cpt-fieldbox">
                <label>Override default text:</label>
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_ad_title">Title:</label>
                <input id="consultant_ad_title" name="consultant_ad_title" type="text" maxwidth="70" value="<?php echo ($custom)?$custom['consultant_ad_title'][0]:$_POST['consultant_ad_title']; ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_ad_subtitle">Subtitle:</label>
                <input id="consultant_ad_subtitle" name="consultant_ad_subtitle" type="text" maxwidth="78" value="<?php echo ($custom)?$custom['consultant_ad_subtitle'][0]:$_POST['consultant_ad_subtitle']; ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_ad_blurb">Blurb:</label>
                <textarea id="consultant_ad_blurb" name="consultant_ad_blurb" rows="3"><?php echo ($custom)?$custom['consultant_ad_blurb'][0]:$_POST['consultant_ad_blurb']; ?></textarea>
            </div>
        </fieldset>
        <?php endif; ?>
    <?php
    $formOutput .= ob_get_clean();
    return $formOutput;
}


// Import any consultant widgets and shortcodes.
require_once 'sc-consultant-categories.php';
require_once 'sc-consultant-join-form.php';
require_once 'sc-consultant-profile-form.php';
require_once 'sc-consultant-post-form.php';
require_once 'wgt-consultant-adspace.php';
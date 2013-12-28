<?php
/**
 * Registers a custom post type for Municipalities and custom taxonomy for Municipality Categories
 */
function municipality_register() {

	$labels = array(
		'name' => _x('Municipalities', 'post type general name'),
		'singular_name' => _x('Municipality', 'post type singular name'),
		'add_new' => _x('Add New', 'municipality item'),
		'add_new_item' => __('Add New Municipality'),
		'edit_item' => __('Edit Municipality'),
		'new_item' => __('New Municipality'),
		'view_item' => __('View Municipality'),
		'search_items' => __('Search Municipalities'),
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
		'rewrite' => array('slug' => '/municipality', 'with_front' => false),
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 5,
        'exclude_from_search' => false,
		'supports' => array('title','editor','revisions')
	  );

	register_post_type( 'municipality' , $args );
    
}
add_action('init', 'municipality_register');

// Set municipality image sizes, roles, etc
function municipality_setup(){
    // New Image Sizes
    add_image_size( 'municipality-large', 305 );
}
add_action('after_setup_theme', 'municipality_setup');


// Add scripts to edit pages for this post type
function municipality_admin_script() {
    global $post_type;
    if('municipality' == $post_type) {
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
    }
}
add_action( 'admin_print_scripts-post-new.php', 'municipality_admin_script', 11 );
add_action( 'admin_print_scripts-post.php', 'municipality_admin_script', 11 );

// Add meta box to acquire custom fields
function municipality_admin_init(){
    add_meta_box("municipality_photos-meta", "Featured Images", "municipality_photos", "municipality", "side", "low");
    add_meta_box("municipality_contact_details-meta", "Contact Details", "municipality_contact_details", "municipality", "normal", "low");
}
add_action("admin_init", "municipality_admin_init");

// Add class to metaboxes for styling purposes
function municipality_metabox_add_classes($classes) {
    array_push($classes,'cpt-metabox');
    return $classes;
}
add_filter('postbox_classes_municipality_municipality_photos-meta','municipality_metabox_add_classes');
add_filter('postbox_classes_municipality_municipality_contact_details-meta','municipality_metabox_add_classes');

// Display code for meta boxes
function municipality_photos(){
  global $post;
  $obj_upload_dir = wp_upload_dir();
  $custom = get_post_custom($post->ID);
  $municipality_profile_image = $custom["municipality_profile_image"][0];
  ?>
    <div id="municipality_profile_image_box" class="cpt-fieldbox">
        <label for="municipality_profile_image">Profile Picture:</label>
        <div class="files municipality-profile-thumb">
            <img src="<?php echo ($municipality_profile_image) ? $obj_upload_dir['baseurl'].'/municipal-pics/'.$municipality_profile_image : get_template_directory_uri().'/images/no-profile-picture.png'; ?>" />
        </div>
        <div class="fileinput-button">
            <input class="fileupload" type="file" name="files">
        </div>
        <div class="progress">
            <div class="progress-bar"></div>
        </div>
        <div class="files-error"></div>
        <input id="municipality_profile_image" name="municipality_profile_image" type="hidden" value="<?php echo $municipality_profile_image; ?>" />
    </div>
  <?php
}
function municipality_contact_details(){
  global $post;
  $custom = get_post_custom($post->ID);
  $municipality_contact_fname = $custom["municipality_contact_fname"][0];
  $municipality_contact_lname = $custom["municipality_contact_lname"][0];
  $municipality_contact_title = $custom["municipality_contact_title"][0];
  $municipality_phone = $custom["municipality_phone"][0];
  $municipality_fax = $custom["municipality_fax"][0];
  $municipality_address = $custom["municipality_address"][0];
  $municipality_city = $custom["municipality_city"][0];
  $municipality_province = $custom["municipality_province"][0];
  $municipality_postal = $custom["municipality_postal"][0];
  $municipality_map_lat = $custom["municipality_map_lat"][0];
  $municipality_map_long = $custom["municipality_map_long"][0];
  $municipality_email = $custom["municipality_email"][0];
  $municipality_website = $custom["municipality_website"][0];
  ?>
    <div class="cpt-fieldbox">
        <label for="municipality_contact_fname">Contact First Name:</label>
        <input id="municipality_contact_fname" name="municipality_contact_fname" type="text" value="<?php echo $municipality_contact_fname; ?>" class="required" />
    </div>
    <div class="cpt-fieldbox">
        <label for="municipality_contact_lname">Contact Last Name:</label>
        <input id="municipality_contact_lname" name="municipality_contact_lname" type="text" value="<?php echo $municipality_contact_lname; ?>" class="required" />
    </div>    
    <div class="cpt-fieldbox">
        <label for="municipality_contact_title">Contact Job Title:</label>
        <input id="municipality_contact_title" name="municipality_contact_title" type="text" value="<?php echo $municipality_contact_title; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="municipality_phone">Telephone:</label>
        <input id="municipality_phone" name="municipality_phone" type="text" value="<?php echo $municipality_phone; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="municipality_fax">Fax:</label>
        <input id="municipality_fax" name="municipality_fax" type="text" value="<?php echo $municipality_fax; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="municipality_address">Address:</label>
        <input id="municipality_address" name="municipality_address" type="text" value="<?php echo $municipality_address; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="municipality_city">Town/City:</label>
        <input id="municipality_city" name="municipality_city" type="text" value="<?php echo $municipality_city; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="municipality_province">Province:</label>
        <select id="municipality_province" name="municipality_province">
            <option value="">- Select -</option>
            <?php echo municipality_provinces_list($municipality_province); ?>
        </select>
    </div>
    <div class="cpt-fieldbox">
        <label for="municipality_postal">Postal Code:</label>
        <input id="municipality_postal" name="municipality_postal" type="text" value="<?php echo $municipality_postal; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="municipality_map_lat">Override map location with Co-ordinates:<br>
            Latitude:</label>
        <input id="municipality_map_lat" name="municipality_map_lat" type="text" value="<?php echo $municipality_map_lat; ?>" />
        <label for="municipality_map_long">Longitude:</label>
        <input id="municipality_map_long" name="municipality_map_long" type="text" value="<?php echo $municipality_map_long; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="municipality_email">Email:</label>
        <input id="municipality_email" name="municipality_email" type="text" value="<?php echo $municipality_email; ?>" />
    </div>
    <div class="cpt-fieldbox">
        <label for="municipality_website">Website:</label>
        <input id="municipality_website" name="municipality_website" type="text" value="<?php echo $municipality_website; ?>" />
    </div>
  <?php
}

// Save the custom fields
function municipality_save_details( $post_id ){
    // If this is an auto save, our form has not been submitted, so we don't want to do anything
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
    // Is the post type correct?
    if ( isset($_POST['post_type']) && 'municipality' != $_POST['post_type'] ) return $post_id;
    // Save meta data
    if ( isset($_POST["municipality_contact_lname"]) ) {
        //Photos
        update_post_meta($post_id, "municipality_profile_image", $_POST["municipality_profile_image"]);        
        //Contact Info
        update_post_meta($post_id, "municipality_contact_fname", $_POST["municipality_contact_fname"]);
        update_post_meta($post_id, "municipality_contact_lname", $_POST["municipality_contact_lname"]);
        update_post_meta($post_id, "municipality_contact_title", $_POST["municipality_contact_title"]);
        update_post_meta($post_id, "municipality_phone", $_POST["municipality_phone"]);
        update_post_meta($post_id, "municipality_fax", $_POST["municipality_fax"]);
        update_post_meta($post_id, "municipality_address", $_POST["municipality_address"]);
        update_post_meta($post_id, "municipality_city", $_POST["municipality_city"]);
        update_post_meta($post_id, "municipality_province", $_POST["municipality_province"]);
        update_post_meta($post_id, "municipality_postal", $_POST["municipality_postal"]);
        update_post_meta($post_id, "municipality_map_lat", $_POST["municipality_map_lat"]);
        update_post_meta($post_id, "municipality_map_long", $_POST["municipality_map_long"]);
        update_post_meta($post_id, "municipality_email", sanitize_email($_POST["municipality_email"]));
        update_post_meta($post_id, "municipality_website", ($_POST["municipality_website"]=='http://')?'':$_POST["municipality_website"]);
    }
}
add_action('save_post', 'municipality_save_details');

// Notify municipality when profile is published
function municipality_publish($post){
    // Is the post type correct?
    if ( wp_is_post_revision($post->ID) || !isset($post->post_type) || 'municipality' != $post->post_type ) return;
    // If we're publishing the profile, notify the municipality by email
    $email = get_post_meta($post->ID, 'municipality_email', true);
    if ($email) {
        $municipality_link = get_permalink( $post->ID );
        
        //Read in template and populate with passed variables
        ob_start();
        include 'emails/user-municipality-published.php';
        $message = ob_get_clean();
        
        $headers[] = 'From: muniSERV <info@muniserv.ca>';
        add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
        wp_mail($email, 'Your muniSERV municipality profile has been approved!', $message, $headers);
    }
}
add_action('pending_to_publish', 'municipality_publish');

// Add custom field columns to admin list
function municipality_edit_columns($columns){
  $columns = array(
    "cb" => "<input type=\"checkbox\" />",
    "post_id" => "ID",
    "title" => "Municipality Name",
    "municipality_contact_lname" => "Contact Name",
    "date" => "Publish Date",
  );

  return $columns;
}
add_action("manage_posts_custom_column",  "municipality_custom_columns");

// Retrieve custom fields to show in admin list
function municipality_custom_columns($column){
  global $post;

  switch ($column) {
    case "post_id":
        the_ID();
        break;
    case "municipality_contact_lname":
        $custom = get_post_custom();
        echo $custom["municipality_contact_fname"][0].' '.$custom["municipality_contact_lname"][0];
        break;
    case "date":
        the_date();
        break;
  }
}
add_filter("manage_edit-municipality_columns", "municipality_edit_columns");

// Return list of select list of provinces
function municipality_provinces_list($selected = '', $abbr = false) {
    $check_municipality_province[$selected] = ' selected';
    return '<option value="AB"'.$check_municipality_province['AB'].'>'.($abbr?'AB':'Alberta').'</option>
            <option value="BC"'.$check_municipality_province['BC'].'>'.($abbr?'BC':'British Columbia').'</option>
            <option value="MB"'.$check_municipality_province['MB'].'>'.($abbr?'MB':'Manitoba').'</option>
            <option value="ON"'.$check_municipality_province['ON'].'>'.($abbr?'ON':'Ontario').'</option>
            <option value="NB"'.$check_municipality_province['NB'].'>'.($abbr?'NB':'New Brunswick').'</option>
            <option value="NL"'.$check_municipality_province['NL'].'>'.($abbr?'NL':'Newfoundland & Labrador').'</option>
            <option value="NT"'.$check_municipality_province['NT'].'>'.($abbr?'NT':'Northwest Territories').'</option>
            <option value="NS"'.$check_municipality_province['NS'].'>'.($abbr?'NS':'Nova Scotia').'</option>
            <option value="NU"'.$check_municipality_province['NU'].'>'.($abbr?'NU':'Nunavut').'</option>
            <option value="PE"'.$check_municipality_province['PE'].'>'.($abbr?'PE':'Prince Edward Island').'</option>
            <option value="QC"'.$check_municipality_province['QC'].'>'.($abbr?'QC':'Qu&eacute;bec').'</option>
            <option value="SK"'.$check_municipality_province['SK'].'>'.($abbr?'SK':'Saskatchewan').'</option>
            <option value="YT"'.$check_municipality_province['YT'].'>'.($abbr?'YT':'Yukon').'</option>';
}
// Call the Image UploadHandler code for Profile Images
function municipality_ajax_profile_image_fileupload(){
    global $_wp_additional_image_sizes;
    require('class-upload-handler.php');
    $obj_upload_dir = wp_upload_dir();
    $options = array(
        'script_url' => admin_url('admin-ajax.php').'?action=municipality_profile_image',
        'upload_dir' => $obj_upload_dir['basedir'].'/municipal-pics/',
        'upload_url' => $obj_upload_dir['baseurl'].'/municipal-pics/',
        'image_versions' => array(
            '' => array(
                'max_width' => $_wp_additional_image_sizes['municipality-large']['width'],
                'max_height' => 2000,
                'jpeg_quality' => 90
            )
        )
    );
    $upload_handler = new UploadHandler($options);
    die();
}
add_action( 'wp_ajax_municipality_profile_image', 'municipality_ajax_profile_image_fileupload' );
add_action( 'wp_ajax_nopriv_municipality_profile_image', 'municipality_ajax_profile_image_fileupload' );

// Build municipality profile form (used in municipality-join-form and municipality-profile-form shortcodes)
function municipality_profile_form_fields($level = 1, $municipality = null, $custom = null, $selected_cats = null) {
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
    wp_enqueue_script('municipality-form-fileupload', get_template_directory_uri() . '/js/municipality-form-fileupload.js', array('jquery', 'jquery.ui.widget', 'jquery.iframe-transport', 'jquery.fileupload', 'jquery.fileupload-process', 'jquery.fileupload-validate'));
    wp_localize_script('municipality-form-fileupload', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_enqueue_script('municipality-profile', get_template_directory_uri() . '/js/municipality-profile.js', array('jquery'), null, true);
    
    $obj_upload_dir = wp_upload_dir();
    ob_start();
    ?>
        <fieldset class="round">
            <legend>Contact Details</legend>
            <div class="cpt-fieldbox">
                <label for="municipality_contact_fname">First Name:</label>
                <input id="municipality_contact_fname" name="municipality_contact_fname" type="text" value="<?php echo ($custom)?$custom['municipality_contact_fname'][0]:$_POST['municipality_contact_fname']; ?>" class="required" />
            </div>
            <div class="cpt-fieldbox">
                <label for="municipality_contact_lname">Last Name:</label>
                <input id="municipality_contact_lname" name="municipality_contact_lname" type="text" value="<?php echo ($custom)?$custom['municipality_contact_lname'][0]:$_POST['municipality_contact_lname']; ?>" class="required" />
            </div>
            <div class="cpt-fieldbox">
                <label for="municipality_contact_title">Job Title:</label>
                <input id="municipality_contact_title" name="municipality_contact_title" type="text" value="<?php echo ($custom)?$custom['municipality_contact_title'][0]:$_POST['municipality_contact_title']; ?>" class="required" />
            </div>
            <div class="cpt-fieldbox">
                <label for="municipality_email">Email:</label>
                <input id="municipality_email" name="municipality_email" type="email" value="<?php echo $_POST['municipality_email']; ?>" data-type="email" class="required" />
            </div>
            <div class="cpt-fieldbox">
                <label for="municipality_phone">Telephone:</label>
                <input id="municipality_phone" name="municipality_phone" type="text" value="<?php echo ($custom)?$custom['municipality_phone'][0]:$_POST['municipality_phone']; ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="municipality_fax">Fax:</label>
                <input id="municipality_fax" name="municipality_fax" type="text" value="<?php echo ($custom)?$custom['municipality_fax'][0]:$_POST['municipality_fax']; ?>" />
            </div>
        </fieldset>
        <fieldset class="round">
            <legend>Images</legend>
            <div id="municipality_profile_image_box" class="cpt-fieldbox cf">
                <label for="municipality_profile_image">Profile Picture: <span class="cpt-desc">(5MB max, your image will automatically be resized)</span></label>
                <div class="cpt-desc">If you have a Premium membership with us, your profile picture will be used in your automatically generated ad space.</div>
                <div class="files municipality-profile-thumb">
                    <span></span><img src="<?php if($custom && $custom['municipality_profile_image'][0]) 
                                                     echo $obj_upload_dir['baseurl'].'/municipal-pics/'.$custom['municipality_profile_image'][0]; 
                                                 else if($_POST['municipality_profile_image']) 
                                                     echo $obj_upload_dir['baseurl'].'/municipal-pics/'.$_POST['municipality_profile_image']; 
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
                <input id="municipality_profile_image" name="municipality_profile_image" type="hidden" value="<?php echo ($custom)?$custom['municipality_profile_image'][0]:$_POST['municipality_profile_image']; ?>" />
            </div>
        </fieldset>
        <fieldset class="round">
            <legend>Municipal Office Details</legend>
            <div class="cpt-fieldbox">
                <label for="municipality_title">Name of Municipality:</label>
                <input id="municipality_title" name="municipality_title" type="text" value="" readonly>
            </div>
            <div class="cpt-fieldbox">
                <label for="municipality_address">Address:</label>
                <input id="municipality_address" name="municipality_address" type="text" value="<?php echo ($custom)?$custom['municipality_address'][0]:$_POST['municipality_address']; ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="municipality_city">Town/City:</label>
                <input id="municipality_city" name="municipality_city" type="text" value="<?php echo ($custom)?$custom['municipality_city'][0]:$_POST['municipality_city']; ?>" />
            </div>
            <div class="cpt-fieldbox">
                <label for="municipality_province">Province:</label>
                <select id="municipality_province" name="municipality_province">
                    <option value="">- Select -</option>
                    <?php echo municipality_provinces_list(($custom)?$custom['municipality_province'][0]:$_POST['municipality_province']); ?>
                </select>
            </div>
            <div class="cpt-fieldbox">
                <label for="municipality_postal">Postal Code:</label>
                <input id="municipality_postal" name="municipality_postal" type="text" value="<?php echo ($custom)?$custom['municipality_postal'][0]:$_POST['municipality_postal']; ?>" maxlength="7" data-regexp="[ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ] ?\d[ABCEGHJKLMNPRSTVWXYZ]\d" data-regexp-flag="i" />
            </div>
            <div class="cpt-fieldbox">
                <label for="municipality_website">Website:</label>
                <input id="municipality_website" name="municipality_website" type="text" value="<?php echo ($custom)?$custom['municipality_website'][0]:(($_POST['municipality_website'])?$_POST['municipality_website']:'http://'); ?>" />
            </div>
        </fieldset>
        <fieldset class="round">
            <div class="cpt-desc">If the Google Map on your profile does not work or you would like to override the location, please use latitude and longitude coordinates.  <a href="http://www.itouchmap.com/latlong.html" target="_blank">Visit this page</a> to figure out your coordinates.</div>
            <legend>Override Google Map Location with Coordinates</legend>
            <div class="cpt-fieldbox last">
                <label for="municipality_map_lat">Latitude:</label>
                <input id="municipality_map_lat" name="municipality_map_lat" type="text" value="<?php echo ($custom)?$custom['municipality_map_lat'][0]:$_POST['municipality_map_lat']; ?>" />
                <label for="municipality_map_long">Longitude:</label>
                <input id="municipality_map_long" name="municipality_map_long" type="text" value="<?php echo ($custom)?$custom['municipality_map_long'][0]:$_POST['municipality_map_long']; ?>" />
            </div>
        </fieldset>
    <?php
    $formOutput .= ob_get_clean();
    return $formOutput;
}


// Import any municipality widgets and shortcodes.;
require_once 'sc-municipality-join-form.php'; ?>
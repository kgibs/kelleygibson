<?php
// Return shortcode for municipality join form
function municipality_join_form_shortcode($atts) {
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
						jQuery('#new_post').hide();
						jQuery('.cpt-exists').show();
					} else {
						// the municipality does not exist.
						jQuery('.cpt-exists').hide();
						jQuery('#new_post').show();
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
        if (!isset($_POST['municipality_email'])) {
            echo '<p class="error">ERROR:  Many fields are required.</p>';
        } else if (empty($_SESSION['captcha']) || trim(strtolower($_POST['captcha'])) != $_SESSION['captcha']) {
            echo '<p class="error">ERROR:  Incorrect Human Verification word. Please try again.</p>';
        } else {
            $title = wp_kses($_POST['municipality_title'], array());
            $new_municipality = array(
                'post_title'    => $title,
                'post_name'     => sanitize_title_with_dashes(wp_kses($_POST['municipality_title'], array()),'','save'),
                'post_content'  => '',
                'post_status'   => 'pending',
                'post_type'     => 'municipality'
            );
            //save the new post (which in turn updates all post meta as well)
            $post_id = wp_insert_post($new_municipality);

            //send emails
            $edit_link = site_url('/wp-admin/post.php?post='.$post_id.'&action=edit');
            $headers[] = 'From: muniSERV <info@muniserv.ca>';
            add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));

            //Email admin about new municipality
            //Read in template and populate with passed variables
            ob_start();
            include 'emails/admin-municipality-submitted.php';
            $message = ob_get_contents();
            ob_end_clean();
            wp_mail(get_bloginfo('admin_email'), 'A municipality has joined muniSERV - Approval required', $message, $headers);

            //email user that their profile has been submitted but awaiting approval
            //Read in template and populate with passed variables
            ob_start();
            include 'emails/user-municipality-submitted.php';
            $message = ob_get_contents();
            ob_end_clean();
            //$email = get_bloginfo('admin_email'); // REMOVE THIS WHEN GOING LIVE
            wp_mail($email, 'Your muniSERV municipality profile is awaiting approval.', $message, $headers);

            $complete = true;
        }
    }

    if ($complete == true) :
        if ($thankyou) :
            wp_redirect($thankyou);
            exit;
        else :
        ?>
        <p>Success!  Your municipality profile is being processed.</p>
        <p>An email has been sent to you confirming your submission.</p>
        <p>You'll receive a second email when your municipality profile has been approved to be included on the muniSERV website.  The approval process may take up to 24 hours.</p>
        <p>Thank you for joining muniSERV!</p>
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
            	<p>A profile for your municipality already exists. Please <a href="<?php bloginfo('url'); ?>/municipalities/your-municipal-profile">click here</a> to edit your profile.</p>
            </div>

        <form id="new_post" style="display:none;" name="new_post" method="post" action="" novalidate>
        
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
add_shortcode('ms-municipality-join-form', 'municipality_join_form_shortcode');

// we are running the check for whether the minicipality already exists in the systemnow.
function ajax_check_municipality(){
	$title = $_POST['municipality'];
	$muni_slug = sanitize_title_with_dashes( $title, '', 'save' );
	$args = array(
		// WP_Query Arguments
		'post_type' => 'municipality',
		'name' => $muni_slug
	);
	$muni_Q = new WP_Query($args);
	
	header('Content-Type: text/json; charset=utf8'); // this tells jQuery that the data we're sending is JSON

	if ( $muni_Q->have_posts() ) : $muni_Q->the_post();
		// we know there is a result so it exists.  send json that says so and pass its id
		echo json_encode(array(
			'exists'=> true,
			'id' => get_the_id()
			// add any other data about the post here:  examples:
				// 'title' => get_the_title(),
				// 'permalink' => get_permalink(get_the_id())
		));
	else :
		// no posts found return json that says it doesn't exist
		echo json_encode(array(
			'exists'=> false
		));
	endif;
	die(); //stop execution, it's an ajax function.  
	// Always end ajax callbacks with die();
}

add_action('wp_ajax_check_municipality', 'ajax_check_municipality' ); // logged in users
add_action('wp_ajax_nopriv_check_municipality', 'ajax_check_municipality' ); // non logged in users

?>
<?php
// Return shortcode for consultant profile form
function consultant_profile_form_shortcode($atts) {
    // Add specific scripts/styles
    wp_enqueue_script('pass-strength', get_template_directory_uri() . '/js/pass-strength.js', array('jquery','password-strength-meter'));
    
    ob_start();
    $complete = false;
    $curr_user = null;
    $consultant = null;
    $custom = null;
    
    // Form submission handling
    if('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action']) &&  $_POST['action'] == "edit_post") {
        $_POST = stripslashes_deep($_POST);
        $title = wp_kses($_POST['consultant_title'], array());
        $level = $_POST['consultant_membership'];
        $email = sanitize_email($_POST['consultant_email']);
        // Do some minor form validation to make sure there is content
        if (!isset($_POST['consultant_title'])) {
            echo '<p class="error">ERROR:  Many fields are required.</p>';
        } else if ($email != $_POST['user_email'] && null != email_exists($email)) {
            echo '<p class="error">ERROR:  Email already exists in the system. Please enter another.</p>';
        } else {    
            //save the post (which in turn updates all post meta as well)
            wp_update_post(array(
                'ID'            => $_POST['consultant_id'],
                'post_title'    => $title,
                'post_content'  => wp_kses($_POST['consultant_description'], array('p'=>array(),'br'=>array()))
            ));
            
            // Save categories
            $int_categories = array();
            foreach ($_POST['consultant_categories'] as $selected_cat)
                $int_categories[] = intval($selected_cat);
            wp_set_post_terms($_POST['consultant_id'], $int_categories, 'consultant_categories');
            
            // Update user email address and maybe password
            $user_args = array(
                'ID' => $_POST['user_id'],
                'user_email' => $email,
                'nickname' => $title,
                'display_name' => $title,
                'first_name' => sanitize_text_field($_POST['consultant_contact_fname']),
                'last_name' => sanitize_text_field($_POST['consultant_contact_lname'])
            );
            if ($_POST['consultant_password'])
                $user_args['user_pass'] = $_POST['consultant_password'];
            wp_update_user($user_args);
            $_POST['user_email'] = $email;
    
            $complete = true;
        }
    } else {
        $curr_user = wp_get_current_user();
        $consultant = get_post($curr_user->consultant_id);
        $custom = get_post_custom($curr_user->consultant_id);
        $level = $custom['consultant_membership'][0];
        $email = $custom['consultant_email'][0];
        $categories = wp_get_post_terms($curr_user->consultant_id, 'consultant_categories');
        $cats = array();
        foreach($categories as $cat) {
            $cats[$cat->term_id] = $cat->name;
        }
    }
    // Success message
    if ($complete) :
    ?>
    <p class="success">Saved.</p>
    <?php
    endif;
    // Form output
    ?>
    <form id="edit_post" name="edit_post" method="post" action="" novalidate>
        <fieldset class="round">
            <legend>Account Details</legend>
            <div class="cpt-fieldbox">
                <label for="consultant_username">Username:</label>
                <input id="consultant_username" name="consultant_username" type="text" autocomplete="off" value="<?php echo ($curr_user)?$curr_user->user_login:$_POST['consultant_username']; ?>" readonly />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_email">Email:</label>
                <input id="consultant_email" name="consultant_email" type="email" value="<?php echo $email; ?>" data-type="email" class="required" />
            </div>
            <div class="cpt-fieldbox">
                <label for="consultant_password">Change Password:</label>
                <input type="password" name="consultant_password" id="consultant_password" value="" autocomplete="off" data-equalto="#consultant_password2" data-error-container="#consultant_password_error" data-error-message="Passwords must match." /> <span class="description"><?php _e( 'If you would like to change the password type a new one. Otherwise leave this blank.' ); ?></span>
                <div id="consultant_password_error"></div>
                <label for="consultant_password">Re-enter Password:</label>
				<input type="password" name="consultant_password2" id="consultant_password2" value="" autocomplete="off" data-equalto="#consultant_password" data-error-container="#consultant_password2_error" data-error-message="Passwords must match." /> <span class="description"><?php _e( 'Type your new password again.' ); ?></span>
                <div id="consultant_password2_error"></div>
                <div id="pass-strength-result"><?php _e( 'Strength indicator', 'theme-my-login' ); ?></div>
				<div class="description indicator-hint"><?php _e( 'Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).' ); ?></div>
            </div>
        </fieldset>
        <?php echo consultant_profile_form_fields($level, $consultant, $custom, $cats); ?>
        <p><input type="submit" value="Update" id="submit" name="submit" class="button" /></p>

        <input type="hidden" name="action" value="edit_post" />
        <input type="hidden" name="consultant_membership" value="<?php echo $level; ?>" />
        <input type="hidden" name="consultant_id" value="<?php echo ($curr_user)?$curr_user->consultant_id:$_POST['consultant_id']; ?>" />
        <input type="hidden" name="user_id" value="<?php echo ($curr_user)?$curr_user->ID:$_POST['user_id']; ?>" />
        <input type="hidden" name="user_email" value="<?php echo ($curr_user)?$curr_user->user_email:$_POST['user_email']; ?>" />
        <?php wp_nonce_field( 'edit-post' ); ?>
    </form>
    <div class="clear"></div>
    <?php
    $widgetOutput .= ob_get_clean();
    
    return $widgetOutput;
}
add_shortcode('ms-consultant-profile-form', 'consultant_profile_form_shortcode');
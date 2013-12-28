<?php
// Return shortcode for consultant join form
function consultant_join_form_shortcode($atts) {
    global $post;
    require get_template_directory().'/lib/class-paypal-api.php';
    
    extract(shortcode_atts(array(
        'level' => '1',
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
    wp_enqueue_script('consultant-form-fileupload', get_template_directory_uri() . '/js/consultant-form-fileupload.js', array('jquery', 'jquery.ui.widget', 'jquery.iframe-transport', 'jquery.fileupload', 'jquery.fileupload-process', 'jquery.fileupload-validate'));
    wp_localize_script('consultant-form-fileupload', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
    
    ob_start();
    $complete = false;
    $submit_ready = false;
    
    // Form submission handling
    if('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action']) &&  $_POST['action'] == "new_post") {
        $_POST = stripslashes_deep($_POST);
        $username = sanitize_user($_POST['consultant_username']);
        $email = sanitize_email($_POST['consultant_email']);
        // Do some minor form validation to make sure there is content
        if (!isset($_POST['consultant_title'])) {
            echo '<p class="error">ERROR:  Many fields are required.</p>';
        } else if (null != username_exists($username)) {
            echo '<p class="error">ERROR:  Username already exists in the system. Please enter another.</p>';
        } else if (null != email_exists($email)) {
            echo '<p class="error">ERROR:  Email already exists in the system. Please enter another.</p>';
        } else if (empty($_SESSION['captcha']) || trim(strtolower($_POST['captcha'])) != $_SESSION['captcha']) {
            echo '<p class="error">ERROR:  Incorrect Human Verification word. Please try again.</p>';
        } else {
            // Image button submit means PayPal, regular submit means free
            if (isset($_POST['submit_x'])) {
                // Store formdata in case it gets cancelled
                if (!$_POST['formdata_id']) {
                    $formdata_id = ms_paypal_api::SaveFormData($_POST);
                    $_POST['formdata_id'] = $formdata_id;
                }
                // Store post to the session and redirect to PayPal
                $_SESSION['join_form_post'] = $_POST;
                ms_paypal_api::StartExpressCheckout(); // Redirects to PayPal then back to page-paypal-confirm.php template.
            } else if (isset($_POST['submit'])) {
                $submit_ready = true;
                $result = array(
                    'AMT' => $_POST['AMT'] + $_POST['TAXAMT'],
                    'ITEMAMT' => $_POST['AMT'],
                    'TAXAMT' => $_POST['TAXAMT'],
                    'CURRENCYCODE' => $_POST['CURRENCYCODE'],
                    'FIRSTNAME' => $_POST['consultant_contact_fname'],
                    'LASTNAME' => $_POST['consultant_contact_lname'],
                    'EMAIL' => $email,
                    'PAYMENTREQUEST_0_DESC' => $_POST['PAYMENTREQUEST_0_DESC']
                );
            }
        }
    }
    
    // PayPal submission handling
    if (isset($_GET['func']) && $_GET['func'] == 'confirm' && isset($_GET['token']) && isset($_GET['PayerID'])) {
        $result = ms_paypal_api::ConfirmExpressCheckout();
        //TODO: Add errors for when the expresscheckout is unsuccessful or if the session has expired (post data not accessible)
        if ($result['ACK'] == 'Success' && $result['do_result']['ACK'] == 'Success' && isset($_SESSION['join_form_post'])) {
            $_POST = $_SESSION['join_form_post'];
            $username = sanitize_user($_POST['consultant_username']);
            $email = sanitize_email($_POST['consultant_email']);
            $submit_ready = true;
        } else {
            print_r($result);
        }
    }
    
    // If PayPal payment is complete or submission of free form has happened, proceed with user/consultant creation
    if ($submit_ready == true) {
        $title = wp_kses($_POST['consultant_title'], array());
        // Generate the password and create the user
        $password = wp_generate_password(12, false);
        $user_id = wp_create_user($username, $password, $email);
        // Set the role
        $user = new WP_User($user_id);
        $user->set_role('consultant');

        $new_consultant = array(
            'post_title'    => $title,
            'post_name'     => sanitize_title_with_dashes(wp_kses($_POST['consultant_title'], array()),'','save'),
            'post_content'  => wp_kses($_POST['consultant_description'], array('p'=>array(),'br'=>array())),
            'post_status'   => 'pending',
            'post_type'     => 'consultant'
        );
        //save the new post (which in turn updates all post meta as well)
        $post_id = wp_insert_post($new_consultant);

        // Save categories
        $int_categories = array();
        foreach ($_POST['consultant_categories'] as $selected_cat)
            $int_categories[] = intval($selected_cat);
        wp_set_post_terms($post_id, $int_categories, 'consultant_categories');

        // Set the nickname and consultant id of created user and disable the account to start
        wp_update_user(array(
            'ID' => $user_id, 
            'nickname' => $title,
            'display_name' => $title,
            'first_name' => sanitize_text_field($_POST['consultant_contact_fname']),
            'last_name' => sanitize_text_field($_POST['consultant_contact_lname']),
            'consultant_id' => $post_id
        ));
        update_user_meta($user_id, 'ja_disable_user', 1);
        
        // Remove stored formdata in the database now that payment has gone through
        if (isset($_POST['formdata_id']))
            ms_paypal_api::RemoveFormData($_POST['formdata_id']);

        // Save paypal transaction to the database
        if ($_POST['payment'] != 'deferred')
            ms_paypal_api::SavePayment($result, $post_id);
        
        //unset the post from the session
        if (isset($_SESSION['join_form_post']))
            unset($_SESSION['join_form_post']);

        //send emails
        $edit_link = site_url('/wp-admin/post.php?post='.$post_id.'&action=edit');
        $headers[] = 'From: muniSERV <info@muniserv.ca>';
        add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));

        //Email admin about new consultant
        //Read in template and populate with passed variables
        ob_start();
        include 'emails/admin-consultant-submitted.php';
        $message = ob_get_contents();
        ob_end_clean();
        wp_mail(get_bloginfo('admin_email'), 'A consultant has joined muniSERV - Approval required', $message, $headers);

        //email user that their profile has been submitted but awaiting approval
        //Read in template and populate with passed variables
        ob_start();
        include 'emails/user-consultant-submitted.php';
        $message = ob_get_contents();
        ob_end_clean();
        //$email = get_bloginfo('admin_email'); // REMOVE THIS WHEN GOING LIVE
        wp_mail($email, 'Your muniSERV consultant profile is awaiting approval.', $message, $headers);

        $complete = true;
    }

    if ($complete == true) :
        if ($thankyou) :
            wp_redirect($thankyou);
            exit;
        else :
        ?>
        <p>Success!  Your payment has been received and your consultant profile is being processed.</p>
        <p>An email has been sent to you which includes your muniSERV account password.</p>
        <p>You'll receive a second email when your consultant profile has been approved to be included on the muniSERV website.  The approval process may take up to 24 hours.</p>
        <p>Thank you for joining muniSERV!</p>
        <p><a href="<?php echo site_url(); ?>">Back to homepage</a></p>
        <?php 
        endif;
    else :
        
        // If PayPal is cancelled we reload populated form
        if (count($_POST) == 0 && isset($_GET['token']) && isset($_SESSION['join_form_post']))
            $_POST = $_SESSION['join_form_post'];

        $check_consultant_declaration[$_POST["consultant_declaration"]] = 'checked';
        $check_consultant_agree[$_POST["consultant_agree"]] = 'checked';
        if (defined('MEMBER_DISCOUNT_PRICING')) {
            $ary_pricing = unserialize(MEMBER_DISCOUNT_PRICING);
            $ary_taxing = unserialize(MEMBER_DISCOUNT_TAX);
            $ary_descriptions = unserialize(MEMBER_DISCOUNT_DESC);
        } else {            
            $ary_pricing = unserialize(MEMBER_PRICING);
            $ary_taxing = unserialize(MEMBER_TAX);
            $ary_descriptions = unserialize(MEMBER_DESC);
        }
        // Form output
        ?>
        <form id="new_post" name="new_post" method="post" action="" novalidate>
            <fieldset class="round">
                <legend>Account Details</legend>
                <div class="cpt-desc">Please create a username that you will remember as you will use it to log in to the muniSERV system in the future. The username is not case sensitive.</div>
                <div class="cpt-desc">We will use the email address you provide to send notifications.  If you lose your password this is the email address used to verify your account and set a new password.</div>
                <div class="cpt-fieldbox">
                    <label for="consultant_username">Username:</label>
                    <input id="consultant_username" name="consultant_username" type="text" value="<?php echo $_POST['consultant_username']; ?>" class="required" />
                </div>
                <div class="cpt-fieldbox">
                    <label for="consultant_email">Email:</label>
                    <input id="consultant_email" name="consultant_email" type="email" value="<?php echo $_POST['consultant_email']; ?>" data-type="email" class="required" />
                </div>
            </fieldset>
            <?php echo consultant_profile_form_fields($level); ?>
            <fieldset class="round">
                <legend>Legal</legend>
                <div class="cpt-fieldbox last">
                    <label for="consultant_declaration">Declarations:</label>
                    <ul class="radio-list">
                        <li><label><input id="consultant_declaration" name="consultant_declaration" type="checkbox" value="1" <?php echo $check_consultant_declaration['1']; ?> class="required" />I have read the above information and certify that all the information provided is accurate to the best of my knowledge and ability.</label></li>
                        <li><label><input id="consultant_agree" name="consultant_agree" type="checkbox" value="1" <?php echo $check_consultant_agree['1']; ?> class="required" />I have read and agree to muniSERV's <a href="<?php echo site_url('/terms-conditions'); ?>" target="_blank">Terms & Conditions</a>.</label></li>
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
                <?php if ($ary_pricing[$level - 1] > 0 && $_GET['payment'] != 'deferred') : ?>
                <legend>Payment</legend>
                <div class="cpt-desc" style="margin-bottom:10px;"><img src="<?php echo get_template_directory_uri(); ?>/images/credit-cards.jpg" alt="Accepted credit cards" width="189" height="25" class="alignright" />PayPal is a secure online payment system that accepts all major credit cards. You do not need to set up a PayPal account to use their secure services.</div>
                <div class="cpt-fieldbox last">
                    <label for="submit"><?php echo $ary_descriptions[$level - 1]; ?>: <strong>$<?php echo number_format($ary_pricing[$level - 1], 2); ?></strong></label>
                    <input type="image" name="submit" id="submit" value="Proceed" src="http://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif" />
                </div>
                <?php else : ?>
                <legend>Confirmation</legend>
                <div class="cpt-fieldbox last">
                    <label for="submit"><?php echo $ary_descriptions[$level - 1]; ?>: 
                    <?php if ($_GET['payment'] == 'deferred') : ?>
                        <strong>$<?php echo number_format($ary_pricing[$level - 1], 2); ?></strong></label>
                    <label><em>NOTE: You have chosen to create a profile and defer payment.</em></label>
                    <?php else : ?>
                        <strong>FREE</strong></label>
                    <?php endif; ?>
                    <input type="submit" name="submit" id="submit" value="Proceed" class="button" />
                </div>
                <?php endif; ?>
            </fieldset>
            <input type="hidden" name="payment" value="<?php echo $_GET['payment']; ?>" />
            <input type="hidden" name="formdata_id" value="<?php echo $_POST['formdata_id']; ?>" />
            <input type="hidden" name="AMT" value="<?php echo $ary_pricing[$level - 1]; ?>" />
            <input type="hidden" name="TAXAMT" value="<?php echo $ary_taxing[$level - 1]; ?>" />
            <input type="hidden" name="PAYMENTREQUEST_0_DESC" value="<?php echo $ary_descriptions[$level - 1]; ?>" />
            <input type="hidden" name="CURRENCYCODE" value="CAD" />
            <input type="hidden" name="RETURN_URL" value="<?php echo get_permalink($post->ID); ?>" />
            <input type="hidden" name="CANCEL_URL" value="<?php echo get_permalink($post->ID); ?>" />
            <input type="hidden" name="action" value="new_post" />
            <input type="hidden" name="consultant_membership" value="<?php echo $level; ?>" />
            <?php wp_nonce_field( 'new-post' ); ?>
        </form>
        <div class="clear"></div>
    <?php
    endif;
    
    $widgetOutput .= ob_get_clean();
    
    return $widgetOutput;
}
add_shortcode('ms-consultant-join-form', 'consultant_join_form_shortcode');
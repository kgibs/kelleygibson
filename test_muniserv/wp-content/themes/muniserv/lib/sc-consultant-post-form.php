<?php
// Return shortcode for consultant article post form
function ms_consultant_post_form_shortcode($atts){
	$storyType = '';
	$title =  '';
	$article =  '';
    $addPost = false;
	$errorOutput = '';
	$thanksOutput = '';
	if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) &&  $_POST['action'] == "new_post") {
		var_pre($_POST);
		// Do some minor form validation to make sure there is content
		if (!$_POST['storytype']) {
			$errorOutput .= '<p>Please select at least one relevant category.</p>';
		}
		else if (!$_POST['title']) {
			$errorOutput .= '<p>Please enter a title for your article.</p>';
		}
		else if (!$_POST['article']) {
			$errorOutput .= '<p>Please enter your article.</p>';
		} else {
            $addPost = true;
            $storyType =  $_POST['storytype'];
            $title =  $_POST['title'];
            $article = $_POST['article'];

            // ADD THE FORM INPUT TO $new_post ARRAY
            $new_post = array(
                'post_title'	=>	$title,
                'post_content'	=>	$article,
                'post_category'	=>	$storyType,  // Usable for custom taxonomies too
                'post_status'	=>	'pending',           // Choose: publish, preview, future, draft, etc.
                'post_type'	=>	'post'  //'post',page' or use a custom post type if you want to
            );
            //SAVE THE POST
            $pid = wp_insert_post($new_post);
            //SEND APPROVAL EMAIL
            send_approve_link($pid);
        }
	}
		
    //Begin Form 
    ob_start(); ?>
    <?php if ($addPost) : ?>
    	<div id="thank-you-message">
        	<h2 class="thank-you-message">Thank you for your submission.</h2>
        </div>	
    <?php else : ?>
        <?php if ($errorOutput) : ?><div id="errorOutput"><?php echo $errorOutput; ?></div><?php endif; ?>
        <form id="new_post" name="new_post" method="post" action="<?php the_permalink();?>" enctype="multipart/form-data">
            <!-- post Content -->
            <fieldset class="content">
                <fieldset name="title">
                    <label for="title">Article Title:</label><br />
                    <input type="text" id="title" value="<?php echo $title; ?>" tabindex="15" name="title" />
                </fieldset>
                <br />
                <fieldset name="article">
                    <label for="article">Article Content:</label>
                    <textarea id="article" tabindex="17" name="article"><?php echo $article; ?></textarea>
                </fieldset>
            </fieldset>
        	
            <br />
            
            <!-- Story type -->
            <fieldset class="storytype">
                <label for="storytype">Select Categories:</label><br />
                	<?php $categoriesList = get_categories('orderby=name&order=ASC&hide_empty=0&exclude=-1'); 
						foreach($categoriesList as $singleCat) {
					?>

                     <input type="checkbox" value="<?php echo $singleCat->cat_ID; ?>'" id="<?php echo $singleCat->slug; ?>" size="60" name="storytype[]"><?php echo $singleCat->name; ?><br />

					<?php } //end for each ?>                    
            </fieldset>

			<br />
            
            <fieldset class="submit">
                <input type="submit" value="submit" tabindex="45" id="submit" name="submit" class="button" />
            </fieldset>
        
            <input type="hidden" name="action" value="new_post" />
            <?php wp_nonce_field( 'new-post' ); ?>
        </form>
    <?php endif; ?>
<?php
	if(empty($widgetOutput)){
		$widgetOutput = '';
	}
    $widgetOutput .= ob_get_clean();
    return $widgetOutput;
}
add_shortcode( 'ms-consultant-post-form', 'ms_consultant_post_form_shortcode' );

function set_html_content_type() { return 'text/html'; }

function send_approve_link($pid) {
    // set the content for the email
    $content = '<p>A new article titled &quot;';
    $content .= esc_html( get_the_title($pid) ) . '&quot;';
    $content .= ' has been submitted for your review.</p>';
    // create an url for verify link to new post
    $url = site_url('/wp-admin/post.php?post='.$pid.'&action=edit');
    $content .= '<p><a href="' . $url . '">Click here to review the new article.</a></p>';
    $from = get_bloginfo('name');
    $from_email = 'gibby@gibby.ca';
    $headers[] = 'From: ' . $from . ' <' . $from_email . '>';
    // sending email in html format
    add_filter( 'wp_mail_content_type', 'set_html_content_type' );
	var_pre($headers);
	//var_pre(wp_mail( get_option('admin_email'), 'Confirm your Post', $content, $headers));
	var_pre(mail('infinitymedia@gmail.com', 'my subject', 'my body'));
    remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
}
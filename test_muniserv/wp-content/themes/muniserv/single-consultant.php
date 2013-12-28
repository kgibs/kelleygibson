<?php
/**
 * The Template for displaying consultant profiles.
 *
 * @package HomeCooked
 * @subpackage html5
 */
get_header();

    //Build jobsearch url if it exists
    $jobsearch = '';
    if ($_SESSION['s_consultant_page'] > 1) {
        $jobsearch .= 'page/'.$_SESSION['s_consultant_page'].'/';
    }
    if (isset($_SESSION['s_consultant_query'])) {
        $jobsearch .= '?'.$_SESSION['s_consultant_query'];
    }
    while (have_posts()) : the_post();
        $obj_upload_dir = wp_upload_dir();
        $user_id = 0;
        $users = get_users(array('meta_key'=>'consultant_id','meta_value'=>get_the_ID()));
        if (count($users) > 0)
            $user_id = $users[0]->ID;
        $consultant_title = get_the_title();
        $custom = get_post_custom(get_the_ID());
        //Membership
        $level = $custom["consultant_membership"][0];
        //Photos
        $consultant_profile_image = $custom["consultant_profile_image"][0];
        $consultant_sidebar_image = $custom["consultant_sidebar_image"][0];
        //Contact Info
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
        //More Info
        $consultant_tagline = $custom["consultant_tagline"][0];
        $consultant_testimonial_1 = $custom["consultant_testimonial_1"][0];
        $consultant_testimonial_1_by = $custom["consultant_testimonial_1_by"][0];
        $consultant_testimonial_2 = $custom["consultant_testimonial_2"][0];
        $consultant_testimonial_2_by = $custom["consultant_testimonial_2_by"][0];
        $consultant_testimonial_3 = $custom["consultant_testimonial_3"][0];
        $consultant_testimonial_3_by = $custom["consultant_testimonial_3_by"][0];
        $consultant_awards_other = unserialize($custom["consultant_awards_other"][0]);
        //Success Story
        $consultant_success_story = $custom["consultant_success_story"][0];
        //Social 
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
        if ($consultant_twitter_username && $consultant_twitter_key && $consultant_twitter_secret && $consultant_twitter_secret && $consultant_twitter_token && $consultant_twitter_access)
            $ary_latest_tweet = get_latest_tweet_array($consultant_twitter_key, $consultant_twitter_secret, $consultant_twitter_token, $consultant_twitter_access, $consultant_twitter_username);
        ?>
        <nav id="nav-above" class="navigation cf">
            <div class="nav-previous"><a href="<?php echo home_url('/consultants/search/').$jobsearch; ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/blue-arrow.png"/>Back to the Search Results</a></div>
            <div class="nav-next"><a href="<?php echo home_url('/consultants/search/'); ?>">Start New Search<img src="<?php echo get_template_directory_uri(); ?>/images/red-arrow.png"/></a></div>
        </nav>
        <div id="primary" <?php if($level > 1): ?>class="one-column"<?php endif; ?>>
			<div id="content" role="main">
                
                <div class="shadow">
                    <div class="inner-shadow content-area cf">
                        <?php if($level > 1): ?>
                        <div id="left-side">
                        <?php endif; ?>
                            <div class="overview-header cf">
                                <?php if ($consultant_profile_image) : ?>
                                <div class="consultant-profile-thumb">
                                    <span></span><img src="<?php echo $obj_upload_dir['baseurl'].'/profile-pics/thumbnail/'.$consultant_profile_image; ?>" src="<?php echo $consultant_title; ?>" />
                                </div>
                                <?php endif; ?>
                                <h1><?php echo $consultant_title; ?></h1>
                                <h2><?php echo $consultant_tagline; ?></h2>
                            </div>
                            <div class="contact-overview">
                                <div class="c-info">
                                    <?php if ($consultant_contact_fname || $consultant_contact_lname) : ?><div class="name"><label>Contact Name: </label><?php echo $consultant_contact_fname.' '.$consultant_contact_lname.(($consultant_contact_designations)?', '.$consultant_contact_designations:'').(($consultant_contact_title)?', '.$consultant_contact_title:''); ?></div><?php endif; ?>
                                    <?php if ($consultant_phone) : ?><div class="number"><label>Telephone: </label><a href="tel:<?php echo $consultant_phone; ?>"><?php echo $consultant_phone; ?></a></div><?php endif; ?>
                                    <?php if ($consultant_email) : ?><div class="email"><label>Email: </label><a href="mailto:<?php echo $consultant_email; ?>"><?php echo $consultant_email; ?></a></div><?php endif; ?>
                                    <?php if ($consultant_website) : ?><div class="website"><label>Website: </label><a href="<?php echo $consultant_website; ?>" target="_blank"><?php echo str_replace('http://','',$consultant_website); ?></a></div><?php endif; ?>
                                    <?php if ($consultant_fax) : ?><div class="fax"><label>Fax: </label><?php echo $consultant_fax; ?></div><?php endif; ?>
                                    <?php if ($consultant_address || $consultant_city || $consultant_province || $consultant_postal) : ?>
                                    <div class="address"><label>Address: </label><?php echo $consultant_address; ?> <?php echo $consultant_city; ?><?php echo ($consultant_province)?', '.$consultant_province:'';?> <?php echo $consultant_postal; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="overview-about">
                                    <?php the_content(); ?>
                                </div>
                            </div>
                            <?php
                            if ($consultant_map_lat && $consultant_map_long) {
                                $gmap = urlencode($consultant_map_lat).','.urlencode($consultant_map_long).' ('.urlencode($consultant_title).')';
                            } else if ($consultant_city && $consultant_province) {
                                $gmap = (($consultant_address) ? urlencode($consultant_address).',+' : urlencode($consultant_title).',+').urlencode($consultant_city).',+'.urlencode($consultant_province).' ('.urlencode($consultant_title).')';
                            }
                            if ($gmap) : ?>
                            <div class="overview-map">
                                <iframe width="100%" height="200" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.ca/?q=<?php echo $gmap; ?>&amp;ie=UTF8&amp;t=m&amp;vpsrc=0&amp;iwloc=&amp;output=embed"></iframe><br />
                                <small><a target="_blank" href="http://maps.google.ca/?q=<?php echo $gmap; ?>&amp;ie=UTF8&amp;t=m&amp;vpsrc=0&amp;source=embed">View Larger Map</a></small>
                            </div>
                            <?php endif; ?>
                            <div class="serv-hours">
                                <?php if ($consultant_serving) : ?><div class="serving"><label>Serving: </label><?php echo $consultant_serving; ?></div><?php endif; ?>
                                <?php if ($consultant_hours) : ?><div class="hours"><label>Hours: </label><?php echo $consultant_hours; ?></div><?php endif; ?>
                            </div>
                            <div class="bottom-border"></div>
                            <?php // Get Articles feed
                            $latest = get_posts(array('numberposts' => 3, 'author' => $user_id));
                            if ($latest) : ?>
                            <div class="articles-overview">
                                <h2 class="articles-title">Articles by <?php the_title(); ?>:</h2>
                                <div id="aticle-box">
                                    <?php foreach ($latest as $post) : setup_postdata($post); ?>
                                    <article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?>>
                                        <header class="entry-header">
                                            <h2> <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                                            <h3><?php echo get_the_category_list(', '); ?></h3>
                                            <p class="date"><?php the_time('l, F, jS, Y') ?> </p>
                                        </header><!-- .entry-header -->
                                        <div class="entry-summary">
                                            <?php echo str_replace( twentyeleven_continue_reading_link(), '', get_the_excerpt() ); ?>
                                        </div><!-- .entry-summary -->
                                       <div class="ReadMore"><?php echo twentyeleven_continue_reading_link('Read Blog'); ?></div>
                                    </article><!-- #post-<?php the_ID(); ?> -->
                                    <?php endforeach;
                                    wp_reset_query()?>
                                    <div class="bottom-border"></div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if ($consultant_success_story) : ?>
                            <div id="success-overview">
                                <h2 class="story">Success Story:</h2>
                                <div class="story-content">
                                    <?php echo $consultant_success_story; ?>
                                </div>
                                <div class="bottom-border"></div>
                            </div>
                            <?php endif; ?>
                            <?php if ($consultant_facebook) : ?>
                            <div id="fb-root"></div>
                            <script>(function(d, s, id) {
                              var js, fjs = d.getElementsByTagName(s)[0];
                              if (d.getElementById(id)) return;
                              js = d.createElement(s); js.id = id;
                              js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=737089039640491";
                              fjs.parentNode.insertBefore(js, fjs);
                            }(document, 'script', 'facebook-jssdk'));</script>
                            <div class="fb-like-box" data-href="<?php echo $consultant_facebook; ?>" data-width="595" data-height="The height of the plugin in pixels (optional)." data-colorscheme="light" data-show-faces="true" data-header="false" data-stream="false" data-show-border="false"></div>
                            <?php endif; ?>
                        <?php if($level > 1): ?>
                        </div>
                        <div id="right-side">
                            <div class="img-endorse">
                                <?php if ($consultant_sidebar_image) : ?>
                                <div class="consultant-sidebar-image">
                                     <img src="<?php echo $obj_upload_dir['baseurl'].'/profile-pics/'.$consultant_sidebar_image; ?>" src="<?php echo $consultant_tagline; ?>" />
                                </div>
                                <?php endif; ?>
                                <?php if ($consultant_testimonial_1) : ?>
                                <div class="endorsement">
                                    <div>"<?php echo $consultant_testimonial_1; ?>"</div>
                                    <?php if ($consultant_testimonial_1_by) : ?>
                                    <p class="side-quote">- <?php echo $consultant_testimonial_1_by; ?></p>
                                    <?php endif; ?>
                                    <div class="bottom-border"></div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($ary_latest_tweet && is_array($ary_latest_tweet)) : ?>
                            <div class="twitter-feed">
                                <div class="twit-title">
                                    <h2><a href="<?php echo 'http://www.twitter.com/'.$ary_latest_tweet[0]->user->screen_name; ?>" target="_blank"><?php echo $ary_latest_tweet[0]->user->name; ?> <?php echo '@'.$ary_latest_tweet[0]->user->screen_name; ?></a></h2>
                                    <div class="feed">
                                        <p><?php echo link_the_urls($ary_latest_tweet[0]->text); ?></p>
                                    </div>
                                </div>
                                <div class="bottom-border"></div>
                            </div>
                            <?php endif; ?>
                            <?php if (is_array($consultant_awards_other) && array_filter($consultant_awards_other)) : ?>
                            <div class="awards">
                                <h2>Awards, Certificates or Designations</h2>
                                <!--<img src="<?php echo get_template_directory_uri(); ?>/images/award-image.jpg"/>
                                <img src="<?php echo get_template_directory_uri(); ?>/images/award-image.jpg"/>-->
                                <ul>
                                <?php for ($i=0; $i < count($consultant_awards_other); $i++): ?>
                                    <?php if ($consultant_awards_other[$i]): ?>
                                    <li><?php echo $consultant_awards_other[$i]; ?></li>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                </ul>
                                <div class="bottom-border"></div>
                            </div>
                            <?php endif; ?>
                            <?php if ($consultant_testimonial_2 || $consultant_testimonial_3) : ?>
                            <div id="endorse-feed">
                                <?php if ($consultant_testimonial_2) : ?>
                                    <div>"<?php echo $consultant_testimonial_2; ?>"</div>
                                    <?php if ($consultant_testimonial_2_by) : ?>
                                    <p class="side-quote">- <?php echo $consultant_testimonial_2_by; ?></p>
                                    <?php endif; ?>
                                    <div class="clear"></div>
                                <?php endif; ?>
                                <?php if ($consultant_testimonial_3) : ?>
                                    <div>"<?php echo $consultant_testimonial_3; ?>"</div>
                                    <?php if ($consultant_testimonial_3_by) : ?>
                                    <p class="side-quote">- <?php echo $consultant_testimonial_3_by; ?></p>
                                    <?php endif; ?>
                                    <div class="bottom-border"></div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($consultant_linkedin || $consultant_twitter || $consultant_facebook || $consultant_googleplus || $consultant_rss || $consultant_youtube) : ?>
                            <div id="social-area">
                                <?php if ($consultant_linkedin) : ?><a id="linkedin" href="<?php echo $consultant_linkedin; ?>" target="_blank"><img alt="Linkedin" src="<?php echo get_template_directory_uri(); ?>/images/linkedin-icon.gif"/></a><?php endif; ?>
                                <?php if ($consultant_twitter) : ?><a id="twitter" href="<?php echo $consultant_twitter; ?>" target="_blank"><img alt="Twitter" src="<?php echo get_template_directory_uri(); ?>/images/twitter-icon.gif"/></a><?php endif; ?>
                                <?php if ($consultant_facebook) : ?><a id="facebook" href="<?php echo $consultant_facebook; ?>" target="_blank"><img alt="facebook" src="<?php echo get_template_directory_uri(); ?>/images/facebook-icon.gif"/></a><?php endif; ?>
                                <?php if ($consultant_googleplus) : ?><a id="google-plus" href="<?php echo $consultant_googleplus; ?>" target="_blank"><img alt="Google Plus" src="<?php echo get_template_directory_uri(); ?>/images/google-plus-icon.gif"/></a><?php endif; ?>
                                <?php if ($consultant_rss) : ?><a id="social" href="<?php echo $consultant_rss; ?>" target="_blank"><img alt="Social" src="<?php echo get_template_directory_uri(); ?>/images/social-icon.gif"/></a><?php endif; ?>
                                <?php if ($consultant_youtube) : ?><a id="youtube" href="<?php echo $consultant_youtube; ?>" target="_blank"><img alt="Youtube" src="<?php echo get_template_directory_uri(); ?>/images/you-tube-icon.gif"/></a><?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div><!-- #content -->
		</div><!-- #primary -->
        <?php if($level == 1): ?>
        <div id="secondary" class="widget-area" role="complementary">
			<?php dynamic_sidebar( 'sidebar-6' ); ?>
		</div><!-- #secondary .widget-area -->
        <?php endif; ?>
        <div class="clear"></div>
        <nav id="nav-below" class="navigation cf">
            <div class="nav-previous"><a href="<?php echo home_url('/consultants/search/').$jobsearch; ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/blue-arrow.png"/>Back to the Search Results</a></div>
            <div class="nav-next"><a href="<?php echo home_url('/consultants/search/'); ?>">Start New Search<img src="<?php echo get_template_directory_uri(); ?>/images/red-arrow.png"/></a></div>
        </nav>
        
    <?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
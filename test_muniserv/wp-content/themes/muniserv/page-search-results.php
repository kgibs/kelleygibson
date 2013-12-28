<?php
/**
 * Template Name: Search Results
 * Description: A Page Template for the Search Results
 *
 * @package HomeCooked
 * @subpackage html5
 */
wp_enqueue_script('consultant-search', get_template_directory_uri().'/js/consultant-search.js', array('jquery'));
get_header();
?>
        <div id="primary" class="one-column">
            <div id="content" role="main">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('content', 'page'); ?>
                <?php endwhile; // end of the loop. ?>

                <div id="search" class="shadow">
                    <div id="inner-search" class="inner-shadow">
                        <h2>Consultant Search</h2>
                        <form id="con-search-form" method="GET">
                            <fieldset>
                                <div id="bus-nam" class="round">
                                    <label>Business Name:</label>
                                    <input type="text" name="s_consultant_title" id="bus-name" autofocus="autofocus" value="<?php echo $_REQUEST['s_consultant_title']; ?>">
                                    <div class="clr-crit">
                                        <p>Clear Criteria</p>
                                        <button type="button" rel="bus-name">X</button>
                                    </div>
                                </div>
                                <div id="con-nam" class="round">
                                    <label>Consultant Name:</label>
                                    <input type="text" name="s_consultant_contact_name" id="con-name" value="<?php echo $_REQUEST['s_consultant_contact_name']; ?>">
                                    <div class="clr-crit">
                                        <p>Clear Criteria</p>
                                        <button type="button" rel="con-name">X</button>
                                    </div>
                                </div>
                                <div id="cat-nam" class="round">
                                    <label>Category:</label>
                                    <?php wp_dropdown_categories(array(
                                                        'child_of' => 0,
                                                        'class' => '', 
                                                        'depth' => 0,
                                                        'echo' => 1,
                                                        'exclude' => '', 
                                                        'hide_empty' => false, 
                                                        'hide_if_empty' => false,
                                                        'hierarchical' => true,
                                                        'id' => 'category',
                                                        'name' => 's_consultant_category', 
                                                        'order' => 'ASC',
                                                        'orderby' => 'name', 
                                                        'selected' => $_REQUEST['s_consultant_category'], 
                                                        'show_count' => 0,
                                                        'show_option_all' => __('All'), 
                                                        'show_option_none' => '',
                                                        'tab_index' => 0, 
                                                        'taxonomy' => 'consultant_categories',
                                                    )
                                        );?>
                                    <div class="clr-crit">
                                        <p>Clear Criteria</p>
                                        <button type="button" rel="category">X</button>
                                    </div>
                                </div>
                                <div id="loca-group" class="round">
                                    <div id="location-nam">
                                        <label>Location:</label>
                                        <select id="loc" name="s_consultant_province">
                                            <option value="">Province</option>
                                            <?php echo consultant_provinces_list($_REQUEST['s_consultant_province'], true); ?>
                                        </select>
                                        <div class="clr-crit">
                                            <p>Clear Criteria</p>
                                            <button type="button" rel="loc">X</button>
                                        </div>
                                    </div>
                                    <div id="city-nam">
                                        <input type="text" name="s_consultant_city" id="cit-name" placeholder="City" value="<?php echo $_REQUEST['s_consultant_city']; ?>" >
                                        <div class="clr-crit">
                                            <p>Clear Criteria</p>
                                            <button type="button" rel="cit-name">X</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="alpha" class="round">
                                    <?php $check_consultant_alpha[$_REQUEST['s_consultant_alpha']] = ' class="selected"'; ?>
                                    <label>Search Alphabetically:</label>
                                    <span>|</span><?php foreach(range('A','Z') as $i) : ?>
                                    <a href="?s_consultant_alpha=<?php echo $i; ?>"<? echo $check_consultant_alpha[$i]; ?>><?php echo $i; ?></a><span>|</span>
                                    <?php endforeach; ?><a href="?s_consultant_alpha=0-9"<? echo $check_consultant_alpha['0-9']; ?>>0 - 9</a><span>|</span>
                                    <div class="clr-crit">
                                        <p>Clear Criteria</p>
                                        <button type="button" rel="alpha">X</button>
                                    </div>
                                </div>
                                <div id="adv-search">
                                    <!--<h3>Do an advanced search</h3>-->
                                    <input id="button" type="submit" value="Search" />
                                </div>
                            </fieldset>
                        </form>

                    </div>
                </div>
                <div id="consultant-search-results" class="cf">
                <?php
                $obj_upload_dir = wp_upload_dir();
                $args = array();
                $columns = 2;
                $count = 0;
                
                include("lib/search-query-consultant.php");
                
                $temp = $wp_query;
                $wp_query = NULL;

                add_filter('posts_orderby', 'consultant_posts_orderby');
                $wp_query = new WP_Query($args);
                remove_filter('posts_orderby', 'consultant_posts_orderby');
                echo 'TEST'.$wp_query->request;
                
                if ( $wp_query->have_posts() ) : 
                    while ( $wp_query->have_posts() ) : $wp_query->the_post(); $count++;
                ?>
                    <?php 
                    $consultant_title = get_the_title();
                    $custom = get_post_custom(get_the_ID());
                    //Membership Level
                    $consultant_membership = $custom["consultant_membership"][0];
                    //Profile Photo
                    $consultant_profile_image = $custom["consultant_profile_image"][0];
                    //Contact Info
                    $consultant_phone = $custom["consultant_phone"][0];
                    $consultant_address = $custom["consultant_address"][0];
                    $consultant_city = $custom["consultant_city"][0];
                    $consultant_province = $custom["consultant_province"][0];
                    $consultant_postal = $custom["consultant_postal"][0];
                    $consultant_email = $custom["consultant_email"][0];
                    //More Info
                    $consultant_tagline = $custom["consultant_tagline"][0];
                    ?>
                    <div class="search-result member-<?php echo $consultant_membership; ?> <?php echo ($count%$columns==0)?'rowend':''; ?>">
                        <div class="inner-shadow">
                            <?php if ($consultant_profile_image) : ?>
                            <a href="<?php the_permalink(); ?>" class="consultant-profile-thumb">
                                <span></span><img src="<?php echo $obj_upload_dir['baseurl'].'/profile-pics/thumbnail/'.$consultant_profile_image; ?>" src="<?php echo $consultant_title; ?>" />
                            </a>
                            <?php endif; ?>
                            <header class="search-result-head">
                                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <h3><?php echo $consultant_tagline; ?></h3>
                            </header>
                            <div class="search-result-address">
                                <?php if ($consultant_address) : ?><address class="st-ad-un"><?php echo $consultant_address; ?></address><?php endif; ?>
                                <?php if ($consultant_city || $consultant_province || $consultant_postal) : ?><address class="to-prov-post"><?php echo $consultant_city; ?><?php echo ($consultant_province)?', '.$consultant_province:'';?> <?php echo $consultant_postal; ?></address><?php endif; ?>
                            </div>
                            <div class="search-result-contact">
                                <?php if ($consultant_phone) : ?><p class="phone"><a href="tel:<?php echo $consultant_phone; ?>"><?php echo $consultant_phone; ?></a></p><?php endif; ?>
                                <?php if ($consultant_email) : ?><p class="email"><a href="mailto:<?php echo $consultant_email; ?>"><?php echo $consultant_email; ?></a></p><?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php
                    endwhile;
                    if ( $wp_query->max_num_pages > 1 ) : ?>
                        <div class="clear"></div>
                        <nav id="nav-below" class="navigation cf">
                            <h3 class="assistive-text"><?php _e( 'Search results navigation', 'twentyeleven' ); ?></h3>
                            <div class="nav-previous"><?php previous_posts_link( '<span class="meta-nav"><img src="'.get_template_directory_uri().'/images/blue-arrow.png"/></span> '.__( 'Previous', 'twentyeleven' ) ); ?></div>
                            <div class="nav-next"><?php next_posts_link( __( 'Next', 'twentyeleven' ).' <span class="meta-nav"><img src="'.get_template_directory_uri().'/images/red-arrow.png"/></span>' ); ?></div>
                        </nav><!-- #nav-above -->
                    <?php endif;
                else : ?>
                    <article id="post-0" class="post no-results not-found">
                        <header class="entry-header">
                            <h2 class="entry-title"><?php _e( 'Nothing Found', 'twentyeleven' ); ?></h2>
                        </header><!-- .entry-header -->
                        <div class="entry-content">
                            <p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different criteria.', 'twentyeleven' ); ?></p>
                        </div><!-- .entry-content -->
                    </article><!-- #post-0 -->
                <?php
                endif;
                wp_reset_postdata();
                $wp_query = $temp;
                ?>
                </div><!-- #consultant-search-results -->
            </div><!-- #content -->
        </div><!-- #primary -->
        <div class="clear"></div>
<?php get_footer(); ?>


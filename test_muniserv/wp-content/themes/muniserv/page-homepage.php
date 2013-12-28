<?php
/**
 * Template Name: Homepage Template
 * Description: A Page Template for the Homepage
 *
 * @package HomeCooked
 * @subpackage html5
 */
wp_enqueue_script('equalize', get_template_directory_uri().'/js/equalize.min.js', array('jquery'), '1.0', true);
wp_enqueue_script('homepage', get_template_directory_uri().'/js/homepage.js', array('jquery','equalize'), '1.0', true);
get_header(); ?>

		<div id="primary">
			<div id="content" role="main">

				<?php while ( have_posts() ) : the_post(); ?>
                 <!---------------------------------------------------Feature Boxes---------------------------------------------------------------->
                 <!--------------------------------------------------- Box One---------------------------------------------------------------->
                 <div class="feature_boxes">
                    <div class="shadow" id="feature-one">
                        <div class="inner-shadow" id="inner-feature">
                             <?php 
								$attachment_id = get_field('box_one_image'); // attachment ID
								$image_attributes = wp_get_attachment_image_src( $attachment_id, 'home_box_image' ); // returns an array
								?>
                             <header id="head-one" class="feature-header" style="background-image:url(<?php echo $image_attributes[0]; ?>);">
                                <h1><?php the_field('box_one_title'); ?></h1>
                                <h2><?php the_field('box_one_sub_heading'); ?></h2>
                                <h3><?php the_field ('box_one_secondary_sub_heading'); ?></h3>
							</header>
                            <div id="content-one" class="feature-content">
                                <?php the_field('box_one_content'); ?>
                            </div>
                            <footer class="feature-footer" id="footer-one">
                                <a href="<?php the_field('link_one_label_too'); ?>" class="button"><?php the_field('link_one_label'); ?></a>
                           </footer> 
                           </div>
                          </div>
                         
                    <div class="shadow" id="feature-two">
                        <div class="inner-shadow" id="inner-feature">
                             <?php 
								$attachment_id = get_field('box_two_image'); // attachment ID
								$image_attributes = wp_get_attachment_image_src( $attachment_id, 'home_box_image' ); // returns an array
								?>
                             <header id="head-two" class="feature-header" style="background-image:url(<?php echo $image_attributes[0]; ?>);">
                                <h1><?php the_field('box_two_title'); ?></h1>
                                <h2><?php the_field('box_two_sub_heading'); ?></h2>
                                <h3><?php the_field ('box_two_secondary_sub_heading'); ?></h3>
							</header>
                            <div id="content-one" class="feature-content">
                                <?php the_field('box_two_content'); ?>
                            </div>
                            <footer class="feature-footer" id="footer-two">
                                <a href="<?php the_field('link_two_too'); ?>" class="button"><?php the_field('link_two_label'); ?></a>
                           </footer> 
                          </div>
                          </div>
                          </div>
							  <!---------------------------------------------End Feature Boxes----------------------------------------------------->
                         <!-------------------------------------------------Memberships--------------------------------------------------------------->
                         				<!--Basic-->  
                  <div class="membership">
                    <div class="shadow" id="basic-memb">
                        <div class="inner-shadow" id="inner-basic">
                             <header id="member-basic" class="member">
                                <h3><?php the_field('basic_membership_title'); ?></h3>
                                <h2><?php the_field('basic_sub_title'); ?></h2>
                                <h4><?php the_field('basic_sub_sub_title'); ?></h4>
							</header>
                            <div id="member-basic-content" class="member-content">
                                <?php the_field ('basic_content'); ?>
                            </div>
                            <footer class="membership-footer" id="footer-basic">
                                <a href="<?php the_field('basic_link'); ?>" class="button"><?php the_field('basic_link_label'); ?></a>
                            </footer> 
                           </div>
                          </div>
                        				 <!--Standard-->
                    <div class="shadow" id="standard-memb">
                        <div class="inner-shadow" id="inner-standard">
                            <header id="member-standard" class="member">
                                <h3><?php the_field('standard_membership_title'); ?></h3>
                                <h2><?php the_field('standard_sub_title'); ?></h2>
                                <h4><?php the_field('standard_sub_sub_title'); ?></h4>
							</header>
                            <div id="member-basic-content" class="member-content">
                                <?php the_field ('standard_content'); ?>
                            </div>
                            <footer class="membership-footer" id="footer-standard">
                                <a href="<?php the_field('standard_link'); ?>" class="button"><?php the_field('standard_link_label'); ?></a>
                           </footer> 
                          </div>
                         </div>
                        				 <!--Premium-->
                    <div class="shadow" id="premium-memb">
                        <div class="inner-shadow" id="inner-premium">
                             <header id="member-premium" class="member">
                                <h3><?php the_field('premium_membership_title'); ?></h3>
                                <h2><?php the_field('premium_sub_title'); ?></h2>
                                <h4><?php the_field('premium_sub_sub_title'); ?></h4>
							</header>
                            <div id="member-premium-content" class="member-content">
                                <?php the_field ('premium_content'); ?>
                            </div>
                            <footer class="membership-footer" id="footer-premium">
                                <a href="<?php the_field('premium_link'); ?>" class="button"><?php the_field('premium_link_label'); ?></a>
                           </footer> 
                        </div>
                     </div> <!-- .membership -->
                     </div>
                                        
                    <?php endwhile; // end of the loop. ?>
               
                    <div id="articles" class="shadow">
                    	<div id="inner-article" class="inner-shadow">
                        <div id="aticle-box">
                     			<header id="article-head">
                        	<h1>Consultant Articles</h1> 
                     </header>   
                          <?php // Get Articles feed
                            $latest = get_posts(array('numberposts' => 3));
                            foreach ($latest as $post) : setup_postdata($post); ?>
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
                         <a class="button" href="articles">See More Articles</a>
                        </div>
                      </div>
                     </div>
			
                 <!--------------------------------------------------- Bottom Page Ads---------------------------------------------------------------->
 				<div id="ads">
                    <div id="ads-one" class="shadow">
                        <div id="inner-ads-one" class="inner-shadow">
                            <?php the_widget('ConsultantAdspaceWidget', 'consultant_level=3'); ?>
                       </div>
                    </div>
                    <div id="ads-two" class="shadow">
                        <div id="inner-ads-two" class="inner-shadow">
                            <?php the_widget('ConsultantAdspaceWidget', 'consultant_level=3'); ?>
                        </div>
                     </div>
                  </div>                
             </div><!-- #content -->
             
		</div><!-- #primary -->
        
        <div id="secondary" class="widget-area" role="complementary">
			<?php dynamic_sidebar( 'sidebar-7' ); ?>
		</div><!-- #secondary .widget-area -->

<?php get_footer(); ?>
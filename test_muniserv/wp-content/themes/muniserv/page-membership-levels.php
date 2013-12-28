<?php
/**
 * Template Name: Membership Levels
 * Description: A Page Template for the Membership levels
 *
 * @package HomeCooked
 * @subpackage html5
 */
wp_enqueue_script('equalize', get_template_directory_uri().'/js/equalize.min.js', array('jquery'), '1.0', true);
wp_enqueue_script('consultant-pricing', get_template_directory_uri().'/js/consultant-pricing.js', array('jquery','equalize'), '1.0', true);
get_header(); ?>

		<div id="primary" class="one-column">
			<div id="content" role="main">
				<div class="shadow">
                 <div class="inner-shadow content-area">
				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'page' ); ?>
                  
				<?php endwhile; // end of the loop. ?>
                </div>
               </div>
                <div id="level-groups" class="cf">
                  <div id="basic-level" class="member-level-box">
                    <div class="shadow">
                        <div class="inner-shadow">
                             <header class="member-level-header">
                                <?php the_field('member_level_basic_content'); ?>
                             </header>
                            <footer class="member-level-footer">
                                <?php if (get_field('basic_level_footer_override')) : ?>
                                <?php the_field('basic_level_footer_override_content'); ?>
                                <?php else : ?>
                            	<p class="price-info"><?php the_field('basic_level_price_description'); ?></p>
                                <p id="basic-price" class="price"><?php the_field('basic_level_price'); ?></p>
                                <p class="price-info"><?php the_field('basic_level_price_per'); ?></p>
                                <p class="price-info"><?php the_field('basic_level_price_per_time'); ?></p>
                                <p class="join" id="basic-level-join"><a href="<?php the_field('basic_level_join_link'); ?>"><?php the_field('basic_level_join_text'); ?></a></p>
                                <?php endif; ?>
                           </footer> 
                           </div>
                          </div>
                          </div>
                          <!--2nd member area-->
                    <div id="standard-level" class="member-level-box">
                    <div class="shadow">
                        <div class="inner-shadow">
                             <header class="member-level-header">
                                <?php the_field('member_level_standard_content'); ?>
                             </header>
                            <footer class="member-level-footer">
                                <?php if (get_field('standard_level_footer_override')) : ?>
                                <?php the_field('standard_level_footer_override_content'); ?>
                                <?php else : ?>
                            	<p class="price-info"><?php the_field('standard_level_price_description'); ?></p>
                                <p id="stand-price" class="price"><?php the_field('standard_level_price'); ?></p>
                                <p class="price-info"><?php the_field('standard_level_price_per'); ?></p>
                                <p class="price-info"><?php the_field('standard_level_price_per_time'); ?></p>
                                <p class="join" id="standard-level-join"><a href="<?php the_field('standard_level_join_link'); ?>"><?php the_field('standard_level_join_text'); ?></a></p>
                                <?php endif; ?>
                           </footer> 
                           </div>
                          </div>      
                         </div> 


 <!--3rd member area-->
                    <div id="premium-level" class="member-level-box">
                    <div class="shadow">
                        <div class="inner-shadow">
                             <header class="member-level-header">
                                <?php the_field('member_level_premium_content'); ?>
                             </header>
                            <footer class="member-level-footer">
                                <?php if (get_field('premium_level_footer_override')) : ?>
                                <?php the_field('premium_level_footer_override_content'); ?>
                                <?php else : ?>
                            	<p class="price-info"><?php the_field('premium_level_price_description'); ?></p>
                                <p id="prem-price" class="price"><?php the_field('premium_level_price'); ?></p>
                                <p class="price-info"><?php the_field('premium_level_price_per'); ?></p>
                                <p class="price-info"><?php the_field('premium_level_price_per_time'); ?></p>
                                <p class="join" id="premium-level-join"><a href="<?php the_field('premium_level_join_link'); ?>"><?php the_field('premium_level_join_text'); ?></a></p>
                                <?php endif; ?>
                           </footer> 
                           </div>
                          </div>      
                         </div> 
						</div>
			</div><!-- #content -->
		</div><!-- #primary -->
<?php get_footer(); ?>
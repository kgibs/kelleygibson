<?php
/**
 * Template Name: Municipalitiy Start
 * Description: A Page Template for the Municipality Start Page
 *
 * @package HomeCooked
 * @subpackage html5
 */
wp_enqueue_script('equalize', get_template_directory_uri().'/js/equalize.min.js', array('jquery'), '1.0', true);
wp_enqueue_script('municipalities-start', get_template_directory_uri().'/js/municipalities-start.js', array('jquery','equalize'), '1.0', true);
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
                <div id="Mun-group">
                  <div class="municip blue-box">
                    <div class="shadow">
                        <div class="inner-shadow">
                         <div class="in-box">
                             <header class="Mun-header">
                                <?php the_field('consultant_title'); ?>
                             </header>
                            <div class="Mun-content">
                            	<div id="subt" class="white"><?php the_field('consultant_sub_title'); ?></div>
                                <div class="muni-con"><?php the_field('find_consultant_content'); ?></div>
                             </div>
                            <footer id="grey-button" class="sub-footer">
                                <a href="<?php the_field('find_consultant_button'); ?>"><?php the_field('find_consultant_button_text'); ?></a>
                           </footer>
                           </div> 
                           </div>
                          </div>
                          </div>
                    
                  <div class="municip grey-box">
                      <div class="coming-soon"></div>
                    <div class="shadow">
                        <div class="inner-shadow">
                         <div class="in-box">
                             <header class="Mun-header">
                                <?php the_field('rfp_title'); ?>
                             </header>
                            <div class="Mun-content">
                            	<div id="subt" class="blue"><?php the_field('rfp_sub_title'); ?></div>
                                <div class="muni-con"><?php the_field('rfp_content'); ?></div>
                              </div>
                            <footer id="blu-button" class="sub-footer">
                                <a href="<?php the_field('find_consultant_button'); ?>"><?php the_field('rfp_button_text'); ?></a>
                           </footer> 
                           </div>
                           </div>
                          </div>
                          </div>
                    
                  <div class="municip green-box last">
                    <div class="shadow">
                        <div class="inner-shadow">
                         <div class="in-box">
                             <header class="Mun-header">
                                <?php the_field('sign_up_title'); ?>
                             </header>
                            <div class="Mun-content">
                            	<div id="subt" class="green"><?php the_field('sign_up_sub_title'); ?></div>
                                <div class="muni-con"><?php the_field('sign_up_content'); ?></div>
                             </div>
                            <footer id="green-button" class="sub-footer">
                                <a href="<?php the_field('sign_up_button'); ?>"><?php the_field('sign_up_button_text'); ?></a>
                           </footer> 
                           </div>
                           </div>
                          </div>
                          </div>
                          
                  <div class="municip grey-box">
                      <div class="coming-soon"></div>
                    <div class="shadow">
                        <div class="inner-shadow">
                         <div class="in-box">
                             <header class="Mun-header">
                                <?php the_field('tutorials_title'); ?>
                             </header>
                            <div class="Mun-content">
                            	<div id="subt" class="blue"><?php the_field('tutorials_sub_title'); ?></div>
                                <div class="muni-con"><?php the_field('tutorials_content'); ?></div>
                              </div>
                            <footer id="blu-button" class="sub-footer">
                                <a href="<?php the_field('tutorials_button'); ?>"><?php the_field('tutorials_button_text'); ?></a>
                           </footer>
                           </div> 
                           </div>
                          </div>
                          </div> 
                                       
                  <div class="municip green-box">
                    <div class="shadow">
                        <div class="inner-shadow">
                         <div class="in-box">
                             <header class="Mun-header">
                                <?php the_field('find_articles_title'); ?>
                             </header>
                            <div class="Mun-content">
                            	<div  id="subt" class="green"><?php the_field('find_articles_sub_title'); ?></div>
                                <div class="muni-con"><?php the_field('find_atricles_contnent'); ?></div>
                             </div>
                            <footer id="green-button" class="sub-footer">
                                <a href="<?php the_field('find_articles_button'); ?>"><?php the_field('find_articles_button_text'); ?></a>
                           </footer> 
                           </div>
                           </div>
                          </div>
                          </div>
              
               </div>
			</div><!-- #content -->
		</div><!-- #primary -->
<?php get_footer(); ?>
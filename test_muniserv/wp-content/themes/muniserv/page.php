<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package HomeCooked
 * @subpackage html5
 */

get_header(); ?>

		<div id="primary">
			<div id="content" role="main">
				<div class="shadow" id="main-content">
                    <div class="inner-shadow content-area">
                        <?php while ( have_posts() ) : the_post(); ?>

                            <?php get_template_part( 'content', 'page' ); ?>

                        <?php endwhile; // end of the loop. ?>
					</div>
                </div>
			</div><!-- #content -->
		</div><!-- #primary -->
        
        <?php get_sidebar(); ?> 

<?php get_footer(); ?>
<?php
/**
 * The Template for displaying all single posts.
 *
 * @package HomeCooked
 * @subpackage html5
 */

get_header(); ?>

		<div id="primary">
			<div id="content" role="main">
                
                <div id="articles" class="shadow">
                    <div id="inner-article" class="inner-shadow">
                        <div id="aticle-box">

                        <?php while ( have_posts() ) : the_post(); ?>
                            <?php get_template_part( 'content', 'single' ); ?>
                            <?php comments_template( '', true ); ?>
                            <nav id="nav-single">
                               <h3 class="assistive-text"><?php _e( 'Post navigation', 'twentyeleven' ); ?></h3>
                               <span class="nav-previous"><?php previous_post_link( '%link', __( '<span class="meta-nav">&larr;</span> Previous', 'twentyeleven' ) ); ?></span>
                               <span class="nav-next"><?php next_post_link( '%link', __( 'Next <span class="meta-nav">&rarr;</span>', 'twentyeleven' ) ); ?></span>
                            </nav><!-- #nav-single -->   
                        <?php endwhile; // end of the loop. ?>
                            
                        </div>
                    </div>
                </div>

			</div><!-- #content -->
		</div><!-- #primary -->

        <?php get_sidebar('blog'); ?>
<?php get_footer(); ?>
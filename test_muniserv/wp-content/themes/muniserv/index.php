<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package HomeCooked
 * @subpackage html5
 */
if (function_exists('get_header')) {
    get_header();
} else {
    //If the theme is called directly, it causes a fatal error when the header can't be found.  So let's redirect to main site instead.
    header("Location: http://" . $_SERVER['HTTP_HOST'] . "");
    exit;
}?>
		<div id="primary">
			<div id="content" role="main">
                
                <div id="articles" class="shadow">
                    <div id="inner-article" class="inner-shadow">
                        <div id="aticle-box">
                            
                            <header class="page-header">
                                <h1><?php echo get_the_title(get_option('page_for_posts')); ?></h1>
                            </header>

                        <?php if ( have_posts() ) : ?>

                            <?php /* Start the Loop */ ?>
                            <?php while ( have_posts() ) : the_post(); ?>

                                <?php get_template_part( 'content', get_post_format() ); ?>

                            <?php endwhile; ?>

                            <?php twentyeleven_content_nav( 'nav-below' ); ?>

                        <?php else : ?>

                            <article id="post-0" class="post no-results not-found">
                                <header class="entry-header">
                                    <h1 class="entry-title"><?php _e( 'Nothing Found', 'twentyeleven' ); ?></h1>
                                </header><!-- .entry-header -->

                                <div class="entry-content">
                                    <p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'twentyeleven' ); ?></p>
                                    <?php get_search_form(); ?>
                                </div><!-- .entry-content -->
                            </article><!-- #post-0 -->

                        <?php endif; ?>
                        </div>
                    </div>
                </div>

			</div><!-- #content -->
		</div><!-- #primary -->

    <?php get_sidebar('blog'); ?>
<?php get_footer(); ?>
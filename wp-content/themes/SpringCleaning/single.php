<?php get_header(); ?>
    <div id="container">
     
        <div id="content">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<h2 class="entry-title" title="<?php printf( __('%s', 'SpringCleaning'), the_title_attribute('echo=0') ); ?>"><?php the_title(); ?></h2>
                <hr class="headerBottomBorder" />
                
                <div class="post">
                
					<?php if(has_post_thumbnail()) { ?>
                    <div class="post-image">
                        <?php the_post_thumbnail('portfolio'); ?>
                    </div>	
                    <?php }?>
                    
                    <div class="post-content">
                    	<h4>Project Details</h4>
						<?php the_content(); ?>
                        <a href="<?php bloginfo('url'); ?>/category/portfolio/">&laquo; Back to Portfolio</a>
                    </div>
                </div><!--.post-->
            <?php endwhile; ?>

		<?php else : ?>
			<h2 class="entry-title">Whoops!</h2>
                <hr class="headerBottomBorder" />
            <h3 class="regular" style="text-align:center;">Can't seem to remember where I put that page!</h3>
            <h3 class="regular" style="text-align:center;">Please visit one of the other pages linked above while I go check the couch cushions.</h3>
		<?php endif; wp_reset_query(); ?>	

        </div><!-- #content -->
     
    </div><!-- #container -->
    
    <div style="clear:left;"></div>
        
<?php get_footer(); ?>
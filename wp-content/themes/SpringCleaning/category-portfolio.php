<?php get_header(); ?>
    <div id="container">
     
        <div id="content">
			<h2 class="entry-title">Portfolio</h2>
            <hr class="headerBottomBorder" />
			
            <div class="portfolio-left">
                <ul class="portfolio-thumbs">
                <?php 
					$thumbnailsQuery = new WP_Query('order=DESC&orderby=date');
					if ($thumbnailsQuery->have_posts()) : while ($thumbnailsQuery->have_posts()) : $thumbnailsQuery->the_post(); ?>
                
                    <li><a class="thumb-link" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                        <?php if(has_post_thumbnail()) { 
                            the_post_thumbnail('thumbnail');
                            } 
                        ?>
                    	</a>
                    
                    	<div class="hidden portfolio-content">
                            <div class="portfolio-sample">
								<?php if(has_post_thumbnail()){
                                    the_post_thumbnail('portfolio');
                                 } ?>
                            </div><!--.portfolio-sample-->
        		            
                            <div class="portfolio-description hidden">
                            	<div class="portfolio-description-container">
                                    <h3><?php the_title(); ?></h3>
                                    <?php the_content(); ?> 
                                    
                                    <?php $portfolioURL = get_post_meta(get_the_ID(), 'portfolioURL'); ?>
                                    <a title="<?php the_title(); ?>" href="<?php echo $portfolioURL[0]; ?>" target="_blank">View the site &raquo;</a> 
                                </div>
                            </div><!--.portfolio-description-->              
                        </div>
                    </li>
                <?php endwhile; ?>
                </ul>
            </div><!--.portfolio-left-->
            
            <div class="portfolio-right">
            </div><!--.portfolio-right-->
            
            <div style="clear:left;"></div>

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
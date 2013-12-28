<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package HomeCooked
 * @subpackage html5
 */
?>

	</div><!-- #main -->
	
	<footer id="colophon" role="contentinfo">
               		<img id="edge" src="<?php echo get_template_directory_uri(); ?>/images/nav-tags.png"  />

    	<div id="inner-footer"> 
        <?php
            // A sidebar in the footer? Yep. You can can customize your footer with three columns of widgets.
            if ( ! is_404() )
                get_sidebar( 'footer' );
        ?>
        <div id="subFooter" class="shadow">
        	<div id="inner-subFooter" class="inner-shadow"></div>
        	</div>
        </div>
    </footer><!-- #colophon -->
           <div id="copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All Rights Reserved.</div>
           <div id="developer">Website created by <a href="http://www.homecooked-websites.com" target="_blank">Home Cooked Website Solutions Inc.</a></div> 
	
<?php wp_footer(); ?>

</div>
</div><!-- #page -->
</body>
</html>
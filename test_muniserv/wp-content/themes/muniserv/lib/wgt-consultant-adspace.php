<?php
// Widget that displays a consultant ad at random
// Acknowledges what other ads have already been displayed on the current page to avoid duplicates
class ConsultantAdspaceWidget extends WP_Widget {
	// Widget setup
	function __construct() {
		parent::__construct(
			false, __( 'Consultant Adspace', 'muniserv' ),
			array('description' => __( 'Displays a random consultant advertisement.', 'muniserv' ))
		);
	}
	
	function form( $instance ) {
        $defaults = array(
            'consultant_level'    => ''
        );
        $values = wp_parse_args( $instance, $defaults );
        $select_consultant_level[$values['consultant_level']] = 'selected';
    ?>
        <p>
            <label for="<?php echo $this->get_field_id('consultant_level'); ?>"><?php _e( 'Membership Level:' ); ?></label> 
            <select id="<?php echo $this->get_field_id('consultant_level'); ?>" name="<?php echo $this->get_field_name('consultant_level'); ?>">
                <option value="">Any</option>
                <option value="2" <?php echo $select_consultant_level['2']; ?>>Standard</option>
                <option value="3" <?php echo $select_consultant_level['3']; ?>>Premium</option>
            </select>
		</p>
    <?php
	}
	
	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
	
	function widget( $args, $instance ) {
        global $wp_query, $post, $ads_used;
    
        echo $args['before_widget'];
        if (!is_array($ads_used))
            $ads_used = array();
        
        $temp_query = clone $wp_query;
        $temp_post = clone $post;
        
        $query_args = array(
            'post_type' => 'consultant',
            'posts_per_page' => 1,
            'orderby' => 'rand',
            'post__not_in' => $ads_used
        );
        if (intval($instance['consultant_level']) > 0) {
            $query_args['meta_key'] = 'consultant_membership';
            $query_args['meta_value'] = $instance['consultant_level'];
        }
        query_posts($query_args);
        
        if ( have_posts() ) while ( have_posts() ) : the_post();
            $ads_used[] = get_the_ID();
            $obj_upload_dir = wp_upload_dir();
            $custom = get_post_custom(get_the_ID());
            $consultant_tagline = $custom["consultant_tagline"][0];
            $consultant_profile_image = $custom["consultant_profile_image"][0];
            $consultant_ad_image = $custom["consultant_ad_image"][0];
            $consultant_ad_title = $custom["consultant_ad_title"][0];
            $consultant_ad_subtitle = $custom["consultant_ad_subtitle"][0];
            $consultant_ad_blurb = $custom["consultant_ad_blurb"][0];
        ?>
        <a class="consultant-adspace-link" href="<?php the_permalink(); ?>" title="View profile of <?php the_title(); ?>">
            <?php if($consultant_ad_image): ?>
            <div class="consultant-adspace-override">
                <img src="<?php echo $obj_upload_dir['baseurl'].'/ad-images/'.$consultant_ad_image; ?>" alt="<?php the_title(); ?>" />
            </div>
            <?php else: ?>
            <div class="consultant-adspace">
                <header class="cf">
                    <?php if($consultant_profile_image) : ?>
                    <div class="consultant-ad-image">
                        <img src="<?php echo $obj_upload_dir['baseurl'].'/profile-pics/'.$consultant_profile_image; ?>" />
                    </div>
                    <?php endif; ?>
                    <h3 class="consultant-ad-title"><?php echo ($consultant_ad_title)?$consultant_ad_title:get_the_title(); ?></h3>
                    <h4 class="consultant-ad-subtitle"><?php echo ($consultant_ad_subtitle)?$consultant_ad_subtitle:$consultant_tagline; ?></h4>
                </header>
                <div class="consultant-ad-blurb"><?php echo ($consultant_ad_blurb)?$consultant_ad_blurb:snippet(strip_tags(get_the_content()),230); ?></div>
            </div>
            <?php endif; ?>
        </a>
        
        <?php
        endwhile;
        $wp_query = clone $temp_query;
        $post = clone $temp_post;
        
		echo $args['after_widget'];
	}
}
function register_ConsultantAdspaceWidget() {
    register_widget('ConsultantAdspaceWidget');
}
add_action( 'widgets_init', 'register_ConsultantAdspaceWidget' );

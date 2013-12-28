<?php
/**
 * The Template for displaying municipality profiles.
 *
 * @package HomeCooked
 * @subpackage html5
 */

get_header();

        $obj_upload_dir = wp_upload_dir();
        $user_id = 0;
        $users = get_users(array('meta_key'=>'consultant_id','meta_value'=>get_the_ID()));
        if (count($users) > 0)
            $user_id = $users[0]->ID;
        $consultant_title = get_the_title();
        $custom = get_post_custom(get_the_ID());
        //Photos
        $municipality_profile_image = $custom["municipality_profile_image"][0];
        //Contact Info
        $municipality_contact_fname = $custom["municipality_contact_fname"][0];
        $municipality_contact_lname = $custom["municipality_contact_lname"][0];
        $municipality_contact_title = $custom["municipality_contact_title"][0];
        $municipality_email = $custom["municipality_email"][0];
        $municipality_phone = $custom["municipality_phone"][0];
        $municipality_fax = $custom["municipality_fax"][0];
        //Office Details
		$municipality_name = $custom["municipality_name"][0];
		$municipality_address = $custom["municipality_address"][0];
        $municipality_city = $custom["municipality_city"][0];
        $municipality_province = $custom["municipality_province"][0];
        $municipality_postal = $custom["municipality_postal"][0];
        $municipality_website = $custom["municipality_website"][0];
        $municipality_map_lat = $custom["municipality_map_lat"][0];
        $municipality_map_long = $custom["municipality_map_long"][0];
 ?>
    <div id="primary">
        <div id="content" role="main">
                
            <div class="shadow">
    			<div class="inner-shadow content-area cf">
                    <div class="business">
                        <h1><?php echo $municipality_name; ?></h1>
                    </div>
                     <div class="overview-header cf">
						<?php if ($municipality_profile_image) : ?>
                        <div id="proThumb" class="consultant-profile-thumb">
                            <span></span>
                            <img src="<?php echo $obj_upload_dir['baseurl'].'/municipal-pics/'.$municipality_profile_image; ?>" src="<?php echo $municipality_name; ?>" />
                        </div>
                        <?php endif; ?>

                  		<div id="Overview" class="contact-overview">
                  			<div class="c-info">
								<?php if ($municipality_contact_fname || $municipality_contact_lname) : ?>
                                    <div class="name">
                                        <label class="two-line">Contact Name: </label><?php echo $municipality_contact_fname.' '.$municipality_contact_lname.(($municipality_contact_title)?', '.$municipality_contact_title:''); ?>
                                    </div>
								<?php endif; ?>
                                <?php if ($municipality_email) : ?>
                                	<div class="email">
                                    	<label>Email: </label><a href="mailto:<?php echo $municipality_email; ?>"><?php echo $municipality_email; ?></a>
                                    </div>
								<?php endif; ?>
                                <?php if ($municipality_phone) : ?>
                                	<div class="number">
                                    	<label>Telephone: </label><a href="tel:<?php echo $municipality_phone; ?>"><?php echo $municipality_phone; ?></a>
                                    </div>
								<?php endif; ?>
                                <?php if ($municipality_fax) : ?>
                                	<div class="fax">
                                    	<label>Fax: </label><?php echo $municipality_fax; ?>
                                    </div>
								<?php endif; ?>
                               
                                <?php if ($municipality_address || $municipality_city || $municipality_province || $municipality_postal) : ?>
                                	<div class="address">
                                    	<label class="two-line">Address: </label><?php echo $municipality_address; ?> <?php echo $municipality_city; ?><?php echo ($municipality_province)?', '.$municipality_province:'';?> <?php echo $municipality_postal; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($municipality_website) : ?>
                                	<div class="website">
                                    	<label>Website: </label><a href="<?php echo $municipality_website; ?>" target="_blank"><?php echo str_replace('http://','',$municipality_website); ?></a>
                                    </div>
								<?php endif; ?>

								<?php
                                if ($municipality_map_lat && $municipality_map_long) {
                                    $gmap = urlencode($municipality_map_lat).','.urlencode($municipality_map_long).' ('.urlencode($municipality_name).')';
                                } else if ($municipality_city && $municipality_province) {
                                    $gmap = (($municipality_address) ? urlencode($municipality_address).',+' : urlencode($municipality_name).',+').urlencode($municipality_city).',+'.urlencode($municipality_province).' ('.urlencode($municipality_name).')';
                                }
                                if ($gmap) : ?>
								<div id="m-p" class="overview-map">
                                    <iframe width="100%" height="200" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.ca/?q=<?php echo $gmap; ?>&amp;ie=UTF8&amp;t=m&amp;vpsrc=0&amp;iwloc=&amp;output=embed"></iframe><br />
                                    <small><a target="_blank" href="http://maps.google.ca/?q=<?php echo $gmap; ?>&amp;ie=UTF8&amp;t=m&amp;vpsrc=0&amp;source=embed">View Larger Map</a></small>
                                </div>
                                <?php endif; ?>

                   			</div> 
                		</div>

					</div>
    
                    <div class="RFP">
                        <h3>RFPs, Tenders &amp; Other Requests for Work:</h3>
                        <ul id="RF">
                        	<li class="arrow">Server Installation
                        		<ul>
                        			<li>Quote Due: June 31, 2013</li>
                        		</ul>
                        	</li>
                        </ul>
                        <p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>
                        <a href="#">Learn More</a>
                    </div>
                </div>

			</div>
		</div><!-- #content -->
	</div><!-- #primary -->

   <div id="secondary" class="widget-area" role="complementary">
		<?php dynamic_sidebar( 'sidebar-8' ); ?>
   </div><!-- #secondary .widget-area -->
       

<?php get_footer(); ?>

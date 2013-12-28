<?php 

add_action( 'init', 'register_my_menus' );

function register_my_menus() {
	register_nav_menus(
		array(
		'mainNav' => __( 'Main Nav' ),
		)
	);
}

add_theme_support( 'post-thumbnails' );

if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'portfolio', 622, 500, true ); //(cropped)
	add_image_size( 'thumbnail', 150, 150, true ); //(cropped)
}        

?>
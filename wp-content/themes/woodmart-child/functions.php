<?php
/**
 * Enqueue script and styles for child theme
 */
function woodmart_child_enqueue_styles() {
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'woodmart-style' ), woodmart_get_theme_info( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 10010 );

add_filter( 'register_post_type_args', function( $args, $name ) {
    if ( 'post' === $name ) {
        $args['show_ui']        = false; 
        $args['show_in_menu']   = false; 
        $args['show_in_admin_bar'] = false; 
    }
    return $args;
}, 10, 2 );
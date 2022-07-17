<?php
/**
 * Theme functions and definitions
 *
 * @package mik_personal
 */ 


if ( ! function_exists( 'mik_personal_enqueue_styles' ) ) :
	/**
	 * Load assets.
	 *
	 * @since 1.0.0
	 */
	function mik_personal_enqueue_styles() {
		wp_enqueue_style( 'mik-style-parent', get_template_directory_uri() . '/style.css' );
		wp_enqueue_style( 'mik-personal-style', get_stylesheet_directory_uri() . '/style.css', array( 'mik-style-parent' ), '1.0.0' );
	}
endif;
add_action( 'wp_enqueue_scripts', 'mik_personal_enqueue_styles', 99 );

function mik_personal_remove_action() {
	remove_action( 'mik_header_start_action', 'mik_header_start', 10 );
}
add_action( 'init', 'mik_personal_remove_action');

if ( ! function_exists( 'mik_personal_theme_defaults' ) ) :
    /**
     * Customize theme defaults.
     *
     * @since 1.0.0
     *
     * @param array $defaults Theme defaults.
     * @param array Custom theme defaults.
     */
    function mik_personal_theme_defaults( $defaults ) {
        $defaults['enable_slider'] = false;
        $defaults['blog_column_type'] = 'column-3';

        return $defaults;
    }
endif;
add_filter( 'mik_default_theme_options', 'mik_personal_theme_defaults', 99 );

if ( ! function_exists( 'mik_personal_header_start' ) ) :
	/**
	 * Header starts html codes
	 *
	 * @since Mik 1.0.0
	 */
	function mik_personal_header_start() { 
        $slider_enable = mik_theme_option('enable_slider', false );
		?>
		<header id="masthead" class="site-header left-align">
		<div class="wrapper">
	<?php }
endif;
add_action( 'mik_header_start_action', 'mik_personal_header_start', 10 );

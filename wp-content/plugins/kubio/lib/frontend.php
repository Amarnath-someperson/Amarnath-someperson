<?php

function kubio_enqueue_frontend_assets() {

	if ( kubio_is_hybdrid_theme_iframe_preview() ) {
		return;
	}

	wp_enqueue_script( 'kubio-frontend' );
	wp_enqueue_style( 'kubio-block-library' );

	$style = array(
		// shapes
		kubio_get_shapes_css(),
		// colors
		kubio_render_global_colors(),
		// global
		kubio_get_global_data( 'additional_css' ),
		// page css
		kubio_get_page_css(),
	);

	wp_add_inline_style( 'kubio-block-library', implode( "\n\n", $style ) );
}

add_action( 'wp_enqueue_scripts', 'kubio_enqueue_frontend_assets' );

function kubio_get_page_css() {
	return Kubio\Core\StyleManager\StyleManager::getInstance()->render();
}

function kubio_render_page_css() {

	if ( wp_doing_ajax() || defined( 'REST_REQUEST' ) ) {
		return;
	}

	$content = '<style type="text/css" data-name="kubio-style">' . kubio_get_page_css() . '</style>';

	return $content;

}

add_filter(
	'style_loader_tag',
	function( $tag, $handle ) {
		$asynced_styles = array( 'kubio-google-fonts' );

		if ( in_array( $handle, $asynced_styles, true ) ) {
			if ( strpos( $tag, ' async' ) === false ) {
				$tag = str_replace( '<link', '<link async', $tag );
			}
		}

		return $tag;
	},
	PHP_INT_MAX,
	4
);

require_once __DIR__ . '/polyfills/polyfills.php';

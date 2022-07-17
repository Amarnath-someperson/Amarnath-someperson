<?php
/**
 * Theme functions and definitions
 *
 * @package mik_maya
 */ 


if ( ! function_exists( 'mik_maya_enqueue_styles' ) ) :
	/**
	 * Load assets.
	 *
	 * @since 1.0.0
	 */
	function mik_maya_enqueue_styles() {
		wp_enqueue_style( 'mik-style-parent', get_template_directory_uri() . '/style.css' );
		wp_enqueue_style( 'mik-maya-style', get_stylesheet_directory_uri() . '/style.css', array( 'mik-style-parent' ), '1.0.0' );
	}
endif;
add_action( 'wp_enqueue_scripts', 'mik_maya_enqueue_styles', 99 );

function mik_maya_remove_action() {
    remove_filter( 'mik_filter_slider_section_details', 'mik_get_slider_section_details' );
    remove_action( 'mik_primary_nav_action', 'mik_primary_nav', 10 );
}
add_action( 'init', 'mik_maya_remove_action');

if ( ! function_exists( 'mik_maya_theme_defaults' ) ) :
    /**
     * Customize theme defaults.
     *
     * @since 1.0.0
     *
     * @param array $defaults Theme defaults.
     * @param array Custom theme defaults.
     */
    function mik_maya_theme_defaults( $defaults ) {
        $defaults['enable_header_social_menu'] = true;
        $defaults['enable_slider'] = false;
        $defaults['blog_column_type'] = 'column-3';

        return $defaults;
    }
endif;
add_filter( 'mik_default_theme_options', 'mik_maya_theme_defaults', 99 );

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function mik_maya_customize_register( $wp_customize ) {
    // header social menu setting and control.
    $wp_customize->add_setting( 'mik_theme_options[enable_header_social_menu]', array(
        'default'           => mik_theme_option( 'enable_header_social_menu' ),
        'sanitize_callback' => 'mik_sanitize_switch',
    ) );

    $wp_customize->add_control( new Mik_Switch_Control( $wp_customize, 'mik_theme_options[enable_header_social_menu]', array(
        'label'             => esc_html__( 'Enable Social Menu in Header', 'mik-maya' ),
        'section'           => 'mik_header_section',
        'on_off_label'      => mik_show_options(),
    ) ) );
}
add_action( 'customize_register', 'mik_maya_customize_register', 100 );

if ( ! function_exists( 'mik_maya_primary_nav' ) ) :
    /**
     * Primary nav codes
     *
     * @since Mik 1.0.0
     */
    function mik_maya_primary_nav() { ?>
        <nav id="site-navigation" class="main-navigation">
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                <span class="screen-reader-text"><?php esc_html_e( 'Menu', 'mik-maya' ); ?></span>
                <svg viewBox="0 0 40 40" class="icon-menu">
                    <g>
                        <rect y="7" width="40" height="2"/>
                        <rect y="19" width="40" height="2"/>
                        <rect y="31" width="40" height="2"/>
                    </g>
                </svg>
                <?php echo mik_get_svg( array( 'icon' => 'close' ) ); ?>
            </button>
            <?php
                $search = '';

                if ( mik_theme_option( 'enable_header_social_menu' ) && has_nav_menu( 'social' ) ) :
                    $search .= '<li class="social-menu">';
                    $search .= wp_nav_menu( array(
                            'theme_location'    => 'social',
                            'container'         => false,
                            'menu_id'           => 'social-icons',
                            'menu_class'        => 'menu',
                            'depth'             => 1,
                            'echo'              => false,
                            'link_before'       => '<span class="screen-reader-text">',
                            'link_after'        => '</span>',
                        ) );
                    $search .= '</li>';
                endif;
                    
                if ( mik_theme_option( 'enable_header_search' ) ) :
                    $search .= '<li class="search-form"><a href="#" class="search">';
                    $search .= mik_get_svg( array( 'icon' => 'search' ) );
                    $search .= '</a><div id="search">';
                    $search .= get_search_form( $echo = false ); 
                    $search .= '</div></li>';
                endif;
                                        
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container' => 'div',
                    'menu_class' => 'menu nav-menu',
                    'menu_id' => 'primary-menu',
                    'fallback_cb' => 'mik_menu_fallback_cb',
                    'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s' . $search . '</ul>',
                ) );
            ?>
        </nav><!-- #site-navigation -->
    <?php }
endif;
add_action( 'mik_primary_nav_action', 'mik_maya_primary_nav', 10 );

if ( ! function_exists( 'mik_maya_get_slider_section_details' ) ) :
    /**
    * slider section details.
    *
    * @since Mik 1.0.0
    * @param array $input slider section details.
    */
    function mik_maya_get_slider_section_details( $input ) {

        $content = array();
        $post_ids = array();

        for ( $i = 1; $i <= 5; $i++ )  :
            $post_ids[] = mik_theme_option( 'slider_content_post_' . $i );
        endfor;
        
        $args = array(
            'post_type'         => 'post',
            'post__in'          =>  ( array ) $post_ids,
            'posts_per_page'    => 5,
            'orderby'           => 'post__in',
            'ignore_sticky_posts' => true,
            );                    

        // Run The Loop.
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) : 
            while ( $query->have_posts() ) : $query->the_post();
                $page_post['id']        = get_the_id();
                $page_post['title']     = get_the_title();
                $page_post['url']       = get_the_permalink();
                $page_post['image']     = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_id(), 'full' ) : '';
                $page_post['small_image']     = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_id(), 'post-thumbnail' ) : '';

                // Push to the main array.
                array_push( $content, $page_post );
            endwhile;
        endif;
        wp_reset_postdata();
            
        if ( ! empty( $content ) )
            $input = $content;
       
        return $input;
    }
endif;
// slider section content details.
add_filter( 'mik_filter_slider_section_details', 'mik_maya_get_slider_section_details' );

function mik_render_slider_section( $content_details = array() ) {
    if ( empty( $content_details ) )
        return;

    $slider_control = mik_theme_option( 'slider_arrow' );
    $auto_slide = mik_theme_option('slider_auto_slide', false );
    $readmore = mik_theme_option( 'slider_btn_label', '' );
    ?>
	<div id="custom-header" class="homepage-section">
        <div class="section-content banner-slider wrapper column-1 center-background-outline" data-slick='{"slidesToShow": 1, "slidesToScroll": 1, "infinite": true, "speed": 1200, "dots": false, "arrows":<?php echo $slider_control ? 'true' : 'false'; ?>, "autoplay": <?php echo $auto_slide ? 'true' : 'false'; ?>, "fade": false, "draggable": true }'>
            <?php foreach ( $content_details as $content ) : ?>
                <div class="custom-header-content-wrapper slide-item">
                    <div class="overlay"></div>
                    <?php if ( ! empty( $content['image'] ) ) : ?>
                        <img src="<?php echo esc_url( $content['image'] ); ?>" alt="<?php echo esc_attr( $content['title'] ); ?>">
                    <?php endif; ?>

                    <div class="wrapper">
                        <div class="custom-header-content">
                            <span class="cat-links">
                                <?php the_category( ', ', '', $content['id'] ); ?>
                            </span>

                            <?php if ( ! empty( $content['title'] ) ) : ?>
                                <h2><a href="<?php echo esc_url( $content['url'] ); ?>"><?php echo esc_html( $content['title'] ); ?></a></h2>
                            <?php endif; 

                            if ( ! empty( $content['url'] ) && ! empty( $readmore ) ) : ?>
                                <a href="<?php echo esc_url( $content['url'] ); ?>" class="btn btn-transparent"><?php echo esc_html( $readmore ); ?></a>
                            <?php endif; ?>
                        </div><!-- .custom-header-content -->
                    </div><!-- .wrapper -->
                </div><!-- .custom-header-content-wrapper -->
            <?php endforeach; ?>
        </div><!-- .banner-slider -->

    </div><!-- #custom-header -->
<?php 
}

<?php
/**
 * Æverything theme — setup, assets, menus, content types.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AE_VERSION', '1.0.0' );
define( 'AE_DIR', get_template_directory() );
define( 'AE_URI', get_template_directory_uri() );

/* -------------------------------------------------------------------------
 * 1. Theme setup
 * ---------------------------------------------------------------------- */
function ae_setup() {
	load_theme_textdomain( 'aeverything', AE_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list',
		'gallery', 'caption', 'style', 'script',
	) );

	add_theme_support( 'custom-logo', array(
		'height'      => 64,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	/* WooCommerce — we override its templates in /woocommerce/ */
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 600,
		'single_image_width'    => 1200,
		'product_grid'          => array(
			'default_columns' => 3,
			'min_columns'     => 2,
			'max_columns'     => 4,
		),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'primary'        => __( 'Primary Navigation', 'aeverything' ),
		'menu-extra'     => __( 'Menu Drawer — Secondary', 'aeverything' ),
		'footer-shop'    => __( 'Footer: Shop', 'aeverything' ),
		'footer-company' => __( 'Footer: Company', 'aeverything' ),
		'footer-help'    => __( 'Footer: Help', 'aeverything' ),
		'footer-legal'   => __( 'Footer: Legal', 'aeverything' ),
	) );

	add_image_size( 'ae-card', 800, 1000, true );
	add_image_size( 'ae-hero', 1400, 1600, true );
	add_image_size( 'ae-wide', 1600, 900, true );
}
add_action( 'after_setup_theme', 'ae_setup' );

/* -------------------------------------------------------------------------
 * 2. Assets
 * ---------------------------------------------------------------------- */
function ae_assets() {
	wp_enqueue_style(
		'ae-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@94..125,400..800&family=Inter:wght@300;400;500;600;700&display=swap',
		array(),
		null
	);

	/* Order matters: tokens -> components -> page layouts */
	wp_enqueue_style( 'ae-tokens',     AE_URI . '/assets/css/style.css',      array( 'ae-fonts' ), AE_VERSION );
	wp_enqueue_style( 'ae-components', AE_URI . '/assets/css/components.css', array( 'ae-tokens' ), AE_VERSION );
	wp_enqueue_style( 'ae-pages',      AE_URI . '/assets/css/pages.css',      array( 'ae-components' ), AE_VERSION );

	/* The theme's own style.css last, so overrides win */
	wp_enqueue_style( 'ae-style', get_stylesheet_uri(), array( 'ae-pages' ), AE_VERSION );

	wp_enqueue_script( 'ae-main', AE_URI . '/assets/js/main.js', array(), AE_VERSION, true );

	wp_localize_script( 'ae-main', 'aeData', array(
		'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
		'dropTarget' => ae_opt( 'ae_drop_date', '' ),
	) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'ae_assets' );

/* Preconnect to Google Fonts so the display font lands fast. */
function ae_resource_hints( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'ae_resource_hints', 10, 2 );

/* -------------------------------------------------------------------------
 * 3. Helpers
 * ---------------------------------------------------------------------- */

/**
 * Hero slide defaults — the single source of truth, read by both the
 * Customizer (to seed its controls) and front-page.php (to render).
 * Keeping them in one place stops the two drifting apart.
 *
 * Order: line 1, line 2, sub-line, button label.
 */
function ae_hero_defaults() {
	return array(
		1 => array( 'I am nothing,', 'æverything.', 'Mind / Body / Spirit / Art',                  'Shop the Collection' ),
		2 => array( 'Mentorship',    '',            'Guidance. Accountability. Transformation.',   'Apply for Mentorship' ),
		3 => array( 'Æverything',    'Fitness',     'Train the body. Strengthen the mind.',        'Explore Programs' ),
		4 => array( 'AI Marketing',  'Videos',      'Send us your product. We build your videos.', 'Start Your Brief' ),
	);
}

/** Theme option with fallback. */
function ae_opt( $key, $default = '' ) {
	$v = get_theme_mod( $key, $default );
	return ( '' === $v || null === $v ) ? $default : $v;
}

/**
 * Render an image slot: a real image if one is set, otherwise the
 * labelled placeholder so the layout still reads during the draft phase.
 *
 * @param int|string $image  Attachment ID, or '' for none.
 * @param string     $label  Placeholder caption.
 * @param int        $hue    Placeholder hue (0-360).
 * @param string     $size   Registered image size.
 * @param string     $class  Extra classes on the wrapper.
 */
function ae_media( $image = '', $label = '', $hue = 210, $size = 'ae-card', $class = '' ) {
	$cls = trim( 'ph ' . $class );
	if ( $image && wp_get_attachment_image_url( (int) $image, $size ) ) {
		echo '<div class="' . esc_attr( $cls ) . '" style="--h:' . (int) $hue . '">';
		echo wp_get_attachment_image(
			(int) $image,
			$size,
			false,
			array( 'class' => 'ph-img', 'loading' => 'lazy', 'decoding' => 'async' )
		);
		echo '</div>';
		return;
	}
	echo '<div class="' . esc_attr( $cls ) . '" style="--h:' . (int) $hue . '">';
	if ( $label ) {
		echo '<span class="ph-lb">' . esc_html( $label ) . '</span>';
	}
	echo '</div>';
}

/** Inline SVG icon from the sprite. */
function ae_icon( $id, $class = '' ) {
	printf(
		'<svg%s aria-hidden="true" focusable="false"><use href="#%s"></use></svg>',
		$class ? ' class="' . esc_attr( $class ) . '"' : '',
		esc_attr( $id )
	);
}

/** Body classes — the sky gradient is applied to <body>. */
function ae_body_class( $classes ) {
	$classes[] = 'sky';
	if ( is_page_template( 'page-templates/tpl-lesson.php' ) || is_singular( 'ae_lesson' ) ) {
		$classes[] = 'sky-deep';
	}
	return $classes;
}
add_filter( 'body_class', 'ae_body_class' );

/* -------------------------------------------------------------------------
 * 4. Content types
 * ---------------------------------------------------------------------- */
require_once AE_DIR . '/inc/garments.php';
require_once AE_DIR . '/inc/post-types.php';
require_once AE_DIR . '/inc/meta-boxes.php';
require_once AE_DIR . '/inc/customizer.php';
require_once AE_DIR . '/inc/ai-brief.php';
require_once AE_DIR . '/inc/woocommerce.php';

/* -------------------------------------------------------------------------
 * 5. Housekeeping
 * ---------------------------------------------------------------------- */

/* Nav fallback so the header isn't empty on a fresh install. */
function ae_nav_fallback() {
	echo '<nav class="nav" aria-label="Primary"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></nav>';
}

/* Strip the default WooCommerce stylesheets — the theme styles everything. */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/* Excerpt tweaks */
add_filter( 'excerpt_length', fn() => 22 );
add_filter( 'excerpt_more', fn() => '&hellip;' );

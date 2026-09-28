<?php
/**
 * WooCommerce integration — strip Woo's own markup so the theme's
 * design comes through, and expose the cart count in the header.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Bail out cleanly if WooCommerce isn't active. */
function ae_wc_active() {
	return class_exists( 'WooCommerce' );
}

/* Remove Woo's default page wrappers — our templates provide their own. */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

/* We render breadcrumbs ourselves, in the design's style. */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/* No sidebar. */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/* The shop page has its own header block in the template. */
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );

/* Default result count and ordering dropdown are replaced by filter pills. */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

/** 9 products per page — three clean rows of three. */
add_filter( 'loop_shop_per_page', fn() => 9, 20 );

/** Three columns, matching the mockups. */
add_filter( 'loop_shop_columns', fn() => 3, 20 );

/**
 * Live cart count for the header bag icon.
 * Returns the number of items, or the demo value before Woo exists.
 */
function ae_cart_count() {
	if ( ! ae_wc_active() || is_null( WC()->cart ) ) {
		return 0;
	}
	return (int) WC()->cart->get_cart_contents_count();
}

/** Refresh the bag badge over AJAX when something is added. */
function ae_cart_fragments( $fragments ) {
	ob_start();
	?>
	<span class="badge" data-cart-count><?php echo esc_html( ae_cart_count() ); ?></span>
	<?php
	$fragments['span.badge'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'ae_cart_fragments' );

/**
 * Shop URL helper — falls back to the posts page if Woo isn't set up yet,
 * so the nav never renders a dead link during the draft phase.
 */
function ae_shop_url() {
	if ( ae_wc_active() ) {
		$id = wc_get_page_id( 'shop' );
		if ( $id && $id > 0 ) {
			return get_permalink( $id );
		}
	}
	return home_url( '/shop/' );
}

/** Cart URL, safe before Woo is configured. */
function ae_cart_url() {
	return ae_wc_active() ? wc_get_cart_url() : home_url( '/cart/' );
}

/** Account URL, safe before Woo is configured. */
function ae_account_url() {
	if ( ae_wc_active() ) {
		$id = wc_get_page_id( 'myaccount' );
		if ( $id && $id > 0 ) {
			return get_permalink( $id );
		}
	}
	return wp_login_url();
}

/**
 * Product card markup — used by the shop loop and any related-products
 * block, so every product tile on the site looks identical.
 */
function ae_product_card( $product = null ) {
	global $post;
	$product = $product ? $product : wc_get_product( $post->ID );
	if ( ! $product ) {
		return;
	}
	$id = $product->get_id();

	/* Product tags drive the client-side filter pills on the shop page. */
	$tags  = get_the_terms( $id, 'product_tag' );
	$slugs = ( $tags && ! is_wp_error( $tags ) ) ? wp_list_pluck( $tags, 'slug' ) : array();
	?>
	<a href="<?php echo esc_url( get_permalink( $id ) ); ?>" class="pcard rv" data-tags="<?php echo esc_attr( implode( ' ', $slugs ) ); ?>">
		<div class="pcard-med">
			<?php
			if ( has_post_thumbnail( $id ) ) {
				echo get_the_post_thumbnail( $id, 'ae-card', array( 'loading' => 'lazy' ) );
			} else {
				ae_garment( $id );
			}
			?>
			<button class="pcard-wish" data-wish type="button" aria-label="<?php esc_attr_e( 'Add to wishlist', 'aeverything' ); ?>">
				<?php ae_icon( 'i-heart' ); ?>
			</button>
		</div>
		<div class="pcard-bd">
			<h3><?php echo esc_html( $product->get_name() ); ?></h3>
			<?php
			$colour = $product->get_attribute( 'colour' );
			if ( ! $colour ) {
				$colour = $product->get_attribute( 'color' );
			}
			if ( $colour ) :
				?>
				<p class="pcard-col"><?php echo esc_html( $colour ); ?></p>
			<?php endif; ?>
			<p class="pcard-pr"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
		</div>
	</a>
	<?php
}

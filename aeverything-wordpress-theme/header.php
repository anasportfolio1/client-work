<?php
/**
 * Header — sticky nav, menu drawer, cart drawer, search overlay.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#1355B8">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="sr" href="#content"><?php esc_html_e( 'Skip to content', 'aeverything' ); ?></a>

<?php get_template_part( 'template-parts/sprite' ); ?>

<header class="hdr">
	<div class="hdr-in">
		<div class="hdr-l">
			<button class="burger" data-open="#menu" aria-label="<?php esc_attr_e( 'Open menu', 'aeverything' ); ?>"><span></span></button>

			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo"><?php echo esc_html( ae_opt( 'ae_logo_text', 'æverything' ) ); ?></a>
			<?php endif; ?>
		</div>

		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => 'nav',
				'container_class' => 'nav',
				'container_aria_label' => __( 'Primary', 'aeverything' ),
				'items_wrap'     => '%3$s',
				'depth'          => 2,
				'fallback_cb'    => false,
			) );
		} else {
			ae_nav_fallback();
		}
		?>

		<div class="hdr-r">
			<button class="ico" data-open="#search" aria-label="<?php esc_attr_e( 'Search', 'aeverything' ); ?>"><?php ae_icon( 'i-search' ); ?></button>
			<a href="<?php echo esc_url( ae_account_url() ); ?>" class="ico" aria-label="<?php esc_attr_e( 'Account', 'aeverything' ); ?>"><?php ae_icon( 'i-user' ); ?></a>
			<button class="ico" data-open="#cart" aria-label="<?php esc_attr_e( 'Bag', 'aeverything' ); ?>">
				<?php ae_icon( 'i-bag' ); ?>
				<span class="badge" data-cart-count><?php echo esc_html( ae_cart_count() ); ?></span>
			</button>
		</div>
	</div>
</header>

<div class="scrim" id="scrim"></div>

<!-- MENU DRAWER -->
<aside class="drawer drawer-l" id="menu" aria-label="<?php esc_attr_e( 'Menu', 'aeverything' ); ?>">
	<div class="drawer-hd">
		<span class="logo" style="font-size:20px"><?php echo esc_html( ae_opt( 'ae_logo_text', 'æverything' ) ); ?></span>
		<button class="x" data-close aria-label="<?php esc_attr_e( 'Close', 'aeverything' ); ?>"><?php ae_icon( 'i-close' ); ?></button>
	</div>

	<div class="drawer-bd">
		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu( array(
				'theme_location'  => 'primary',
				'container'       => 'nav',
				'container_class' => 'mnav',
				'items_wrap'      => '%3$s',
				'depth'           => 2,
				'fallback_cb'     => false,
				'link_after'      => '<svg aria-hidden="true"><use href="#i-arr-r"></use></svg>',
			) );
		}

		if ( has_nav_menu( 'menu-extra' ) ) :
			?>
			<p class="eyebrow" style="margin:26px 0 8px"><?php esc_html_e( 'Also in the Æ world', 'aeverything' ); ?></p>
			<?php
			wp_nav_menu( array(
				'theme_location'  => 'menu-extra',
				'container'       => 'nav',
				'container_class' => 'mnav',
				'items_wrap'      => '%3$s',
				'depth'           => 1,
				'fallback_cb'     => false,
				'link_after'      => '<svg aria-hidden="true"><use href="#i-arr-r"></use></svg>',
			) );
		endif;
		?>
	</div>

	<div class="drawer-ft">
		<?php get_template_part( 'template-parts/socials' ); ?>
	</div>
</aside>

<!-- CART DRAWER -->
<aside class="drawer drawer-r" id="cart" aria-label="<?php esc_attr_e( 'Bag', 'aeverything' ); ?>">
	<div class="drawer-hd">
		<h3 class="h-sec" style="font-size:14px">
			<?php esc_html_e( 'Your Bag', 'aeverything' ); ?>
			<span class="pink">(<?php echo esc_html( ae_cart_count() ); ?>)</span>
		</h3>
		<button class="x" data-close aria-label="<?php esc_attr_e( 'Close', 'aeverything' ); ?>"><?php ae_icon( 'i-close' ); ?></button>
	</div>

	<div class="drawer-bd">
		<?php
		$ae_cart = ( ae_wc_active() && ! is_null( WC()->cart ) ) ? WC()->cart->get_cart() : array();

		if ( empty( $ae_cart ) ) :
			?>
			<p class="lede" style="padding:20px 0"><?php esc_html_e( 'Your bag is empty.', 'aeverything' ); ?></p>
			<?php
		else :
			foreach ( $ae_cart as $item ) :
				$_product = $item['data'];
				if ( ! $_product ) {
					continue;
				}
				?>
				<div class="cart-item">
					<div class="cart-thumb">
						<?php
						if ( has_post_thumbnail( $_product->get_id() ) ) {
							echo get_the_post_thumbnail( $_product->get_id(), 'thumbnail' );
						} else {
							ae_garment( $_product->get_id() );
						}
						?>
					</div>
					<div>
						<h4 class="h-card" style="margin-bottom:3px"><?php echo esc_html( $_product->get_name() ); ?></h4>
						<p class="tiny muted"><?php echo esc_html( sprintf( __( 'Qty %d', 'aeverything' ), $item['quantity'] ) ); ?></p>
					</div>
					<b><?php echo wp_kses_post( WC()->cart->get_product_subtotal( $_product, $item['quantity'] ) ); ?></b>
				</div>
				<?php
			endforeach;
			?>
			<div style="margin-top:22px">
				<div class="cart-line">
					<span class="muted"><?php esc_html_e( 'Subtotal', 'aeverything' ); ?></span>
					<b><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></b>
				</div>
				<div class="cart-line" style="border-top:1px solid rgba(255,255,255,.18);margin-top:6px;padding-top:12px;font-size:15px">
					<b><?php esc_html_e( 'Total', 'aeverything' ); ?></b>
					<b><?php echo wp_kses_post( WC()->cart->get_total() ); ?></b>
				</div>
			</div>
			<?php
		endif;
		?>
	</div>

	<div class="drawer-ft">
		<?php if ( ae_wc_active() ) : ?>
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn-block" style="margin-bottom:9px"><?php esc_html_e( 'Checkout', 'aeverything' ); ?></a>
		<?php endif; ?>
		<a href="<?php echo esc_url( ae_shop_url() ); ?>" class="btn btn-glass btn-block"><?php esc_html_e( 'Continue Shopping', 'aeverything' ); ?></a>
	</div>
</aside>

<!-- SEARCH -->
<div class="searchbox" id="search" aria-label="<?php esc_attr_e( 'Search', 'aeverything' ); ?>">
	<div class="searchbox-in">
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input class="search-f" type="search" name="s"
				placeholder="<?php esc_attr_e( 'Search products, articles, lessons…', 'aeverything' ); ?>"
				value="<?php echo get_search_query(); ?>"
				aria-label="<?php esc_attr_e( 'Search', 'aeverything' ); ?>">
			<button class="search-go" type="submit" aria-label="<?php esc_attr_e( 'Go', 'aeverything' ); ?>"><?php ae_icon( 'i-arr-r' ); ?></button>
		</form>
	</div>
</div>

<main id="content">

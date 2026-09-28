<?php
/**
 * Shop / product archive — matches the design: title and category list on
 * the left, filter pills and a three-column product grid on the right.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$ae_cats = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => false,
	'parent'     => 0,
	'exclude'    => array( get_option( 'default_product_cat' ) ),
) );

$ae_tags    = get_terms( array( 'taxonomy' => 'product_tag', 'hide_empty' => true ) );
$ae_current = is_product_category() ? get_queried_object_id() : 0;
?>
<section class="sect-t sect-b">
	<div class="wrap">
		<div class="shop-grid">

			<aside class="shop-side">
				<div class="rv">
					<h1 class="h-page">
						<?php
						if ( is_product_category() || is_product_tag() ) {
							echo esc_html( single_term_title( '', false ) );
						} else {
							echo esc_html( get_the_title( wc_get_page_id( 'shop' ) ) );
						}
						?>
					</h1>
					<?php
					$ae_shop_page = get_post( wc_get_page_id( 'shop' ) );
					$ae_intro     = ( $ae_shop_page && $ae_shop_page->post_excerpt )
						? $ae_shop_page->post_excerpt
						: __( 'Timeless pieces. Built for the mind, body and beyond.', 'aeverything' );
					?>
					<p class="lede" style="max-width:220px"><?php echo esc_html( $ae_intro ); ?></p>
				</div>

				<?php if ( ! is_wp_error( $ae_cats ) && $ae_cats ) : ?>
					<nav class="cats rv">
						<a href="<?php echo esc_url( ae_shop_url() ); ?>"<?php echo ! $ae_current ? ' class="is-on"' : ''; ?>>
							<?php esc_html_e( 'All', 'aeverything' ); ?>
						</a>
						<?php foreach ( $ae_cats as $ae_cat ) : ?>
							<a href="<?php echo esc_url( get_term_link( $ae_cat ) ); ?>"<?php echo $ae_current === $ae_cat->term_id ? ' class="is-on"' : ''; ?>>
								<?php echo esc_html( $ae_cat->name ); ?>
							</a>
						<?php endforeach; ?>
					</nav>
				<?php endif; ?>
			</aside>

			<div>
				<div class="shop-bar rv">
					<?php if ( ! is_wp_error( $ae_tags ) && $ae_tags ) : ?>
						<div class="pills" data-filter-group data-filter-target="#prodGrid">
							<button class="pill is-on" data-val="all" type="button"><?php esc_html_e( 'All', 'aeverything' ); ?></button>
							<?php foreach ( $ae_tags as $ae_tag ) : ?>
								<button class="pill" data-val="<?php echo esc_attr( $ae_tag->slug ); ?>" type="button">
									<?php echo esc_html( $ae_tag->name ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<div></div>
					<?php endif; ?>

					<button class="pill filter-btn" type="button"><?php ae_icon( 'i-filter' ); ?> <?php esc_html_e( 'Filter', 'aeverything' ); ?></button>
				</div>

				<?php if ( woocommerce_product_loop() && have_posts() ) : ?>

					<div class="prods" id="prodGrid">
						<?php
						while ( have_posts() ) :
							the_post();
							ae_product_card();
						endwhile;
						?>
					</div>

					<?php woocommerce_pagination(); ?>

				<?php else : ?>
					<p class="lede"><?php esc_html_e( 'No products yet. Add them under Products in the dashboard.', 'aeverything' ); ?></p>
				<?php endif; ?>
			</div>

		</div>
	</div>
</section>
<?php
get_footer();

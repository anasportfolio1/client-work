<?php
/**
 * Single product — thumbnails, main image, buy panel, accordions.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	global $product;
	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) {
		continue;
	}

	$ae_gallery = $product->get_gallery_image_ids();
	$ae_main    = get_post_thumbnail_id();
	$ae_thumbs  = array_filter( array_merge( array( $ae_main ), $ae_gallery ) );

	/* Sizes come from a product attribute when one exists. Without it we
	   still render the design's default row so the layout reads correctly
	   during the draft — add a "Size" attribute to make them selectable. */
	$ae_sizes = $product->get_attribute( 'size' );
	$ae_sizes = $ae_sizes ? array_map( 'trim', explode( ',', $ae_sizes ) ) : array( 'XS', 'S', 'M', 'L', 'XL' );

	$ae_terms = get_the_terms( get_the_ID(), 'product_cat' );
	?>

	<section class="sect-t" style="padding-bottom:14px">
		<div class="wrap">
			<nav class="crumb rv" aria-label="<?php esc_attr_e( 'Breadcrumb', 'aeverything' ); ?>">
				<a href="<?php echo esc_url( ae_shop_url() ); ?>"><?php esc_html_e( 'Shop', 'aeverything' ); ?></a>
				<?php if ( $ae_terms && ! is_wp_error( $ae_terms ) ) : ?>
					<?php ae_icon( 'i-chev-r' ); ?>
					<a href="<?php echo esc_url( get_term_link( $ae_terms[0] ) ); ?>"><?php echo esc_html( $ae_terms[0]->name ); ?></a>
				<?php endif; ?>
				<?php ae_icon( 'i-chev-r' ); ?>
				<span class="cur"><?php the_title(); ?></span>
			</nav>
		</div>
	</section>

	<section class="sect-b">
		<div class="wrap">
			<div class="pdp">

				<div class="pdp-thumbs rv">
					<?php if ( $ae_thumbs ) : ?>
						<?php foreach ( array_slice( $ae_thumbs, 0, 4 ) as $i => $tid ) : ?>
							<button class="pdp-thumb<?php echo 0 === $i ? ' is-on' : ''; ?>" type="button"
								aria-label="<?php echo esc_attr( sprintf( __( 'View %d', 'aeverything' ), $i + 1 ) ); ?>">
								<?php echo wp_get_attachment_image( $tid, 'woocommerce_thumbnail' ); ?>
							</button>
						<?php endforeach; ?>
					<?php else : ?>
						<?php for ( $i = 0; $i < 4; $i++ ) : ?>
							<button class="pdp-thumb<?php echo 0 === $i ? ' is-on' : ''; ?>" type="button"
								aria-label="<?php echo esc_attr( sprintf( __( 'View %d', 'aeverything' ), $i + 1 ) ); ?>">
								<?php ae_garment( get_the_ID() ); ?>
							</button>
						<?php endfor; ?>
					<?php endif; ?>
				</div>

				<div class="pdp-main rv" id="pdpMain" style="transition:opacity .22s var(--e)">
					<?php
					if ( $ae_main ) {
						echo wp_get_attachment_image( $ae_main, 'woocommerce_single' );
					} else {
						ae_garment( get_the_ID() );
					}
					?>
				</div>

				<div class="pdp-info rv">
					<h1><?php the_title(); ?></h1>
					<p class="pdp-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>

					<?php if ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) : ?>

						<span class="lbl"><?php esc_html_e( 'Size', 'aeverything' ); ?></span>
						<div class="sizes">
							<?php foreach ( $ae_sizes as $i => $s ) : ?>
								<button class="size<?php echo 2 === $i ? ' is-on' : ''; ?>" type="button"><?php echo esc_html( $s ); ?></button>
							<?php endforeach; ?>
						</div>

						<form class="cart" method="post" enctype="multipart/form-data"
							action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>">

							<span class="lbl"><?php esc_html_e( 'Quantity', 'aeverything' ); ?></span>
							<div class="qty">
								<button id="qMinus" type="button" aria-label="<?php esc_attr_e( 'Decrease', 'aeverything' ); ?>"><?php ae_icon( 'i-minus' ); ?></button>
								<input id="qty" type="number" name="quantity" value="1" min="1" step="1" inputmode="numeric"
									aria-label="<?php esc_attr_e( 'Quantity', 'aeverything' ); ?>">
								<button id="qPlus" type="button" aria-label="<?php esc_attr_e( 'Increase', 'aeverything' ); ?>"><?php ae_icon( 'i-plus' ); ?></button>
							</div>

							<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>">
							<button type="submit" class="btn btn-block btn-lg single_add_to_cart_button">
								<?php echo esc_html( $product->single_add_to_cart_text() ); ?>
							</button>
						</form>

					<?php else : ?>
						<?php woocommerce_template_single_add_to_cart(); ?>
					<?php endif; ?>

					<button class="wish-link" data-wish type="button">
						<?php ae_icon( 'i-heart' ); ?> <?php esc_html_e( 'Add to Wishlist', 'aeverything' ); ?>
					</button>

					<?php
					$ae_accordions = array();

					if ( $product->get_description() ) {
						$ae_accordions[ __( 'Description', 'aeverything' ) ] = wpautop( $product->get_description() );
					}

					$ae_attr_html = '';
					foreach ( $product->get_attributes() as $ae_attr ) {
						$ae_vals = $product->get_attribute( $ae_attr->get_name() );
						if ( $ae_vals ) {
							$ae_attr_html .= '<li><strong>' . esc_html( wc_attribute_label( $ae_attr->get_name() ) ) . ':</strong> ' . esc_html( $ae_vals ) . '</li>';
						}
					}
					if ( $ae_attr_html ) {
						$ae_accordions[ __( 'Details', 'aeverything' ) ] = '<ul>' . $ae_attr_html . '</ul>';
					}

					foreach ( $ae_accordions as $ae_head => $ae_body ) :
						?>
						<div class="acc">
							<button class="acc-hd" type="button" aria-expanded="false">
								<?php echo esc_html( $ae_head ); ?> <span class="acc-ic"></span>
							</button>
							<div class="acc-bd"><div class="acc-bd-in"><?php echo wp_kses_post( $ae_body ); ?></div></div>
						</div>
					<?php endforeach; ?>
				</div>

			</div>
		</div>
	</section>

	<?php
	/* Related products */
	$ae_related = wc_get_related_products( $product->get_id(), 4 );
	if ( $ae_related ) :
		?>
		<section class="sect-b">
			<div class="wrap">
				<div class="sec-head rv">
					<h2 class="h-sec"><?php esc_html_e( 'Complete the Set', 'aeverything' ); ?></h2>
					<a href="<?php echo esc_url( ae_shop_url() ); ?>" class="tlink">
						<?php esc_html_e( 'View All', 'aeverything' ); ?> <?php ae_icon( 'i-arr-r' ); ?>
					</a>
				</div>
				<div class="prods" style="grid-template-columns:repeat(4,1fr)">
					<?php
					foreach ( $ae_related as $ae_rid ) {
						ae_product_card( wc_get_product( $ae_rid ) );
					}
					?>
				</div>
			</div>
		</section>
		<?php
	endif;

endwhile;

get_footer();

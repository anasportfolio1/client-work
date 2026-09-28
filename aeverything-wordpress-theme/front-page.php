<?php
/**
 * Front page — hero carousel, drop countdown, Shop by World.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/* Hero slides — only the ones with an image, unless none are set yet,
   in which case all four render as placeholders so the carousel works. */
$ae_hues   = array( 338, 206, 20, 300 );
$ae_slides = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$ae_slides[] = array(
		'img'   => ae_opt( "ae_hero_img_$i", '' ),
		'cap'   => ae_opt( "ae_hero_cap_$i", sprintf( __( 'Hero %d image', 'aeverything' ), $i ) ),
		'hue'   => $ae_hues[ $i - 1 ],
	);
}
$ae_with_img = array_filter( $ae_slides, fn( $s ) => ! empty( $s['img'] ) );
if ( $ae_with_img ) {
	$ae_slides = array_values( $ae_with_img );
}
?>

<section class="hero">
	<div class="hero-grid">

		<div class="hero-l">
			<?php get_template_part( 'template-parts/widgets' ); ?>

			<div class="hero-copy rv">
				<h1 class="h-hero">
					<?php echo esc_html( ae_opt( 'ae_hero_line1', 'I am nothing,' ) ); ?><br>
					<?php echo esc_html( ae_opt( 'ae_hero_line2', 'æverything.' ) ); ?>
				</h1>
				<p class="hero-sub"><?php echo esc_html( ae_opt( 'ae_hero_sub', 'Mind / Body / Spirit / Art' ) ); ?></p>
				<a href="<?php echo esc_url( ae_opt( 'ae_hero_btn_url' ) ? ae_opt( 'ae_hero_btn_url' ) : ae_shop_url() ); ?>" class="btn btn-lg">
					<?php echo esc_html( ae_opt( 'ae_hero_btn', 'Shop the Collection' ) ); ?>
				</a>
			</div>

			<?php if ( count( $ae_slides ) > 1 ) : ?>
				<div class="dots" id="hero-dots">
					<?php foreach ( $ae_slides as $i => $s ) : ?>
						<button class="dot<?php echo 0 === $i ? ' is-on' : ''; ?>" type="button"
							aria-label="<?php echo esc_attr( sprintf( __( 'Slide %d', 'aeverything' ), $i + 1 ) ); ?>"></button>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div></div>
			<?php endif; ?>
		</div>

		<div class="hero-r" id="hero-car">
			<?php foreach ( $ae_slides as $i => $s ) : ?>
				<div data-slide style="position:absolute;inset:0;<?php echo 0 === $i ? '' : 'opacity:0;'; ?>transition:opacity .9s var(--e),transform 1.4s var(--e)">
					<?php ae_media( $s['img'], $s['cap'], $s['hue'], 'ae-hero', 'ae-slide' ); ?>
				</div>
			<?php endforeach; ?>

			<a href="<?php echo esc_url( home_url( '/world-of-ae/' ) ); ?>" class="ae-badge" aria-label="<?php esc_attr_e( 'World of Æ', 'aeverything' ); ?>">&aelig;</a>
		</div>

	</div>
</section>

<section class="sect-b">
	<div class="wrap">
		<div class="sec-head rv">
			<h2 class="h-sec"><?php echo esc_html( ae_opt( 'ae_worlds_title', 'Shop by World' ) ); ?></h2>
		</div>

		<div class="worlds">
			<?php
			$ae_world_hues = array( 338, 222, 346, 36, 330 );
			for ( $i = 1; $i <= 5; $i++ ) :
				$title = ae_opt( "ae_world_{$i}_title", '' );
				if ( ! $title ) {
					continue;
				}
				$url = ae_opt( "ae_world_{$i}_url" );
				?>
				<a href="<?php echo esc_url( $url ? $url : ae_shop_url() ); ?>" class="icard rv">
					<?php ae_media( ae_opt( "ae_world_{$i}_img", '' ), $title, $ae_world_hues[ $i - 1 ], 'ae-card' ); ?>
					<div class="icard-veil"></div>
					<div class="icard-txt"><h3><?php echo esc_html( $title ); ?></h3></div>
					<span class="icard-go"><?php ae_icon( 'i-arr-r' ); ?></span>
				</a>
			<?php endfor; ?>
		</div>
	</div>
</section>

<?php
/* Latest products, if WooCommerce has any. */
if ( ae_wc_active() ) :
	$ae_products = new WP_Query( array(
		'post_type'           => 'product',
		'posts_per_page'      => 4,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	) );

	if ( $ae_products->have_posts() ) :
		?>
		<section class="sect-b">
			<div class="wrap">
				<div class="sec-head rv">
					<h2 class="h-sec"><?php esc_html_e( 'New In', 'aeverything' ); ?></h2>
					<a href="<?php echo esc_url( ae_shop_url() ); ?>" class="tlink">
						<?php esc_html_e( 'View All', 'aeverything' ); ?> <?php ae_icon( 'i-arr-r' ); ?>
					</a>
				</div>
				<div class="prods" style="grid-template-columns:repeat(4,1fr)">
					<?php
					while ( $ae_products->have_posts() ) :
						$ae_products->the_post();
						ae_product_card();
					endwhile;
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
	wp_reset_postdata();
endif;

get_footer();

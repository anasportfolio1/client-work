<?php
/**
 * Front page — rotating hero, drop countdown, Shop by World.
 *
 * Every slide carries its own headline, sub-line, button and cut-out, so
 * the copy changes with the image rather than staying fixed.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/* Fall back to the bundled cut-out when the client hasn't uploaded one. */
$ae_fallback = AE_URI . '/assets/img/model-1.webp';

$ae_slides = array();
foreach ( ae_hero_defaults() as $i => $d ) {
	$line1 = ae_opt( "ae_hero_line1_$i", $d[0] );
	$line2 = ae_opt( "ae_hero_line2_$i", $d[1] );
	if ( '' === $line1 && '' === $line2 ) {
		continue;
	}
	$img_id  = ae_opt( "ae_hero_img_$i", '' );
	$img_url = $img_id ? wp_get_attachment_image_url( (int) $img_id, 'full' ) : '';
	$ae_slides[] = array(
		'l1'  => $line1,
		'l2'  => $line2,
		'sub' => ae_opt( "ae_hero_sub_$i", $d[2] ),
		'btn' => ae_opt( "ae_hero_btn_$i", $d[3] ),
		'url' => ae_opt( "ae_hero_url_$i", '' ) ? ae_opt( "ae_hero_url_$i" ) : ae_shop_url(),
		/* Until the client uploads a cut-out per slide, every slide reuses
		   the one supplied — better than an empty right-hand column. */
		'img' => $img_url ? $img_url : $ae_fallback,
	);
}
if ( ! $ae_slides ) {
	$ae_slides[] = array(
		'l1' => 'I am nothing,', 'l2' => 'æverything.',
		'sub' => 'Mind / Body / Spirit / Art',
		'btn' => 'Shop the Collection', 'url' => ae_shop_url(),
		'img' => $ae_fallback,
	);
}
$ae_first = $ae_slides[0];
?>

<section class="hero">
	<div class="hero-grid">

		<div class="hero-l">
			<?php get_template_part( 'template-parts/widgets' ); ?>

			<div class="hero-copy rv" id="heroCopy">
				<h1 class="h-hero" id="heroTitle">
					<span><?php echo esc_html( $ae_first['l1'] ); ?></span>
					<?php if ( $ae_first['l2'] ) : ?><br><span><?php echo esc_html( $ae_first['l2'] ); ?></span><?php endif; ?>
				</h1>
				<p class="hero-sub" id="heroSub"><?php echo esc_html( $ae_first['sub'] ); ?></p>
				<a href="<?php echo esc_url( $ae_first['url'] ); ?>" class="btn btn-lg" id="heroBtn">
					<?php echo esc_html( $ae_first['btn'] ); ?>
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
			<?php if ( $ae_first['img'] ) : ?>
				<img class="hero-model" id="heroModel"
					src="<?php echo esc_url( $ae_first['img'] ); ?>"
					alt="" fetchpriority="high" decoding="async">
			<?php else : ?>
				<?php ae_media( '', __( 'Hero cut-out', 'aeverything' ), 338, 'ae-hero' ); ?>
			<?php endif; ?>
		</div>

		<a href="<?php echo esc_url( home_url( '/world-of-ae/' ) ); ?>" class="ae-badge"
			aria-label="<?php esc_attr_e( 'World of Æ', 'aeverything' ); ?>">&aelig;</a>
	</div>

	<script type="application/json" id="heroSlides">
		<?php echo wp_json_encode( $ae_slides ); ?>
	</script>
</section>

<section class="sect-b sect-soft">
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

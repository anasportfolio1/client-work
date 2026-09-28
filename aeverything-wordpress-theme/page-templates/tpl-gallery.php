<?php
/**
 * Template Name: Gallery
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
the_post();

$mediums = get_terms( array( 'taxonomy' => 'ae_gallery_type', 'hide_empty' => false ) );
$works   = new WP_Query( array(
	'post_type'      => 'ae_gallery',
	'posts_per_page' => 40,
	'orderby'        => 'menu_order date',
	'order'          => 'ASC',
	'no_found_rows'  => true,
) );
?>
<section class="phead">
	<div class="wrap">
		<div class="sec-head rv" style="align-items:center">
			<div>
				<h1 class="h-page"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?><p class="lede"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
			</div>
			<?php if ( ! is_wp_error( $mediums ) && $mediums ) : ?>
				<div class="pills" data-filter-group data-filter-target="#galGrid">
					<button class="pill is-on" data-val="all"><?php esc_html_e( 'All', 'aeverything' ); ?></button>
					<?php foreach ( $mediums as $m ) : ?>
						<button class="pill" data-val="<?php echo esc_attr( $m->slug ); ?>"><?php echo esc_html( $m->name ); ?></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="sect-b">
	<div class="wrap">
		<?php if ( $works->have_posts() ) : ?>
			<div class="gal-masonry" id="galGrid">
				<?php
				$ratios = array( '1/1.16', '1/1.3', '1/.8', '1/1.42', '1/.72', '1/1.08', '1/.9' );
				$hues   = array( 206, 196, 20, 338, 346, 200, 226 );
				$i = 0;
				while ( $works->have_posts() ) : $works->the_post();
					$terms = get_the_terms( get_the_ID(), 'ae_gallery_type' );
					$slugs = ( $terms && ! is_wp_error( $terms ) ) ? wp_list_pluck( $terms, 'slug' ) : array();
					$label = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
					?>
					<a href="<?php the_permalink(); ?>" class="gal-item rv" data-tags="<?php echo esc_attr( implode( ' ', $slugs ) ); ?>">
						<div class="ph" style="aspect-ratio:<?php echo esc_attr( $ratios[ $i % 7 ] ); ?>;--h:<?php echo (int) $hues[ $i % 7 ]; ?>">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'ae-card', array( 'class' => 'ph-img', 'loading' => 'lazy' ) );
							} else {
								echo '<span class="ph-lb">' . esc_html( get_the_title() ) . '</span>';
							}
							?>
						</div>
						<?php if ( $label ) : ?>
							<div class="gal-ov"><div><b><?php echo esc_html( $label ); ?></b></div></div>
						<?php endif; ?>
					</a>
					<?php
					$i++;
				endwhile;
				?>
			</div>
		<?php else : ?>
			<p class="lede"><?php esc_html_e( 'No artwork yet. Add some under Æ Gallery in the dashboard.', 'aeverything' ); ?></p>
		<?php endif; wp_reset_postdata(); ?>
	</div>
</section>
<?php
get_footer();

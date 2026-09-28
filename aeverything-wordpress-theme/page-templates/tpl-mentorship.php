<?php
/**
 * Template Name: Mentorship
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="hero">
	<div class="hero-grid">
		<div class="hero-l">
			<?php get_template_part( 'template-parts/widgets' ); ?>
			<div class="hero-copy rv">
				<h1 class="h-hero"><?php echo esc_html( ae_opt( 'ae_mentor_title', 'Mentorship' ) ); ?></h1>
				<p class="lede" style="margin:10px 0 22px"><?php echo nl2br( esc_html( ae_opt( 'ae_mentor_sub', 'Guidance. Accountability. Transformation.' ) ) ); ?></p>
				<a href="#apply" class="btn btn-lg"><?php echo esc_html( ae_opt( 'ae_mentor_btn', 'Apply for Mentorship' ) ); ?> <?php ae_icon( 'i-arr-r' ); ?></a>
			</div>
			<div></div>
		</div>
		<div class="hero-r">
			<?php ae_media( ae_opt( 'ae_mentor_img', '' ), __( 'Mentorship hero', 'aeverything' ), 222, 'ae-hero' ); ?>
			<a href="<?php echo esc_url( home_url( '/world-of-ae/' ) ); ?>" class="ae-badge">&aelig;</a>
		</div>
	</div>
</section>

<?php
$ae_programs = new WP_Query( array(
	'post_type'      => 'ae_program',
	'posts_per_page' => 8,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'no_found_rows'  => true,
	'tax_query'      => array( array(
		'taxonomy' => 'ae_program_type',
		'field'    => 'name',
		'terms'    => 'Mentorship',
	) ),
) );

if ( $ae_programs->have_posts() ) : ?>
<section class="sect-b">
	<div class="wrap">
		<div class="sec-head rv">
			<h2 class="h-sec"><?php esc_html_e( 'Mentorship Programs', 'aeverything' ); ?></h2>
		</div>
		<div class="prog-grid-4">
			<?php
			$h = array( 22, 208, 32, 14 ); $i = 0;
			while ( $ae_programs->have_posts() ) : $ae_programs->the_post(); ?>
				<a href="<?php the_permalink(); ?>" class="icard rv" style="aspect-ratio:1/1.05">
					<?php ae_media( get_post_thumbnail_id(), get_the_title(), $h[ $i % 4 ], 'ae-card' ); $i++; ?>
					<div class="icard-veil"></div>
					<div class="icard-txt">
						<h3><?php the_title(); ?></h3>
						<p><?php echo esc_html( get_the_excerpt() ); ?></p>
					</div>
					<span class="icard-go"><?php ae_icon( 'i-arr-r' ); ?></span>
				</a>
			<?php endwhile; ?>
		</div>
	</div>
</section>
<?php endif; wp_reset_postdata(); ?>

<section class="sect-b" id="apply">
	<div class="wrap">
		<?php
		while ( have_posts() ) : the_post();
			if ( trim( get_the_content() ) ) : ?>
				<div class="glass rv" style="padding:clamp(20px,3vw,40px)">
					<div class="ae-prose"><?php the_content(); ?></div>
				</div>
			<?php endif;
		endwhile;
		?>
	</div>
</section>
<?php
get_footer();

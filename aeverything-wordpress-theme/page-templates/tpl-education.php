<?php
/**
 * Template Name: Education
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
the_post();

$ae_topics = get_terms( array( 'taxonomy' => 'ae_topic', 'hide_empty' => false ) );
?>
<section class="phead">
	<div class="wrap">
		<div class="rv">
			<h1 class="h-page"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="lede"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			<a href="#topics" class="tlink" style="margin-top:14px">
				<?php esc_html_e( 'Browse All Topics', 'aeverything' ); ?> <?php ae_icon( 'i-arr-r' ); ?>
			</a>
		</div>
	</div>
</section>

<?php if ( ! is_wp_error( $ae_topics ) && $ae_topics ) : ?>
<section class="sect-b">
	<div class="wrap">
		<div class="edu-grid" id="topics">
			<?php
			$ae_hues = array( 206, 228, 26, 192 );
			$ae_i    = 0;
			foreach ( $ae_topics as $ae_topic ) :
				$ae_img = get_term_meta( $ae_topic->term_id, 'ae_topic_image', true );
				?>
				<a href="<?php echo esc_url( get_term_link( $ae_topic ) ); ?>" class="edu-card rv">
					<?php ae_media( $ae_img, $ae_topic->name, $ae_hues[ $ae_i % 4 ], 'ae-card' ); ?>
					<div class="edu-card-veil"></div>
					<div class="edu-card-bd">
						<h3><?php echo esc_html( $ae_topic->name ); ?></h3>
						<?php if ( $ae_topic->description ) : ?>
							<p><?php echo esc_html( $ae_topic->description ); ?></p>
						<?php endif; ?>
					</div>
					<span class="icard-go"><?php ae_icon( 'i-arr-r' ); ?></span>
				</a>
				<?php
				$ae_i++;
			endforeach;
			?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
$ae_featured = new WP_Query( array(
	'post_type'      => 'ae_lesson',
	'posts_per_page' => 1,
	'orderby'        => 'menu_order date',
	'order'          => 'ASC',
	'no_found_rows'  => true,
) );

if ( $ae_featured->have_posts() ) :
	$ae_featured->the_post();
	?>
	<section class="sect-b">
		<div class="wrap">
			<p class="lbl rv"><?php esc_html_e( 'Featured Lesson', 'aeverything' ); ?></p>
			<div class="feat-lesson glass-dark rv">
				<div class="feat-med">
					<?php ae_media( get_post_thumbnail_id(), get_the_title(), 24, 'ae-wide' ); ?>
					<a href="<?php the_permalink(); ?>" class="play" aria-label="<?php esc_attr_e( 'Play lesson', 'aeverything' ); ?>">
						<?php ae_icon( 'i-play' ); ?>
					</a>
				</div>
				<div>
					<h3 style="margin-bottom:18px"><?php the_title(); ?></h3>
					<a href="<?php the_permalink(); ?>" class="btn btn-sm"><?php esc_html_e( 'Watch Lesson', 'aeverything' ); ?></a>
				</div>
			</div>
		</div>
	</section>
	<?php
endif;
wp_reset_postdata();

get_footer();

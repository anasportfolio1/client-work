<?php
/**
 * Lesson detail — the dark Education layout.
 *
 * Three columns: topic nav, lesson body with the numbered list,
 * and a rail with progress, resources and related lessons.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$ae_id      = get_the_ID();
	$ae_topics  = get_the_terms( $ae_id, 'ae_topic' );
	$ae_topic   = ( $ae_topics && ! is_wp_error( $ae_topics ) ) ? $ae_topics[0] : null;
	$ae_edu     = get_page_by_path( 'education' );
	$ae_edu_url = $ae_edu ? get_permalink( $ae_edu ) : home_url( '/education/' );

	$ae_principles = ae_lines( get_post_meta( $ae_id, '_ae_principles', true ), 2 );
	$ae_resources  = ae_lines( get_post_meta( $ae_id, '_ae_resources', true ), 3 );
	$ae_quote      = get_post_meta( $ae_id, '_ae_quote', true );
	$ae_qby        = get_post_meta( $ae_id, '_ae_quote_by', true );
	$ae_guide      = get_post_meta( $ae_id, '_ae_guide', true );
	$ae_guide_meta = get_post_meta( $ae_id, '_ae_guide_meta', true );
	$ae_progress   = (int) get_post_meta( $ae_id, '_ae_progress', true );
	$ae_prog_note  = get_post_meta( $ae_id, '_ae_progress_note', true );
	$ae_duration   = get_post_meta( $ae_id, '_ae_duration', true );
	$ae_level      = get_post_meta( $ae_id, '_ae_level', true );
	$ae_num        = get_post_meta( $ae_id, '_ae_number', true );
	$ae_of         = get_post_meta( $ae_id, '_ae_of_total', true );

	$ae_all_topics = get_terms( array( 'taxonomy' => 'ae_topic', 'hide_empty' => false ) );

	/* Other lessons in the same topic. */
	$ae_related = $ae_topic ? get_posts( array(
		'post_type'      => 'ae_lesson',
		'posts_per_page' => 3,
		'post__not_in'   => array( $ae_id ),
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
		'tax_query'      => array( array(
			'taxonomy' => 'ae_topic',
			'field'    => 'term_id',
			'terms'    => $ae_topic->term_id,
		) ),
	) ) : array();

	$ae_pr_icons  = array( 'p-mental', 'p-corr', 'p-vib', 'p-pol', 'p-rhy', 'p-cause', 'p-gen' );
	$ae_res_icons = array( 'i-file', 'i-headphones', 'i-file', 'i-help' );
	?>

	<section class="sect-t" style="padding-bottom:14px">
		<div class="wrap">
			<nav class="crumb rv" aria-label="<?php esc_attr_e( 'Breadcrumb', 'aeverything' ); ?>">
				<a href="<?php echo esc_url( $ae_edu_url ); ?>"><?php esc_html_e( 'Education', 'aeverything' ); ?></a>
				<?php if ( $ae_topic ) : ?>
					<?php ae_icon( 'i-chev-r' ); ?>
					<a href="<?php echo esc_url( get_term_link( $ae_topic ) ); ?>"><?php echo esc_html( $ae_topic->name ); ?></a>
				<?php endif; ?>
				<?php ae_icon( 'i-chev-r' ); ?>
				<span class="cur"><?php the_title(); ?></span>
			</nav>
		</div>
	</section>

	<section class="sect-b">
		<div class="wrap">
			<div class="lesson">

				<!-- TOPIC NAV -->
				<aside class="lesson-side glass-dark rv">
					<h3><?php echo esc_html( $ae_topic ? $ae_topic->name : __( 'Education', 'aeverything' ) ); ?></h3>
					<a href="<?php echo esc_url( $ae_topic ? get_term_link( $ae_topic ) : $ae_edu_url ); ?>" class="back">
						<?php ae_icon( 'i-arr-l' ); ?>
						<?php echo esc_html( sprintf( __( 'Back to %s', 'aeverything' ), $ae_topic ? $ae_topic->name : __( 'Education', 'aeverything' ) ) ); ?>
					</a>

					<?php if ( ! is_wp_error( $ae_all_topics ) && $ae_all_topics ) : ?>
						<nav class="lnav">
							<a href="<?php echo esc_url( get_permalink() ); ?>" class="is-on">
								<?php ae_icon( 'i-sparkle' ); ?> <?php esc_html_e( 'Overview', 'aeverything' ); ?>
							</a>
							<?php
							$ae_t_icons = array( 't-ankh', 't-tree', 't-compass', 't-flask', 't-geo', 't-temple', 't-eye', 't-star', 't-scroll' );
							foreach ( $ae_all_topics as $ae_i => $ae_t ) :
								?>
								<a href="<?php echo esc_url( get_term_link( $ae_t ) ); ?>">
									<?php ae_icon( $ae_t_icons[ $ae_i % count( $ae_t_icons ) ] ); ?>
									<?php echo esc_html( $ae_t->name ); ?>
									<svg class="chev" aria-hidden="true"><use href="#i-chev-r"></use></svg>
								</a>
							<?php endforeach; ?>
						</nav>
					<?php endif; ?>

					<?php if ( $ae_guide ) : ?>
						<a href="<?php echo esc_url( $ae_guide ); ?>" class="dl-box" download>
							<div>
								<h5><?php esc_html_e( 'Download Study Guide', 'aeverything' ); ?></h5>
								<?php if ( $ae_guide_meta ) : ?><small><?php echo esc_html( $ae_guide_meta ); ?></small><?php endif; ?>
							</div>
							<span class="dl-ic"><?php ae_icon( 'i-download' ); ?></span>
						</a>
					<?php endif; ?>
				</aside>

				<!-- LESSON BODY -->
				<div>
					<div class="lesson-hero rv">
						<div class="lesson-hero-med">
							<?php ae_media( get_post_thumbnail_id(), get_the_title(), 32, 'ae-card' ); ?>
						</div>
						<div>
							<?php if ( $ae_num && $ae_of ) : ?>
								<span class="eyebrow">
									<?php echo esc_html( sprintf( __( 'Lesson %1$s of %2$s', 'aeverything' ), $ae_num, $ae_of ) ); ?>
								</span>
							<?php endif; ?>

							<h1><?php the_title(); ?></h1>

							<?php if ( has_excerpt() ) : ?>
								<p class="lede" style="margin-bottom:10px"><?php echo esc_html( get_the_excerpt() ); ?></p>
							<?php endif; ?>

							<div class="ae-prose" style="font-size:13px"><?php the_content(); ?></div>

							<div class="lmeta">
								<?php if ( $ae_duration ) : ?>
									<span><?php ae_icon( 'i-clock' ); ?> <?php echo esc_html( $ae_duration ); ?></span>
								<?php endif; ?>
								<?php if ( $ae_level ) : ?>
									<span><?php ae_icon( 'i-level' ); ?> <?php echo esc_html( $ae_level ); ?></span>
								<?php endif; ?>
								<button class="saved" data-wish type="button">
									<?php ae_icon( 'i-bookmark' ); ?> <?php esc_html_e( 'Save Lesson', 'aeverything' ); ?>
								</button>
							</div>

							<a href="#lesson-body" class="btn">
								<svg style="fill:currentColor;stroke:none" aria-hidden="true"><use href="#i-play"></use></svg>
								<?php esc_html_e( 'Start Lesson', 'aeverything' ); ?>
							</a>
						</div>
					</div>

					<?php if ( $ae_principles ) : ?>
						<h2 class="h-sec rv" id="lesson-body" style="margin:26px 0 14px">
							<?php echo esc_html( sprintf( __( 'The %d Principles', 'aeverything' ), count( $ae_principles ) ) ); ?>
						</h2>

						<div class="principles">
							<?php foreach ( $ae_principles as $ae_i => $ae_row ) : ?>
								<a href="#" class="pr rv">
									<span class="pr-ic"><?php ae_icon( $ae_pr_icons[ $ae_i % count( $ae_pr_icons ) ] ); ?></span>
									<div class="pr-tx">
										<h4><?php echo esc_html( ( $ae_i + 1 ) . '. ' . $ae_row[0] ); ?></h4>
										<?php if ( $ae_row[1] ) : ?><p><?php echo esc_html( $ae_row[1] ); ?></p><?php endif; ?>
									</div>
									<span class="pr-go"><?php ae_icon( 'i-arr-r' ); ?></span>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( $ae_quote ) : ?>
						<div class="quote rv" style="margin-top:18px">
							<?php ae_media( '', __( 'Quote background', 'aeverything' ), 28, 'ae-wide' ); ?>
							<div class="quote-veil"></div>
							<div class="quote-bd">
								<span class="quote-mk">&ldquo;</span>
								<p><?php echo esc_html( $ae_quote ); ?></p>
								<?php if ( $ae_qby ) : ?>
									<p class="quote-by">&mdash; <?php echo esc_html( $ae_qby ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>

				<!-- RAIL -->
				<aside class="lesson-aside">

					<?php if ( $ae_progress ) : ?>
						<div class="panel glass-dark rv">
							<h4><?php esc_html_e( 'Your Progress', 'aeverything' ); ?></h4>
							<div class="donut">
								<svg viewBox="0 0 120 120" width="118" height="118" aria-hidden="true">
									<defs>
										<linearGradient id="pgrad" x1="0" y1="0" x2="1" y2="1">
											<stop offset="0%" stop-color="#FF3D6E"/><stop offset="100%" stop-color="#FFA3BE"/>
										</linearGradient>
									</defs>
									<circle class="donut-bg" cx="60" cy="60" r="50"/>
									<circle class="donut-fg" cx="60" cy="60" r="50" data-p="<?php echo esc_attr( $ae_progress ); ?>"/>
								</svg>
								<div class="donut-c">
									<b><?php echo esc_html( $ae_progress ); ?>%</b>
									<span><?php esc_html_e( 'Overall Progress', 'aeverything' ); ?></span>
								</div>
							</div>
							<?php if ( $ae_prog_note ) : ?>
								<p class="tiny muted" style="text-align:center;margin-bottom:14px;letter-spacing:.04em;text-transform:none">
									<?php echo esc_html( $ae_prog_note ); ?>
								</p>
							<?php endif; ?>
							<a href="<?php echo esc_url( $ae_edu_url ); ?>" class="btn btn-glass btn-block btn-sm">
								<?php esc_html_e( 'View All Lessons', 'aeverything' ); ?> <?php ae_icon( 'i-arr-r' ); ?>
							</a>
						</div>
					<?php endif; ?>

					<?php if ( $ae_resources ) : ?>
						<div class="panel glass-dark rv">
							<h4><?php esc_html_e( 'Lesson Resources', 'aeverything' ); ?></h4>
							<?php foreach ( $ae_resources as $ae_i => $ae_r ) : ?>
								<a href="<?php echo esc_url( $ae_r[2] ? $ae_r[2] : '#' ); ?>" class="res"<?php echo $ae_r[2] ? ' download' : ''; ?>>
									<span class="res-ic"><?php ae_icon( $ae_res_icons[ $ae_i % count( $ae_res_icons ) ] ); ?></span>
									<span>
										<b><?php echo esc_html( $ae_r[0] ); ?></b>
										<?php if ( $ae_r[1] ) : ?><small><?php echo esc_html( $ae_r[1] ); ?></small><?php endif; ?>
									</span>
									<span class="dl"><?php ae_icon( 'i-download' ); ?></span>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( $ae_related ) : ?>
						<div class="panel glass-dark rv">
							<h4><?php esc_html_e( 'Related Lessons', 'aeverything' ); ?></h4>
							<?php
							$ae_hues = array( 214, 34, 26 );
							foreach ( $ae_related as $ae_i => $ae_rel ) :
								$ae_rp = (int) get_post_meta( $ae_rel->ID, '_ae_progress', true );
								?>
								<a href="<?php echo esc_url( get_permalink( $ae_rel ) ); ?>" class="rel">
									<span class="rel-th">
										<?php ae_media( get_post_thumbnail_id( $ae_rel->ID ), '', $ae_hues[ $ae_i % 3 ], 'thumbnail' ); ?>
									</span>
									<span style="flex:1">
										<b><?php echo esc_html( $ae_rel->post_title ); ?></b>
										<?php if ( $ae_rp ) : ?>
											<small><?php echo esc_html( sprintf( __( '%d%% Complete', 'aeverything' ), $ae_rp ) ); ?></small>
											<span class="pbar"><i data-p="<?php echo esc_attr( $ae_rp ); ?>"></i></span>
										<?php endif; ?>
									</span>
								</a>
							<?php endforeach; ?>

							<a href="<?php echo esc_url( $ae_topic ? get_term_link( $ae_topic ) : $ae_edu_url ); ?>" class="btn btn-glass btn-block btn-sm" style="margin-top:14px">
								<?php esc_html_e( 'View All Lessons', 'aeverything' ); ?> <?php ae_icon( 'i-arr-r' ); ?>
							</a>
						</div>
					<?php endif; ?>

				</aside>

			</div>
		</div>
	</section>

	<?php
endwhile;

get_footer();

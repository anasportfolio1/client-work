<?php
/**
 * Article detail — Æ Magazine.
 *
 * Three columns: category sidebar, article body, media rail.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$ae_cats    = get_the_category();
	$ae_cat     = $ae_cats ? $ae_cats[0] : null;
	$ae_keys    = ae_lines( get_post_meta( get_the_ID(), '_ae_keys', true ), 2 );
	$ae_quote   = get_post_meta( get_the_ID(), '_ae_quote', true );
	$ae_qby     = get_post_meta( get_the_ID(), '_ae_quote_by', true );
	$ae_mag_url = get_permalink( get_option( 'page_for_posts' ) );

	/* Siblings in the same category drive the sidebar list. */
	$ae_siblings = $ae_cat ? get_posts( array(
		'posts_per_page' => 12,
		'category'       => $ae_cat->term_id,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) ) : array();

	$ae_key_icons = array( 'i-moon', 'i-heart', 'i-atom', 'i-sparkle', 'i-target', 'i-brain' );
	?>

	<section class="sect-t sect-b">
		<div class="wrap">
			<div class="art">

				<!-- SIDEBAR -->
				<aside class="art-side rv">
					<a href="<?php echo esc_url( $ae_mag_url ); ?>" class="back">
						<?php ae_icon( 'i-arr-l' ); ?> <?php esc_html_e( 'Back to Magazine', 'aeverything' ); ?>
					</a>

					<div class="art-id">
						<?php ae_media( '', __( 'Magazine cover', 'aeverything' ), 214, 'ae-card' ); ?>
						<div class="art-id-veil"></div>
						<span class="ae-neon">&aelig;</span>
						<div class="art-id-tx">
							<h3><?php echo esc_html( get_the_title( get_option( 'page_for_posts' ) ) ); ?></h3>
							<p><?php echo esc_html( get_post_field( 'post_excerpt', get_option( 'page_for_posts' ) ) ); ?></p>
						</div>
					</div>

					<?php if ( $ae_siblings ) : ?>
						<div>
							<p class="lbl" style="padding-left:14px"><?php echo esc_html( $ae_cat->name ); ?></p>
							<nav class="art-list">
								<?php foreach ( $ae_siblings as $ae_sib ) : ?>
									<a href="<?php echo esc_url( get_permalink( $ae_sib ) ); ?>"<?php echo get_the_ID() === $ae_sib->ID ? ' class="is-on"' : ''; ?>>
										<?php echo esc_html( $ae_sib->post_title ); ?> <?php ae_icon( 'i-arr-r' ); ?>
									</a>
								<?php endforeach; ?>
							</nav>
						</div>
					<?php endif; ?>
				</aside>

				<!-- BODY -->
				<article class="art-body rv">
					<?php if ( $ae_cat ) : ?>
						<span class="tag"><?php echo esc_html( $ae_cat->name ); ?></span>
					<?php endif; ?>

					<h1><?php the_title(); ?></h1>

					<?php if ( has_excerpt() ) : ?>
						<p class="art-stand"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>

					<div class="art-rule"></div>

					<div class="ae-prose"><?php the_content(); ?></div>

					<?php if ( $ae_keys ) : ?>
						<div class="art-keys">
							<div class="khd"><span><?php esc_html_e( 'Key Aspects', 'aeverything' ); ?></span><i></i></div>
							<?php foreach ( $ae_keys as $ae_i => $ae_row ) : ?>
								<div class="key">
									<div class="key-ic"><?php ae_icon( $ae_key_icons[ $ae_i % count( $ae_key_icons ) ] ); ?></div>
									<div>
										<h4><?php echo esc_html( $ae_row[0] ); ?></h4>
										<?php if ( $ae_row[1] ) : ?><p><?php echo esc_html( $ae_row[1] ); ?></p><?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</article>

				<!-- MEDIA RAIL -->
				<div class="art-media rv">
					<div class="art-hero-img">
						<?php ae_media( get_post_thumbnail_id(), __( 'Article image', 'aeverything' ), 28, 'ae-hero' ); ?>
					</div>

					<?php if ( $ae_quote ) : ?>
						<blockquote class="art-quote">
							<span class="qm">&ldquo;</span>
							<p><?php echo esc_html( $ae_quote ); ?></p>
							<?php if ( $ae_qby ) : ?><cite><?php echo esc_html( $ae_qby ); ?></cite><?php endif; ?>
						</blockquote>
					<?php endif; ?>

					<?php
					$ae_prev = get_previous_post();
					$ae_next = get_next_post();
					if ( $ae_prev || $ae_next ) :
						?>
						<nav class="art-nav">
							<?php if ( $ae_prev ) : ?>
								<a href="<?php echo esc_url( get_permalink( $ae_prev ) ); ?>">
									<span class="rnd"><?php ae_icon( 'i-arr-l' ); ?></span>
									<span><?php esc_html_e( 'Prev Article', 'aeverything' ); ?></span>
								</a>
							<?php else : ?><span></span><?php endif; ?>

							<?php if ( $ae_next ) : ?>
								<a href="<?php echo esc_url( get_permalink( $ae_next ) ); ?>">
									<span><?php esc_html_e( 'Next Article', 'aeverything' ); ?></span>
									<span class="rnd"><?php ae_icon( 'i-arr-r' ); ?></span>
								</a>
							<?php else : ?><span></span><?php endif; ?>
						</nav>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</section>

	<?php
endwhile;

get_footer();

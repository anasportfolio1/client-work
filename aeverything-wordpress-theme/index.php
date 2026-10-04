<?php
/**
 * Fallback template — blog index, archives, search results.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="phead">
	<div class="wrap">
		<div class="rv">
			<h1 class="h-page">
				<?php
				if ( is_search() ) {
					/* translators: %s: search term */
					printf( esc_html__( 'Results for “%s”', 'aeverything' ), esc_html( get_search_query() ) );
				} elseif ( is_archive() ) {
					the_archive_title();
				} else {
					echo esc_html( get_the_title( get_option( 'page_for_posts' ) ) ?: __( 'Magazine', 'aeverything' ) );
				}
				?>
			</h1>
			<?php if ( is_archive() && get_the_archive_description() ) : ?>
				<div class="lede"><?php the_archive_description(); ?></div>
			<?php elseif ( ! is_search() ) : ?>
				<p class="lede"><?php esc_html_e( 'Ideas. Culture. Symbols. Stories.', 'aeverything' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="sect-b">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>

			<div class="mag-grid">
				<?php
				$ae_hues = array( 206, 232, 22, 258, 34, 190 );
				$ae_i    = 0;

				while ( have_posts() ) :
					the_post();
					$ae_hue = $ae_hues[ $ae_i % count( $ae_hues ) ];
					$ae_i++;
					?>
					<a href="<?php the_permalink(); ?>" class="acard rv">
						<?php ae_media( get_post_thumbnail_id(), get_the_title(), $ae_hue, 'ae-card' ); ?>
						<div class="acard-veil"></div>
						<?php
						$ae_cats = get_the_category();
						if ( $ae_cats ) :
							?>
							<span class="tag"><?php echo esc_html( $ae_cats[0]->name ); ?></span>
						<?php endif; ?>
						<div class="acard-bd">
							<h3><?php the_title(); ?></h3>
							<span class="tlink"><?php esc_html_e( 'Read Article', 'aeverything' ); ?> <?php ae_icon( 'i-arr-r' ); ?></span>
						</div>
					</a>
					<?php
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination( array(
				'mid_size'  => 1,
				'prev_text' => __( 'Previous', 'aeverything' ),
				'next_text' => __( 'Next', 'aeverything' ),
			) );
			?>

		<?php else : ?>
			<p class="lede"><?php esc_html_e( 'Nothing here yet.', 'aeverything' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();

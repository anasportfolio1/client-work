<?php
/**
 * Footer — big Æ mark, newsletter, four link columns.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ae_cols = array(
	'footer-shop'    => __( 'Shop', 'aeverything' ),
	'footer-company' => __( 'Company', 'aeverything' ),
	'footer-help'    => __( 'Help', 'aeverything' ),
	'footer-legal'   => __( 'Legal', 'aeverything' ),
);
?>
</main><!-- #content -->

<footer class="ftr">
	<div class="wrap">

		<div class="ftr-hero rv">
			<div class="ftr-mark">&aelig;</div>
			<h2><?php echo esc_html( ae_opt( 'ae_foot_head', 'I am nothing, æverything.' ) ); ?></h2>
			<p><?php echo esc_html( ae_opt( 'ae_foot_text', 'Join the movement. Get exclusive drops, content and inspiration.' ) ); ?></p>

			<form class="nl" data-newsletter method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="ae_newsletter">
				<?php wp_nonce_field( 'ae_newsletter', 'ae_nl_nonce' ); ?>
				<input class="field" type="email" name="ae_nl_email"
					placeholder="<?php esc_attr_e( 'Enter your email', 'aeverything' ); ?>"
					aria-label="<?php esc_attr_e( 'Email', 'aeverything' ); ?>" required>
				<button type="submit" aria-label="<?php esc_attr_e( 'Subscribe', 'aeverything' ); ?>"><?php ae_icon( 'i-arr-r' ); ?></button>
			</form>

			<?php get_template_part( 'template-parts/socials' ); ?>
		</div>

		<div class="ftr-cols">
			<?php foreach ( $ae_cols as $location => $heading ) : ?>
				<div>
					<h5><?php echo esc_html( $heading ); ?></h5>
					<?php
					if ( has_nav_menu( $location ) ) {
						wp_nav_menu( array(
							'theme_location' => $location,
							'container'      => false,
							'items_wrap'     => '%3$s',
							'depth'          => 1,
							'fallback_cb'    => false,
						) );
					}
					?>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="ftr-bot">
			<span><?php echo esc_html( ae_opt( 'ae_foot_copy', '© ' . gmdate( 'Y' ) . ' æverything. All rights reserved.' ) ); ?></span>
			<button class="cur-sel" type="button">
				<?php echo esc_html( ae_opt( 'ae_currency_label', 'UK £' ) ); ?>
				<?php ae_icon( 'i-chev-d' ); ?>
			</button>
		</div>

	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

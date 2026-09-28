<?php
/**
 * 404.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="sect" style="min-height:58vh;display:grid;place-items:center;text-align:center">
	<div class="wrap rv">
		<p class="eyebrow"><?php esc_html_e( 'Error 404', 'aeverything' ); ?></p>
		<h1 class="h-page" style="margin:8px 0 12px"><?php esc_html_e( 'Nothing here.', 'aeverything' ); ?></h1>
		<p class="lede" style="margin:0 auto 22px;max-width:420px">
			<?php esc_html_e( 'This page does not exist — or it moved. Everything else is still where you left it.', 'aeverything' ); ?>
		</p>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-lg"><?php esc_html_e( 'Back Home', 'aeverything' ); ?></a>
	</div>
</section>
<?php
get_footer();

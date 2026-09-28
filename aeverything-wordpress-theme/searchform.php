<?php
/**
 * Search form.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form role="search" method="get" class="nl" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input class="field" type="search" name="s" value="<?php echo get_search_query(); ?>"
		placeholder="<?php esc_attr_e( 'Search…', 'aeverything' ); ?>"
		aria-label="<?php esc_attr_e( 'Search', 'aeverything' ); ?>">
	<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'aeverything' ); ?>"><svg><use href="#i-arr-r"></use></svg></button>
</form>

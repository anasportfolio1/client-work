<?php
/**
 * Social links — only renders the ones the client has filled in.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ae_socials = array(
	'instagram' => array( 's-ig',  'Instagram' ),
	'tiktok'    => array( 's-tt',  'TikTok' ),
	'x'         => array( 's-x',   'X' ),
	'youtube'   => array( 's-yt',  'YouTube' ),
	'pinterest' => array( 's-pin', 'Pinterest' ),
);

$ae_align = isset( $args['align'] ) ? $args['align'] : '';
$ae_any   = false;
foreach ( $ae_socials as $k => $v ) {
	if ( ae_opt( "ae_soc_$k" ) ) {
		$ae_any = true;
		break;
	}
}
?>
<div class="socials"<?php echo $ae_align ? ' style="' . esc_attr( $ae_align ) . '"' : ''; ?>>
	<?php
	foreach ( $ae_socials as $key => $meta ) {
		$url = ae_opt( "ae_soc_$key" );

		/* Before the client adds their links, show the icons as placeholders
		   so the footer matches the design rather than sitting empty. */
		$href = $url ? $url : '#';
		printf(
			'<a class="soc" href="%s"%s aria-label="%s">',
			esc_url( $href ),
			$url ? ' target="_blank" rel="noopener noreferrer"' : '',
			esc_attr( $meta[1] )
		);
		ae_icon( $meta[0] );
		echo '</a>';
	}
	?>
</div>

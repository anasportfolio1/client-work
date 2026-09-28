<?php
/**
 * Hero widget stack — live analog clock and the drop countdown.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ae_show_drop = ae_opt( 'ae_drop_on', true );
$ae_drop_date = ae_opt( 'ae_drop_date', '' );
?>
<div class="widgets rv">

	<div class="w-clock" id="clock">
		<svg class="clock-face" viewBox="0 0 100 100" aria-hidden="true">
			<circle cx="50" cy="50" r="46" stroke="rgba(255,255,255,.3)" stroke-width="1.4" fill="none"/>
			<g stroke="rgba(255,255,255,.55)" stroke-width="1.6" stroke-linecap="round">
				<path d="M50 8v6"/><path d="M50 86v6"/><path d="M8 50h6"/><path d="M86 50h6"/>
			</g>
			<g stroke="rgba(255,255,255,.3)" stroke-width="1.2" stroke-linecap="round">
				<path d="M71 13.4l-3 5.2"/><path d="M86.6 29l-5.2 3"/><path d="M86.6 71l-5.2-3"/><path d="M71 86.6l-3-5.2"/>
				<path d="M29 86.6l3-5.2"/><path d="M13.4 71l5.2-3"/><path d="M13.4 29l5.2 3"/><path d="M29 13.4l3 5.2"/>
			</g>
			<line class="clock-hand clock-h" x1="50" y1="50" x2="50" y2="28"/>
			<line class="clock-hand clock-m" x1="50" y1="50" x2="50" y2="18"/>
			<line class="clock-hand clock-s" x1="50" y1="56" x2="50" y2="16"/>
			<circle cx="50" cy="50" r="2.6" fill="#fff"/>
		</svg>
		<div class="clock-t" id="clockTime">09:41</div>
		<div class="clock-ap" id="clockAp">AM</div>
	</div>

	<?php if ( $ae_show_drop ) : ?>
		<div class="w-drop">
			<span class="eyebrow"><?php esc_html_e( 'Next Drop', 'aeverything' ); ?></span>

			<div class="cdown" id="countdown"<?php echo $ae_drop_date ? ' data-target="' . esc_attr( $ae_drop_date ) . '"' : ''; ?>>
				<div class="cd-u"><div class="cd-n" id="cdD">03</div><div class="cd-l"><?php esc_html_e( 'DAYS', 'aeverything' ); ?></div></div>
				<div class="cd-s">:</div>
				<div class="cd-u"><div class="cd-n" id="cdH">14</div><div class="cd-l"><?php esc_html_e( 'HRS', 'aeverything' ); ?></div></div>
				<div class="cd-s">:</div>
				<div class="cd-u"><div class="cd-n" id="cdM">27</div><div class="cd-l"><?php esc_html_e( 'MINS', 'aeverything' ); ?></div></div>
				<div class="cd-s">:</div>
				<div class="cd-u"><div class="cd-n" id="cdS">18</div><div class="cd-l"><?php esc_html_e( 'SECS', 'aeverything' ); ?></div></div>
			</div>

			<div class="drop-meta">
				<b><?php echo esc_html( ae_opt( 'ae_drop_name', 'ÆD01 / BUBBLE GUM' ) ); ?></b><br>
				<?php echo esc_html( ae_opt( 'ae_drop_when', 'April 4th, 2PM GMT' ) ); ?>
			</div>

			<a class="tlink" href="<?php echo esc_url( ae_opt( 'ae_drop_link' ) ? ae_opt( 'ae_drop_link' ) : ae_shop_url() ); ?>">
				<?php esc_html_e( 'View Drop', 'aeverything' ); ?> <?php ae_icon( 'i-arr-r' ); ?>
			</a>
		</div>
	<?php endif; ?>

</div>

<?php
/**
 * The sky — drawn in code, not photographed.
 *
 * Three layers of fractal noise, each thresholded into soft-edged cloud and
 * drifting at its own speed, over a deep-to-bright blue gradient with a sun
 * in the top-left. The noise is rasterised once by the browser; only the
 * wrapping div moves, so the drift costs almost nothing per frame.
 *
 * Filter ids must stay unique — they are referenced by url() from the CSS
 * layer below.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sky-bg" aria-hidden="true">
	<span class="sky-sun"></span>

	<!-- far: big slow masses -->
	<div class="sky-l sky-l1">
		<svg viewBox="0 0 1200 600" preserveAspectRatio="none">
			<filter id="aeCloudA" x="0" y="0" width="100%" height="100%" color-interpolation-filters="sRGB">
				<feTurbulence type="fractalNoise" baseFrequency="0.0036 0.0115" numOctaves="4" seed="3" stitchTiles="stitch"/>
				<feColorMatrix type="matrix" values="0 0 0 0 1  0 0 0 0 1  0 0 0 0 1  1 0 0 0 0"/>
				<feComponentTransfer><feFuncA type="table" tableValues="0 0 0 0.07 0.46 0.86 1 1"/></feComponentTransfer>
			</filter>
			<rect width="1200" height="600" filter="url(#aeCloudA)"/>
		</svg>
	</div>

	<!-- mid -->
	<div class="sky-l sky-l2">
		<svg viewBox="0 0 1200 600" preserveAspectRatio="none">
			<filter id="aeCloudB" x="0" y="0" width="100%" height="100%" color-interpolation-filters="sRGB">
				<feTurbulence type="fractalNoise" baseFrequency="0.0062 0.019" numOctaves="5" seed="17" stitchTiles="stitch"/>
				<feColorMatrix type="matrix" values="0 0 0 0 1  0 0 0 0 1  0 0 0 0 1  1 0 0 0 0"/>
				<feComponentTransfer><feFuncA type="table" tableValues="0 0 0 0.05 0.42 0.84 1 1"/></feComponentTransfer>
			</filter>
			<rect width="1200" height="600" filter="url(#aeCloudB)"/>
		</svg>
	</div>

	<!-- near: smaller wisps, quickest -->
	<div class="sky-l sky-l3">
		<svg viewBox="0 0 1200 600" preserveAspectRatio="none">
			<filter id="aeCloudC" x="0" y="0" width="100%" height="100%" color-interpolation-filters="sRGB">
				<feTurbulence type="fractalNoise" baseFrequency="0.0115 0.032" numOctaves="5" seed="41" stitchTiles="stitch"/>
				<feColorMatrix type="matrix" values="0 0 0 0 1  0 0 0 0 1  0 0 0 0 1  1 0 0 0 0"/>
				<feComponentTransfer><feFuncA type="table" tableValues="0 0 0 0 0.34 0.80 1 1"/></feComponentTransfer>
			</filter>
			<rect width="1200" height="600" filter="url(#aeCloudC)"/>
		</svg>
	</div>
</div>

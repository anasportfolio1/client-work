<?php
/**
 * Default page template.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<section class="phead">
		<div class="wrap"><h1 class="h-page rv"><?php the_title(); ?></h1></div>
	</section>

	<section class="sect-b">
		<div class="wrap">
			<div class="glass rv" style="padding:clamp(20px,3vw,40px)">
				<div class="ae-prose"><?php the_content(); ?></div>
			</div>
		</div>
	</section>
	<?php
endwhile;

get_footer();

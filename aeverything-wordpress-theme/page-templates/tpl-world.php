<?php
/**
 * Template Name: World of Æ
 *
 * Cards are the child pages of this page, so the client adds a pillar by
 * creating a child page — no code, no theme edit.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
the_post();
?>
<section class="world-hero">
	<svg class="globe" viewBox="0 0 400 400" aria-hidden="true">
		<defs>
			<radialGradient id="gg" cx="34%" cy="28%" r="78%">
				<stop offset="0%" stop-color="#6FA9EA" stop-opacity=".55"/>
				<stop offset="62%" stop-color="#1559BE" stop-opacity=".55"/>
				<stop offset="100%" stop-color="#04163A" stop-opacity=".8"/>
			</radialGradient>
			<filter id="glow"><feGaussianBlur stdDeviation="3" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
		</defs>
		<circle cx="200" cy="200" r="152" fill="url(#gg)"/>
		<g fill="none" stroke="rgba(255,255,255,.26)" stroke-width="1">
			<circle cx="200" cy="200" r="152"/>
			<ellipse cx="200" cy="200" rx="152" ry="46"/><ellipse cx="200" cy="200" rx="152" ry="98"/>
			<ellipse cx="200" cy="200" rx="152" ry="136"/><ellipse cx="200" cy="200" rx="46" ry="152"/>
			<ellipse cx="200" cy="200" rx="98" ry="152"/><ellipse cx="200" cy="200" rx="136" ry="152"/>
		</g>
		<g stroke="rgba(255,127,163,.7)" stroke-width="1.3" fill="none" filter="url(#glow)">
			<path d="M112 132 Q180 96 248 128"/><path d="M96 216 Q168 262 262 232"/>
			<path d="M150 104 Q112 190 158 278"/><path d="M248 128 Q300 200 262 232"/>
		</g>
		<g fill="#FFA3BE" filter="url(#glow)">
			<circle cx="112" cy="132" r="4"/><circle cx="248" cy="128" r="4"/><circle cx="96" cy="216" r="3.4"/>
			<circle cx="262" cy="232" r="4"/><circle cx="150" cy="104" r="3"/><circle cx="158" cy="278" r="3.6"/>
			<circle cx="205" cy="180" r="3"/><circle cx="300" cy="188" r="2.6"/><circle cx="82" cy="164" r="2.4"/>
		</g>
	</svg>
	<div class="wrap">
		<div class="rv" style="max-width:520px">
			<h1 class="h-page"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="lede" style="margin-top:8px"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			<a href="#pillars" class="tlink" style="margin-top:18px"><?php esc_html_e( 'Explore More', 'aeverything' ); ?> <?php ae_icon( 'i-arr-r' ); ?></a>
		</div>
	</div>
</section>

<?php
$kids = get_children( array(
	'post_parent' => get_the_ID(),
	'post_type'   => 'page',
	'post_status' => 'publish',
	'orderby'     => 'menu_order',
	'order'       => 'ASC',
) );

if ( $kids ) : ?>
<section class="sect-b" id="pillars">
	<div class="wrap">
		<div class="worldc">
			<?php
			$icons = array( 'i-users', 'i-calendar', 'i-award', 'i-shake', 'i-leaf', 'i-globe', 'i-sparkle' );
			$i = 0;
			foreach ( $kids as $kid ) : ?>
				<a href="<?php echo esc_url( get_permalink( $kid->ID ) ); ?>" class="wcard rv">
					<span class="wcard-ic"><?php ae_icon( $icons[ $i % count( $icons ) ] ); $i++; ?></span>
					<div>
						<h3><?php echo esc_html( $kid->post_title ); ?></h3>
						<?php if ( $kid->post_excerpt ) : ?>
							<p><?php echo esc_html( $kid->post_excerpt ); ?></p>
						<?php endif; ?>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( trim( get_the_content() ) ) : ?>
<section class="sect-b">
	<div class="wrap"><div class="glass rv" style="padding:clamp(20px,3vw,40px)"><div class="ae-prose"><?php the_content(); ?></div></div></div>
</section>
<?php endif; ?>
<?php
get_footer();

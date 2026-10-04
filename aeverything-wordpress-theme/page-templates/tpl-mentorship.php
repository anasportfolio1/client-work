<?php
/**
 * Template Name: Mentorship
 *
 * Built from the client's own design. Deliberately no clock and no drop
 * countdown: those belong to the shop, and a product-launch timer on a
 * coaching page sells the wrong thing.
 *
 * Nothing here is a checkout. The three packages are a choice, and the
 * booking button opens the client's calendar carrying that choice, so
 * whoever takes the call already knows which plan it is about.
 *
 * Every string below is editable under Appearance > Customise > Mentorship.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$ae_booking = ae_opt( 'ae_calendly_url', '' );

/* "Title | Description" per line */
$ae_benefits = ae_lines( ae_opt( 'ae_mentor_benefits', ae_mentor_benefit_defaults() ), 2 );

/* "Name | Duration | Price | bullet; bullet; bullet" per line */
$ae_plans = ae_lines( ae_opt( 'ae_mentor_plans', ae_mentor_plan_defaults() ), 4 );

/* one icon per benefit, in order */
$ae_icons = array( 'i-target', 'i-headphones', 'i-brain', 'i-chart', 'i-leaf' );
?>

<section class="hero hero-page">
	<div class="hero-grid">
		<div class="hero-l">
			<nav class="crumb rv" aria-label="<?php esc_attr_e( 'Breadcrumb', 'aeverything' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'aeverything' ); ?></a>
				<span>/</span>
				<span aria-current="page"><?php echo esc_html( ae_opt( 'ae_mentor_title', 'Mentorship' ) ); ?></span>
			</nav>

			<div class="hero-copy rv">
				<h1 class="h-hero"><?php echo esc_html( ae_opt( 'ae_mentor_title', 'Mentorship' ) ); ?></h1>
				<p class="hero-sub"><?php echo esc_html( ae_opt( 'ae_mentor_sub', 'Guidance. Growth. Alignment.' ) ); ?></p>
				<p class="lede" style="max-width:34ch;margin-bottom:26px">
					<?php echo esc_html( ae_opt( 'ae_mentor_intro', '1 on 1 mentorship designed to help you unlock your full potential and create the life you’re meant to live.' ) ); ?>
				</p>
				<a href="#apply" class="btn btn-lg">
					<?php echo esc_html( ae_opt( 'ae_mentor_btn', 'Apply for Mentorship' ) ); ?> <?php ae_icon( 'i-arr-r' ); ?>
				</a>
			</div>
			<div></div>
		</div>

		<div class="hero-r">
			<?php ae_media( ae_opt( 'ae_mentor_img', '' ), __( 'Hero image', 'aeverything' ), 222, 'ae-hero' ); ?>
			<a href="<?php echo esc_url( home_url( '/world-of-ae/' ) ); ?>" class="ae-badge"
				aria-label="<?php esc_attr_e( 'World of Æ', 'aeverything' ); ?>">&aelig;</a>
		</div>
	</div>
</section>

<?php if ( $ae_benefits ) : ?>
<section class="sect-b">
	<div class="wrap">
		<div class="sec-head sec-head-c rv">
			<h2 class="h-sec"><?php echo esc_html( ae_opt( 'ae_mentor_benefits_title', 'What You’ll Get' ) ); ?></h2>
		</div>
		<div class="bene-grid">
			<?php foreach ( $ae_benefits as $i => $b ) : ?>
				<div class="bene rv">
					<?php ae_icon( isset( $ae_icons[ $i ] ) ? $ae_icons[ $i ] : 'i-sparkle' ); ?>
					<h3><?php echo esc_html( $b[0] ); ?></h3>
					<?php if ( $b[1] ) : ?><p><?php echo esc_html( $b[1] ); ?></p><?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( $ae_plans ) : ?>
<section class="sect-b" id="apply">
	<div class="wrap">
		<div class="sec-head sec-head-c rv">
			<h2 class="h-sec"><?php echo esc_html( ae_opt( 'ae_mentor_plans_title', 'Mentorship Packages' ) ); ?></h2>
		</div>

		<div class="pkg-grid pkg-grid-3" id="planGrid">
			<?php
			$ae_popular = strtolower( trim( ae_opt( 'ae_mentor_popular', 'Transformation' ) ) );
			foreach ( $ae_plans as $plan ) :
				list( $name, $duration, $price, $bullets ) = $plan;
				$is_best = $name && strtolower( $name ) === $ae_popular;
				?>
				<button type="button" class="pkg rv<?php echo $is_best ? ' pkg-best' : ''; ?>"
					data-pkg="<?php echo esc_attr( $name ); ?>">
					<?php if ( $is_best ) : ?>
						<span class="pkg-flag"><?php esc_html_e( 'Most Popular', 'aeverything' ); ?></span>
					<?php endif; ?>
					<span class="pkg-name"><?php echo esc_html( $name ); ?></span>
					<?php if ( $duration ) : ?><span class="pkg-blurb"><?php echo esc_html( $duration ); ?></span><?php endif; ?>
					<?php if ( $price ) : ?><span class="pkg-price"><?php echo esc_html( $price ); ?></span><?php endif; ?>

					<?php if ( $bullets ) : ?>
						<span class="pkg-list">
							<?php foreach ( array_filter( array_map( 'trim', explode( ';', $bullets ) ) ) as $line ) : ?>
								<span><?php ae_icon( 'i-check' ); ?><?php echo esc_html( $line ); ?></span>
							<?php endforeach; ?>
						</span>
					<?php endif; ?>

					<span class="pkg-cta"><?php esc_html_e( 'Select Plan', 'aeverything' ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="sect-b">
	<div class="wrap">
		<div class="book-band rv">
			<div>
				<h2><?php echo esc_html( ae_opt( 'ae_mentor_cta_title', 'Ready to Transform?' ) ); ?></h2>
				<p><?php echo esc_html( ae_opt( 'ae_mentor_cta_sub', 'Spots are limited. Serious inquiries only.' ) ); ?></p>
			</div>
			<span class="book-hint"><?php esc_html_e( 'Choose a package above to continue.', 'aeverything' ); ?></span>
			<a href="<?php echo $ae_booking ? esc_url( $ae_booking ) : '#apply'; ?>"
				class="btn btn-lg" data-book
				data-book-url="<?php echo esc_attr( $ae_booking ); ?>"
				<?php echo $ae_booking ? 'target="_blank" rel="noopener"' : ''; ?>>
				<?php echo esc_html( ae_opt( 'ae_mentor_cta_btn', 'Apply Now' ) ); ?> <?php ae_icon( 'i-arr-r' ); ?>
			</a>
		</div>
	</div>
</section>

<?php
get_footer();

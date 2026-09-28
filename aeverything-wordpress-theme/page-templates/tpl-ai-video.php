<?php
/**
 * Template Name: AI Video Brief
 *
 * Intake form only. The team produces the videos with their own tools,
 * so nothing here calls an AI service and a submission costs nothing.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
the_post();

$ae_sent  = isset( $_GET['brief'] ) && 'sent' === sanitize_text_field( wp_unslash( $_GET['brief'] ) );
$ae_error = get_transient( 'ae_brief_error_' . ae_client_key() );
if ( $ae_error ) {
	delete_transient( 'ae_brief_error_' . ae_client_key() );
}

$ae_fields = ae_brief_fields();
$ae_groups = array(
	__( 'About you', 'aeverything' )          => array( 'package', 'name', 'email', 'phone', 'brand' ),
	__( 'Your files', 'aeverything' )         => array( '__uploads' ),
	__( 'Format', 'aeverything' )             => array( 'ratio', 'length', 'quantity', 'platform' ),
	__( 'Creative direction', 'aeverything' ) => array( 'style', 'script', 'voiceover', 'language', 'captions', 'music' ),
	__( 'The brief', 'aeverything' )          => array( 'brief', 'refs', 'avoid', 'deadline' ),
);
?>
<section class="phead">
	<div class="wrap">
		<div class="rv" style="max-width:640px">
			<h1 class="h-page"><?php echo esc_html( ae_opt( 'ae_ai_title', 'AI Marketing Videos' ) ); ?></h1>
			<p class="lede" style="margin-top:10px"><?php echo esc_html( ae_opt( 'ae_ai_sub', '' ) ); ?></p>
		</div>
	</div>
</section>

<section class="sect-b">
	<div class="wrap">

		<?php if ( $ae_sent ) : ?>

			<div class="glass rv ae-notice ae-notice-ok">
				<h3 class="h-sec" style="margin-bottom:8px"><?php esc_html_e( 'Brief received', 'aeverything' ); ?></h3>
				<p class="lede"><?php echo esc_html( ae_opt( 'ae_ai_thanks', 'Got it. Our team will be in touch shortly.' ) ); ?></p>
			</div>

		<?php else : ?>

			<!-- PACKAGES -->
			<p class="lbl rv"><?php esc_html_e( 'Choose a package', 'aeverything' ); ?></p>
			<div class="pkg-grid rv" id="packages">
				<?php foreach ( ae_brief_packages() as $ae_key => $ae_pk ) : ?>
					<button type="button" class="pkg<?php echo ! empty( $ae_pk['best'] ) ? ' pkg-best' : ''; ?>"
						data-pkg="<?php echo esc_attr( $ae_pk['name'] ); ?>">
						<?php if ( ! empty( $ae_pk['best'] ) ) : ?>
							<span class="pkg-flag"><?php esc_html_e( 'Most picked', 'aeverything' ); ?></span>
						<?php endif; ?>
						<span class="pkg-name"><?php echo esc_html( $ae_pk['name'] ); ?></span>
						<span class="pkg-price"><?php echo esc_html( $ae_pk['price'] ); ?></span>
						<span class="pkg-blurb"><?php echo esc_html( $ae_pk['blurb'] ); ?></span>
						<span class="pkg-list">
							<?php foreach ( $ae_pk['items'] as $ae_it ) : ?>
								<span><?php ae_icon( 'i-check' ); ?><?php echo esc_html( $ae_it ); ?></span>
							<?php endforeach; ?>
						</span>
						<span class="pkg-cta">
							<?php esc_html_e( 'Choose', 'aeverything' ); ?> <?php ae_icon( 'i-arr-r' ); ?>
						</span>
					</button>
				<?php endforeach; ?>
			</div>

			<?php if ( $ae_error ) : ?>
				<div class="glass rv ae-notice ae-notice-err"><p><?php echo esc_html( $ae_error ); ?></p></div>
			<?php endif; ?>

			<div class="brief-wrap" id="briefWrap">
				<p class="lbl rv" style="margin-top:34px">
					<?php esc_html_e( 'Your brief', 'aeverything' ); ?>
					<span class="pkg-chosen" id="pkgChosen"></span>
				</p>

			<form class="ae-form glass rv" method="post" enctype="multipart/form-data" action="">
				<?php wp_nonce_field( 'ae_brief', 'ae_brief_nonce' ); ?>
				<input type="hidden" name="ae_brief_submit" value="1">

				<p class="ae-hp">
					<label><?php esc_html_e( 'Leave this empty', 'aeverything' ); ?>
						<input type="text" name="ae_website" tabindex="-1" autocomplete="off">
					</label>
				</p>

				<?php foreach ( $ae_groups as $ae_heading => $ae_keys ) : ?>
					<fieldset class="ae-fs">
						<legend class="lbl"><?php echo esc_html( $ae_heading ); ?></legend>

						<?php
						foreach ( $ae_keys as $ae_key ) :

							/* ---- upload slots ---- */
							if ( '__uploads' === $ae_key ) {
								foreach ( ae_brief_uploads() as $ae_slot => $ae_cfg ) {
									$ae_multi = ! empty( $ae_cfg['multiple'] );
									$ae_req   = ! empty( $ae_cfg['required'] );
									?>
									<div class="ae-row">
										<label class="ae-lb" for="ae_<?php echo esc_attr( $ae_slot ); ?>">
											<?php echo esc_html( $ae_cfg['label'] ); ?><?php echo $ae_req ? ' <em>*</em>' : ''; ?>
										</label>
										<input class="ae-file" type="file"
											id="ae_<?php echo esc_attr( $ae_slot ); ?>"
											name="ae_<?php echo esc_attr( $ae_slot ); ?><?php echo $ae_multi ? '[]' : ''; ?>"
											accept="image/jpeg,image/png,image/webp"
											<?php echo $ae_multi ? 'multiple' : ''; ?>
											<?php echo $ae_req ? 'required' : ''; ?>>
										<small class="ae-hint"><?php echo esc_html( $ae_cfg['hint'] ); ?></small>
									</div>
									<?php
								}
								continue;
							}

							/* ---- the package is set by the cards above ---- */
							$ae_cfg = $ae_fields[ $ae_key ];
							if ( 'hidden' === $ae_cfg['type'] ) {
								printf(
									'<input type="hidden" id="ae_%s" name="ae_%s" value="">',
									esc_attr( $ae_key ),
									esc_attr( $ae_key )
								);
								continue;
							}

							/* ---- normal fields ---- */
							$ae_req = ! empty( $ae_cfg['required'] );
							?>
							<div class="ae-row">
								<label class="ae-lb" for="ae_<?php echo esc_attr( $ae_key ); ?>">
									<?php echo esc_html( $ae_cfg['label'] ); ?><?php echo $ae_req ? ' <em>*</em>' : ''; ?>
								</label>

								<?php if ( 'textarea' === $ae_cfg['type'] ) : ?>

									<textarea class="ae-input" id="ae_<?php echo esc_attr( $ae_key ); ?>"
										name="ae_<?php echo esc_attr( $ae_key ); ?>" rows="4"
										<?php echo $ae_req ? 'required' : ''; ?>></textarea>

								<?php elseif ( 'checks' === $ae_cfg['type'] || 'radio' === $ae_cfg['type'] ) : ?>

									<div class="ae-opts">
										<?php
										$ae_input = ( 'checks' === $ae_cfg['type'] ) ? 'checkbox' : 'radio';
										$ae_nm    = ( 'checks' === $ae_cfg['type'] ) ? 'ae_' . $ae_key . '[]' : 'ae_' . $ae_key;
										foreach ( $ae_cfg['options'] as $ae_v => $ae_l ) :
											?>
											<label class="ae-opt">
												<input type="<?php echo esc_attr( $ae_input ); ?>"
													name="<?php echo esc_attr( $ae_nm ); ?>"
													value="<?php echo esc_attr( $ae_v ); ?>">
												<span><?php echo esc_html( $ae_l ); ?></span>
											</label>
										<?php endforeach; ?>
									</div>

								<?php else : ?>

									<input class="ae-input" type="<?php echo esc_attr( $ae_cfg['type'] ); ?>"
										id="ae_<?php echo esc_attr( $ae_key ); ?>"
										name="ae_<?php echo esc_attr( $ae_key ); ?>"
										<?php echo $ae_req ? 'required' : ''; ?>>

								<?php endif; ?>
							</div>
							<?php
						endforeach;
						?>
					</fieldset>
				<?php endforeach; ?>

				<button type="submit" class="btn btn-lg">
					<?php esc_html_e( 'Send My Brief', 'aeverything' ); ?> <?php ae_icon( 'i-arr-r' ); ?>
				</button>
			</form>
			</div><!-- /.brief-wrap -->

		<?php endif; ?>

	</div>
</section>
<?php
get_footer();

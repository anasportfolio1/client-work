<?php
/**
 * AI Video Brief — customers send their assets and requirements, the
 * team produces the videos. This is an intake form, not a generator:
 * nothing here calls an AI service or costs money per submission.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * 1. Where submissions land
 * ---------------------------------------------------------------------- */
function ae_register_brief_cpt() {
	register_post_type( 'ae_brief', array(
		'labels' => array(
			'name'          => __( 'Video Briefs', 'aeverything' ),
			'singular_name' => __( 'Video Brief', 'aeverything' ),
			'menu_name'     => __( 'Video Briefs', 'aeverything' ),
			'all_items'     => __( 'All Briefs', 'aeverything' ),
			'edit_item'     => __( 'View Brief', 'aeverything' ),
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-video-alt3',
		'menu_position'       => 24,
		'supports'            => array( 'title' ),
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
	) );

	register_taxonomy( 'ae_brief_status', 'ae_brief', array(
		'labels'            => array(
			'name'          => __( 'Status', 'aeverything' ),
			'singular_name' => __( 'Status', 'aeverything' ),
		),
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'hierarchical'      => true,
	) );
}
add_action( 'init', 'ae_register_brief_cpt' );

/**
 * Packages shown above the brief. Choosing one scrolls to the form and
 * locks that package in, so the team knows what was ordered.
 */
function ae_brief_packages() {
	return array(
		'single' => array(
			'name'  => '1 Video',
			'price' => '£95',
			'blurb' => 'One finished video. Good for testing a product before you scale.',
			'items' => array( '1 video, any ratio', '2 revisions', '48-hour delivery' ),
		),
		'three'  => array(
			'name'  => '3 Videos',
			'price' => '£240',
			'blurb' => 'Enough to test three angles and find what your audience responds to.',
			'items' => array( '3 videos', '3 ratios included', '2 revisions each', '4-day delivery' ),
			'best'  => true,
		),
		'seven'  => array(
			'name'  => '7 Videos',
			'price' => '£490',
			'blurb' => 'A full week of content from a single shoot of your product.',
			'items' => array( '7 videos', 'All ratios', 'Voiceover included', 'Captions', '7-day delivery' ),
		),
		'custom' => array(
			'name'  => 'Custom',
			'price' => 'Talk to us',
			'blurb' => 'Ongoing volume, a full campaign, or something we have not thought of.',
			'items' => array( 'Built around your goals', 'Priority turnaround', 'Direct line to the team' ),
		),
	);
}

/** The fields a video team actually needs, in the order they're asked. */
function ae_brief_fields() {
	return array(
		'package'   => array( 'label' => 'Package',          'type' => 'hidden' ),
		'name'      => array( 'label' => 'Your name',        'type' => 'text',   'required' => true ),
		'email'     => array( 'label' => 'Email',            'type' => 'email',  'required' => true ),
		'phone'     => array( 'label' => 'WhatsApp / phone', 'type' => 'text' ),
		'brand'     => array( 'label' => 'Brand name',       'type' => 'text' ),

		'ratio'     => array( 'label' => 'Aspect ratio', 'type' => 'checks', 'required' => true,
			'options' => array(
				'9:16' => '9:16 — Reels, TikTok, Shorts',
				'1:1'  => '1:1 — Feed square',
				'4:5'  => '4:5 — Instagram portrait',
				'16:9' => '16:9 — YouTube, website',
			) ),
		'length'    => array( 'label' => 'Video length', 'type' => 'radio',
			'options' => array( '15s' => '15 seconds', '30s' => '30 seconds', '60s' => '60 seconds' ) ),
		'quantity'  => array( 'label' => 'How many videos', 'type' => 'number' ),
		'platform'  => array( 'label' => 'Where will you post it', 'type' => 'checks',
			'options' => array(
				'tiktok'    => 'TikTok',
				'instagram' => 'Instagram',
				'youtube'   => 'YouTube',
				'facebook'  => 'Facebook',
			) ),

		'style'     => array( 'label' => 'Style', 'type' => 'radio',
			'options' => array(
				'energetic' => 'Energetic',
				'luxury'    => 'Luxury',
				'minimal'   => 'Minimal',
				'funny'     => 'Funny',
			) ),
		'script'    => array( 'label' => 'Do you have a script?', 'type' => 'radio',
			'options' => array( 'have' => 'Yes — it is below', 'write' => 'No — please write it' ) ),
		'voiceover' => array( 'label' => 'Voiceover', 'type' => 'radio',
			'options' => array( 'none' => 'None', 'male' => 'Male', 'female' => 'Female' ) ),
		'language'  => array( 'label' => 'Voice language / accent', 'type' => 'text' ),
		'captions'  => array( 'label' => 'On-screen captions', 'type' => 'radio',
			'options' => array( 'yes' => 'Yes', 'no' => 'No' ) ),
		'music'     => array( 'label' => 'Music vibe', 'type' => 'text' ),

		'brief'     => array( 'label' => 'Instructions — tell us exactly what you want', 'type' => 'textarea', 'required' => true ),
		'refs'      => array( 'label' => 'Reference videos you like (links)', 'type' => 'textarea' ),
		'avoid'     => array( 'label' => 'Anything to avoid', 'type' => 'textarea' ),
		'deadline'  => array( 'label' => 'Deadline', 'type' => 'date' ),
	);
}

/** Upload slots. */
function ae_brief_uploads() {
	return array(
		'product_images' => array( 'label' => 'Product photos', 'multiple' => true, 'required' => true,
			'hint' => 'Up to 8 images — JPG, PNG or WebP, max 8MB each.' ),
		'avatar'         => array( 'label' => 'Your photo / avatar', 'multiple' => false,
			'hint' => 'Only if you want to appear in the video.' ),
		'logo'           => array( 'label' => 'Logo', 'multiple' => false,
			'hint' => 'PNG with a transparent background works best.' ),
	);
}

/* -------------------------------------------------------------------------
 * 2. Handling a submission
 * ---------------------------------------------------------------------- */
function ae_handle_brief() {
	if ( empty( $_POST['ae_brief_submit'] ) ) {
		return;
	}

	$nonce = isset( $_POST['ae_brief_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['ae_brief_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'ae_brief' ) ) {
		ae_brief_error( __( 'Your session expired. Please try again.', 'aeverything' ) );
		return;
	}

	/* Honeypot — real people leave this empty. */
	if ( ! empty( $_POST['ae_website'] ) ) {
		return;
	}

	$fields = ae_brief_fields();
	$data   = array();

	foreach ( $fields as $key => $cfg ) {
		$raw = isset( $_POST[ "ae_$key" ] ) ? wp_unslash( $_POST[ "ae_$key" ] ) : '';

		if ( 'checks' === $cfg['type'] ) {
			$val = is_array( $raw ) ? implode( ', ', array_map( 'sanitize_text_field', $raw ) ) : '';
		} elseif ( 'textarea' === $cfg['type'] ) {
			$val = sanitize_textarea_field( $raw );
		} elseif ( 'email' === $cfg['type'] ) {
			$val = sanitize_email( $raw );
		} else {
			$val = sanitize_text_field( $raw );
		}

		if ( ! empty( $cfg['required'] ) && '' === $val ) {
			/* translators: %s: field label */
			ae_brief_error( sprintf( __( 'Please fill in: %s', 'aeverything' ), $cfg['label'] ) );
			return;
		}
		$data[ $key ] = $val;
	}

	if ( ! is_email( $data['email'] ) ) {
		ae_brief_error( __( 'That email address does not look right.', 'aeverything' ) );
		return;
	}

	/* ---- Files -------------------------------------------------- */
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$allowed  = array( 'jpg|jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp' );
	$max_size = 8 * MB_IN_BYTES;
	$attached = array();

	foreach ( ae_brief_uploads() as $slot => $cfg ) {
		if ( empty( $_FILES[ "ae_$slot" ]['name'] ) ) {
			if ( ! empty( $cfg['required'] ) ) {
				/* translators: %s: upload slot label */
				ae_brief_error( sprintf( __( 'Please attach: %s', 'aeverything' ), $cfg['label'] ) );
				return;
			}
			continue;
		}

		$names = (array) $_FILES[ "ae_$slot" ]['name'];
		$count = min( count( $names ), 8 );

		for ( $i = 0; $i < $count; $i++ ) {
			$file = array(
				'name'     => sanitize_file_name( is_array( $_FILES[ "ae_$slot" ]['name'] ) ? $_FILES[ "ae_$slot" ]['name'][ $i ] : $_FILES[ "ae_$slot" ]['name'] ),
				'type'     => is_array( $_FILES[ "ae_$slot" ]['type'] ) ? $_FILES[ "ae_$slot" ]['type'][ $i ] : $_FILES[ "ae_$slot" ]['type'],
				'tmp_name' => is_array( $_FILES[ "ae_$slot" ]['tmp_name'] ) ? $_FILES[ "ae_$slot" ]['tmp_name'][ $i ] : $_FILES[ "ae_$slot" ]['tmp_name'],
				'error'    => is_array( $_FILES[ "ae_$slot" ]['error'] ) ? $_FILES[ "ae_$slot" ]['error'][ $i ] : $_FILES[ "ae_$slot" ]['error'],
				'size'     => is_array( $_FILES[ "ae_$slot" ]['size'] ) ? $_FILES[ "ae_$slot" ]['size'][ $i ] : $_FILES[ "ae_$slot" ]['size'],
			);

			if ( UPLOAD_ERR_NO_FILE === $file['error'] || ! $file['name'] ) {
				continue;
			}
			if ( $file['size'] > $max_size ) {
				ae_brief_error( __( 'One of your files is over 8MB. Please compress it and try again.', 'aeverything' ) );
				return;
			}

			$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $allowed );
			if ( empty( $check['ext'] ) || empty( $check['type'] ) ) {
				ae_brief_error( __( 'Images only, please — JPG, PNG or WebP.', 'aeverything' ) );
				return;
			}

			$moved = wp_handle_upload( $file, array( 'test_form' => false, 'mimes' => $allowed ) );
			if ( isset( $moved['error'] ) ) {
				ae_brief_error( $moved['error'] );
				return;
			}

			$att_id = wp_insert_attachment( array(
				'post_mime_type' => $moved['type'],
				'post_title'     => $file['name'],
				'post_status'    => 'inherit',
			), $moved['file'] );

			if ( ! is_wp_error( $att_id ) ) {
				wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $moved['file'] ) );
				$attached[ $slot ][] = $att_id;
			}
		}
	}

	/* ---- Save --------------------------------------------------- */
	$title = sprintf( '%s — %s', $data['name'], $data['brand'] ? $data['brand'] : gmdate( 'j M Y' ) );

	$post_id = wp_insert_post( array(
		'post_type'   => 'ae_brief',
		'post_title'  => wp_strip_all_tags( $title ),
		'post_status' => 'publish',
	), true );

	if ( is_wp_error( $post_id ) ) {
		ae_brief_error( __( 'Something went wrong saving your brief. Please try again.', 'aeverything' ) );
		return;
	}

	foreach ( $data as $k => $v ) {
		update_post_meta( $post_id, "_ae_$k", $v );
	}
	foreach ( $attached as $slot => $ids ) {
		update_post_meta( $post_id, "_ae_files_$slot", $ids );
	}
	update_post_meta( $post_id, '_ae_submitted', current_time( 'mysql' ) );

	wp_set_object_terms( $post_id, 'New', 'ae_brief_status' );

	ae_notify_team( $post_id, $data, $attached );

	/* PRG so a refresh doesn't resubmit. */
	wp_safe_redirect( add_query_arg( 'brief', 'sent', wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
	exit;
}
add_action( 'template_redirect', 'ae_handle_brief' );

function ae_brief_error( $msg ) {
	set_transient( 'ae_brief_error_' . ae_client_key(), $msg, 60 );
}

function ae_client_key() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'x';
	return md5( $ip );
}

/** Email the team, and confirm to the customer. */
function ae_notify_team( $post_id, $data, $attached ) {
	$to = ae_opt( 'ae_ai_email', get_option( 'admin_email' ) );

	$lines = array( 'New AI video brief', str_repeat( '=', 40 ), '' );
	foreach ( ae_brief_fields() as $key => $cfg ) {
		if ( ! empty( $data[ $key ] ) ) {
			$lines[] = $cfg['label'] . ': ' . $data[ $key ];
		}
	}
	$lines[] = '';
	foreach ( $attached as $slot => $ids ) {
		foreach ( $ids as $id ) {
			$lines[] = 'File: ' . wp_get_attachment_url( $id );
		}
	}
	$lines[] = '';
	$lines[] = 'Open in admin: ' . admin_url( 'post.php?post=' . $post_id . '&action=edit' );

	wp_mail(
		$to,
		'[' . get_bloginfo( 'name' ) . '] Video brief — ' . $data['name'],
		implode( "\n", $lines )
	);

	if ( is_email( $data['email'] ) ) {
		wp_mail(
			$data['email'],
			'We got your video brief',
			"Hi " . $data['name'] . ",\n\n"
			. "Thanks — your brief is with our team and we'll be in touch shortly.\n\n"
			. get_bloginfo( 'name' ) . "\n" . home_url( '/' )
		);
	}
}

/* -------------------------------------------------------------------------
 * 3. Reading a submission in wp-admin
 * ---------------------------------------------------------------------- */
function ae_brief_metabox() {
	add_meta_box( 'ae_brief_view', __( 'Brief', 'aeverything' ), 'ae_brief_metabox_html', 'ae_brief', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'ae_brief_metabox' );

function ae_brief_metabox_html( $post ) {
	echo '<table class="widefat striped"><tbody>';
	foreach ( ae_brief_fields() as $key => $cfg ) {
		$v = get_post_meta( $post->ID, "_ae_$key", true );
		if ( '' === $v ) {
			continue;
		}
		printf(
			'<tr><th style="width:220px;text-align:left">%s</th><td>%s</td></tr>',
			esc_html( $cfg['label'] ),
			nl2br( esc_html( $v ) )
		);
	}
	echo '</tbody></table>';

	foreach ( ae_brief_uploads() as $slot => $cfg ) {
		$ids = (array) get_post_meta( $post->ID, "_ae_files_$slot", true );
		$ids = array_filter( $ids );
		if ( ! $ids ) {
			continue;
		}
		echo '<h3>' . esc_html( $cfg['label'] ) . '</h3><p>';
		foreach ( $ids as $id ) {
			printf(
				'<a href="%s" target="_blank" rel="noopener" style="display:inline-block;margin:0 8px 8px 0">%s</a>',
				esc_url( wp_get_attachment_url( $id ) ),
				wp_get_attachment_image( $id, array( 140, 140 ) )
			);
		}
		echo '</p>';
	}
}

/** Show who sent it in the list table. */
function ae_brief_columns( $cols ) {
	return array(
		'cb'        => $cols['cb'],
		'title'     => __( 'From', 'aeverything' ),
		'ae_email'  => __( 'Email', 'aeverything' ),
		'ae_ratio'  => __( 'Ratio', 'aeverything' ),
		'ae_files'  => __( 'Files', 'aeverything' ),
		'date'      => __( 'Received', 'aeverything' ),
	);
}
add_filter( 'manage_ae_brief_posts_columns', 'ae_brief_columns' );

function ae_brief_column( $col, $post_id ) {
	if ( 'ae_email' === $col ) {
		echo esc_html( get_post_meta( $post_id, '_ae_email', true ) );
	} elseif ( 'ae_ratio' === $col ) {
		echo esc_html( get_post_meta( $post_id, '_ae_ratio', true ) );
	} elseif ( 'ae_files' === $col ) {
		$n = 0;
		foreach ( ae_brief_uploads() as $slot => $x ) {
			$n += count( array_filter( (array) get_post_meta( $post_id, "_ae_files_$slot", true ) ) );
		}
		echo (int) $n;
	}
}
add_action( 'manage_ae_brief_posts_custom_column', 'ae_brief_column', 10, 2 );

<?php
/**
 * Editable fields for the article and lesson layouts.
 *
 * Repeating blocks (key aspects, principles, resources) use one textarea
 * with "Title | Description" per line. It keeps the client on native
 * WordPress with no paid custom-fields plugin, and it is quick to edit.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Parse a "Title | Description" textarea into rows.
 *
 * @param string $raw   Raw meta value.
 * @param int    $parts How many columns to expect.
 * @return array
 */
function ae_lines( $raw, $parts = 2 ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$bits = array_map( 'trim', explode( '|', $line ) );
		$bits = array_pad( $bits, $parts, '' );
		$out[] = array_slice( $bits, 0, $parts );
	}
	return $out;
}

/** Render a labelled textarea / input inside a metabox. */
function ae_mb_field( $post_id, $key, $label, $hint = '', $rows = 0 ) {
	$val = get_post_meta( $post_id, $key, true );
	echo '<p style="margin:0 0 16px">';
	echo '<label for="' . esc_attr( $key ) . '" style="display:block;font-weight:600;margin-bottom:5px">' . esc_html( $label ) . '</label>';
	if ( $rows ) {
		printf(
			'<textarea id="%1$s" name="%1$s" rows="%2$d" style="width:100%%;font-family:monospace;font-size:12px">%3$s</textarea>',
			esc_attr( $key ),
			(int) $rows,
			esc_textarea( $val )
		);
	} else {
		printf(
			'<input type="text" id="%1$s" name="%1$s" value="%2$s" style="width:100%%">',
			esc_attr( $key ),
			esc_attr( $val )
		);
	}
	if ( $hint ) {
		echo '<span class="description" style="display:block;margin-top:5px">' . esc_html( $hint ) . '</span>';
	}
	echo '</p>';
}

/* -------------------------------------------------------------------------
 * Articles (Posts)
 * ---------------------------------------------------------------------- */
function ae_article_metabox() {
	add_meta_box( 'ae_article', __( 'Æ Article Layout', 'aeverything' ), 'ae_article_metabox_html', 'post', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'ae_article_metabox' );

function ae_article_metabox_html( $post ) {
	wp_nonce_field( 'ae_article_meta', 'ae_article_nonce' );

	echo '<p class="description" style="margin-bottom:14px">'
		. esc_html__( 'The sub-heading under the title uses the Excerpt field. Featured Image becomes the large article image.', 'aeverything' )
		. '</p>';

	ae_mb_field(
		$post->ID,
		'_ae_keys',
		__( 'Key Aspects', 'aeverything' ),
		__( 'One per line:  Title | Description', 'aeverything' ),
		6
	);
	ae_mb_field( $post->ID, '_ae_quote', __( 'Pull quote', 'aeverything' ), '', 3 );
	ae_mb_field( $post->ID, '_ae_quote_by', __( 'Quote author', 'aeverything' ) );
}

function ae_article_save( $post_id ) {
	if ( ! isset( $_POST['ae_article_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ae_article_nonce'] ) ), 'ae_article_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array( '_ae_keys', '_ae_quote', '_ae_quote_by' ) as $k ) {
		if ( isset( $_POST[ $k ] ) ) {
			update_post_meta( $post_id, $k, sanitize_textarea_field( wp_unslash( $_POST[ $k ] ) ) );
		}
	}
}
add_action( 'save_post_post', 'ae_article_save' );

/* -------------------------------------------------------------------------
 * Lessons
 * ---------------------------------------------------------------------- */
function ae_lesson_body_metabox() {
	add_meta_box( 'ae_lesson_body', __( 'Æ Lesson Layout', 'aeverything' ), 'ae_lesson_body_html', 'ae_lesson', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'ae_lesson_body_metabox' );

function ae_lesson_body_html( $post ) {
	wp_nonce_field( 'ae_lesson_body', 'ae_lesson_body_nonce' );

	ae_mb_field(
		$post->ID,
		'_ae_principles',
		__( 'Numbered list (the seven principles)', 'aeverything' ),
		__( 'One per line:  Title | Description', 'aeverything' ),
		9
	);
	ae_mb_field(
		$post->ID,
		'_ae_resources',
		__( 'Lesson resources', 'aeverything' ),
		__( 'One per line:  Name | Detail | URL      e.g.  Lesson Slides | PDF • 18 Pages | https://…', 'aeverything' ),
		5
	);
	ae_mb_field( $post->ID, '_ae_quote', __( 'Pull quote', 'aeverything' ), '', 3 );
	ae_mb_field( $post->ID, '_ae_quote_by', __( 'Quote author', 'aeverything' ) );
	ae_mb_field(
		$post->ID,
		'_ae_guide',
		__( 'Study guide file URL', 'aeverything' ),
		__( 'Shown as the download box in the sidebar.', 'aeverything' )
	);
	ae_mb_field( $post->ID, '_ae_guide_meta', __( 'Study guide detail', 'aeverything' ), __( 'e.g. PDF • 42 Pages', 'aeverything' ) );
	ae_mb_field( $post->ID, '_ae_progress', __( 'Overall progress %', 'aeverything' ), __( 'Shown in the progress ring. 0–100.', 'aeverything' ) );
	ae_mb_field( $post->ID, '_ae_progress_note', __( 'Progress caption', 'aeverything' ), __( 'e.g. 7 of 56 Lessons Completed', 'aeverything' ) );
}

function ae_lesson_body_save( $post_id ) {
	if ( ! isset( $_POST['ae_lesson_body_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ae_lesson_body_nonce'] ) ), 'ae_lesson_body' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$keys = array( '_ae_principles', '_ae_resources', '_ae_quote', '_ae_quote_by', '_ae_guide', '_ae_guide_meta', '_ae_progress', '_ae_progress_note' );
	foreach ( $keys as $k ) {
		if ( isset( $_POST[ $k ] ) ) {
			update_post_meta( $post_id, $k, sanitize_textarea_field( wp_unslash( $_POST[ $k ] ) ) );
		}
	}
}
add_action( 'save_post_ae_lesson', 'ae_lesson_body_save' );

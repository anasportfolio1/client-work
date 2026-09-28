<?php
/**
 * Content types — everything the client edits from wp-admin.
 *
 * Æ Magazine uses the built-in Posts type (with categories Mind / Body /
 * Spirit / Art / Culture), so the client writes articles the normal way.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ae_register_post_types() {

	/* ---- Gallery ---------------------------------------------------- */
	register_post_type( 'ae_gallery', array(
		'labels' => array(
			'name'               => __( 'Gallery', 'aeverything' ),
			'singular_name'      => __( 'Artwork', 'aeverything' ),
			'add_new_item'       => __( 'Add Artwork', 'aeverything' ),
			'edit_item'          => __( 'Edit Artwork', 'aeverything' ),
			'menu_name'          => __( 'Æ Gallery', 'aeverything' ),
			'all_items'          => __( 'All Artwork', 'aeverything' ),
			'featured_image'     => __( 'Artwork Image', 'aeverything' ),
			'set_featured_image' => __( 'Set artwork image', 'aeverything' ),
		),
		'public'        => true,
		'has_archive'   => false,
		'menu_icon'     => 'dashicons-format-image',
		'menu_position' => 21,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'rewrite'       => array( 'slug' => 'gallery' ),
		'show_in_rest'  => true,
	) );

	register_taxonomy( 'ae_gallery_type', 'ae_gallery', array(
		'labels'            => array(
			'name'          => __( 'Mediums', 'aeverything' ),
			'singular_name' => __( 'Medium', 'aeverything' ),
			'menu_name'     => __( 'Mediums', 'aeverything' ),
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'medium' ),
	) );

	/* ---- Education lessons ------------------------------------------ */
	register_post_type( 'ae_lesson', array(
		'labels' => array(
			'name'          => __( 'Lessons', 'aeverything' ),
			'singular_name' => __( 'Lesson', 'aeverything' ),
			'add_new_item'  => __( 'Add Lesson', 'aeverything' ),
			'edit_item'     => __( 'Edit Lesson', 'aeverything' ),
			'menu_name'     => __( 'Æ Education', 'aeverything' ),
			'all_items'     => __( 'All Lessons', 'aeverything' ),
		),
		'public'        => true,
		'has_archive'   => true,
		'menu_icon'     => 'dashicons-welcome-learn-more',
		'menu_position' => 22,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		'rewrite'       => array( 'slug' => 'lesson' ),
		'show_in_rest'  => true,
	) );

	register_taxonomy( 'ae_topic', 'ae_lesson', array(
		'labels'            => array(
			'name'          => __( 'Topics', 'aeverything' ),
			'singular_name' => __( 'Topic', 'aeverything' ),
			'menu_name'     => __( 'Topics', 'aeverything' ),
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'topic' ),
	) );

	/* ---- Programs (Mentorship + Fitness) ---------------------------- */
	register_post_type( 'ae_program', array(
		'labels' => array(
			'name'          => __( 'Programs', 'aeverything' ),
			'singular_name' => __( 'Program', 'aeverything' ),
			'add_new_item'  => __( 'Add Program', 'aeverything' ),
			'edit_item'     => __( 'Edit Program', 'aeverything' ),
			'menu_name'     => __( 'Æ Programs', 'aeverything' ),
			'all_items'     => __( 'All Programs', 'aeverything' ),
		),
		'public'        => true,
		'has_archive'   => false,
		'menu_icon'     => 'dashicons-universal-access',
		'menu_position' => 23,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		'rewrite'       => array( 'slug' => 'program' ),
		'show_in_rest'  => true,
	) );

	register_taxonomy( 'ae_program_type', 'ae_program', array(
		'labels'            => array(
			'name'          => __( 'Program Types', 'aeverything' ),
			'singular_name' => __( 'Program Type', 'aeverything' ),
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'program-type' ),
	) );
}
add_action( 'init', 'ae_register_post_types' );

/**
 * Seed the taxonomy terms the design expects, once, on theme activation.
 * The client can rename or add to these freely afterwards.
 */
function ae_seed_terms() {
	if ( get_option( 'ae_terms_seeded' ) ) {
		return;
	}

	$sets = array(
		'ae_gallery_type' => array( 'Photo', 'Digital', 'Painting', '3D' ),
		'ae_topic'        => array(
			'Esoteric Studies', 'Self Mastery',
			'Philosophy & Psychology', 'Health & Wellness',
		),
		'ae_program_type' => array( 'Mentorship', 'Fitness' ),
		'category'        => array( 'Mind', 'Body', 'Spirit', 'Art', 'Culture' ),
	);

	foreach ( $sets as $tax => $terms ) {
		if ( ! taxonomy_exists( $tax ) ) {
			continue;
		}
		foreach ( $terms as $t ) {
			if ( ! term_exists( $t, $tax ) ) {
				wp_insert_term( $t, $tax );
			}
		}
	}

	update_option( 'ae_terms_seeded', 1 );
}
add_action( 'init', 'ae_seed_terms', 20 );

/**
 * Lesson meta — duration, level, lesson number. Simple native metabox so
 * the client doesn't need a paid custom-fields plugin.
 */
function ae_lesson_metabox() {
	add_meta_box(
		'ae_lesson_meta',
		__( 'Lesson Details', 'aeverything' ),
		'ae_lesson_metabox_html',
		'ae_lesson',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'ae_lesson_metabox' );

function ae_lesson_metabox_html( $post ) {
	wp_nonce_field( 'ae_lesson_meta', 'ae_lesson_meta_nonce' );

	$fields = array(
		'_ae_duration' => array( __( 'Duration (e.g. 45 min)', 'aeverything' ), 'text' ),
		'_ae_level'    => array( __( 'Level', 'aeverything' ), 'select', array( 'Beginner', 'Intermediate', 'Advanced' ) ),
		'_ae_number'   => array( __( 'Lesson number', 'aeverything' ), 'number' ),
		'_ae_of_total' => array( __( 'Out of how many', 'aeverything' ), 'number' ),
	);

	foreach ( $fields as $key => $cfg ) {
		$val = get_post_meta( $post->ID, $key, true );
		echo '<p><label style="display:block;font-weight:600;margin-bottom:4px">' . esc_html( $cfg[0] ) . '</label>';
		if ( 'select' === $cfg[1] ) {
			echo '<select name="' . esc_attr( $key ) . '" style="width:100%">';
			foreach ( $cfg[2] as $opt ) {
				echo '<option value="' . esc_attr( $opt ) . '"' . selected( $val, $opt, false ) . '>' . esc_html( $opt ) . '</option>';
			}
			echo '</select>';
		} else {
			echo '<input type="' . esc_attr( $cfg[1] ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" style="width:100%">';
		}
		echo '</p>';
	}
}

function ae_lesson_save( $post_id ) {
	if ( ! isset( $_POST['ae_lesson_meta_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ae_lesson_meta_nonce'] ) ), 'ae_lesson_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( array( '_ae_duration', '_ae_level', '_ae_number', '_ae_of_total' ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
}
add_action( 'save_post_ae_lesson', 'ae_lesson_save' );

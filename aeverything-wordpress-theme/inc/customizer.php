<?php
/**
 * Customizer — Appearance → Customize.
 *
 * Every image slot and every headline on the site is wired to a control
 * here, so the client changes content by clicking, never by editing code.
 *
 * @package Aeverything
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Small helper to cut the boilerplate down. */
function ae_add( $wp, $id, $label, $section, $type = 'text', $default = '', $desc = '' ) {
	$sanitize = 'sanitize_text_field';
	if ( 'textarea' === $type ) {
		$sanitize = 'wp_kses_post';
	} elseif ( 'url' === $type ) {
		$sanitize = 'esc_url_raw';
	} elseif ( 'checkbox' === $type ) {
		$sanitize = fn( $v ) => (bool) $v;
	} elseif ( 'color' === $type ) {
		$sanitize = 'sanitize_hex_color';
	}

	$wp->add_setting( $id, array(
		'default'           => $default,
		'sanitize_callback' => $sanitize,
		'transport'         => 'refresh',
	) );

	if ( 'image' === $type ) {
		$wp->add_setting( $id, array(
			'default'           => $default,
			'sanitize_callback' => 'absint',
		) );
		$wp->add_control( new WP_Customize_Media_Control( $wp, $id, array(
			'label'       => $label,
			'section'     => $section,
			'mime_type'   => 'image',
			'description' => $desc,
		) ) );
		return;
	}

	if ( 'color' === $type ) {
		$wp->add_control( new WP_Customize_Color_Control( $wp, $id, array(
			'label'       => $label,
			'section'     => $section,
			'description' => $desc,
		) ) );
		return;
	}

	$wp->add_control( $id, array(
		'label'       => $label,
		'section'     => $section,
		'type'        => $type,
		'description' => $desc,
	) );
}

function ae_customize_register( $wp ) {

	$wp->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp->get_setting( 'blogdescription' )->transport = 'postMessage';

	/* =====================================================
	 * PANEL: Æverything
	 * ================================================== */
	$wp->add_panel( 'ae_panel', array(
		'title'       => __( 'Æverything Settings', 'aeverything' ),
		'description' => __( 'Everything on the site you can change without code.', 'aeverything' ),
		'priority'    => 10,
	) );

	/* ---- Brand ------------------------------------------------------ */
	$wp->add_section( 'ae_brand', array(
		'title' => __( 'Brand & Colours', 'aeverything' ),
		'panel' => 'ae_panel',
	) );
	ae_add( $wp, 'ae_logo_text', __( 'Logo text', 'aeverything' ), 'ae_brand', 'text', 'æverything',
		__( 'Used when no logo image is uploaded.', 'aeverything' ) );
	ae_add( $wp, 'ae_accent', __( 'Accent colour (Bubble Gum)', 'aeverything' ), 'ae_brand', 'color', '#FF3D6E' );
	ae_add( $wp, 'ae_accent_light', __( 'Accent light', 'aeverything' ), 'ae_brand', 'color', '#FF7FA3' );
	ae_add( $wp, 'ae_currency_label', __( 'Currency label in footer', 'aeverything' ), 'ae_brand', 'text', 'UK £' );

	/* ---- Home hero -------------------------------------------------- */
	$wp->add_section( 'ae_hero', array(
		'title' => __( 'Home — Hero', 'aeverything' ),
		'panel' => 'ae_panel',
	) );
	ae_add( $wp, 'ae_hero_line1', __( 'Headline line 1', 'aeverything' ), 'ae_hero', 'text', 'I am nothing,' );
	ae_add( $wp, 'ae_hero_line2', __( 'Headline line 2', 'aeverything' ), 'ae_hero', 'text', 'æverything.' );
	ae_add( $wp, 'ae_hero_sub', __( 'Sub-line', 'aeverything' ), 'ae_hero', 'text', 'Mind / Body / Spirit / Art' );
	ae_add( $wp, 'ae_hero_btn', __( 'Button text', 'aeverything' ), 'ae_hero', 'text', 'Shop the Collection' );
	ae_add( $wp, 'ae_hero_btn_url', __( 'Button link', 'aeverything' ), 'ae_hero', 'url', '' );

	for ( $i = 1; $i <= 4; $i++ ) {
		ae_add( $wp, "ae_hero_img_$i", sprintf( __( 'Slide %d image', 'aeverything' ), $i ), 'ae_hero', 'image', '' );
		ae_add( $wp, "ae_hero_cap_$i", sprintf( __( 'Slide %d caption (shown only while empty)', 'aeverything' ), $i ),
			'ae_hero', 'text', '' );
	}

	/* ---- Drop countdown --------------------------------------------- */
	$wp->add_section( 'ae_drop', array(
		'title'       => __( 'Next Drop Countdown', 'aeverything' ),
		'panel'       => 'ae_panel',
		'description' => __( 'The live countdown widget in the hero.', 'aeverything' ),
	) );
	ae_add( $wp, 'ae_drop_on', __( 'Show the countdown', 'aeverything' ), 'ae_drop', 'checkbox', true );
	ae_add( $wp, 'ae_drop_name', __( 'Drop name', 'aeverything' ), 'ae_drop', 'text', 'ÆD01 / BUBBLE GUM' );
	ae_add( $wp, 'ae_drop_when', __( 'Date text shown under the name', 'aeverything' ), 'ae_drop', 'text', 'April 4th, 2PM GMT' );
	ae_add( $wp, 'ae_drop_date', __( 'Countdown target', 'aeverything' ), 'ae_drop', 'text', '',
		__( 'Format: 2026-04-04 14:00 — leave blank to run a rolling demo countdown.', 'aeverything' ) );
	ae_add( $wp, 'ae_drop_link', __( 'View Drop link', 'aeverything' ), 'ae_drop', 'url', '' );

	/* ---- Shop by World ---------------------------------------------- */
	$wp->add_section( 'ae_worlds', array(
		'title'       => __( 'Home — Shop by World', 'aeverything' ),
		'panel'       => 'ae_panel',
		'description' => __( 'The five category cards under the hero.', 'aeverything' ),
	) );
	ae_add( $wp, 'ae_worlds_title', __( 'Section heading', 'aeverything' ), 'ae_worlds', 'text', 'Shop by World' );

	$defaults = array( 'Women', 'Men', 'Accessories', 'Jewellery', 'Headwear' );
	foreach ( $defaults as $i => $name ) {
		$n = $i + 1;
		ae_add( $wp, "ae_world_{$n}_title", sprintf( __( 'Card %d — title', 'aeverything' ), $n ), 'ae_worlds', 'text', $name );
		ae_add( $wp, "ae_world_{$n}_url", sprintf( __( 'Card %d — link', 'aeverything' ), $n ), 'ae_worlds', 'url', '' );
		ae_add( $wp, "ae_world_{$n}_img", sprintf( __( 'Card %d — image', 'aeverything' ), $n ), 'ae_worlds', 'image', '' );
	}

	/* ---- Page heroes ------------------------------------------------ */
	$wp->add_section( 'ae_pagehero', array(
		'title'       => __( 'Mentorship & Fitness Heroes', 'aeverything' ),
		'panel'       => 'ae_panel',
	) );
	ae_add( $wp, 'ae_mentor_title', __( 'Mentorship — headline', 'aeverything' ), 'ae_pagehero', 'text', 'Mentorship' );
	ae_add( $wp, 'ae_mentor_sub', __( 'Mentorship — sub-line', 'aeverything' ), 'ae_pagehero', 'textarea', 'Guidance. Accountability. Transformation.' );
	ae_add( $wp, 'ae_mentor_btn', __( 'Mentorship — button', 'aeverything' ), 'ae_pagehero', 'text', 'Apply for Mentorship' );
	ae_add( $wp, 'ae_mentor_img', __( 'Mentorship — hero image', 'aeverything' ), 'ae_pagehero', 'image', '' );

	ae_add( $wp, 'ae_fit_title', __( 'Fitness — headline', 'aeverything' ), 'ae_pagehero', 'text', 'Æverything Fitness' );
	ae_add( $wp, 'ae_fit_sub', __( 'Fitness — sub-line', 'aeverything' ), 'ae_pagehero', 'textarea', 'Train the body. Strengthen the mind. Elevate the spirit.' );
	ae_add( $wp, 'ae_fit_btn', __( 'Fitness — button', 'aeverything' ), 'ae_pagehero', 'text', 'Explore Programs' );
	ae_add( $wp, 'ae_fit_img', __( 'Fitness — hero image', 'aeverything' ), 'ae_pagehero', 'image', '' );

	/* ---- Footer ----------------------------------------------------- */
	$wp->add_section( 'ae_footer', array(
		'title' => __( 'Footer', 'aeverything' ),
		'panel' => 'ae_panel',
	) );
	ae_add( $wp, 'ae_foot_head', __( 'Big heading', 'aeverything' ), 'ae_footer', 'text', 'I am nothing, æverything.' );
	ae_add( $wp, 'ae_foot_text', __( 'Text under it', 'aeverything' ), 'ae_footer', 'textarea',
		'Join the movement. Get exclusive drops, content and inspiration.' );
	ae_add( $wp, 'ae_foot_copy', __( 'Copyright line', 'aeverything' ), 'ae_footer', 'text',
		'© 2025 æverything. All rights reserved.' );

	foreach ( array( 'instagram' => 'Instagram', 'tiktok' => 'TikTok', 'x' => 'X', 'youtube' => 'YouTube', 'pinterest' => 'Pinterest' ) as $k => $label ) {
		ae_add( $wp, "ae_soc_$k", $label . __( ' URL', 'aeverything' ), 'ae_footer', 'url', '' );
	}

	/* ---- AI video brief --------------------------------------------- */
	$wp->add_section( 'ae_aivideo', array(
		'title'       => __( 'AI Video Brief', 'aeverything' ),
		'panel'       => 'ae_panel',
		'description' => __( 'The request form customers fill in so your team can produce their videos.', 'aeverything' ),
	) );
	ae_add( $wp, 'ae_ai_title', __( 'Page headline', 'aeverything' ), 'ae_aivideo', 'text', 'AI Marketing Videos' );
	ae_add( $wp, 'ae_ai_sub', __( 'Intro text', 'aeverything' ), 'ae_aivideo', 'textarea',
		'Send us your product and we will build your marketing videos.' );
	ae_add( $wp, 'ae_ai_email', __( 'Send submissions to', 'aeverything' ), 'ae_aivideo', 'text', get_option( 'admin_email' ),
		__( 'Your team receives an email for every brief.', 'aeverything' ) );
	ae_add( $wp, 'ae_ai_thanks', __( 'Thank-you message', 'aeverything' ), 'ae_aivideo', 'textarea',
		'Got it. Our team will be in touch shortly.' );
	ae_add( $wp, 'ae_ai_img', __( 'Hero image', 'aeverything' ), 'ae_aivideo', 'image', '' );
}
add_action( 'customize_register', 'ae_customize_register' );

/**
 * Push the two brand colours into CSS variables so changing them in the
 * Customizer restyles every button, glow and accent on the site.
 */
function ae_customizer_css() {
	$accent = ae_opt( 'ae_accent', '#FF3D6E' );
	$light  = ae_opt( 'ae_accent_light', '#FF7FA3' );

	if ( '#FF3D6E' === $accent && '#FF7FA3' === $light ) {
		return; // defaults already in the stylesheet
	}

	printf(
		'<style id="ae-customizer">:root{--pink-600:%1$s;--pink-500:%1$s;--pink-400:%2$s;--pink-300:%2$s;}</style>',
		esc_attr( $accent ),
		esc_attr( $light )
	);
}
add_action( 'wp_head', 'ae_customizer_css', 20 );

/** Live-refresh the title/tagline in the Customizer preview. */
function ae_customize_preview_js() {
	wp_add_inline_script(
		'customize-preview',
		"wp.customize('blogname',function(v){v.bind(function(t){document.querySelectorAll('.logo').forEach(function(e){e.textContent=t;});});});"
	);
}
add_action( 'customize_preview_init', 'ae_customize_preview_js' );

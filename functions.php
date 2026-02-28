<?php
/**
 * geller2026 Theme Functions
 *
 * @package geller2026
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/inc/theme-options.php';
require_once __DIR__ . '/inc/icons.php';
require_once __DIR__ . '/inc/tracking.php';

// ─── Theme Setup ─────────────────────────────────────────────────────────────

add_action( 'after_setup_theme', function (): void {
	// Opt into per-block stylesheets (only load CSS when block is on page).
	add_theme_support( 'wp-block-styles' );

	// Load editor-specific global styles.
	add_editor_style( 'assets/css/editor-style.css' );

	// Register navigation menu locations.
	register_nav_menus( [
		'primary'       => __( 'Primary Navigation', 'geller2026' ),
		'header-cta'    => __( 'Header CTA Button', 'geller2026' ),
		'footer-col-1'  => __( 'Footer — Column 1', 'geller2026' ),
		'footer-col-2'  => __( 'Footer — Column 2', 'geller2026' ),
	] );
} );

// ─── Block Registration ───────────────────────────────────────────────────────

add_action( 'init', function (): void {
	// Auto-register all compiled blocks from build/blocks/.
	$block_dirs = glob( __DIR__ . '/build/blocks/*', GLOB_ONLYDIR );
	if ( $block_dirs ) {
		foreach ( $block_dirs as $dir ) {
			register_block_type( $dir );
		}
	}

	// Register custom pattern category.
	register_block_pattern_category(
		'geller2026/sections',
		array( 'label' => __( 'Geller 2026 — Sections', 'geller2026' ) )
	);
} );

// ─── Performance: Remove Emoji ────────────────────────────────────────────────

add_action( 'init', function (): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
} );

// ─── Global Frontend Styles ───────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', function (): void {
	wp_enqueue_style(
		'geller2026-global',
		get_theme_file_uri( 'assets/css/global.css' ),
		array(),
		wp_get_theme()->get( 'Version' )
	);
} );

// ─── Performance: Dequeue Unused Styles ──────────────────────────────────────

add_action( 'wp_enqueue_scripts', function (): void {
	// Remove redundant block library theme styles (FSE themes don't need them).
	wp_dequeue_style( 'wp-block-library-theme' );
}, 100 );

// ─── Performance: Remove Unused Head Tags ────────────────────────────────────

add_action( 'init', function (): void {
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
} );

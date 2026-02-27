<?php
/**
 * Theme Options — Settings API admin page.
 *
 * Add new options:
 *  1. Add a key + default to geller2026_option_defaults().
 *  2. Register a field in geller2026_register_settings().
 *  3. Add a sanitize rule in geller2026_sanitize_options().
 *  4. Use geller2026_option( 'your_key' ) anywhere in PHP.
 *
 * @package geller2026
 */

declare( strict_types=1 );

// ─── Defaults ─────────────────────────────────────────────────────────────────

/**
 * Returns the default value for every option.
 * This is the single source of truth — add new options here first.
 */
function geller2026_option_defaults(): array {
	return [
		'show_page_title'  => true,
		'show_post_title'  => true,
	];
}

// ─── Public helper ────────────────────────────────────────────────────────────

/**
 * Get a single theme option, falling back to its default.
 *
 * Usage: geller2026_option( 'show_page_title' )
 */
function geller2026_option( string $key ): mixed {
	static $options = null;
	if ( null === $options ) {
		$options = get_option( 'geller2026_options', [] );
	}
	$defaults = geller2026_option_defaults();
	return $options[ $key ] ?? $defaults[ $key ] ?? null;
}

// ─── Registration ─────────────────────────────────────────────────────────────

add_action( 'admin_init', 'geller2026_register_settings' );

function geller2026_register_settings(): void {
	register_setting(
		'geller2026_options_group',
		'geller2026_options',
		[
			'sanitize_callback' => 'geller2026_sanitize_options',
			'default'           => geller2026_option_defaults(),
		]
	);

	// ── Section: Content ──────────────────────────────────────────────────────
	add_settings_section(
		'geller2026_section_content',
		'Content',
		null,
		'geller2026_options_page'
	);

	add_settings_field(
		'show_page_title',
		'Show page title',
		'geller2026_field_toggle',
		'geller2026_options_page',
		'geller2026_section_content',
		[
			'key'         => 'show_page_title',
			'description' => 'Display the title at the top of pages (e.g. About, Contact).',
		]
	);

	add_settings_field(
		'show_post_title',
		'Show post title',
		'geller2026_field_toggle',
		'geller2026_options_page',
		'geller2026_section_content',
		[
			'key'         => 'show_post_title',
			'description' => 'Display the title at the top of blog posts.',
		]
	);
}

// ─── Sanitize ─────────────────────────────────────────────────────────────────

function geller2026_sanitize_options( mixed $input ): array {
	$sanitized = [];

	// Checkboxes: present = true, absent = false
	$sanitized['show_page_title'] = ! empty( $input['show_page_title'] );
	$sanitized['show_post_title'] = ! empty( $input['show_post_title'] );

	return $sanitized;
}

// ─── Admin menu ───────────────────────────────────────────────────────────────

add_action( 'admin_menu', 'geller2026_add_options_page' );

function geller2026_add_options_page(): void {
	add_theme_page(
		'Theme Options',
		'Theme Options',
		'manage_options',
		'geller2026_options_page',
		'geller2026_render_options_page'
	);
}

// ─── Page render ──────────────────────────────────────────────────────────────

function geller2026_render_options_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<?php if ( isset( $_GET['settings-updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
			<div class="notice notice-success is-dismissible">
				<p><strong>Settings saved.</strong></p>
			</div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'geller2026_options_group' );
			do_settings_sections( 'geller2026_options_page' );
			submit_button( 'Save settings' );
			?>
		</form>
	</div>
	<?php
}

// ─── Field renderers ──────────────────────────────────────────────────────────

/**
 * Renders a toggle-style checkbox field.
 */
function geller2026_field_toggle( array $args ): void {
	$key   = $args['key'];
	$value = geller2026_option( $key );
	?>
	<label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
		<input
			type="checkbox"
			id="geller2026_<?php echo esc_attr( $key ); ?>"
			name="geller2026_options[<?php echo esc_attr( $key ); ?>]"
			value="1"
			<?php checked( $value, true ); ?>
		>
		<?php if ( ! empty( $args['description'] ) ) : ?>
			<span class="description"><?php echo esc_html( $args['description'] ); ?></span>
		<?php endif; ?>
	</label>
	<?php
}

// ─── Apply options to the frontend ────────────────────────────────────────────

/**
 * Hide core/post-title blocks based on the post type and theme option.
 */
add_filter( 'render_block', 'geller2026_maybe_hide_title', 10, 2 );

function geller2026_maybe_hide_title( string $block_content, array $block ): string {
	if ( 'core/post-title' !== $block['blockName'] ) {
		return $block_content;
	}

	$post_type = get_post_type();

	if ( 'page' === $post_type && ! geller2026_option( 'show_page_title' ) ) {
		return '';
	}

	if ( 'post' === $post_type && ! geller2026_option( 'show_post_title' ) ) {
		return '';
	}

	return $block_content;
}

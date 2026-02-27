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

function geller2026_option_defaults(): array {
	return [
		'logo_id'          => 0,
		'show_page_title'  => true,
		'show_post_title'  => true,
	];
}

// ─── Public helper ────────────────────────────────────────────────────────────

function geller2026_option( string $key ): mixed {
	static $options = null;
	if ( null === $options ) {
		$options = get_option( 'geller2026_options', [] );
	}
	$defaults = geller2026_option_defaults();
	return $options[ $key ] ?? $defaults[ $key ] ?? null;
}

// ─── Enqueue media uploader on our options page ───────────────────────────────

add_action( 'admin_enqueue_scripts', 'geller2026_admin_enqueue' );

function geller2026_admin_enqueue( string $hook ): void {
	if ( 'appearance_page_geller2026_options_page' !== $hook ) {
		return;
	}
	wp_enqueue_media();
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

	// ── Section: Branding ─────────────────────────────────────────────────────
	add_settings_section(
		'geller2026_section_branding',
		'Branding',
		null,
		'geller2026_options_page'
	);

	add_settings_field(
		'logo_id',
		'Logo',
		'geller2026_field_logo',
		'geller2026_options_page',
		'geller2026_section_branding'
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

	$sanitized['logo_id']         = absint( $input['logo_id'] ?? 0 );
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
 * Media upload field for the logo.
 */
function geller2026_field_logo(): void {
	$logo_id  = (int) geller2026_option( 'logo_id' );
	$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
	?>
	<div id="geller2026-logo-wrap" style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">

		<div id="geller2026-logo-preview" style="<?php echo $logo_url ? '' : 'display:none;'; ?>">
			<img src="<?php echo esc_url( $logo_url ); ?>"
				style="max-height:60px;width:auto;display:block;border:1px solid #ddd;border-radius:4px;padding:4px;background:#fff;">
		</div>

		<input type="hidden" id="geller2026_logo_id" name="geller2026_options[logo_id]" value="<?php echo esc_attr( $logo_id ); ?>">

		<button type="button" class="button" id="geller2026-logo-upload">
			<?php echo $logo_id ? 'Change logo' : 'Upload / select logo'; ?>
		</button>

		<button type="button" class="button" id="geller2026-logo-remove"
			style="<?php echo $logo_id ? '' : 'display:none;'; ?>color:#b32d2e;">
			Remove
		</button>

	</div>

	<p class="description" style="margin-top:8px;">
		Shown in the header and footer. Use an SVG or PNG with transparent background. Recommended height: 40px.
	</p>

	<script>
	( function() {
		var frame;
		var uploadBtn  = document.getElementById( 'geller2026-logo-upload' );
		var removeBtn  = document.getElementById( 'geller2026-logo-remove' );
		var input      = document.getElementById( 'geller2026_logo_id' );
		var preview    = document.getElementById( 'geller2026-logo-preview' );
		var previewImg = preview.querySelector( 'img' );

		uploadBtn.addEventListener( 'click', function( e ) {
			e.preventDefault();
			if ( frame ) { frame.open(); return; }

			frame = wp.media( {
				title:    'Select logo',
				button:   { text: 'Use this image' },
				multiple: false,
				library:  { type: 'image' },
			} );

			frame.on( 'select', function() {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				input.value        = attachment.id;
				previewImg.src     = attachment.url;
				preview.style.display  = '';
				removeBtn.style.display = '';
				uploadBtn.textContent  = 'Change logo';
			} );

			frame.open();
		} );

		removeBtn.addEventListener( 'click', function( e ) {
			e.preventDefault();
			input.value             = '0';
			preview.style.display   = 'none';
			removeBtn.style.display = 'none';
			uploadBtn.textContent   = 'Upload / select logo';
		} );
	} )();
	</script>
	<?php
}

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

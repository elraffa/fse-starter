<?php
/**
 * Theme Options — Settings API admin page.
 *
 * Adding a new option:
 *  1. Add key + default to geller2026_option_defaults().
 *  2. Sanitize it in geller2026_sanitize_options().
 *  3. Render the field in geller2026_render_options_page() inside the right tab.
 *  4. Use geller2026_option( 'key' ) anywhere in PHP.
 *
 * @package geller2026
 */

declare( strict_types=1 );

// ─── Defaults ─────────────────────────────────────────────────────────────────

function geller2026_option_defaults(): array {
	return [
		// Branding
		'logo_id'          => 0,
		'footer_logo_id'   => 0,
		'footer_tagline'   => '',
		// Social
		'social_linkedin'  => '',
		'social_whatsapp'  => '',
		'social_instagram' => '',
		'social_youtube'   => '',
		// Contact
		'contact_phone'    => '',
		'contact_address'  => '',
		'contact_hours'    => '',
		// Content
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

// ─── Admin enqueue ────────────────────────────────────────────────────────────

add_action( 'admin_enqueue_scripts', 'geller2026_admin_enqueue' );

function geller2026_admin_enqueue( string $hook ): void {
	if ( 'appearance_page_geller2026_options_page' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style(
		'geller2026-admin-options',
		get_theme_file_uri( 'assets/css/admin-options.css' ),
		[],
		wp_get_theme()->get( 'Version' )
	);
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
}

// ─── Sanitize ─────────────────────────────────────────────────────────────────

function geller2026_sanitize_options( mixed $input ): array {
	$s = [];

	// Branding
	$s['logo_id']          = absint( $input['logo_id'] ?? 0 );
	$s['footer_logo_id']   = absint( $input['footer_logo_id'] ?? 0 );
	$s['footer_tagline']   = sanitize_textarea_field( $input['footer_tagline'] ?? '' );
	// Social
	$s['social_linkedin']  = esc_url_raw( $input['social_linkedin'] ?? '' );
	$s['social_whatsapp']  = esc_url_raw( $input['social_whatsapp'] ?? '' );
	$s['social_instagram'] = esc_url_raw( $input['social_instagram'] ?? '' );
	$s['social_youtube']   = esc_url_raw( $input['social_youtube'] ?? '' );
	// Contact
	$s['contact_phone']    = sanitize_text_field( $input['contact_phone'] ?? '' );
	$s['contact_address']  = sanitize_textarea_field( $input['contact_address'] ?? '' );
	$s['contact_hours']    = sanitize_text_field( $input['contact_hours'] ?? '' );
	// Content
	$s['show_page_title']  = ! empty( $input['show_page_title'] );
	$s['show_post_title']  = ! empty( $input['show_post_title'] );

	return $s;
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

	$tabs = [
		'branding' => [ 'label' => 'Branding',  'icon' => 'dashicons-format-image' ],
		'social'   => [ 'label' => 'Social',    'icon' => 'dashicons-share' ],
		'contact'  => [ 'label' => 'Contact',   'icon' => 'dashicons-phone' ],
		'content'  => [ 'label' => 'Content',   'icon' => 'dashicons-visibility' ],
	];

	// Active tab from URL, default to first.
	$active = sanitize_key( $_GET['_tab'] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification
	if ( ! array_key_exists( $active, $tabs ) ) {
		$active = 'branding';
	}
	?>
	<div class="wrap gop-wrap">

		<h1>Theme Options</h1>

		<?php if ( isset( $_GET['settings-updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
		<div class="gop-saved-notice">
			<span class="dashicons dashicons-yes-alt" style="font-size:16px;width:16px;height:16px;"></span>
			Settings saved.
		</div>
		<?php endif; ?>

		<!-- ── Tab nav ──────────────────────────────────────────────── -->
		<nav class="gop-tabs">
			<?php foreach ( $tabs as $slug => $tab ) : ?>
			<button
				type="button"
				class="gop-tab<?php echo $active === $slug ? ' is-active' : ''; ?>"
				data-tab="<?php echo esc_attr( $slug ); ?>"
			>
				<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span>
				<?php echo esc_html( $tab['label'] ); ?>
			</button>
			<?php endforeach; ?>
		</nav>

		<!-- ── Form (all fields always present — tabs are CSS only) ── -->
		<form method="post" action="options.php">
			<?php settings_fields( 'geller2026_options_group' ); ?>
			<input type="hidden" name="_tab" value="">

			<!-- ── Branding tab ──────────────────────────────────── -->
			<div class="gop-panel<?php echo $active === 'branding' ? ' is-active' : ''; ?>" data-tab="branding">

				<div class="gop-section">
					<p class="gop-section__title">Logos</p>

					<div class="gop-field">
						<div class="gop-field__label">
							Header logo
							<small>Light bg — SVG or PNG, transparent bg</small>
						</div>
						<div>
							<?php geller2026_logo_field( 'logo_id', false ); ?>
						</div>
					</div>

					<div class="gop-field">
						<div class="gop-field__label">
							Footer logo
							<small>Dark bg — use white version</small>
						</div>
						<div>
							<?php geller2026_logo_field( 'footer_logo_id', true ); ?>
						</div>
					</div>
				</div>

				<div class="gop-section">
					<p class="gop-section__title">Footer</p>

					<div class="gop-field">
						<div class="gop-field__label">
							Tagline
							<small>Short description below footer logo</small>
						</div>
						<div>
							<textarea
								name="geller2026_options[footer_tagline]"
								class="gop-textarea"
								rows="3"
							><?php echo esc_textarea( (string) geller2026_option( 'footer_tagline' ) ); ?></textarea>
						</div>
					</div>
				</div>

				<?php geller2026_render_actions(); ?>
			</div>

			<!-- ── Social tab ────────────────────────────────────── -->
			<div class="gop-panel<?php echo $active === 'social' ? ' is-active' : ''; ?>" data-tab="social">
				<div class="gop-section">
					<p class="gop-section__title">Social Profiles</p>

					<?php
					$socials = [
						'social_linkedin'  => [ 'LinkedIn',  'linkedin',  'https://linkedin.com/company/...' ],
						'social_whatsapp'  => [ 'WhatsApp',  'whatsapp',  'https://wa.me/...' ],
						'social_instagram' => [ 'Instagram', 'instagram', 'https://instagram.com/...' ],
						'social_youtube'   => [ 'YouTube',   'youtube',   'https://youtube.com/...' ],
					];
					foreach ( $socials as $key => [ $label, $slug, $placeholder ] ) :
					?>
					<div class="gop-field">
						<div class="gop-field__label">
							<span class="gop-social-badge gop-social-badge--<?php echo esc_attr( $slug ); ?>">
								<?php echo geller2026_icon( $slug, 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
							<?php echo esc_html( $label ); ?>
						</div>
						<div>
							<input
								type="url"
								name="geller2026_options[<?php echo esc_attr( $key ); ?>]"
								class="gop-input"
								value="<?php echo esc_attr( (string) geller2026_option( $key ) ); ?>"
								placeholder="<?php echo esc_attr( $placeholder ); ?>"
							>
						</div>
					</div>
					<?php endforeach; ?>
				</div>

				<?php geller2026_render_actions(); ?>
			</div>

			<!-- ── Contact tab ───────────────────────────────────── -->
			<div class="gop-panel<?php echo $active === 'contact' ? ' is-active' : ''; ?>" data-tab="contact">
				<div class="gop-section">
					<p class="gop-section__title">Contact Info</p>

					<div class="gop-field">
						<div class="gop-field__label">
							Phone
							<small>e.g. +54 11 1111-1111</small>
						</div>
						<div>
							<input
								type="text"
								name="geller2026_options[contact_phone]"
								class="gop-input"
								value="<?php echo esc_attr( (string) geller2026_option( 'contact_phone' ) ); ?>"
								placeholder="+54 11 1111-1111"
							>
						</div>
					</div>

					<div class="gop-field">
						<div class="gop-field__label">
							Address
							<small>Use line breaks for multiple lines</small>
						</div>
						<div>
							<textarea
								name="geller2026_options[contact_address]"
								class="gop-textarea"
								rows="3"
							><?php echo esc_textarea( (string) geller2026_option( 'contact_address' ) ); ?></textarea>
						</div>
					</div>

					<div class="gop-field">
						<div class="gop-field__label">
							Hours
							<small>e.g. Lun–Vie 09hs – 19hs</small>
						</div>
						<div>
							<input
								type="text"
								name="geller2026_options[contact_hours]"
								class="gop-input"
								value="<?php echo esc_attr( (string) geller2026_option( 'contact_hours' ) ); ?>"
								placeholder="Lun–Vie 09hs – 19hs"
							>
						</div>
					</div>
				</div>

				<?php geller2026_render_actions(); ?>
			</div>

			<!-- ── Content tab ───────────────────────────────────── -->
			<div class="gop-panel<?php echo $active === 'content' ? ' is-active' : ''; ?>" data-tab="content">
				<div class="gop-section">
					<p class="gop-section__title">Page & Post Titles</p>

					<div class="gop-field">
						<div class="gop-field__label">Page title</div>
						<label class="gop-toggle">
							<input
								type="checkbox"
								name="geller2026_options[show_page_title]"
								value="1"
								<?php checked( geller2026_option( 'show_page_title' ), true ); ?>
							>
							<span class="gop-toggle__body">
								<span class="gop-toggle__label">Show title at top of pages</span>
								<span class="gop-desc">Applies to About, Contact, and other pages.</span>
							</span>
						</label>
					</div>

					<div class="gop-field">
						<div class="gop-field__label">Post title</div>
						<label class="gop-toggle">
							<input
								type="checkbox"
								name="geller2026_options[show_post_title]"
								value="1"
								<?php checked( geller2026_option( 'show_post_title' ), true ); ?>
							>
							<span class="gop-toggle__body">
								<span class="gop-toggle__label">Show title at top of blog posts</span>
								<span class="gop-desc">Applies to single post pages.</span>
							</span>
						</label>
					</div>
				</div>

				<?php geller2026_render_actions(); ?>
			</div>

		</form>
	</div>

	<script>
	( function () {
		var tabs   = document.querySelectorAll( '.gop-tab' );
		var panels = document.querySelectorAll( '.gop-panel' );
		var input  = document.querySelector( 'input[name="_tab"]' );

		function activate( slug ) {
			tabs.forEach( function( t ) {
				t.classList.toggle( 'is-active', t.dataset.tab === slug );
			} );
			panels.forEach( function( p ) {
				p.classList.toggle( 'is-active', p.dataset.tab === slug );
			} );
			if ( input ) input.value = slug;
			try { localStorage.setItem( 'geller_opts_tab', slug ); } catch(e) {}
		}

		tabs.forEach( function( tab ) {
			tab.addEventListener( 'click', function() {
				activate( tab.dataset.tab );
			} );
		} );

		// Restore active tab: URL param → localStorage → first tab.
		var urlParam = new URLSearchParams( location.search ).get( '_tab' );
		var stored   = '';
		try { stored = localStorage.getItem( 'geller_opts_tab' ) || ''; } catch(e) {}
		var initial  = urlParam || stored || 'branding';
		if ( ! document.querySelector( '[data-tab="' + initial + '"].gop-tab' ) ) {
			initial = 'branding';
		}
		activate( initial );
	} )();
	</script>
	<?php
}

// ─── Helper: action bar ───────────────────────────────────────────────────────

function geller2026_render_actions(): void {
	?>
	<div class="gop-actions">
		<button type="submit" class="button button-primary">Save settings</button>
	</div>
	<?php
}

// ─── Helper: logo upload field ────────────────────────────────────────────────

/**
 * Renders a media-upload field for a logo attachment ID.
 *
 * @param string $key        Option key (e.g. 'logo_id', 'footer_logo_id').
 * @param bool   $dark_preview  Show preview on a dark background.
 */
function geller2026_logo_field( string $key, bool $dark_preview = false ): void {
	$logo_id  = (int) geller2026_option( $key );
	$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
	$uid      = 'geller2026_' . $key;
	?>
	<div class="gop-logo" id="<?php echo esc_attr( $uid ); ?>-wrap">

		<div
			id="<?php echo esc_attr( $uid ); ?>-preview"
			class="gop-logo__preview<?php echo $dark_preview ? ' gop-logo__preview--dark' : ''; ?>"
			<?php echo $logo_url ? '' : 'style="display:none"'; ?>
		>
			<img
				src="<?php echo esc_url( $logo_url ); ?>"
				alt=""
				id="<?php echo esc_attr( $uid ); ?>-img"
			>
		</div>

		<input
			type="hidden"
			id="<?php echo esc_attr( $uid ); ?>"
			name="geller2026_options[<?php echo esc_attr( $key ); ?>]"
			value="<?php echo esc_attr( (string) $logo_id ); ?>"
		>

		<div class="gop-logo__actions">
			<button type="button" class="button" id="<?php echo esc_attr( $uid ); ?>-upload">
				<?php echo $logo_id ? esc_html__( 'Change', 'geller2026' ) : esc_html__( 'Upload / select', 'geller2026' ); ?>
			</button>
			<button
				type="button"
				class="button gop-logo__remove"
				id="<?php echo esc_attr( $uid ); ?>-remove"
				<?php echo $logo_id ? '' : 'style="display:none"'; ?>
			>
				<?php esc_html_e( 'Remove', 'geller2026' ); ?>
			</button>
		</div>

	</div>
	<script>
	( function () {
		var uid       = <?php echo wp_json_encode( $uid ); ?>;
		var frame;
		var input     = document.getElementById( uid );
		var preview   = document.getElementById( uid + '-preview' );
		var img       = document.getElementById( uid + '-img' );
		var uploadBtn = document.getElementById( uid + '-upload' );
		var removeBtn = document.getElementById( uid + '-remove' );

		uploadBtn.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			if ( frame ) { frame.open(); return; }
			frame = wp.media( {
				title: 'Select logo',
				button: { text: 'Use this image' },
				multiple: false,
				library: { type: 'image' },
			} );
			frame.on( 'select', function () {
				var att = frame.state().get( 'selection' ).first().toJSON();
				input.value           = att.id;
				img.src               = att.url;
				preview.style.display = '';
				removeBtn.style.display = '';
				uploadBtn.textContent = 'Change';
			} );
			frame.open();
		} );

		removeBtn.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			input.value             = '0';
			preview.style.display   = 'none';
			removeBtn.style.display = 'none';
			uploadBtn.textContent   = 'Upload / select';
		} );
	} )();
	</script>
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

<?php
/**
 * Frontend UI features:
 *  - Announcement bar (top of page, dismissible)
 *  - WhatsApp floating button
 *  - Open Graph / Twitter Card meta tags
 *
 * @package geller2026
 */

declare( strict_types=1 );

// ─── Announcement bar ──────────────────────────────────────────────────────────
// Renders right after <body> via template-canvas.php → wp_body_open().
// The bar is in normal flow, so the sticky header sits below it correctly.

add_action( 'wp_body_open', 'geller2026_announcement_bar', 5 );

function geller2026_announcement_bar(): void {
	if ( ! geller2026_option( 'announcement_enabled' ) ) {
		return;
	}

	$text = trim( (string) geller2026_option( 'announcement_text' ) );
	if ( ! $text ) {
		return;
	}

	$link = trim( (string) geller2026_option( 'announcement_link' ) );
	$bg   = sanitize_hex_color( (string) geller2026_option( 'announcement_bg' ) ) ?: '#DEB83E';

	// Auto-contrast: dark text on light backgrounds, white on dark.
	$r         = (int) hexdec( substr( $bg, 1, 2 ) );
	$g         = (int) hexdec( substr( $bg, 3, 2 ) );
	$b         = (int) hexdec( substr( $bg, 5, 2 ) );
	$luminance = ( 0.299 * $r + 0.587 * $g + 0.114 * $b ) / 255;
	$color     = $luminance > 0.55 ? '#0a0a0a' : '#ffffff';

	// Hash of content so the bar re-shows when the admin changes the text.
	$hash = substr( md5( $text . $link ), 0, 8 );

	$inner = $link
		? '<a href="' . esc_url( $link ) . '" class="geller-bar__link">' . esc_html( $text ) . '</a>'
		: esc_html( $text );
	?>
	<div
		class="geller-bar"
		id="geller-bar"
		data-hash="<?php echo esc_attr( $hash ); ?>"
		style="--bar-bg:<?php echo esc_attr( $bg ); ?>;--bar-color:<?php echo esc_attr( $color ); ?>"
		role="region"
		aria-label="<?php esc_attr_e( 'Announcement', 'geller2026' ); ?>"
	>
		<span class="geller-bar__text"><?php echo $inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<button class="geller-bar__close" type="button" aria-label="<?php esc_attr_e( 'Dismiss announcement', 'geller2026' ); ?>">
			<svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
				<path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
			</svg>
		</button>
	</div>
	<style>
	.geller-bar {
		display: flex;
		align-items: center;
		justify-content: center;
		background: var(--bar-bg);
		color: var(--bar-color);
		padding: .625rem 3.5rem;
		font-size: .8125rem;
		font-weight: 500;
		line-height: 1.4;
		text-align: center;
		position: relative;
		z-index: 10001;
	}
	.geller-bar.is-hidden { display: none; }
	.geller-bar__link {
		color: inherit;
		text-decoration: underline;
		text-underline-offset: 2px;
	}
	.geller-bar__link:hover { opacity: .75; }
	.geller-bar__close {
		position: absolute;
		right: 1rem;
		top: 50%;
		transform: translateY(-50%);
		display: flex;
		align-items: center;
		justify-content: center;
		width: 28px;
		height: 28px;
		padding: 0;
		border: none;
		background: transparent;
		color: inherit;
		opacity: .65;
		cursor: pointer;
		border-radius: 4px;
		transition: opacity .15s;
	}
	.geller-bar__close:hover { opacity: 1; }
	</style>
	<script>
	( function () {
		var bar  = document.getElementById( 'geller-bar' );
		var hash = bar ? bar.dataset.hash : '';
		var key  = 'geller_bar_' + hash;

		// Hide immediately if already dismissed (avoids flash of bar on reload).
		if ( bar && localStorage.getItem( key ) === '1' ) {
			bar.classList.add( 'is-hidden' );
		}

		var btn = bar ? bar.querySelector( '.geller-bar__close' ) : null;
		if ( btn ) {
			btn.addEventListener( 'click', function () {
				bar.classList.add( 'is-hidden' );
				try { localStorage.setItem( key, '1' ); } catch (e) {}
			} );
		}
	} )();
	</script>
	<?php
}

// ─── WhatsApp floating button ─────────────────────────────────────────────────

add_action( 'wp_footer', 'geller2026_whatsapp_float', 20 );

function geller2026_whatsapp_float(): void {
	if ( ! geller2026_option( 'whatsapp_float' ) ) {
		return;
	}

	$wa_url = trim( (string) geller2026_option( 'social_whatsapp' ) );
	if ( ! $wa_url ) {
		return;
	}

	$message = trim( (string) geller2026_option( 'whatsapp_float_message' ) );
	if ( $message ) {
		$wa_url = add_query_arg( 'text', rawurlencode( $message ), $wa_url );
	}

	$label = trim( (string) geller2026_option( 'whatsapp_float_label' ) );
	?>
	<div class="geller-wa-wrap">
		<?php if ( $label ) : ?>
		<span class="geller-wa-label" aria-hidden="true"><?php echo esc_html( $label ); ?></span>
		<?php endif; ?>
		<a
			href="<?php echo esc_url( $wa_url ); ?>"
			class="geller-wa-float"
			target="_blank"
			rel="noopener noreferrer"
			aria-label="<?php echo $label ? esc_attr( $label ) : esc_attr__( 'Chat on WhatsApp', 'geller2026' ); ?>"
		>
			<?php echo geller2026_icon( 'whatsapp', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
	</div>
	<style>
	.geller-wa-wrap {
		position: fixed;
		bottom: 24px;
		right: 24px;
		z-index: 9999;
		display: flex;
		align-items: center;
		gap: 10px;
	}
	.geller-wa-label {
		background: #fff;
		color: #0a0a0a;
		font-size: .8125rem;
		font-weight: 500;
		line-height: 1.3;
		white-space: nowrap;
		padding: .45rem .9rem;
		border-radius: 20px;
		box-shadow: 0 2px 12px rgba(0,0,0,.15);
		pointer-events: none;
	}
	.geller-wa-float {
		flex-shrink: 0;
		display: flex;
		align-items: center;
		justify-content: center;
		width: 56px;
		height: 56px;
		border-radius: 50%;
		background: #25D366;
		color: #fff;
		text-decoration: none;
		box-shadow: 0 4px 16px rgba(0,0,0,.2), 0 1px 4px rgba(0,0,0,.12);
		transition: transform .2s ease, box-shadow .2s ease;
	}
	.geller-wa-float svg { display: block; }
	.geller-wa-wrap:hover .geller-wa-float,
	.geller-wa-float:focus-visible {
		transform: scale(1.08) translateY(-2px);
		box-shadow: 0 8px 24px rgba(0,0,0,.25);
		outline: none;
	}
	@media (max-width: 767px) {
		.geller-wa-wrap { bottom: 16px; right: 16px; }
		.geller-wa-float { width: 50px; height: 50px; }
		.geller-wa-label { display: none; }
	}
	</style>
	<?php
}

// ─── Open Graph / Twitter Card ────────────────────────────────────────────────
// Only outputs if Yoast SEO or RankMath is NOT active — they handle this.

add_action( 'wp_head', 'geller2026_og_tags', 2 );

function geller2026_og_tags(): void {
	// Bail if a dedicated SEO plugin is handling OG.
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
		return;
	}

	$site_name      = get_bloginfo( 'name' );
	$default_img_id = (int) geller2026_option( 'og_image_id' );

	// ── Image ───────────────────────────────────────────────────────────────
	$img_url = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$img_url = (string) get_the_post_thumbnail_url( null, 'full' );
	} elseif ( $default_img_id ) {
		$img_url = (string) wp_get_attachment_image_url( $default_img_id, 'full' );
	}

	// ── Title, description, URL, type ───────────────────────────────────────
	if ( is_singular() ) {
		$title = get_the_title() . ' — ' . $site_name;
		$desc  = has_excerpt() ? strip_tags( get_the_excerpt() ) : get_bloginfo( 'description' );
		$url   = (string) get_permalink();
		$type  = 'article';
	} elseif ( is_front_page() ) {
		$title = $site_name . ( get_bloginfo( 'description' ) ? ' — ' . get_bloginfo( 'description' ) : '' );
		$desc  = get_bloginfo( 'description' );
		$url   = home_url( '/' );
		$type  = 'website';
	} else {
		$title = wp_title( ' — ', false, 'right' ) . $site_name;
		$desc  = get_bloginfo( 'description' );
		$url   = (string) get_pagenum_link();
		$type  = 'website';
	}

	$desc = wp_trim_words( $desc, 25, '…' );
	?>
	<!-- Open Graph -->
	<meta property="og:type"        content="<?php echo esc_attr( $type ); ?>">
	<meta property="og:site_name"   content="<?php echo esc_attr( $site_name ); ?>">
	<meta property="og:title"       content="<?php echo esc_attr( $title ); ?>">
	<meta property="og:url"         content="<?php echo esc_url( $url ); ?>">
	<?php if ( $desc ) : ?>
	<meta property="og:description" content="<?php echo esc_attr( $desc ); ?>">
	<?php endif; ?>
	<?php if ( $img_url ) : ?>
	<meta property="og:image"       content="<?php echo esc_url( $img_url ); ?>">
	<?php endif; ?>
	<!-- Twitter Card -->
	<meta name="twitter:card"  content="<?php echo $img_url ? 'summary_large_image' : 'summary'; ?>">
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
	<?php if ( $img_url ) : ?>
	<meta name="twitter:image" content="<?php echo esc_url( $img_url ); ?>">
	<?php endif; ?>
	<?php
}

// ─── Organization schema (JSON-LD) ───────────────────────────────────────────

add_action( 'wp_head', 'geller2026_organization_schema', 3 );

function geller2026_organization_schema(): void {
	// Bail if a SEO plugin is active — they output their own schema.
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
		return;
	}

	$name = get_bloginfo( 'name' );
	if ( ! $name ) {
		return;
	}

	$schema = [
		'@context' => 'https://schema.org',
		'@type'    => 'LegalService',
		'name'     => $name,
		'url'      => home_url( '/' ),
	];

	// Logo.
	$logo_id = (int) geller2026_option( 'logo_id' );
	if ( $logo_id ) {
		$logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
		if ( $logo_url ) {
			$schema['logo'] = $logo_url;
		}
	}

	// Contact.
	$phone = trim( (string) geller2026_option( 'contact_phone' ) );
	if ( $phone ) {
		$schema['telephone'] = $phone;
	}

	$address = trim( (string) geller2026_option( 'contact_address' ) );
	if ( $address ) {
		$schema['address'] = [
			'@type'         => 'PostalAddress',
			'streetAddress' => $address,
		];
	}

	$hours = trim( (string) geller2026_option( 'contact_hours' ) );
	if ( $hours ) {
		$schema['openingHours'] = $hours;
	}

	// Area served + description.
	$area = trim( (string) geller2026_option( 'schema_area_served' ) );
	if ( $area ) {
		$schema['areaServed'] = $area;
	}

	$desc = trim( (string) geller2026_option( 'schema_description' ) );
	if ( $desc ) {
		$schema['description'] = $desc;
	}

	// Social profiles → sameAs.
	$same_as = array_filter( [
		(string) geller2026_option( 'social_linkedin' ),
		(string) geller2026_option( 'social_instagram' ),
		(string) geller2026_option( 'social_youtube' ),
	] );
	if ( $same_as ) {
		$schema['sameAs'] = array_values( $same_as );
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

<?php
/**
 * Tracking & analytics — outputs scripts in <head> and right after <body>.
 *
 * Priority 1 on wp_head so tags fire as early as possible.
 * GTM noscript is added via wp_body_open (requires theme to call wp_body_open()).
 *
 * @package geller2026
 */

declare( strict_types=1 );

// ─── Head scripts ─────────────────────────────────────────────────────────────

add_action( 'wp_head', 'geller2026_output_tracking', 1 );

function geller2026_output_tracking(): void {

	$gtm_id       = trim( (string) geller2026_option( 'gtm_id' ) );
	$ga4_id       = trim( (string) geller2026_option( 'ga4_id' ) );
	$pixel_id     = trim( (string) geller2026_option( 'meta_pixel_id' ) );
	$linkedin_id  = trim( (string) geller2026_option( 'linkedin_partner_id' ) );
	$gsc_token    = trim( (string) geller2026_option( 'gsc_verification' ) );
	$head_scripts = trim( (string) geller2026_option( 'head_scripts' ) );

	// ── Google Tag Manager ──────────────────────────────────────────────────
	if ( $gtm_id ) {
		echo '<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':' .
			'new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],' .
			'j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=' .
			'\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);' .
			'})(window,document,\'script\',\'dataLayer\',' . wp_json_encode( $gtm_id ) . ');</script>' . "\n";
	}

	// ── Google Analytics 4 (direct — skip if GTM is set) ───────────────────
	if ( $ga4_id && ! $gtm_id ) {
		echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr( $ga4_id ) . '"></script>' . "\n";
		echo '<script>window.dataLayer=window.dataLayer||[];' .
			'function gtag(){dataLayer.push(arguments);}' .
			'gtag(\'js\',new Date());gtag(\'config\',' . wp_json_encode( $ga4_id ) . ');</script>' . "\n";
	}

	// ── Meta (Facebook) Pixel ───────────────────────────────────────────────
	if ( $pixel_id ) {
		echo '<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){' .
			'n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};' .
			'if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version=\'2.0\';n.queue=[];' .
			't=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];' .
			's.parentNode.insertBefore(t,s)}(window,document,\'script\',' .
			'\'https://connect.facebook.net/en_US/fbevents.js\');' .
			'fbq(\'init\',' . wp_json_encode( $pixel_id ) . ');fbq(\'track\',\'PageView\');</script>' . "\n" .
			'<noscript><img height="1" width="1" style="display:none" ' .
			'src="https://www.facebook.com/tr?id=' . esc_attr( $pixel_id ) . '&amp;ev=PageView&amp;noscript=1"/></noscript>' . "\n";
	}

	// ── LinkedIn Insight Tag ────────────────────────────────────────────────
	if ( $linkedin_id ) {
		echo '<script>_linkedin_partner_id=' . wp_json_encode( $linkedin_id ) . ';' .
			'window._linkedin_data_partner_ids=window._linkedin_data_partner_ids||[];' .
			'window._linkedin_data_partner_ids.push(_linkedin_partner_id);' .
			'(function(l){if(!l){window.lintrk=function(a,b){window.lintrk.q.push([a,b])};' .
			'window.lintrk.q=[]}var s=document.getElementsByTagName("script")[0];' .
			'var b=document.createElement("script");b.type="text/javascript";b.async=true;' .
			'b.src="https://snap.licdn.com/li.lms-analytics/insight.min.js";' .
			's.parentNode.insertBefore(b,s)})(window.lintrk);</script>' . "\n" .
			'<noscript><img height="1" width="1" style="display:none;" alt="" ' .
			'src="https://px.ads.linkedin.com/collect/?pid=' . esc_attr( $linkedin_id ) . '&amp;fmt=gif"/></noscript>' . "\n";
	}

	// ── Google Search Console verification ──────────────────────────────────
	if ( $gsc_token ) {
		echo '<meta name="google-site-verification" content="' . esc_attr( $gsc_token ) . '">' . "\n";
	}

	// ── Custom <head> code ──────────────────────────────────────────────────
	if ( $head_scripts ) {
		echo $head_scripts . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — admin-only field.
	}
}

// ─── GTM noscript (right after <body>) ────────────────────────────────────────

add_action( 'wp_body_open', 'geller2026_output_gtm_noscript', 1 );

function geller2026_output_gtm_noscript(): void {
	$gtm_id = trim( (string) geller2026_option( 'gtm_id' ) );
	if ( ! $gtm_id ) {
		return;
	}
	echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . esc_attr( $gtm_id ) . '" ' .
		'height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n";
}

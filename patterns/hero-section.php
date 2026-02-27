<?php
/**
 * Title: Hero Section
 * Slug: geller2026/hero-section
 * Categories: geller2026/sections
 * Block Types: core/template-part/content
 * Description: Full-width hero with headline, subtext, and CTA button.
 */
?>
<!-- wp:geller2026/hero {"align":"full","style":{"color":{"background":"var(--wp--preset--color--contrast)"},"spacing":{"padding":{"top":"6rem","bottom":"6rem"}}}} -->
<div class="wp-block-geller2026-hero alignfull" style="background-color:var(--wp--preset--color--contrast);padding-top:6rem;padding-bottom:6rem">

	<!-- wp:heading {"level":1,"textAlign":"center","style":{"color":{"text":"var(--wp--preset--color--base)"},"typography":{"fontSize":"var(--wp--preset--font-size--4xl)","lineHeight":"1.1"}}} -->
	<h1 class="wp-block-heading has-text-align-center has-base-color has-text-color">Build something remarkable.</h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","style":{"color":{"text":"var(--wp--preset--color--muted)"},"typography":{"fontSize":"var(--wp--preset--font-size--lg)"},"spacing":{"margin":{"top":"1rem"}}}} -->
	<p class="has-text-align-center has-muted-color has-text-color">A modern WordPress theme built for performance, clarity, and full creative control.</p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"2rem"}}}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"backgroundColor":"accent","textColor":"base"} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-base-color has-accent-background-color has-text-color has-background wp-element-button">Get Started</a></div>
		<!-- /wp:button -->
		<!-- wp:button {"className":"is-style-outline","style":{"color":{"text":"var(--wp--preset--color--base)","border":"var(--wp--preset--color--base)"}}} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" style="color:var(--wp--preset--color--base)">Learn More</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->

</div>
<!-- /wp:geller2026/hero -->

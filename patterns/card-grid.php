<?php
/**
 * Title: Card Grid
 * Slug: geller2026/card-grid
 * Categories: geller2026/sections
 * Description: A grid of recent posts displayed as cards using the Query Loop block.
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem","left":"var(--wp--style--root--padding-left)","right":"var(--wp--style--root--padding-right)"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull">

	<!-- wp:heading {"textAlign":"center","style":{"spacing":{"margin":{"bottom":"2.5rem"}}}} -->
	<h2 class="wp-block-heading has-text-align-center">Latest Posts</h2>
	<!-- /wp:heading -->

	<!-- wp:query {"queryId":0,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false}} -->
	<div class="wp-block-query">

		<!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->

			<!-- wp:group {"style":{"border":{"radius":"0.5rem","width":"1px","color":"rgba(0,0,0,0.08)"},"spacing":{"padding":{"top":"0","right":"0","bottom":"1.25rem","left":"0"}}},"backgroundColor":"base","layout":{"type":"constrained"}} -->
			<div class="wp-block-group has-base-background-color has-background" style="border-color:rgba(0,0,0,0.08);border-width:1px;border-radius:0.5rem;padding-bottom:1.25rem;overflow:hidden">

				<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2","style":{"spacing":{"margin":{"bottom":"1rem"}}}} /-->

				<!-- wp:group {"style":{"spacing":{"padding":{"right":"1.25rem","left":"1.25rem"},"blockGap":"0.5rem"}},"layout":{"type":"constrained"}} -->
				<div class="wp-block-group">
					<!-- wp:post-terms {"term":"category","style":{"typography":{"fontSize":"var(--wp--preset--font-size--sm)","fontWeight":"600","textTransform":"uppercase"},"color":{"text":"var(--wp--preset--color--accent)"}}} /-->
					<!-- wp:post-title {"isLink":true,"level":3,"style":{"typography":{"fontSize":"var(--wp--preset--font-size--xl)","fontWeight":"700"}}} /-->
					<!-- wp:post-excerpt {"numberOfWords":20,"style":{"color":{"text":"var(--wp--preset--color--muted)"},"typography":{"fontSize":"var(--wp--preset--font-size--sm)"}}} /-->
					<!-- wp:post-date {"style":{"color":{"text":"var(--wp--preset--color--muted)"},"typography":{"fontSize":"var(--wp--preset--font-size--sm)"}}} /-->
				</div>
				<!-- /wp:group -->

			</div>
			<!-- /wp:group -->

		<!-- /wp:post-template -->

		<!-- wp:query-no-results -->
		<!-- wp:paragraph {"style":{"color":{"text":"var(--wp--preset--color--muted)"}}} -->
		<p>No posts to display yet.</p>
		<!-- /wp:paragraph -->
		<!-- /wp:query-no-results -->

	</div>
	<!-- /wp:query -->

</div>
<!-- /wp:group -->

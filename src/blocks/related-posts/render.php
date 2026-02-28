<?php
/**
 * Related Posts block — server-side render.
 *
 * Shows up to 3 posts from the same category as the current post.
 * Silently outputs nothing if there are no related posts.
 *
 * @package geller2026
 */

$post_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : get_the_ID();

if ( ! $post_id ) {
	return;
}

$categories = get_the_category( $post_id );
$cat_ids    = array_map( static fn( $c ) => $c->term_id, $categories );

$args = [
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 3,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'post__not_in'   => [ $post_id ],
];

if ( $cat_ids ) {
	$args['category__in'] = $cat_ids;
}

$query = new WP_Query( $args );

if ( ! $query->have_posts() ) {
	return;
}
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'related-posts' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="related-posts__inner">

		<p class="related-posts__label"><?php esc_html_e( 'Artículos relacionados', 'geller2026' ); ?></p>

		<div class="related-posts__grid">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
			<a href="<?php the_permalink(); ?>" class="related-posts__card">

				<?php if ( has_post_thumbnail() ) : ?>
				<div class="related-posts__img-wrap">
					<?php the_post_thumbnail( 'medium_large', [ 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ] ); ?>
				</div>
				<?php endif; ?>

				<div class="related-posts__body">
					<?php $cats = get_the_category(); ?>
					<?php if ( $cats ) : ?>
					<span class="related-posts__cat"><?php echo esc_html( $cats[0]->name ); ?></span>
					<?php endif; ?>
					<h3 class="related-posts__title"><?php the_title(); ?></h3>
					<span class="related-posts__date"><?php echo esc_html( get_the_date() ); ?></span>
				</div>

			</a>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>

	</div>
</section>

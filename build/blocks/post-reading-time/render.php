<?php
/**
 * Post Reading Time block — server-side render.
 *
 * @package geller2026
 */

$post_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : get_the_ID();
$post    = $post_id ? get_post( $post_id ) : null;

if ( ! $post ) {
	return;
}

$content = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
$words   = str_word_count( $content );
$minutes = max( 1, (int) round( $words / 200 ) );

printf(
	'<span %s>%d min de lectura</span>',
	get_block_wrapper_attributes( [ 'class' => 'post-reading-time' ] ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$minutes
);

<?php
/**
 * Site Header block — server-side render.
 *
 * Variables available: $attributes, $content, $block (WP_Block).
 *
 * @package geller2026
 */

$wrapper_attrs = get_block_wrapper_attributes( [
	'class' => 'site-header',
	'id'    => 'site-header',
] );
?>
<div <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<div class="site-header__inner">

		<!-- ── Brand ────────────────────────────────────────── -->
		<div class="site-header__brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__name" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
			<?php endif; ?>
		</div>

		<!-- ── Desktop primary navigation ───────────────────── -->
		<?php
		wp_nav_menu( [
			'theme_location'       => 'primary',
			'container'            => 'nav',
			'container_class'      => 'site-header__nav',
			'container_aria_label' => __( 'Primary navigation', 'geller2026' ),
			'menu_class'           => 'site-header__menu',
			'fallback_cb'          => false,
			'depth'                => 2,
		] );
		?>

		<!-- ── Desktop CTA + mobile toggle ──────────────────── -->
		<div class="site-header__actions">

			<?php
			// CTA button — assign a menu to "Header CTA" in WP Admin → Menus.
			wp_nav_menu( [
				'theme_location' => 'header-cta',
				'container'      => false,
				'menu_class'     => 'site-header__cta-menu',
				'fallback_cb'    => false,
				'depth'          => 1,
			] );
			?>

			<button
				class="site-header__toggle"
				aria-label="<?php esc_attr_e( 'Open navigation', 'geller2026' ); ?>"
				aria-expanded="false"
				aria-controls="site-mobile-menu"
			>
				<span class="site-header__burger" aria-hidden="true">
					<span></span>
					<span></span>
				</span>
			</button>

		</div>

	</div><!-- /.site-header__inner -->

	<!-- ── Mobile drawer (slides in from right) ─────────────── -->
	<div
		class="site-header__mobile"
		id="site-mobile-menu"
		aria-hidden="true"
		aria-label="<?php esc_attr_e( 'Mobile navigation', 'geller2026' ); ?>"
	>
		<div class="site-header__mobile-inner">

			<?php
			wp_nav_menu( [
				'theme_location' => 'primary',
				'container'      => 'nav',
				'container_class' => 'site-header__mobile-nav',
				'menu_class'     => 'site-header__mobile-menu',
				'fallback_cb'    => false,
				'depth'          => 2,
			] );
			?>

			<?php
			wp_nav_menu( [
				'theme_location' => 'header-cta',
				'container'      => false,
				'menu_class'     => 'site-header__mobile-cta',
				'fallback_cb'    => false,
				'depth'          => 1,
			] );
			?>

		</div><!-- /.site-header__mobile-inner -->
	</div><!-- /.site-header__mobile -->

</div><!-- /.site-header -->

<?php
/**
 * Site Footer block — server-side render.
 *
 * @package geller2026
 */

// ── Theme options ──────────────────────────────────────────────────────────────
$logo_id       = (int) geller2026_option( 'logo_id' );
$tagline       = (string) geller2026_option( 'footer_tagline' );
$phone         = (string) geller2026_option( 'contact_phone' );
$address       = (string) geller2026_option( 'contact_address' );
$hours         = (string) geller2026_option( 'contact_hours' );
$ln_url        = (string) geller2026_option( 'social_linkedin' );
$wa_url        = (string) geller2026_option( 'social_whatsapp' );
$ig_url        = (string) geller2026_option( 'social_instagram' );
$yt_url        = (string) geller2026_option( 'social_youtube' );

// ── Footer nav column names ────────────────────────────────────────────────────
$menu_locs  = get_nav_menu_locations();
$col1_name  = '';
$col2_name  = '';

if ( ! empty( $menu_locs['footer-col-1'] ) ) {
	$obj       = wp_get_nav_menu_object( $menu_locs['footer-col-1'] );
	$col1_name = $obj ? $obj->name : '';
}
if ( ! empty( $menu_locs['footer-col-2'] ) ) {
	$obj       = wp_get_nav_menu_object( $menu_locs['footer-col-2'] );
	$col2_name = $obj ? $obj->name : '';
}

$has_col1    = has_nav_menu( 'footer-col-1' );
$has_col2    = has_nav_menu( 'footer-col-2' );
$has_social  = $ln_url || $wa_url || $ig_url || $yt_url;
$has_contact = $phone || $address || $hours;

$wrapper_attrs = get_block_wrapper_attributes( [ 'class' => 'site-footer' ] );
?>
<footer <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<div class="site-footer__inner">

		<!-- ── Brand column ─────────────────────────────────────────────── -->
		<div class="site-footer__brand">

			<?php if ( $logo_id ) : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-footer__logo-link" rel="home">
					<?php echo wp_get_attachment_image( $logo_id, 'full', false, [ 'class' => 'site-footer__logo', 'loading' => 'lazy', 'decoding' => 'async' ] ); ?>
				</a>
			<?php elseif ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-footer__name" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
			<?php endif; ?>

			<?php if ( $tagline ) : ?>
				<p class="site-footer__tagline"><?php echo esc_html( $tagline ); ?></p>
			<?php endif; ?>

			<?php if ( $has_social ) : ?>
			<div class="site-footer__social">

				<?php if ( $ln_url ) : ?>
				<a href="<?php echo esc_url( $ln_url ); ?>"
					class="site-footer__social-link"
					target="_blank"
					rel="noopener noreferrer"
					aria-label="<?php esc_attr_e( 'LinkedIn', 'geller2026' ); ?>">
					<?php echo geller2026_icon( 'linkedin', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<?php endif; ?>

				<?php if ( $wa_url ) : ?>
				<a href="<?php echo esc_url( $wa_url ); ?>"
					class="site-footer__social-link"
					target="_blank"
					rel="noopener noreferrer"
					aria-label="<?php esc_attr_e( 'WhatsApp', 'geller2026' ); ?>">
					<?php echo geller2026_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<?php endif; ?>

				<?php if ( $ig_url ) : ?>
				<a href="<?php echo esc_url( $ig_url ); ?>"
					class="site-footer__social-link"
					target="_blank"
					rel="noopener noreferrer"
					aria-label="<?php esc_attr_e( 'Instagram', 'geller2026' ); ?>">
					<?php echo geller2026_icon( 'instagram', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<?php endif; ?>

				<?php if ( $yt_url ) : ?>
				<a href="<?php echo esc_url( $yt_url ); ?>"
					class="site-footer__social-link"
					target="_blank"
					rel="noopener noreferrer"
					aria-label="<?php esc_attr_e( 'YouTube', 'geller2026' ); ?>">
					<?php echo geller2026_icon( 'youtube', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<?php endif; ?>

			</div>
			<?php endif; ?>

		</div><!-- /.site-footer__brand -->

		<!-- ── Nav column 1 ─────────────────────────────────────────────── -->
		<?php if ( $has_col1 ) : ?>
		<div class="site-footer__nav-col">
			<?php if ( $col1_name ) : ?>
				<p class="site-footer__col-heading"><?php echo esc_html( $col1_name ); ?></p>
			<?php endif; ?>
			<?php
			wp_nav_menu( [
				'theme_location' => 'footer-col-1',
				'container'      => false,
				'menu_class'     => 'site-footer__nav-menu',
				'fallback_cb'    => false,
				'depth'          => 1,
			] );
			?>
		</div>
		<?php endif; ?>

		<!-- ── Nav column 2 ─────────────────────────────────────────────── -->
		<?php if ( $has_col2 ) : ?>
		<div class="site-footer__nav-col">
			<?php if ( $col2_name ) : ?>
				<p class="site-footer__col-heading"><?php echo esc_html( $col2_name ); ?></p>
			<?php endif; ?>
			<?php
			wp_nav_menu( [
				'theme_location' => 'footer-col-2',
				'container'      => false,
				'menu_class'     => 'site-footer__nav-menu',
				'fallback_cb'    => false,
				'depth'          => 1,
			] );
			?>
		</div>
		<?php endif; ?>

		<!-- ── Contact column ───────────────────────────────────────────── -->
		<?php if ( $has_contact ) : ?>
		<div class="site-footer__contact">

			<p class="site-footer__col-heading"><?php esc_html_e( 'Contacto', 'geller2026' ); ?></p>

			<ul class="site-footer__contact-list">

				<?php if ( $phone ) :
					$phone_href = 'tel:' . preg_replace( '/[^\d+]/', '', $phone );
				?>
				<li class="site-footer__contact-item">
					<?php echo geller2026_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<a href="<?php echo esc_attr( $phone_href ); ?>"><?php echo esc_html( $phone ); ?></a>
				</li>
				<?php endif; ?>

				<?php if ( $address ) : ?>
				<li class="site-footer__contact-item">
					<?php echo geller2026_icon( 'map-pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php echo nl2br( esc_html( $address ) ); ?></span>
				</li>
				<?php endif; ?>

				<?php if ( $hours ) : ?>
				<li class="site-footer__contact-item">
					<?php echo geller2026_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php echo esc_html( $hours ); ?></span>
				</li>
				<?php endif; ?>

			</ul>
		</div>
		<?php endif; ?>

	</div><!-- /.site-footer__inner -->

	<!-- ── Bottom bar ───────────────────────────────────────────────────── -->
	<div class="site-footer__bottom">
		<p>&copy; <?php echo esc_html( (string) gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Todos los derechos reservados.', 'geller2026' ); ?></p>
	</div>

</footer>

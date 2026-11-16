<?php
/**
 * Menu mobile en panneau latéral.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_location = has_nav_menu( 'mobile' ) ? 'mobile' : 'primary';
?>
<div id="mrz-mobile-menu" class="mrz-offcanvas" hidden>
	<div class="mrz-offcanvas__overlay" data-mrz-close></div>
	<div class="mrz-offcanvas__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'mrz-theme-exp' ); ?>">
		<button type="button" class="mrz-offcanvas__close" data-mrz-close>
			<span class="screen-reader-text"><?php esc_html_e( 'Close menu', 'mrz-theme-exp' ); ?></span>
			<svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
		</button>
		<?php if ( has_nav_menu( $mrz_location ) ) : ?>
			<nav aria-label="<?php esc_attr_e( 'Mobile menu', 'mrz-theme-exp' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => $mrz_location,
						'container'      => false,
						'menu_class'     => 'mrz-mobile-nav',
						'depth'          => 3,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		<?php endif; ?>
		<?php if ( has_nav_menu( 'secondary' ) ) : ?>
			<nav aria-label="<?php esc_attr_e( 'Secondary menu', 'mrz-theme-exp' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'secondary',
						'container'      => false,
						'menu_class'     => 'mrz-mobile-nav mrz-mobile-nav--secondary',
						'depth'          => 2,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		<?php endif; ?>
		<?php mrz_theme_language_switcher( 'mrz-lang--mobile' ); ?>
	</div>
</div>

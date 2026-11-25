<?php
/**
 * Menu mobile : un seul balisage pour les quatre styles du Customizer
 * (panneau à droite, à gauche, plein écran, sous le header).
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_location = has_nav_menu( 'mobile' ) ? 'mobile' : 'primary';
$mrz_style    = mrz_theme_option( 'mobile_menu_style' );
$mrz_style    = array_key_exists( $mrz_style, mrz_theme_mobile_menu_styles() ) ? $mrz_style : 'right';
?>
<div id="mrz-mobile-menu" class="mrz-offcanvas mrz-offcanvas--<?php echo esc_attr( $mrz_style ); ?>" data-mrz-style="<?php echo esc_attr( $mrz_style ); ?>" hidden>
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
		<?php
		// Les sections du menu ne comptent pas dans le rang des sections de la page (image prioritaire).
		$GLOBALS['mrz_theme_in_menu'] = true;
		do_action( 'mrz_mobile_menu_end' );
		$GLOBALS['mrz_theme_in_menu'] = false;
		?>
	</div>
</div>

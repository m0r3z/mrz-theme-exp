<?php
/**
 * Menus de l'admin : un menu MRZ Theme qui rassemble les réglages du thème.
 *
 * Les réglages restent dans le Customizer natif (aperçu en direct) : le menu
 * ouvre directement son panneau MRZ Theme.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lien du menu : le Customizer, ouvert sur le panneau MRZ Theme.
 * Sert aussi de menu parent aux sections globales.
 *
 * @return string
 */
function mrz_theme_admin_menu_slug() {
	return 'customize.php?autofocus%5Bpanel%5D=mrz_theme';
}

/**
 * Icône du menu MRZ Theme : le SVG du thème (assets/icons/menu.svg), en data URI.
 * WordPress la repeint aux couleurs de l'admin (gris, blanc au survol), comme ses
 * propres icônes : le SVG n'a donc que des formes pleines, sans blanc dessiné.
 *
 * @return string
 */
function mrz_theme_admin_menu_icon() {
	$file = MRZ_THEME_DIR . '/assets/icons/menu.svg';
	$svg  = is_readable( $file ) ? file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- fichier local du thème.

	return $svg ? 'data:image/svg+xml;base64,' . base64_encode( $svg ) : 'dashicons-admin-appearance'; // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- format attendu par add_menu_page().
}

/**
 * Déclare le menu MRZ Theme et son premier sous-menu.
 */
function mrz_theme_admin_menu() {
	add_menu_page(
		__( 'MRZ Theme', 'mrz-theme-exp' ),
		__( 'MRZ Theme', 'mrz-theme-exp' ),
		'edit_theme_options',
		mrz_theme_admin_menu_slug(),
		'',
		mrz_theme_admin_menu_icon(),
		59
	);

	add_submenu_page(
		mrz_theme_admin_menu_slug(),
		__( 'Theme options', 'mrz-theme-exp' ),
		__( 'Theme options', 'mrz-theme-exp' ),
		'edit_theme_options',
		mrz_theme_admin_menu_slug()
	);
}
add_action( 'admin_menu', 'mrz_theme_admin_menu', 9 );

/**
 * Retire d'Apparence les écrans de Gutenberg, inutiles avec l'éditeur classique :
 * Compositions et Polices.
 */
function mrz_theme_remove_block_screens() {
	global $submenu;

	foreach ( $submenu['themes.php'] ?? array() as $item ) {
		$slug = $item[2];

		if ( str_starts_with( $slug, 'site-editor.php' ) || str_starts_with( $slug, 'font-library.php' ) || 'edit.php?post_type=wp_block' === $slug ) {
			remove_submenu_page( 'themes.php', $slug );
		}
	}
}
add_action( 'admin_menu', 'mrz_theme_remove_block_screens', 999 );

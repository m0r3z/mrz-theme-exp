<?php
/**
 * MRZ Theme Experience : chargeur.
 *
 * Ce fichier ne contient aucune logique : chaque fonctionnalité vit dans inc/.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

define( 'MRZ_THEME_VERSION', '0.1.0' );
define( 'MRZ_THEME_DIR', get_template_directory() );
define( 'MRZ_THEME_URI', get_template_directory_uri() );

$mrz_theme_includes = array(
	'options',
	'setup',
	'assets',
	'customizer',
	'template-tags',
	'builder',
	'global-sections',
	'woocommerce',
	'updater',
);

foreach ( $mrz_theme_includes as $mrz_theme_file ) {
	require_once MRZ_THEME_DIR . '/inc/' . $mrz_theme_file . '.php';
}

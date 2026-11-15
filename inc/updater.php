<?php
/**
 * Mises à jour du thème depuis les releases GitHub (Plugin Update Checker).
 *
 * Chaque site voit « Mise à jour disponible » dans son admin et dans ManageWP
 * dès qu'une release GitHub porte une version supérieure à celle de style.css.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

use YahnisElsts\PluginUpdateChecker\v6\PucFactory;

/**
 * Branche le contrôle des mises à jour.
 *
 * Désactivé sur une copie de travail Git (site de développement) : une mise à
 * jour remplacerait le dossier du thème, dépôt Git compris.
 */
function mrz_theme_updater() {
	if ( is_dir( MRZ_THEME_DIR . '/.git' ) ) {
		return;
	}

	require_once MRZ_THEME_DIR . '/lib/plugin-update-checker/plugin-update-checker.php';

	$update_checker = PucFactory::buildUpdateChecker(
		'https://github.com/m0r3z/mrz-theme-exp/',
		MRZ_THEME_DIR . '/functions.php',
		'mrz-theme-exp'
	);

	// Branche « main » : PUC lit d'abord la dernière release, puis le tag le plus haut.
	$update_checker->setBranch( 'main' );
}
add_action( 'after_setup_theme', 'mrz_theme_updater' );

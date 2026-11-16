<?php
/**
 * Valeurs par défaut des options du Customizer et accès centralisé.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Valeurs par défaut de toutes les options du thème.
 *
 * @return array
 */
function mrz_theme_option_defaults() {
	return array(
		'header_layout'       => 'classic',
		'header_sticky'       => true,
		'header_transparent'  => false,
		'header_search'       => false,
		'header_account'      => false,
		'logo_transparent'    => 0,
		'logo_height'         => 48,
		'logo_height_mobile'  => 40,
		'mobile_sticky'       => true,
		'mobile_breakpoint'   => 1000,
		'transition_enabled'  => false,
		'transition_effect'   => 'fade',
		'transition_duration' => 300,
		'container_width'     => 1280,
		'page_404'            => 0,
	);
}

/**
 * Lit une option du thème (theme_mod préfixé mrz_).
 *
 * @param string $name Nom de l'option sans préfixe.
 * @return mixed
 */
function mrz_theme_option( $name ) {
	$defaults = mrz_theme_option_defaults();
	$default  = $defaults[ $name ] ?? null;

	return get_theme_mod( 'mrz_' . $name, $default );
}

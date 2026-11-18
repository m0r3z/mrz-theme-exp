<?php
/**
 * Petits scripts des sections (sous-menu d'ancres, bandeau défilant, compteur) :
 * déclarés ici, chargés seulement par la section qui s'en sert (wp_enqueue_script).
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Déclare les scripts et leurs textes traduits.
 */
function mrz_theme_register_module_scripts() {
	$modules = array(
		'mrz-anchors' => array( 'assets/js/anchors.js', '', array() ),
		'mrz-marquee' => array(
			'assets/js/marquee.js',
			'mrzMarquee',
			array(
				'pause' => __( 'Pause the scrolling band', 'mrz-theme-exp' ),
				'play'  => __( 'Play the scrolling band', 'mrz-theme-exp' ),
			),
		),
		'mrz-counter' => array( 'assets/js/counter.js', '', array() ),
		'mrz-motion'  => array(
			'assets/js/motion.js',
			'mrzMotion',
			array( 'once' => (bool) mrz_theme_option( 'motion_once' ) ),
		),
	);

	foreach ( $modules as $handle => $module ) {
		wp_register_script(
			$handle,
			MRZ_THEME_URI . '/' . $module[0],
			array(),
			mrz_theme_asset_version( $module[0] ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
		if ( $module[1] ) {
			wp_add_inline_script( $handle, 'window.' . $module[1] . ' = ' . wp_json_encode( $module[2] ) . ';', 'before' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'mrz_theme_register_module_scripts' );

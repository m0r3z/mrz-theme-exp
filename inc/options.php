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
		'color_primary'             => '#2a5bd7',
		'color_secondary'           => '#1d1d1f',
		'color_tertiary'            => '#e0a526',
		'color_quaternary'          => '#8a5cc2',
		'color_dark'                => '#20201f',
		'color_light'               => '#f4f4f2',
		'color_border'              => '#dededd',
		'color_background'          => '',
		'color_body'                => '',
		'color_primary_contrast'    => 'auto',
		'color_secondary_contrast'  => 'auto',
		'color_tertiary_contrast'   => 'auto',
		'color_quaternary_contrast' => 'auto',
		'header_layout'             => 'classic',
		'header_sticky'             => true,
		'header_transparent'        => false,
		'header_search'             => false,
		'header_account'            => false,
		'lang_switcher'             => 'codes',
		'logo_transparent'          => 0,
		'logo_height'               => 48,
		'logo_height_mobile'        => 40,
		'mobile_sticky'             => true,
		'mobile_breakpoint'         => 1000,
		'mobile_menu_style'         => 'right',
		'transition_enabled'        => false,
		'transition_effect'         => 'fade',
		'transition_duration'       => 300,
		'transition_color'          => 'background',
		'transition_direction'      => 'up',
		'menu_button_desktop'       => 'none',
		'menu_button_label'         => false,
		'container_width'           => 1280,
		'page_404'                  => 0,
		'highlight_animated'        => false,
		'motion_once'               => false,
		'shop_cart_behavior'        => 'drawer',
		'shop_ajax_single'          => true,
		'shop_product_details'      => 'summary',
		'shop_columns'              => 4,
		'shop_columns_mobile'       => 2,
		'shop_per_page'             => 12,
		'shop_related_count'        => 4,
		'shop_sorting'              => true,
		'shop_result_count'         => true,
		'shop_sale_badge'           => 'text',
		'shop_quantity_buttons'     => true,
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

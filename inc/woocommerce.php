<?php
/**
 * Intégration WooCommerce : uniquement hooks et CSS, aucune surcharge de template.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * Met à jour la pastille du panier après un ajout en AJAX.
 *
 * @param array $fragments Fragments.
 * @return array
 */
function mrz_theme_cart_fragments( $fragments ) {
	$fragments['.mrz-cart__count'] = mrz_theme_cart_count();

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'mrz_theme_cart_fragments' );

/**
 * Produits apparentés : 4 colonnes, comme la boutique.
 *
 * @param array $args Arguments.
 * @return array
 */
function mrz_theme_related_products_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'mrz_theme_related_products_args' );

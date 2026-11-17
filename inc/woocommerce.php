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
 * Produits apparentés : une ligne de la boutique (même nombre de colonnes).
 *
 * @param array $args Arguments.
 * @return array
 */
function mrz_theme_related_products_args( $args ) {
	$columns = mrz_theme_shop_columns();

	$args['posts_per_page'] = $columns;
	$args['columns']        = $columns;

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'mrz_theme_related_products_args' );

/**
 * Page Boutique : pas de titre imposé par WooCommerce (comme les autres pages du thème).
 * Les catégories de produits gardent le leur.
 *
 * @param bool $show Afficher le titre.
 * @return bool
 */
function mrz_theme_shop_page_title( $show ) {
	return function_exists( 'is_shop' ) && is_shop() ? false : $show;
}
add_filter( 'woocommerce_show_page_title', 'mrz_theme_shop_page_title' );

/**
 * Pages Panier et Commande créées en shortcode, pas en blocs, à l'installation de
 * WooCommerce : l'éditeur classique du thème, ses boutons et le mini-panier visent
 * le panier et la commande classiques, et les blocs chargent React sur ces pages.
 *
 * @param array $pages Pages créées par WooCommerce.
 * @return array
 */
function mrz_theme_woocommerce_create_pages( $pages ) {
	if ( isset( $pages['cart'] ) ) {
		$pages['cart']['content'] = '[woocommerce_cart]';
	}
	if ( isset( $pages['checkout'] ) ) {
		$pages['checkout']['content'] = '[woocommerce_checkout]';
	}

	return $pages;
}
add_filter( 'woocommerce_create_pages', 'mrz_theme_woocommerce_create_pages' );

/**
 * Réglages Customizer > MRZ Theme > Boutique (seulement quand WooCommerce est actif).
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mrz_theme_shop_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'mrz_shop',
		array(
			'title' => __( 'Shop', 'mrz-theme-exp' ),
			'panel' => 'mrz_theme',
		)
	);

	mrz_theme_add_setting( $wp_customize, 'shop_cart_behavior', 'mrz_theme_sanitize_choice' );
	$wp_customize->add_control(
		'mrz_shop_cart_behavior',
		array(
			'label'   => __( 'Cart icon', 'mrz-theme-exp' ),
			'section' => 'mrz_shop',
			'type'    => 'radio',
			'choices' => array(
				'drawer' => __( 'Opens a cart panel on the right', 'mrz-theme-exp' ),
				'page'   => __( 'Goes to the cart page', 'mrz-theme-exp' ),
			),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'shop_ajax_single', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_shop_ajax_single',
		array(
			'label'       => __( 'Add to cart without reloading the product page', 'mrz-theme-exp' ),
			'description' => __( 'The product lists follow the WooCommerce setting (Settings > Products).', 'mrz-theme-exp' ),
			'section'     => 'mrz_shop',
			'type'        => 'checkbox',
		)
	);

	mrz_theme_add_setting( $wp_customize, 'shop_product_details', 'mrz_theme_sanitize_choice' );
	$wp_customize->add_control(
		'mrz_shop_product_details',
		array(
			'label'       => __( 'Description, additional information and reviews', 'mrz-theme-exp' ),
			'description' => __( 'Shown as accordions instead of tabs.', 'mrz-theme-exp' ),
			'section'     => 'mrz_shop',
			'type'        => 'radio',
			'choices'     => array(
				'summary' => __( 'Next to the images, under the add to cart button', 'mrz-theme-exp' ),
				'below'   => __( 'Under the images, full width', 'mrz-theme-exp' ),
			),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'shop_columns', 'absint' );
	$wp_customize->add_control(
		'mrz_shop_columns',
		array(
			'label'       => __( 'Products per row', 'mrz-theme-exp' ),
			'description' => __( 'Shop, categories and related products. The [products] shortcode keeps its own columns.', 'mrz-theme-exp' ),
			'section'     => 'mrz_shop',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 2,
				'max' => 5,
			),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'shop_columns_mobile', 'mrz_theme_sanitize_choice' );
	$wp_customize->add_control(
		'mrz_shop_columns_mobile',
		array(
			'label'   => __( 'Products per row on mobile', 'mrz-theme-exp' ),
			'section' => 'mrz_shop',
			'type'    => 'radio',
			'choices' => array(
				'1' => '1',
				'2' => '2',
			),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'shop_per_page', 'absint' );
	$wp_customize->add_control(
		'mrz_shop_per_page',
		array(
			'label'       => __( 'Products per page', 'mrz-theme-exp' ),
			'section'     => 'mrz_shop',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 1,
				'max' => 96,
			),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'shop_sorting', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_shop_sorting',
		array(
			'label'   => __( 'Sorting menu', 'mrz-theme-exp' ),
			'section' => 'mrz_shop',
			'type'    => 'checkbox',
		)
	);

	mrz_theme_add_setting( $wp_customize, 'shop_result_count', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_shop_result_count',
		array(
			'label'   => __( 'Number of results', 'mrz-theme-exp' ),
			'section' => 'mrz_shop',
			'type'    => 'checkbox',
		)
	);

	mrz_theme_add_setting( $wp_customize, 'shop_sale_badge', 'mrz_theme_sanitize_choice' );
	$wp_customize->add_control(
		'mrz_shop_sale_badge',
		array(
			'label'   => __( 'Sale badge', 'mrz-theme-exp' ),
			'section' => 'mrz_shop',
			'type'    => 'radio',
			'choices' => array(
				'text'    => __( 'Text ("Sale!")', 'mrz-theme-exp' ),
				'percent' => __( 'Discount ("-20%")', 'mrz-theme-exp' ),
				'none'    => __( 'None', 'mrz-theme-exp' ),
			),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'shop_quantity_buttons', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_shop_quantity_buttons',
		array(
			'label'   => __( 'Plus and minus buttons around the quantity', 'mrz-theme-exp' ),
			'section' => 'mrz_shop',
			'type'    => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'mrz_theme_shop_customize_register' );

/**
 * Nombre de produits par ligne (boutique, catégories, produits apparentés).
 *
 * @return int
 */
function mrz_theme_shop_columns() {
	return min( 5, max( 2, absint( mrz_theme_option( 'shop_columns' ) ) ) );
}
add_filter( 'loop_shop_columns', 'mrz_theme_shop_columns' );

/**
 * Nombre de produits par page.
 *
 * @return int
 */
function mrz_theme_shop_per_page() {
	return max( 1, absint( mrz_theme_option( 'shop_per_page' ) ) );
}
add_filter( 'loop_shop_per_page', 'mrz_theme_shop_per_page', 20 );

/**
 * Tri et nombre de résultats au-dessus des produits, selon les réglages.
 * Sur « wp » : les réglages sont lus après le chargement du Customizer (aperçu).
 */
function mrz_theme_shop_loop_tools() {
	if ( ! mrz_theme_option( 'shop_result_count' ) ) {
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	}
	if ( ! mrz_theme_option( 'shop_sorting' ) ) {
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	}
}
add_action( 'wp', 'mrz_theme_shop_loop_tools' );

/**
 * Badge promo : texte de WooCommerce, réduction en pourcentage, ou rien.
 * Produit variable : la plus forte réduction de ses variations.
 *
 * @param string     $html    Badge.
 * @param WP_Post    $post    Article.
 * @param WC_Product $product Produit.
 * @return string
 */
function mrz_theme_sale_flash( $html, $post, $product ) {
	$badge = mrz_theme_option( 'shop_sale_badge' );

	if ( 'none' === $badge ) {
		return '';
	}
	if ( 'percent' !== $badge || ! $product instanceof WC_Product ) {
		return $html;
	}

	$prices = array();
	if ( $product->is_type( 'variable' ) ) {
		$variation_prices = $product->get_variation_prices();
		foreach ( $variation_prices['regular_price'] as $id => $regular ) {
			$prices[] = array( (float) $regular, (float) $variation_prices['sale_price'][ $id ] );
		}
	} else {
		$prices[] = array( (float) $product->get_regular_price(), (float) $product->get_sale_price() );
	}

	$percent = 0;
	foreach ( $prices as $price ) {
		if ( $price[0] > 0 && $price[1] < $price[0] ) {
			$percent = max( $percent, (int) round( ( 1 - $price[1] / $price[0] ) * 100 ) );
		}
	}

	if ( ! $percent ) {
		return $html;
	}

	/* translators: %d: réduction en pourcentage. */
	return '<span class="onsale">' . esc_html( sprintf( __( '-%d%%', 'mrz-theme-exp' ), $percent ) ) . '</span>';
}
add_filter( 'woocommerce_sale_flash', 'mrz_theme_sale_flash', 10, 3 );

/**
 * Variables de la boutique réglées dans le Customizer, posées sur le CSS WooCommerce.
 *
 * @return string
 */
function mrz_theme_shop_inline_css() {
	return ':root{--mrz-shop-columns-mobile:' . ( '1' === (string) mrz_theme_option( 'shop_columns_mobile' ) ? 1 : 2 ) . '}';
}

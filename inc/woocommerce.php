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
 * Page Boutique avec des sections dans le constructeur (première page de la liste seulement).
 *
 * @return bool
 */
function mrz_theme_shop_has_sections() {
	return function_exists( 'is_shop' ) && is_shop() && ! is_paged() && ! is_search()
		&& wc_get_page_id( 'shop' ) > 0 && mrz_theme_has_builder( wc_get_page_id( 'shop' ) );
}

/**
 * Page Boutique : quand ses sections sont affichées, elles portent le h1 (comme toute page
 * du thème) et le titre de WooCommerce disparaît. Sinon il reste : la page garde son h1.
 *
 * @param bool $show Afficher le titre.
 * @return bool
 */
function mrz_theme_shop_page_title( $show ) {
	return mrz_theme_shop_has_sections() ? false : $show;
}
add_filter( 'woocommerce_show_page_title', 'mrz_theme_shop_page_title' );

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

	mrz_theme_add_setting( $wp_customize, 'shop_variation_swatches', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_shop_variation_swatches',
		array(
			'label'       => __( 'Variation buttons instead of drop-down menus', 'mrz-theme-exp' ),
			'description' => __( 'Sizes, colors and other options shown as buttons to pick on the product page.', 'mrz-theme-exp' ),
			'section'     => 'mrz_shop',
			'type'        => 'checkbox',
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

/**
 * Fiche produit : description, informations complémentaires, avis (et tout onglet
 * ajouté par un plugin) en accordéons, sous le bouton d'ajout ou sous la fiche.
 */
function mrz_theme_product_details_setup() {
	if ( ! is_product() ) {
		return;
	}

	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

	if ( 'below' === mrz_theme_option( 'shop_product_details' ) ) {
		add_action( 'woocommerce_after_single_product_summary', 'mrz_theme_product_details', 10 );
	} else {
		// Après le prix, le bouton d'ajout, les catégories et étiquettes (40) et le partage (50).
		add_action( 'woocommerce_single_product_summary', 'mrz_theme_product_details', 60 );
	}
}
add_action( 'wp', 'mrz_theme_product_details_setup' );

/**
 * Accordéons de la fiche produit : même balisage et mêmes variables
 * (--mrz-accordion-*) que la section Accordéon. <details> natif, sans JS.
 */
function mrz_theme_product_details() {
	$tabs = apply_filters( 'woocommerce_product_tabs', array() );
	if ( ! $tabs ) {
		return;
	}
	?>
	<div class="mrz-accordion mrz-product-details">
		<?php foreach ( $tabs as $key => $tab ) : ?>
			<?php
			if ( empty( $tab['callback'] ) ) {
				continue;
			}
			?>
			<details class="mrz-accordion__item mrz-product-details__item" id="tab-<?php echo esc_attr( $key ); ?>">
				<summary class="mrz-accordion__question"><?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ); ?></summary>
				<div class="mrz-accordion__answer<?php echo 'description' === $key ? ' mrz-prose' : ''; ?>">
					<?php call_user_func( $tab['callback'], $key, $tab ); ?>
				</div>
			</details>
		<?php endforeach; ?>
	</div>
	<?php
}

/*
 * Titres répétés dans les accordéons (« Description », « Informations complémentaires ») :
 * le libellé de l'accordéon suffit.
 */
add_filter( 'woocommerce_product_description_heading', '__return_empty_string' );
add_filter( 'woocommerce_product_additional_information_heading', '__return_empty_string' );

/**
 * Panneau du panier actif sur cette page : réglage « panneau », sauf sur les pages
 * Panier et Commande, où l'icône mène simplement au panier.
 *
 * @return bool
 */
function mrz_theme_cart_drawer_enabled() {
	return 'drawer' === mrz_theme_option( 'shop_cart_behavior' ) && ! is_cart() && ! is_checkout();
}

/**
 * Classe du body pour le CSS du mode panneau (lien « Voir le panier » des listes masqué).
 *
 * @param array $classes Classes.
 * @return array
 */
function mrz_theme_shop_body_classes( $classes ) {
	if ( mrz_theme_cart_drawer_enabled() ) {
		$classes[] = 'mrz-cart-drawer';
	}

	return $classes;
}
add_filter( 'body_class', 'mrz_theme_shop_body_classes' );

/**
 * Le module de la boutique (assets/js/shop.js) sert-il sur cette page ?
 * Panneau du panier (toutes les pages), fiche produit (ajout sans rechargement,
 * boutons quantité), panier en shortcode (boutons quantité ; le bloc Panier a les siens).
 *
 * @return bool
 */
function mrz_theme_shop_script_needed() {
	return mrz_theme_cart_drawer_enabled()
		|| ( is_product() && ( mrz_theme_option( 'shop_ajax_single' ) || mrz_theme_option( 'shop_quantity_buttons' ) || mrz_theme_option( 'shop_variation_swatches' ) ) )
		|| ( is_cart() && mrz_theme_option( 'shop_quantity_buttons' ) && ! has_block( 'woocommerce/cart', wc_get_page_id( 'cart' ) ) );
}

/**
 * Charge le module de la boutique et ses réglages.
 */
function mrz_theme_shop_enqueue() {
	if ( ! mrz_theme_shop_script_needed() ) {
		return;
	}

	wp_enqueue_script(
		'mrz-theme-shop',
		MRZ_THEME_URI . '/assets/js/shop.js',
		array(),
		mrz_theme_asset_version( 'assets/js/shop.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_add_inline_script(
		'mrz-theme-shop',
		'window.mrzShop = ' . wp_json_encode(
			array(
				'ajaxUrl'         => WC_AJAX::get_endpoint( '%%endpoint%%' ),
				'drawer'          => mrz_theme_cart_drawer_enabled(),
				// Fiche produit : pas d'ajout sans rechargement si WooCommerce doit mener au panier après l'ajout.
				'ajaxSingle'      => is_product() && mrz_theme_option( 'shop_ajax_single' ) && 'yes' !== get_option( 'woocommerce_cart_redirect_after_add' ),
				'quantityButtons' => (bool) mrz_theme_option( 'shop_quantity_buttons' ),
				'swatches'        => (bool) mrz_theme_option( 'shop_variation_swatches' ),
				'i18n'            => array(
					'close'      => __( 'Close', 'mrz-theme-exp' ),
					'outOfStock' => __( 'out of stock', 'mrz-theme-exp' ),
					'decrease'   => __( 'Decrease quantity', 'mrz-theme-exp' ),
					'increase'   => __( 'Increase quantity', 'mrz-theme-exp' ),
					'error'      => __( 'The product could not be added to the cart. Please try again.', 'mrz-theme-exp' ),
				),
			)
		) . ';',
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'mrz_theme_shop_enqueue' );

/**
 * Panneau du panier et zone des notifications, en bas de page.
 * Le contenu du panier n'est jamais dans le HTML de la page (compatible avec le
 * cache de page) : shop.js le charge à l'ouverture, par les fragments de WooCommerce.
 */
function mrz_theme_shop_footer() {
	if ( ! mrz_theme_shop_script_needed() ) {
		return;
	}
	?>
	<div class="mrz-toast" role="status" aria-live="polite" aria-atomic="true"></div>
	<?php if ( mrz_theme_cart_drawer_enabled() ) : ?>
		<dialog class="mrz-minicart woocommerce" id="mrz-minicart" aria-labelledby="mrz-minicart-title">
			<?php mrz_theme_topbar( 'cart' ); // Sections globales « Top bar » : même bandeau qu'au-dessus du header. ?>
			<div class="mrz-minicart__header">
				<h2 class="mrz-minicart__title" id="mrz-minicart-title"><?php esc_html_e( 'Cart', 'mrz-theme-exp' ); ?></h2>
				<button type="button" class="mrz-minicart__close" data-mrz-minicart-close>
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="24" height="24"><path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
					<span class="screen-reader-text"><?php esc_html_e( 'Close the cart', 'mrz-theme-exp' ); ?></span>
				</button>
			</div>
			<div class="widget_shopping_cart_content"></div>
		</dialog>
	<?php endif; ?>
	<?php
}
add_action( 'wp_footer', 'mrz_theme_shop_footer' );

/**
 * Panier vide dans le panneau : un bouton pour revenir à la boutique.
 */
function mrz_theme_mini_cart_empty_button() {
	if ( WC()->cart && WC()->cart->is_empty() ) {
		printf(
			'<p class="mrz-minicart__continue"><a class="button" href="%s">%s</a></p>',
			esc_url( wc_get_page_permalink( 'shop' ) ),
			esc_html__( 'Continue shopping', 'mrz-theme-exp' )
		);
	}
}
add_action( 'woocommerce_after_mini_cart', 'mrz_theme_mini_cart_empty_button' );

/**
 * Ajout au panier depuis la fiche produit (shop.js) : les messages de WooCommerce
 * (ajout réussi, rupture de stock…) partent avec les fragments, pour la notification.
 * Seulement pour une requête d'ajout : sinon les messages restent pour la page suivante.
 *
 * @param array $fragments Fragments.
 * @return array
 */
function mrz_theme_cart_notices_fragment( $fragments ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- simple lecture, l'ajout est fait par WooCommerce.
	if ( ! isset( $_REQUEST['add-to-cart'] ) ) {
		return $fragments;
	}

	$html = '';
	foreach ( wc_get_notices() as $type => $notices ) {
		foreach ( $notices as $notice ) {
			$html .= '<p data-type="' . esc_attr( $type ) . '">' . wp_kses_post( $notice['notice'] ) . '</p>';
		}
	}
	wc_clear_notices();

	$fragments['div.mrz-shop-notices'] = '<div class="mrz-shop-notices">' . $html . '</div>';

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'mrz_theme_cart_notices_fragment' );

/**
 * Panier vide (bloc Panier) : seulement le message « Votre panier est vide » et le lien
 * vers la boutique. Le séparateur, « Nouveau dans la boutique » et sa grille de produits
 * (blocs de WooCommerce, pas la grille du thème) sont retirés au rendu : rien à changer
 * dans le contenu de la page, et le même résultat sur chaque site.
 *
 * @param array $parsed_block Bloc analysé.
 * @return array
 */
function mrz_theme_empty_cart_block( $parsed_block ) {
	if ( 'woocommerce/empty-cart-block' !== ( $parsed_block['blockName'] ?? '' ) || empty( $parsed_block['innerBlocks'] ) ) {
		return $parsed_block;
	}

	$blocks  = array();
	$content = array();
	$index   = 0;
	$cut     = false;

	// innerContent mêle le HTML du bloc (chaînes) et la place de chaque bloc enfant (null).
	foreach ( $parsed_block['innerContent'] as $chunk ) {
		if ( null !== $chunk ) {
			$content[] = $chunk;
			continue;
		}

		$inner = $parsed_block['innerBlocks'][ $index ] ?? null;
		++$index;
		// Tout ce qui suit le premier séparateur est la suggestion de produits.
		if ( ! $inner || 'core/separator' === $inner['blockName'] ) {
			$cut = true;
		}
		if ( $cut ) {
			continue;
		}

		$blocks[]  = $inner;
		$content[] = null;
	}

	$parsed_block['innerBlocks']  = $blocks;
	$parsed_block['innerContent'] = $content;

	return $parsed_block;
}
add_filter( 'render_block_data', 'mrz_theme_empty_cart_block' );

/**
 * Variations en pastilles (réglage « shop_variation_swatches ») : chaque menu déroulant
 * d'une variation est doublé d'un groupe de boutons radio. Le <select> de WooCommerce
 * reste, masqué : son script y lit le choix pour trouver la variation (prix, stock, image,
 * bouton). shop.js les synchronise, et grise les choix indisponibles.
 * Pas de copie de template : filtre sur le HTML du menu.
 *
 * @param string $html Menu déroulant de WooCommerce.
 * @param array  $args Arguments (options, attribute, product, selected).
 * @return string
 */
function mrz_theme_variation_swatches( $html, $args ) {
	if ( ! mrz_theme_option( 'shop_variation_swatches' ) || empty( $args['options'] ) || ! $args['product'] instanceof WC_Product ) {
		return $html;
	}

	$attribute = $args['attribute'];
	$name      = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$selected  = (string) $args['selected'];
	$choices   = array();

	// Même ordre et mêmes valeurs que le menu de WooCommerce.
	if ( taxonomy_exists( $attribute ) ) {
		foreach ( wc_get_product_terms( $args['product']->get_id(), $attribute, array( 'fields' => 'all' ) ) as $term ) {
			if ( in_array( $term->slug, $args['options'], true ) ) {
				$choices[ $term->slug ] = $term->name;
			}
		}
	} else {
		foreach ( $args['options'] as $option ) {
			$choices[ $option ] = $option;
		}
	}

	$swatches = '';
	foreach ( $choices as $value => $label ) {
		$swatches .= sprintf(
			'<label class="mrz-swatch"><input type="radio" name="%1$s" value="%2$s"%3$s><span>%4$s</span></label>',
			esc_attr( 'mrz_swatch_' . $name ),
			esc_attr( $value ),
			checked( $selected, (string) $value, false ),
			esc_html( apply_filters( 'woocommerce_variation_option_name', $label, null, $attribute, $args['product'] ) )
		);
	}

	return sprintf(
		'<fieldset class="mrz-swatches" data-mrz-swatches="%1$s"><legend class="screen-reader-text">%2$s</legend>%3$s</fieldset><div class="mrz-swatches__native">%4$s</div>',
		esc_attr( $name ),
		esc_html( wc_attribute_label( $attribute, $args['product'] ) ),
		$swatches,
		$html
	);
}
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'mrz_theme_variation_swatches', 10, 2 );

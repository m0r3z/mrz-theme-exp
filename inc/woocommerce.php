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
 * Produits similaires : leur propre réglage (Customizer > Shop), sur une ligne,
 * indépendant des colonnes de la boutique. 0 les masque (voir mrz_theme_related_products_display).
 *
 * @param array $args Arguments.
 * @return array
 */
function mrz_theme_related_products_args( $args ) {
	$count = min( 6, max( 1, absint( mrz_theme_option( 'shop_related_count' ) ) ) );

	$args['posts_per_page'] = $count;
	$args['columns']        = $count;

	return $args;
}

/**
 * Produits similaires masqués quand leur nombre vaut 0.
 */
function mrz_theme_related_products_display() {
	if ( is_product() && 0 === absint( mrz_theme_option( 'shop_related_count' ) ) ) {
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	}
}
add_action( 'wp', 'mrz_theme_related_products_display' );
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

	mrz_theme_add_setting( $wp_customize, 'shop_gallery_layout', 'mrz_theme_sanitize_choice' );
	$wp_customize->add_control(
		'mrz_shop_gallery_layout',
		array(
			'label'       => __( 'Product images', 'mrz-theme-exp' ),
			'description' => __( 'Stacked: every image one under the other, the product details stay on screen while scrolling. On mobile, stacked images are swiped sideways, and side thumbnails go under the image.', 'mrz-theme-exp' ),
			'section'     => 'mrz_shop',
			'type'        => 'radio',
			'choices'     => array(
				'below'         => __( 'Slideshow, thumbnails below', 'mrz-theme-exp' ),
				'left'          => __( 'Slideshow, thumbnails on the left', 'mrz-theme-exp' ),
				'right'         => __( 'Slideshow, thumbnails on the right', 'mrz-theme-exp' ),
				'stacked-left'  => __( 'Stacked images, thumbnails on the left', 'mrz-theme-exp' ),
				'stacked-right' => __( 'Stacked images, thumbnails on the right', 'mrz-theme-exp' ),
			),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'shop_gallery_zoom', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_shop_gallery_zoom',
		array(
			'label'       => __( 'Zoom on the product image', 'mrz-theme-exp' ),
			'description' => __( 'Enlarges the image under the mouse; the magnifier button opens it in full screen. Without zoom, a click on the image opens it in full screen.', 'mrz-theme-exp' ),
			'section'     => 'mrz_shop',
			'type'        => 'checkbox',
		)
	);

	mrz_theme_add_setting( $wp_customize, 'shop_columns', 'absint' );
	$wp_customize->add_control(
		'mrz_shop_columns',
		array(
			'label'       => __( 'Products per row', 'mrz-theme-exp' ),
			'description' => __( 'Shop and categories. The [products] shortcode keeps its own columns.', 'mrz-theme-exp' ),
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

	mrz_theme_add_setting( $wp_customize, 'shop_related_count', 'absint' );
	$wp_customize->add_control(
		'mrz_shop_related_count',
		array(
			'label'       => __( 'Related products', 'mrz-theme-exp' ),
			'description' => __( 'Number shown under the product page, on one row. 0: none.', 'mrz-theme-exp' ),
			'section'     => 'mrz_shop',
			'type'        => 'number',
			'input_attrs' => array(
				'min' => 0,
				'max' => 6,
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
 * Disposition des images de la fiche produit (Customizer > MRZ Theme > Boutique).
 *
 * @return string below | left | right | stacked-left | stacked-right
 */
function mrz_theme_product_gallery_layout() {
	$layout = (string) mrz_theme_option( 'shop_gallery_layout' );

	return in_array( $layout, array( 'left', 'right', 'stacked-left', 'stacked-right' ), true ) ? $layout : 'below';
}

/**
 * Images empilées : vrai pour stacked-left et stacked-right.
 *
 * @return bool
 */
function mrz_theme_product_gallery_is_stacked() {
	return 0 === strpos( mrz_theme_product_gallery_layout(), 'stacked' );
}

/**
 * Classe de la disposition sur la galerie : le CSS (woocommerce.css) s'y accroche.
 *
 * @param array $classes Classes de la galerie.
 * @return array
 */
function mrz_theme_product_gallery_classes( $classes ) {
	$classes[] = 'mrz-product-gallery--' . mrz_theme_product_gallery_layout();

	return $classes;
}
add_filter( 'woocommerce_single_product_image_gallery_classes', 'mrz_theme_product_gallery_classes' );

/**
 * Images empilées : sans le diaporama, WooCommerce affiche toutes les images à la suite.
 *
 * @param bool $enabled Diaporama activé.
 * @return bool
 */
function mrz_theme_product_gallery_slider( $enabled ) {
	return mrz_theme_product_gallery_is_stacked() ? false : $enabled;
}
add_filter( 'woocommerce_single_product_flexslider_enabled', 'mrz_theme_product_gallery_slider' );

/**
 * Images empilées : sans diaporama, WooCommerce affiche les images après la première
 * dans la taille des miniatures. Toutes prennent la taille de la grande image.
 *
 * @param string|array $size Taille des images de la galerie.
 * @return string|array
 */
function mrz_theme_product_gallery_image_size( $size ) {
	return is_product() && mrz_theme_product_gallery_is_stacked() ? 'woocommerce_single' : $size;
}
add_filter( 'woocommerce_gallery_image_size', 'mrz_theme_product_gallery_image_size' );

/**
 * Images empilées : les images et la fiche sont enveloppées ensemble. La fiche, fixe au
 * défilement, s'arrête au bas des images au lieu de passer sur les produits similaires
 * et les informations placées sous la fiche (tous dans div.product).
 */
function mrz_theme_product_main_open() {
	if ( mrz_theme_product_gallery_is_stacked() ) {
		echo '<div class="mrz-product-main">';
	}
}
add_action( 'woocommerce_before_single_product_summary', 'mrz_theme_product_main_open', 1 );

/**
 * Ferme l'enveloppe ouverte par mrz_theme_product_main_open(), avant les accordéons
 * placés sous la fiche (10), les ventes croisées (15) et les produits similaires (20).
 */
function mrz_theme_product_main_close() {
	if ( mrz_theme_product_gallery_is_stacked() ) {
		echo '</div>';
	}
}
add_action( 'woocommerce_after_single_product_summary', 'mrz_theme_product_main_close', 1 );

/**
 * Zoom au survol de l'image : réglage du Customizer. La loupe (plein écran) reste.
 *
 * @param bool $enabled Zoom activé.
 * @return bool
 */
function mrz_theme_product_gallery_zoom( $enabled ) {
	return $enabled && (bool) mrz_theme_option( 'shop_gallery_zoom' );
}
add_filter( 'woocommerce_single_product_zoom_enabled', 'mrz_theme_product_gallery_zoom' );

/**
 * Images empilées : chaque image reçoit une ancre (#mrz-product-image-1…), cible des
 * miniatures. Les identifiants sont gardés pour mrz_theme_product_gallery_thumbs().
 *
 * @param string $html          Balisage d'une image de la galerie.
 * @param int    $attachment_id Image.
 * @return string
 */
function mrz_theme_product_gallery_anchor( $html, $attachment_id ) {
	if ( ! is_product() || ! mrz_theme_product_gallery_is_stacked() || ! $attachment_id ) {
		return $html;
	}

	$GLOBALS['mrz_theme_product_images'][] = (int) $attachment_id;
	$anchor                               = 'mrz-product-image-' . count( $GLOBALS['mrz_theme_product_images'] );

	return preg_replace( '/^\s*<div /', '<div id="' . esc_attr( $anchor ) . '" ', $html, 1 );
}
add_filter( 'woocommerce_single_product_image_thumbnail_html', 'mrz_theme_product_gallery_anchor', 10, 2 );

/**
 * Images empilées : colonne de miniatures, de simples liens vers chaque image (sans JS).
 * Après les images de la galerie (woocommerce_show_product_thumbnails, priorité 20),
 * seulement à partir de deux images.
 */
function mrz_theme_product_gallery_thumbs() {
	$images = $GLOBALS['mrz_theme_product_images'] ?? array();

	if ( ! mrz_theme_product_gallery_is_stacked() || count( $images ) < 2 ) {
		return;
	}

	echo '<nav class="mrz-product-thumbs" aria-label="' . esc_attr__( 'Product images', 'mrz-theme-exp' ) . '">';
	foreach ( $images as $index => $image_id ) {
		printf(
			'<a href="#mrz-product-image-%1$d">%2$s</a>',
			(int) $index + 1,
			wp_get_attachment_image(
				$image_id,
				'woocommerce_gallery_thumbnail',
				false,
				array(
					/* translators: %d: image number. */
					'alt'     => sprintf( __( 'Image %d', 'mrz-theme-exp' ), (int) $index + 1 ),
					'loading' => 'lazy',
				)
			)
		);
	}
	echo '</nav>';

	$GLOBALS['mrz_theme_product_images'] = array();
}
add_action( 'woocommerce_product_thumbnails', 'mrz_theme_product_gallery_thumbs', 30 );

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
 * Panneau du panier (toutes les pages), fiche produit (ajout sans rechargement, pastilles,
 * boutons quantité), panier en shortcode (boutons quantité ; le bloc Panier a les siens).
 *
 * @return bool
 */
function mrz_theme_shop_script_needed() {
	return mrz_theme_cart_drawer_enabled()
		|| is_product()
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
				'i18n'            => array(
					'close'       => __( 'Close', 'mrz-theme-exp' ),
					'outOfStock'  => __( 'out of stock', 'mrz-theme-exp' ),
					/* translators: %s: couleur choisie, après le libellé de l'attribut (« Couleur : Moutarde »). */
					'swatchValue' => __( ': %s', 'mrz-theme-exp' ),
					'decrease'    => __( 'Decrease quantity', 'mrz-theme-exp' ),
					'increase'    => __( 'Increase quantity', 'mrz-theme-exp' ),
					'error'       => __( 'The product could not be added to the cart. Please try again.', 'mrz-theme-exp' ),
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

/*
 * Article du panneau du panier : titre (nom du produit) et options de la variation en
 * dessous, prix de la ligne et quantité modifiable sur la dernière ligne. Par filtres,
 * seulement pendant l'affichage du mini-panier : la page Panier et les commandes gardent
 * les noms de WooCommerce.
 */
add_action(
	'woocommerce_before_mini_cart',
	static function () {
		$GLOBALS['mrz_theme_in_mini_cart'] = true;
	}
);
add_action(
	'woocommerce_after_mini_cart',
	static function () {
		$GLOBALS['mrz_theme_in_mini_cart'] = false;
	},
	1
);

/**
 * Mini-panier : nom du produit, puis ses options sur une ligne (« Moutarde – 40 × 40 cm »).
 *
 * @param string $name      Nom de l'article (avec ses options pour une variation).
 * @param array  $cart_item Article.
 * @return string
 */
function mrz_theme_mini_cart_item_name( $name, $cart_item ) {
	if ( empty( $GLOBALS['mrz_theme_in_mini_cart'] ) || empty( $cart_item['data'] ) || ! $cart_item['data'] instanceof WC_Product ) {
		return $name;
	}

	$product = $cart_item['data'];
	$parent  = $product->get_parent_id() ? wc_get_product( $product->get_parent_id() ) : null;
	$title   = $parent ? $parent->get_name() : $product->get_name();
	$values  = array();

	foreach ( (array) ( $cart_item['variation'] ?? array() ) as $key => $value ) {
		$taxonomy = wc_sanitize_taxonomy_name( str_replace( 'attribute_', '', $key ) );
		$term     = taxonomy_exists( $taxonomy ) ? get_term_by( 'slug', $value, $taxonomy ) : false;
		$values[] = $term ? $term->name : $value;
	}
	$values = array_filter( $values, 'strlen' );

	// Espace entre les deux : le libellé « Retirer … du panier » est construit sans les balises.
	return '<span class="mrz-minicart__name">' . esc_html( $title ) . '</span>'
		. ( $values ? ' <span class="mrz-minicart__options">' . esc_html( implode( ' – ', $values ) ) . '</span>' : '' );
}
add_filter( 'woocommerce_cart_item_name', 'mrz_theme_mini_cart_item_name', 10, 2 );

/**
 * Mini-panier : à la place de « 2 × 19,00 € », le prix de la ligne et la quantité
 * modifiable (− et +, champ au clavier). shop.js envoie le changement (wc-ajax=mrz_cart_quantity).
 *
 * @param string $html          Quantité de WooCommerce.
 * @param array  $cart_item     Article.
 * @param string $cart_item_key Clé de l'article.
 * @return string
 */
function mrz_theme_mini_cart_item_quantity( $html, $cart_item, $cart_item_key ) {
	$product = $cart_item['data'] ?? null;
	if ( ! $product instanceof WC_Product ) {
		return $html;
	}

	$quantity = (int) $cart_item['quantity'];
	$price    = '<span class="mrz-minicart__price">' . WC()->cart->get_product_subtotal( $product, $quantity ) . '</span>';

	if ( $product->is_sold_individually() ) {
		return '<span class="mrz-minicart__row">' . $price . '</span>';
	}

	$max  = $product->get_max_purchase_quantity();
	$name = wp_strip_all_tags( $product->get_name() );

	return sprintf(
		'<span class="mrz-minicart__row">%1$s<span class="quantity mrz-qty" data-mrz-cart-item="%2$s"><button type="button" class="mrz-qty__button mrz-qty__button--decrease"><span aria-hidden="true">−</span><span class="screen-reader-text">%3$s</span></button><input type="number" class="qty" value="%4$d" min="0" step="1"%5$s aria-label="%6$s" inputmode="numeric"><button type="button" class="mrz-qty__button mrz-qty__button--increase"><span aria-hidden="true">+</span><span class="screen-reader-text">%7$s</span></button></span></span>',
		$price,
		esc_attr( $cart_item_key ),
		esc_html__( 'Decrease quantity', 'mrz-theme-exp' ),
		$quantity,
		$max > 0 ? ' max="' . (int) $max . '"' : '',
		/* translators: %s: nom du produit. */
		esc_attr( sprintf( __( 'Quantity of %s', 'mrz-theme-exp' ), $name ) ),
		esc_html__( 'Increase quantity', 'mrz-theme-exp' )
	);
}
add_filter( 'woocommerce_widget_cart_item_quantity', 'mrz_theme_mini_cart_item_quantity', 10, 3 );

/**
 * Change la quantité d'un article du panier (0 : le retire), puis renvoie les fragments.
 * Point d'entrée wc-ajax, sur le modèle de remove_from_cart de WooCommerce (sans nonce
 * comme lui : la page peut venir du cache, et l'action ne touche que le panier du visiteur).
 */
function mrz_theme_ajax_cart_quantity() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- voir le commentaire de la fonction.
	$key      = isset( $_POST['cart_item_key'] ) ? wc_clean( wp_unslash( $_POST['cart_item_key'] ) ) : '';
	$quantity = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : -1;
	// phpcs:enable

	if ( ! $key || $quantity < 0 || ! WC()->cart->get_cart_item( $key ) ) {
		wp_send_json_error();
	}

	WC()->cart->set_quantity( $key, $quantity );
	WC_AJAX::get_refreshed_fragments();
}
add_action( 'wc_ajax_mrz_cart_quantity', 'mrz_theme_ajax_cart_quantity' );

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

// Variations en pastilles ou en couleurs, réglées attribut par attribut.
require_once MRZ_THEME_DIR . '/inc/woocommerce-swatches.php';

/*
 * Mon compte, visiteur non connecté : une seule colonne à la fois, connexion par défaut,
 * inscription avec ?action=register (quand l'inscription est ouverte dans WooCommerce),
 * et un lien de l'une à l'autre. Le formulaire d'inscription s'envoie sur la même adresse :
 * en cas d'erreur, il reste affiché.
 */

/**
 * Le visiteur demande-t-il le formulaire d'inscription ?
 *
 * @return bool
 */
function mrz_theme_account_wants_register() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- simple choix d'affichage.
	return isset( $_GET['action'] ) && 'register' === sanitize_key( wp_unslash( $_GET['action'] ) )
		&& 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
}

/**
 * Classes du body : formulaire affiché (connexion ou inscription).
 *
 * @param array $classes Classes.
 * @return array
 */
function mrz_theme_account_body_classes( $classes ) {
	if ( function_exists( 'is_account_page' ) && is_account_page() && ! is_user_logged_in() ) {
		$classes[] = mrz_theme_account_wants_register() ? 'mrz-account-register' : 'mrz-account-login';
	}

	return $classes;
}
add_filter( 'body_class', 'mrz_theme_account_body_classes' );

/**
 * Sous le formulaire de connexion : « Vous n'avez pas encore de compte ? S'inscrire ».
 */
function mrz_theme_account_register_link() {
	if ( 'yes' !== get_option( 'woocommerce_enable_myaccount_registration' ) || ! is_account_page() ) {
		return;
	}
	printf(
		'<p class="mrz-account-switch">%1$s <a href="%2$s">%3$s</a></p>',
		esc_html__( 'Don’t have an account yet?', 'mrz-theme-exp' ),
		esc_url( add_query_arg( 'action', 'register', wc_get_page_permalink( 'myaccount' ) ) ),
		esc_html__( 'Sign up', 'mrz-theme-exp' )
	);
}
add_action( 'woocommerce_login_form_end', 'mrz_theme_account_register_link' );

/**
 * Sous le formulaire d'inscription : « Vous avez déjà un compte ? Se connecter ».
 */
function mrz_theme_account_login_link() {
	printf(
		'<p class="mrz-account-switch">%1$s <a href="%2$s">%3$s</a></p>',
		esc_html__( 'Already have an account?', 'mrz-theme-exp' ),
		esc_url( wc_get_page_permalink( 'myaccount' ) ),
		esc_html__( 'Log in', 'mrz-theme-exp' )
	);
}
add_action( 'woocommerce_register_form_end', 'mrz_theme_account_login_link' );

// Texte sur les données personnelles : sous le bouton « S'inscrire », avant le lien de connexion.
remove_action( 'woocommerce_register_form', 'wc_registration_privacy_policy_text', 20 );
add_action( 'woocommerce_register_form_end', 'wc_registration_privacy_policy_text', 5 );

/**
 * Sous le formulaire « Mot de passe perdu » : retour à la connexion.
 */
function mrz_theme_lost_password_back_link() {
	printf(
		'<p class="mrz-account-switch"><a href="%1$s">%2$s</a></p>',
		esc_url( wc_get_page_permalink( 'myaccount' ) ),
		esc_html__( 'Back to log in', 'mrz-theme-exp' )
	);
}
add_action( 'woocommerce_after_lost_password_form', 'mrz_theme_lost_password_back_link' );

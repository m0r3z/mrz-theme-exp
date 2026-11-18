<?php
/**
 * Variations en pastilles ou en couleurs, réglées attribut par attribut.
 *
 * - Produits > Attributs > Modifier : champ « Type » de WooCommerce, enrichi de
 *   « Pastilles » (texte) et « Couleurs » (ronds de couleur). « Sélectionner » reste le
 *   menu déroulant de WooCommerce, le défaut.
 * - Attribut en « Couleurs » : une couleur par terme (meta mrz_color), saisie dans
 *   Produits > Attributs > Configurer les termes.
 * - Fiche produit : le menu de WooCommerce reste, masqué ; son script y lit le choix
 *   pour trouver la variation (prix, stock, image, bouton). shop.js le synchronise.
 *
 * Thème changé : WooCommerce revient au menu déroulant, les couleurs restent sur les termes.
 * Le type natif « Couleur / image » de WooCommerce (wc-visual) est réservé aux thèmes en blocs.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Types d'attribut du thème.
 *
 * @return array Type => libellé.
 */
function mrz_theme_swatch_types() {
	return array(
		'mrz_buttons' => __( 'Buttons', 'mrz-theme-exp' ),
		'mrz_colors'  => __( 'Colors', 'mrz-theme-exp' ),
	);
}

/**
 * Ajoute les types du thème au champ « Type » des attributs.
 *
 * @param array $types Types.
 * @return array
 */
function mrz_theme_attribute_types( $types ) {
	// Le type natif s'appelle « Texte » en français : c'est le menu déroulant.
	if ( isset( $types['select'] ) ) {
		$types['select'] = __( 'Drop-down menu', 'mrz-theme-exp' );
	}

	return $types + mrz_theme_swatch_types();
}
add_filter( 'product_attributes_type_selector', 'mrz_theme_attribute_types' );

/**
 * Type d'un attribut global (pa_…) : select, mrz_buttons, mrz_colors…
 *
 * @param string $taxonomy Taxonomie de l'attribut.
 * @return string
 */
function mrz_theme_attribute_type( $taxonomy ) {
	$attribute_id = taxonomy_exists( $taxonomy ) ? wc_attribute_taxonomy_id_by_name( $taxonomy ) : 0;
	$attribute    = $attribute_id ? wc_get_attribute( $attribute_id ) : null;

	return $attribute ? (string) $attribute->type : 'select';
}

/**
 * Fiche produit en admin : WooCommerce n'affiche le champ des valeurs (termes) que pour
 * ses propres types. Même champ pour les types du thème, sinon on ne peut rien choisir.
 *
 * @param object|null          $attribute_taxonomy Attribut global.
 * @param int                  $i                  Rang de l'attribut dans le formulaire.
 * @param WC_Product_Attribute $attribute          Attribut du produit.
 */
function mrz_theme_attribute_terms_field( $attribute_taxonomy, $i, $attribute ) {
	if ( ! $attribute_taxonomy || ! array_key_exists( $attribute_taxonomy->attribute_type, mrz_theme_swatch_types() ) ) {
		return;
	}
	?>
	<select multiple="multiple"
		data-minimum_input_length="0"
		data-limit="<?php echo esc_attr( absint( apply_filters( 'woocommerce_admin_terms_metabox_datalimit', 50 ) ) ); ?>" data-return_id="id"
		data-placeholder="<?php esc_attr_e( 'Select values', 'woocommerce' ); ?>"
		data-orderby="<?php echo esc_attr( $attribute_taxonomy->attribute_orderby ? $attribute_taxonomy->attribute_orderby : 'name' ); ?>"
		class="multiselect attribute_values wc-taxonomy-term-search"
		name="attribute_values[<?php echo esc_attr( $i ); ?>][]"
		data-taxonomy="<?php echo esc_attr( $attribute->get_taxonomy() ); ?>">
		<?php foreach ( (array) $attribute->get_terms() as $term ) : ?>
			<option value="<?php echo esc_attr( $term->term_id ); ?>" selected="selected"><?php echo esc_html( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ); ?></option>
		<?php endforeach; ?>
	</select>
	<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'woocommerce' ); ?></button>
	<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'woocommerce' ); ?></button>
	<button class="button fr plus add_new_attribute"><?php esc_html_e( 'Create value', 'woocommerce' ); ?></button>
	<?php
}
add_action( 'woocommerce_product_option_terms', 'mrz_theme_attribute_terms_field', 10, 3 );

/**
 * Couleur d'un terme (attribut en « Couleurs »), vide si aucune.
 *
 * @param int $term_id Terme.
 * @return string Couleur hexadécimale.
 */
function mrz_theme_term_color( $term_id ) {
	return (string) sanitize_hex_color( (string) get_term_meta( $term_id, 'mrz_color', true ) );
}

/**
 * Champ « Couleur » sur les termes des attributs en « Couleurs » (ajout, modification,
 * colonne de la liste). Attributs lus sur admin_init : leurs taxonomies existent alors.
 */
function mrz_theme_term_color_hooks() {
	foreach ( wc_get_attribute_taxonomies() as $attribute ) {
		if ( 'mrz_colors' !== $attribute->attribute_type ) {
			continue;
		}
		$taxonomy = wc_attribute_taxonomy_name( $attribute->attribute_name );
		add_action( $taxonomy . '_add_form_fields', 'mrz_theme_term_color_add_field' );
		add_action( $taxonomy . '_edit_form_fields', 'mrz_theme_term_color_edit_field' );
		add_action( 'created_' . $taxonomy, 'mrz_theme_term_color_save' );
		add_action( 'edited_' . $taxonomy, 'mrz_theme_term_color_save' );
		add_filter( 'manage_edit-' . $taxonomy . '_columns', 'mrz_theme_term_color_column' );
		add_filter( 'manage_' . $taxonomy . '_custom_column', 'mrz_theme_term_color_column_content', 10, 3 );
	}
}
add_action( 'admin_init', 'mrz_theme_term_color_hooks' );

/**
 * Champ couleur (sélecteur de couleur de WordPress, qui accepte une valeur vide).
 *
 * @param string $value Couleur.
 */
function mrz_theme_term_color_input( $value ) {
	wp_nonce_field( 'mrz_term_color', 'mrz_term_color_nonce' );
	printf( '<input type="text" name="mrz_color" id="mrz_color" class="mrz-term-color" value="%s">', esc_attr( $value ) );
	echo '<p class="description">' . esc_html__( 'Shown as a round swatch on the product page. Empty: the name is shown instead.', 'mrz-theme-exp' ) . '</p>';
}

/**
 * Écran « Ajouter un terme ».
 */
function mrz_theme_term_color_add_field() {
	echo '<div class="form-field"><label for="mrz_color">' . esc_html__( 'Color', 'mrz-theme-exp' ) . '</label>';
	mrz_theme_term_color_input( '' );
	echo '</div>';
}

/**
 * Écran « Modifier le terme ».
 *
 * @param WP_Term $term Terme.
 */
function mrz_theme_term_color_edit_field( $term ) {
	echo '<tr class="form-field"><th scope="row"><label for="mrz_color">' . esc_html__( 'Color', 'mrz-theme-exp' ) . '</label></th><td>';
	mrz_theme_term_color_input( mrz_theme_term_color( $term->term_id ) );
	echo '</td></tr>';
}

/**
 * Enregistre la couleur du terme.
 *
 * @param int $term_id Terme.
 */
function mrz_theme_term_color_save( $term_id ) {
	if ( ! isset( $_POST['mrz_term_color_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['mrz_term_color_nonce'] ) ), 'mrz_term_color' ) || ! current_user_can( 'manage_product_terms' ) ) {
		return;
	}

	$color = isset( $_POST['mrz_color'] ) ? sanitize_hex_color( sanitize_text_field( wp_unslash( $_POST['mrz_color'] ) ) ) : '';
	if ( $color ) {
		update_term_meta( $term_id, 'mrz_color', $color );
	} else {
		delete_term_meta( $term_id, 'mrz_color' );
	}
}

/**
 * Colonne « Couleur » de la liste des termes.
 *
 * @param array $columns Colonnes.
 * @return array
 */
function mrz_theme_term_color_column( $columns ) {
	$first = array_slice( $columns, 0, 2, true );

	return $first + array( 'mrz_color' => __( 'Color', 'mrz-theme-exp' ) ) + $columns;
}

/**
 * Contenu de la colonne « Couleur ».
 *
 * @param string $content Contenu.
 * @param string $column  Colonne.
 * @param int    $term_id Terme.
 * @return string
 */
function mrz_theme_term_color_column_content( $content, $column, $term_id ) {
	if ( 'mrz_color' !== $column ) {
		return $content;
	}
	$color = mrz_theme_term_color( $term_id );

	return $color ? sprintf( '<span style="display:inline-block;width:1.5em;height:1.5em;border:1px solid #c3c4c7;border-radius:50%%;background:%1$s" title="%1$s"></span>', esc_attr( $color ) ) : '—';
}

/**
 * Sélecteur de couleur de WordPress sur les écrans des termes en « Couleurs ».
 *
 * @param string $hook Écran.
 */
function mrz_theme_term_color_assets( $hook ) {
	if ( ! in_array( $hook, array( 'edit-tags.php', 'term.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'mrz_colors' !== mrz_theme_attribute_type( (string) $screen->taxonomy ) ) {
		return;
	}
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	wp_add_inline_script( 'wp-color-picker', 'jQuery(function($){$(".mrz-term-color").wpColorPicker();});' );
}
add_action( 'admin_enqueue_scripts', 'mrz_theme_term_color_assets' );

/**
 * Fiche produit : le menu déroulant d'un attribut en « Pastilles » ou « Couleurs » est
 * doublé d'un groupe de boutons radio (pas de copie de template : filtre sur son HTML).
 * Attributs en « Sélectionner » et attributs propres au produit : menu de WooCommerce.
 *
 * @param string $html Menu déroulant de WooCommerce.
 * @param array  $args Arguments (options, attribute, product, selected).
 * @return string
 */
function mrz_theme_variation_swatches( $html, $args ) {
	$attribute = (string) $args['attribute'];
	$type      = mrz_theme_attribute_type( $attribute );

	if ( ! array_key_exists( $type, mrz_theme_swatch_types() ) || empty( $args['options'] ) || ! $args['product'] instanceof WC_Product ) {
		return $html;
	}

	$name     = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$selected = (string) $args['selected'];
	$swatches = '';

	// Même ordre et mêmes valeurs que le menu de WooCommerce.
	foreach ( wc_get_product_terms( $args['product']->get_id(), $attribute, array( 'fields' => 'all' ) ) as $term ) {
		if ( ! in_array( $term->slug, $args['options'], true ) ) {
			continue;
		}
		$label = apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $args['product'] );
		$color = 'mrz_colors' === $type ? mrz_theme_term_color( $term->term_id ) : '';

		// Couleur : un rond, le nom en texte pour lecteur d'écran et au survol. Sans couleur : le nom.
		$content = $color
			? sprintf( '<span class="mrz-swatch__dot" style="--mrz-swatch-color:%1$s"></span><span class="screen-reader-text">%2$s</span>', esc_attr( $color ), esc_html( $label ) )
			: esc_html( $label );

		$swatches .= sprintf(
			'<label class="mrz-swatch%1$s"%2$s><input type="radio" name="%3$s" value="%4$s" data-label="%5$s"%6$s><span class="mrz-swatch__body">%7$s</span></label>',
			$color ? ' mrz-swatch--color' : '',
			$color ? ' title="' . esc_attr( $label ) . '"' : '',
			esc_attr( 'mrz_swatch_' . $name ),
			esc_attr( $term->slug ),
			esc_attr( $label ),
			checked( $selected, $term->slug, false ),
			$content
		);
	}

	if ( '' === $swatches ) {
		return $html;
	}

	return sprintf(
		'<fieldset class="mrz-swatches mrz-swatches--%1$s" data-mrz-swatches="%2$s"><legend class="screen-reader-text">%3$s</legend>%4$s</fieldset><div class="mrz-swatches__native">%5$s</div>',
		esc_attr( 'mrz_colors' === $type ? 'colors' : 'buttons' ),
		esc_attr( $name ),
		esc_html( wc_attribute_label( $attribute, $args['product'] ) ),
		$swatches,
		$html
	);
}
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'mrz_theme_variation_swatches', 10, 2 );

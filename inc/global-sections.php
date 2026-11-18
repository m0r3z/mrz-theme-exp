<?php
/**
 * Sections globales : contenus réutilisables (CPT mrz_section) insérés
 * sur tout le site par un hook, ou à la main par le layout « Section globale ».
 *
 * Une section a un emplacement, une priorité et des conditions : sur tout le
 * site, seulement sur certains types de contenus ou pages, ou partout sauf sur eux.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Emplacements disponibles.
 *
 * @return array Hook => libellé.
 */
function mrz_theme_global_section_hooks() {
	return array(
		'mrz_topbar'                       => __( 'Top bar (above the header, hidden on scroll)', 'mrz-theme-exp' ),
		'mrz_before_header'                => __( 'Before the header', 'mrz-theme-exp' ),
		'mrz_after_header'                 => __( 'After the header', 'mrz-theme-exp' ),
		'mrz_before_content'               => __( 'Before the content', 'mrz-theme-exp' ),
		'mrz_after_content'                => __( 'After the content', 'mrz-theme-exp' ),
		'mrz_footer'                       => __( 'Footer (the site footer)', 'mrz-theme-exp' ),
		'mrz_before_footer'                => __( 'Before the footer', 'mrz-theme-exp' ),
		'mrz_after_footer'                 => __( 'After the footer', 'mrz-theme-exp' ),
		'mrz_mobile_menu_end'              => __( 'Mobile menu: below the links', 'mrz-theme-exp' ),
		'woocommerce_before_shop_loop'     => __( 'WooCommerce: before the product list', 'mrz-theme-exp' ),
		'woocommerce_after_single_product' => __( 'WooCommerce: after a product', 'mrz-theme-exp' ),
	);
}

/**
 * Déclare le type de contenu.
 */
function mrz_theme_register_global_sections() {
	register_post_type(
		'mrz_section',
		array(
			'labels'              => array(
				'name'          => __( 'Global sections', 'mrz-theme-exp' ),
				'singular_name' => __( 'Global section', 'mrz-theme-exp' ),
				'add_new_item'  => __( 'Add a global section', 'mrz-theme-exp' ),
				'edit_item'     => __( 'Edit the global section', 'mrz-theme-exp' ),
				'all_items'     => __( 'Global sections', 'mrz-theme-exp' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => mrz_theme_admin_menu_slug(),
			// Proposée dans Apparence > Menus : une section globale peut former une colonne de mega menu.
			'show_in_nav_menus'   => true,
			'exclude_from_search' => true,
			'menu_position'       => 21,
			'menu_icon'           => 'dashicons-layout',
			'supports'            => array( 'title', 'editor', 'revisions' ),
			'capability_type'     => 'page',
		)
	);
}
add_action( 'init', 'mrz_theme_register_global_sections' );

/**
 * Polylang : sections globales traduisibles d'office (une version par langue),
 * sans cocher le type dans les réglages de Polylang.
 *
 * @param array $post_types Types traduisibles.
 * @return array
 */
function mrz_theme_translatable_global_sections( $post_types ) {
	$post_types['mrz_section'] = 'mrz_section';

	return $post_types;
}
add_filter( 'pll_get_post_types', 'mrz_theme_translatable_global_sections' );

/**
 * Affiche une section globale (garde-fou contre une section qui s'inclut elle-même).
 *
 * @param int $section_id ID de la section.
 */
function mrz_theme_render_global_section( $section_id ) {
	static $rendering = array();

	// Section insérée à la main dans une page : sa traduction, si elle existe.
	$section_id = mrz_theme_translated_id( $section_id );
	if ( ! $section_id || isset( $rendering[ $section_id ] ) || 'mrz_section' !== get_post_type( $section_id ) || 'publish' !== get_post_status( $section_id ) ) {
		return;
	}

	$rendering[ $section_id ] = true;

	echo '<div class="mrz-global-section mrz-global-section--' . esc_attr( $section_id ) . '">';
	if ( mrz_theme_has_builder( $section_id ) ) {
		mrz_theme_render_builder( $section_id );
	} else {
		echo '<div class="mrz-container mrz-prose py-8">' . apply_filters( 'the_content', get_post_field( 'post_content', $section_id ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- contenu filtré par the_content.
	}
	echo '</div>';

	unset( $rendering[ $section_id ] );
}

/**
 * Accroche chaque section globale publiée à son emplacement.
 */
function mrz_theme_hook_global_sections() {
	if ( is_admin() ) {
		return;
	}

	$section_ids = get_posts(
		array(
			'post_type'      => 'mrz_section',
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- quelques sections au plus.
				array(
					'key'     => 'mrz_section_hook',
					'value'   => '',
					'compare' => '!=',
				),
			),
		)
	);

	$hooks = mrz_theme_global_section_hooks();

	foreach ( $section_ids as $section_id ) {
		$hook = get_post_meta( $section_id, 'mrz_section_hook', true );
		if ( ! isset( $hooks[ $hook ] ) || ! mrz_theme_global_section_matches( $section_id ) ) {
			continue;
		}

		$priority = (int) get_post_meta( $section_id, 'mrz_section_priority', true );

		add_action(
			$hook,
			static function () use ( $section_id ) {
				mrz_theme_render_global_section( $section_id );
			},
			$priority ? $priority : 10
		);
	}
}
add_action( 'wp', 'mrz_theme_hook_global_sections' );

/**
 * La section doit-elle s'afficher sur la page en cours ?
 *
 * @param int $section_id Section.
 * @return bool
 */
function mrz_theme_global_section_matches( $section_id ) {
	$display = get_post_meta( $section_id, 'mrz_section_display', true );
	if ( 'include' !== $display && 'exclude' !== $display ) {
		return true;
	}

	$post_types = array_filter( (array) get_post_meta( $section_id, 'mrz_section_post_types', true ) );
	$post_ids   = array_map( 'absint', array_filter( (array) get_post_meta( $section_id, 'mrz_section_posts', true ) ) );

	return 'include' === $display ? mrz_theme_request_matches( $post_types, $post_ids ) : ! mrz_theme_request_matches( $post_types, $post_ids );
}

/**
 * La page en cours fait-elle partie de ces types de contenus ou de ces contenus précis ?
 * Un type couvre ses contenus et ses pages de liste (blog et taxonomies pour les
 * articles, boutique et catégories de produits pour WooCommerce).
 *
 * @param array $post_types Types de contenus.
 * @param array $post_ids   Contenus précis (une page, la page du blog…).
 * @return bool
 */
function mrz_theme_request_matches( $post_types, $post_ids ) {
	$queried_id = get_queried_object_id();

	if ( $post_ids && ( is_singular() || is_home() || ( function_exists( 'is_shop' ) && is_shop() ) ) ) {
		$current_id = ( function_exists( 'is_shop' ) && is_shop() ) ? (int) wc_get_page_id( 'shop' ) : $queried_id;
		if ( in_array( $current_id, $post_ids, true ) ) {
			return true;
		}
	}

	foreach ( $post_types as $post_type ) {
		if ( is_singular( $post_type ) || is_post_type_archive( $post_type ) ) {
			return true;
		}
		if ( 'post' === $post_type && ( is_home() || is_category() || is_tag() || is_author() || is_date() ) ) {
			return true;
		}
		if ( 'product' === $post_type && function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
			return true;
		}
	}

	return false;
}

/**
 * Choix des types de contenus : les types publics, sauf les médias et les sections.
 *
 * @param array $field Champ cases à cocher.
 * @return array
 */
function mrz_theme_load_section_post_type_choices( $field ) {
	$field['choices'] = array();
	foreach ( mrz_theme_section_condition_post_types() as $post_type ) {
		$object                         = get_post_type_object( $post_type );
		$field['choices'][ $post_type ] = $object ? $object->labels->name : $post_type;
	}

	return $field;
}
add_filter( 'acf/load_field/key=field_mrz_section_post_types', 'mrz_theme_load_section_post_type_choices' );

/**
 * Contenus proposés dans « Pages précises » : les mêmes types publics.
 *
 * @param array $field Champ.
 * @return array
 */
function mrz_theme_load_section_posts_types( $field ) {
	$field['post_type'] = mrz_theme_section_condition_post_types();

	return $field;
}
add_filter( 'acf/load_field/key=field_mrz_section_posts', 'mrz_theme_load_section_posts_types' );

/**
 * Types de contenus publics utilisables dans les conditions.
 *
 * @return array
 */
function mrz_theme_section_condition_post_types() {
	$post_types = get_post_types( array( 'public' => true ), 'names' );
	unset( $post_types['attachment'], $post_types['mrz_section'] );

	return array_values( $post_types );
}

/**
 * Choix des emplacements dans le champ ACF.
 *
 * @param array $field Champ select.
 * @return array
 */
function mrz_theme_load_hook_choices( $field ) {
	$field['choices'] = array( '' => __( 'Manual insertion only', 'mrz-theme-exp' ) ) + mrz_theme_global_section_hooks();

	return $field;
}
add_filter( 'acf/load_field/key=field_mrz_section_hook', 'mrz_theme_load_hook_choices' );

/**
 * Colonne « Où ça s'affiche » dans la liste des sections.
 *
 * @param array $columns Colonnes.
 * @return array
 */
function mrz_theme_section_columns( $columns ) {
	$date = $columns['date'] ?? null;
	unset( $columns['date'] );

	$columns['mrz_location'] = __( 'Where it shows', 'mrz-theme-exp' );
	if ( $date ) {
		$columns['date'] = $date;
	}

	return $columns;
}
add_filter( 'manage_mrz_section_posts_columns', 'mrz_theme_section_columns' );

/**
 * Contenu de la colonne.
 *
 * @param string $column  Colonne.
 * @param int    $post_id Section.
 */
function mrz_theme_section_column_content( $column, $post_id ) {
	if ( 'mrz_location' !== $column ) {
		return;
	}

	$hooks = mrz_theme_global_section_hooks();
	$hook  = get_post_meta( $post_id, 'mrz_section_hook', true );

	if ( isset( $hooks[ $hook ] ) ) {
		$priority = (int) get_post_meta( $post_id, 'mrz_section_priority', true );
		$display  = get_post_meta( $post_id, 'mrz_section_display', true );
		$targets  = array();
		foreach ( array_filter( (array) get_post_meta( $post_id, 'mrz_section_post_types', true ) ) as $post_type ) {
			$object    = get_post_type_object( $post_type );
			$targets[] = $object ? $object->labels->name : $post_type;
		}
		foreach ( array_filter( (array) get_post_meta( $post_id, 'mrz_section_posts', true ) ) as $target_id ) {
			$targets[] = get_the_title( (int) $target_id );
		}

		if ( 'include' === $display && $targets ) {
			/* translators: %s: list of content types or pages. */
			$scope = sprintf( __( 'only on %s', 'mrz-theme-exp' ), implode( ', ', $targets ) );
		} elseif ( 'exclude' === $display && $targets ) {
			/* translators: %s: list of content types or pages. */
			$scope = sprintf( __( 'everywhere except %s', 'mrz-theme-exp' ), implode( ', ', $targets ) );
		} else {
			$scope = __( 'whole site', 'mrz-theme-exp' );
		}

		/* translators: 1: location, 2: where it shows, 3: priority. */
		echo esc_html( sprintf( __( '%1$s, %2$s (priority %3$d)', 'mrz-theme-exp' ), $hooks[ $hook ], $scope, $priority ? $priority : 10 ) );
	} else {
		esc_html_e( 'Manual insertion only', 'mrz-theme-exp' );
	}
}
add_action( 'manage_mrz_section_posts_custom_column', 'mrz_theme_section_column_content', 10, 2 );

/**
 * Mega menu : une section globale placée au deuxième niveau d'un élément « mrz-megamenu »
 * forme une colonne (image, texte, bouton…). Ailleurs (premier niveau, menu mobile,
 * menu secondaire), l'élément est retiré : une section n'est pas un lien.
 *
 * @param array    $items Éléments du menu.
 * @param stdClass $args  Arguments de wp_nav_menu().
 * @return array
 */
function mrz_theme_megamenu_section_items( $items, $args ) {
	$by_id = array();
	foreach ( $items as $item ) {
		$by_id[ $item->ID ] = $item;
	}

	$is_desktop_menu = isset( $args->menu_class ) && 'mrz-menu' === $args->menu_class;

	return array_filter(
		$items,
		static function ( $item ) use ( $by_id, $is_desktop_menu ) {
			if ( 'mrz_section' !== $item->object ) {
				return true;
			}
			$parent = $by_id[ $item->menu_item_parent ] ?? null;

			return $is_desktop_menu && $parent && ! $parent->menu_item_parent && in_array( 'mrz-megamenu', (array) $parent->classes, true );
		}
	);
}
add_filter( 'wp_nav_menu_objects', 'mrz_theme_megamenu_section_items', 10, 2 );

/**
 * Colonne de mega menu : la section globale à la place du lien.
 *
 * @param string   $output Lien de l'élément.
 * @param WP_Post  $item   Élément du menu.
 * @param int      $depth  Profondeur.
 * @param stdClass $args   Arguments de wp_nav_menu().
 * @return string
 */
function mrz_theme_megamenu_section_output( $output, $item, $depth, $args ) {
	if ( 'mrz_section' !== $item->object || ! isset( $args->menu_class ) || 'mrz-menu' !== $args->menu_class ) {
		return $output;
	}

	// Pendant le rendu du menu, les sections ne comptent pas comme première section de la page (image prioritaire).
	$GLOBALS['mrz_theme_in_menu'] = true;
	ob_start();
	mrz_theme_render_global_section( (int) $item->object_id );
	$section = ob_get_clean();
	$GLOBALS['mrz_theme_in_menu'] = false;

	return '<div class="mrz-megamenu__section">' . $section . '</div>';
}
add_filter( 'walker_nav_menu_start_el', 'mrz_theme_megamenu_section_output', 10, 4 );

/**
 * Classe de la colonne qui porte une section globale.
 *
 * @param array   $classes Classes de l'élément.
 * @param WP_Post $item    Élément du menu.
 * @return array
 */
function mrz_theme_megamenu_section_class( $classes, $item ) {
	if ( 'mrz_section' === $item->object ) {
		$classes[] = 'mrz-megamenu__column--section';
	}

	return $classes;
}
add_filter( 'nav_menu_css_class', 'mrz_theme_megamenu_section_class', 10, 2 );

<?php
/**
 * Sections globales : contenus réutilisables (CPT mrz_section) insérés
 * sur tout le site par un hook, ou à la main par le layout « Section globale ».
 *
 * V1 : emplacement + priorité, sur tout le site. Les conditions d'affichage
 * (types de contenus, pages, exclusions) arrivent en V2.
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
		'mrz_before_header'                => __( 'Before the header', 'mrz-theme-exp' ),
		'mrz_after_header'                 => __( 'After the header', 'mrz-theme-exp' ),
		'mrz_before_content'               => __( 'Before the content', 'mrz-theme-exp' ),
		'mrz_after_content'                => __( 'After the content', 'mrz-theme-exp' ),
		'mrz_footer'                       => __( 'Footer (the site footer)', 'mrz-theme-exp' ),
		'mrz_before_footer'                => __( 'Before the footer', 'mrz-theme-exp' ),
		'mrz_after_footer'                 => __( 'After the footer', 'mrz-theme-exp' ),
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
			'show_in_nav_menus'   => false,
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
 * Affiche une section globale (garde-fou contre une section qui s'inclut elle-même).
 *
 * @param int $section_id ID de la section.
 */
function mrz_theme_render_global_section( $section_id ) {
	static $rendering = array();

	$section_id = absint( $section_id );
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
		if ( ! isset( $hooks[ $hook ] ) ) {
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
		/* translators: 1: location, 2: priority. */
		echo esc_html( sprintf( __( '%1$s, whole site (priority %2$d)', 'mrz-theme-exp' ), $hooks[ $hook ], $priority ? $priority : 10 ) );
	} else {
		esc_html_e( 'Manual insertion only', 'mrz-theme-exp' );
	}
}
add_action( 'manage_mrz_section_posts_custom_column', 'mrz_theme_section_column_content', 10, 2 );

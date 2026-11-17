<?php
/**
 * Moteur du constructeur de pages : champ ACF Flexible Content « mrz_builder ».
 *
 * Le groupe de champs du constructeur appartient au thème enfant (sections sur
 * mesure, préfixe mrzchild_). Le parent y injecte ses propres sections
 * (inc/builder-sections.php) et affiche chaque section avec layouts/{nom}.php,
 * cherché d'abord dans l'enfant.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Alerte dans l'admin si ACF Pro manque : le thème se rabat alors sur the_content().
 */
function mrz_theme_acf_missing_notice() {
	if ( class_exists( 'ACF' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	echo '<div class="notice notice-error"><p>' . esc_html__( 'MRZ Theme Experience requires Advanced Custom Fields Pro. Without it, pages only display their classic content.', 'mrz-theme-exp' ) . '</p></div>';
}
add_action( 'admin_notices', 'mrz_theme_acf_missing_notice' );

/**
 * Charge les groupes de champs du thème depuis acf-json/.
 *
 * @param array $paths Chemins existants.
 * @return array
 */
function mrz_theme_acf_load_paths( $paths ) {
	$paths[] = MRZ_THEME_DIR . '/acf-json';

	return $paths;
}
add_filter( 'acf/settings/load_json', 'mrz_theme_acf_load_paths' );

/**
 * En développement seulement (constante MRZ_THEME_DEV), les groupes du parent
 * (group_mrz_*) s'enregistrent dans son dossier acf-json/. Sur un site client,
 * ils ne s'écrivent jamais dans le thème parent. Les groupes de l'enfant
 * (group_mrzchild_*) suivent le chemin par défaut d'ACF : acf-json/ de l'enfant.
 *
 * @param array $paths Chemins d'enregistrement.
 * @param array $post  Groupe de champs.
 * @return array
 */
function mrz_theme_acf_save_paths( $paths, $post ) {
	if ( defined( 'MRZ_THEME_DEV' ) && MRZ_THEME_DEV && isset( $post['key'] ) && str_starts_with( $post['key'], 'group_mrz_' ) ) {
		return array( MRZ_THEME_DIR . '/acf-json' );
	}

	return $paths;
}
add_filter( 'acf/json/save_paths', 'mrz_theme_acf_save_paths', 10, 2 );

/**
 * Titre lisible des layouts repliés : nom du layout + titre ou début du texte.
 *
 * @param string $title  Titre par défaut.
 * @param array  $field  Champ flexible.
 * @param array  $layout Layout.
 * @return string
 */
function mrz_theme_layout_title( $title, $field, $layout ) {
	$label = get_sub_field( 'title' );

	if ( ! $label && 'mrz_global_section' === $layout['name'] ) {
		$section_id = (int) get_sub_field( 'section' );
		$label      = $section_id ? get_the_title( $section_id ) : '';
	}
	if ( ! $label ) {
		$content = get_sub_field( 'content' );
		$label   = $content ? wp_trim_words( wp_strip_all_tags( $content ), 8 ) : '';
	}

	return $label ? $title . ' : <em>' . esc_html( $label ) . '</em>' : $title;
}
add_filter( 'acf/fields/flexible_content/layout_title/name=mrz_builder', 'mrz_theme_layout_title', 10, 3 );

/**
 * La page a-t-elle des sections dans le constructeur ?
 *
 * @param int|null $post_id Post concerné.
 * @return bool
 */
function mrz_theme_has_builder( $post_id = null ) {
	if ( ! function_exists( 'have_rows' ) ) {
		return false;
	}

	$layouts = get_post_meta( $post_id ? $post_id : get_the_ID(), 'mrz_builder', true );

	return is_array( $layouts ) && ! empty( $layouts );
}

/**
 * Affiche toutes les sections du constructeur.
 *
 * @param int|null $post_id Post concerné.
 */
function mrz_theme_render_builder( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	while ( have_rows( 'mrz_builder', $post_id ) ) {
		the_row();
		$layout = str_replace( '_', '-', sanitize_key( get_row_layout() ) );
		get_template_part( 'layouts/' . $layout );
	}
}

/**
 * Ouvre une section : fond, espacement, largeur, ancre (réglages communs).
 *
 * @param string $layout Nom du layout.
 * @param array  $args   image (ID d'image de fond), class (classes en plus : chaîne
 *                       séparée par des espaces, ou tableau).
 */
function mrz_theme_section_open( $layout, $args = array() ) {
	$settings = (array) get_sub_field( 'settings' );

	$background = $settings['background'] ?? 'none';
	$spacing    = $settings['spacing'] ?? 'normal';
	$width      = $settings['width'] ?? 'container';
	$anchor     = $settings['anchor'] ?? '';
	$image_id   = ! empty( $args['image'] ) ? (int) $args['image'] : (int) ( $settings['background_image'] ?? 0 );

	$classes = array(
		'mrz-section',
		'mrz-section--' . $layout,
		'mrz-bg--' . $background,
		'mrz-space--' . $spacing,
	);
	if ( $image_id ) {
		$classes[] = 'mrz-has-image';
	}
	if ( ! empty( $args['class'] ) ) {
		$extra   = is_array( $args['class'] ) ? $args['class'] : preg_split( '/\s+/', (string) $args['class'], -1, PREG_SPLIT_NO_EMPTY );
		$classes = array_merge( $classes, $extra );
	}

	$containers = array(
		'container' => 'mrz-container',
		'narrow'    => 'mrz-container mrz-container--narrow',
		'wide'      => 'mrz-container mrz-container--wide',
		'full'      => 'mrz-container--full',
	);

	printf(
		'<section%1$s class="%2$s"%3$s><div class="%4$s">',
		$anchor ? ' id="' . esc_attr( sanitize_title( $anchor ) ) . '"' : '',
		esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) ),
		$image_id ? ' style="background-image:url(' . esc_url( wp_get_attachment_image_url( $image_id, 'full' ) ) . ')"' : '',
		esc_attr( $containers[ $width ] ?? 'mrz-container' )
	);
}

/**
 * Ferme une section.
 */
function mrz_theme_section_close() {
	echo '</div></section>';
}

/**
 * Titre et introduction d'une section.
 *
 * @param string $title Titre.
 * @param string $intro Introduction.
 */
function mrz_theme_section_heading( $title, $intro = '' ) {
	if ( ! $title && ! $intro ) {
		return;
	}

	echo '<header class="mrz-section__heading">';
	if ( $title ) {
		echo '<h2 class="mrz-section__title">' . esc_html( $title ) . '</h2>';
	}
	if ( $intro ) {
		echo '<p class="mrz-section__intro">' . esc_html( $intro ) . '</p>';
	}
	echo '</header>';
}

